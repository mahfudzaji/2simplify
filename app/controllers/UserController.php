<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;

class UserController{

    private $role;

    private $placeholderResetPassword = array(
        'email' => 'required|email',
        'password' => 'required',
        'new_password' => 'required',
        're_password' => 'required'
    );

    public function checkSession(){

        $user=Auth::user();
        
        $userId=$user[0]->id;
        
        //checking access right      
        $this->role = App::get('role');
        
        $this->role -> getRole($userId);

    }

    /*
    Login functionality. 
    processing login when the account is exist then will redirected to home page
    otherwise it will redirected back to login page with error message
    */

    public function login(){

        $password=filterUserInput($_POST['password']);
        $email=filterUserInput($_POST['email']);      

        $password=md5(App::get('config')['salt'].$password);

        if(!isset($password,$email) || isEmpty([$password,$email])){
            redirectWithMessage([["Mohon untuk form diisi lengkap", 0]],'/');
        }

        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            redirectWithMessage([["Mohon periksa alamat email anda", 0]],'/');
        }

        /* $recaptcha = App::get('config')['recaptcha']['development'];

        //https://github.com/google/recaptcha
        if(isset($_POST['g-recaptcha-response'])){
            $recaptcha = new \ReCaptcha\ReCaptcha($recaptcha['secretKey']);

            // If file_get_contents() is locked down on your PHP installation to disallow
            // its use with URLs, then you can use the alternative request method instead.
            // This makes use of fsockopen() instead.
            //  $recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\SocketPost());

            // Make the call to verify the response and also pass the user's IP address
            $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()){
                // If it's not successful, then one or more error codes will be returned.
                redirectWithMessage([["Mohon verifikasi ulang", 0]], getLastVisitedPage());
            }
        }else{
            redirectWithMessage([["Mohon verifikasi bahwa anda bukan robot", 0]], getLastVisitedPage());
        } */

        $parameters=[
            'id',
            'name',
            'email',
            'password'
        ];

        $where=[
            'email' => $email,
            'password' => $password,
            'active' => 1
        ];
        
        $userLogin=App::get('builder')->getSpecificData('users',$parameters,$where,'and','User');

        if(count($userLogin)==0){
            $_SESSION['sim-messages']=[["Akun tidak terdaftar atau belum aktif. Untuk mengaktifkan mohon hubungi admin.", 0]];
            redirect('/');
            exit();
        }
        
        $forVerify=App::get('config')['salt'].$where['email'];
        $id=$userLogin[0]->id;

        $_SESSION['sim-isLogin']=true;
        $_SESSION['sim-forVerify']=password_hash($forVerify, PASSWORD_BCRYPT);
        $_SESSION['sim-email']=$where['email'];
        $_SESSION['sim-name']=$userLogin[0]->name;
        $_SESSION['sim-id']=rand(100,999).$id.rand(100,999);
        $_SESSION['sim-lang']='id';
        
        $_SESSION['sim-messages']=[["Welcome", 1]];
  
        recordLog('Login', "Login berhasil");

        header("Location: /home");
        exit();

    }

    public function logout(){
        
        recordLog('Logout', "Logout berhasil");

        session_unset();

        session_destroy();

        redirect("/");

        exit();
        
    }

    public function registerFirstUser(){
        
        //check whether users table has row or not
        //if not, script continue otherwise redirect back to index page

        $this->checkSession();

        $builder=App::get('builder');
        
        $checkAnyUser=$builder->getAllData('users', 'User');
        
        if($checkAnyUser){
            redirectWithMessage(['Maaf, anda bukan merupakan pengguna pertama',0], '/');
            
        }

        $this->processingRegister($builder);

        //give him admin role
        
        $firstUserId=$builder->getPdo()->lastInsertId();

        $insertAdminRole=$builder->insert('role_user', ['user_id'=>$firstUserId, 'role_id'=>1]);
        
        if(!$insertAdminRole){
            redirectWithMessage(["Pendaftaran sebagai admin gagal. Coba lagi", 0], '/');
        }

        $builder->save();

        recordLog('Register owner', "Register user pertama berhasil");

        redirectWithMessage(['Anda telah didaftarkan sebagai admin',1],'/');
    }

    /*
        Registering user and if success, send link to user's email for verify the user's account. 
        the link redirect to a page that the user can fill the password. 
        if success, user will redirected to login page with notification message. 
    */

    public function register(){

        $this->checkSession();

        if(!$this->role->can("create-user")){
            redirectWithMessage([["Anda tidak memiliki hak untuk mendaftarkan user", 0]],'/');
        }

        $userRole=filterUserInput($_POST['role']);

        //dd($_POST);

        $builder=App::get('builder');

        $processingRegister = $this->processingRegister($builder);

        if(!$processingRegister){
            redirectWithMessage([['Pendaftaran user gagal', 0]], getLastVisitedPage());
        }

        $userId=$builder->getPdo()->lastInsertId();

        $insertUserRole=$builder->insert('role_user', ['user_id'=>$userId, 'role_id'=>$userRole]);

        recordLog('Register', "Register berhasil");

        $builder->save();

        redirectWithMessage([['Pendaftaran user berhasil', 1]], 'home');
    }

    public function processingRegister($builder){

        $name=filterUserInput($_POST['username']);
        $email=filterUserInput($_POST['email']);
        $department=filterUserInput($_POST['department']);
        $link=uniqid();

        /*
        $validasi=Auth::validate([$name,$password,$email],[
            'username'=>'required',
            'password'=>'required',
            'email'=>'required|email'
            ]);
        
        
        if(count($validasi)>0){
            $message=new Message();
            $message->setMessage('error',['error'=>$validasi]);
            return header("Location:/");
        }*/

        if(!isset($name,$email) || isEmpty([$name,$email])){
            redirectWithMessage([["mohon untuk form diisi lengkap", 0]],'/home');
        }

        if(!preg_match("/^[a-zA-Z\s]*$/",$name)){
            redirectWithMessage([["username hanya boleh berupa huruf", 0]],'/home');
        }


        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            redirectWithMessage([["periksa alamat email anda", 0]],'/home');
        }
        
        if(strlen($name)<3 || strlen($name)>20){
            redirectWithMessage([["username yang diterima hanya boleh terdiri 3-20 karakter", 0]],'/home');
        }elseif(strlen($email)<3 || strlen($email)>30){
            redirectWithMessage([["email yang diterima hanya boleh terdiri 3-30 karakter", 0]],'/home');
        }

        
        //check whether email already used or not
        $checkEmailExist = $builder->getSpecificData("users", ['*'], ['email' => $email], '', 'User');
        if(count($checkEmailExist)>0){
            redirectWithMessage([["Email sudah pernah didaftarkan", 0]],'/home');
        }
        
        $parameters=[
            'name' => $name,
            'email' => $email,
            'department' => $department, 
            'confirmation_link' => $link,
            'created_by' => substr(substr($_SESSION['sim-id'],3), 0, -3),
            'updated_by' => substr(substr($_SESSION['sim-id'],3), 0, -3)
        ];

        //here is processing upload file then get the result
        if(isset($_FILES["photo"]) && !empty($_FILES["photo"]) && $_FILES["photo"]!=''){
            
            $processingUpload = new UploadController();

            $uploadResult = $processingUpload->processingUpload($_FILES["photo"]);

            if($uploadResult){
                $lastUploadedId=$processingUpload->getLastUploadedId();

                $parameters['photo']=$lastUploadedId;
            }else{
                $_SESSION['sim-messages']=[['Maaf, gagal upload photo', 0]];
            }
            unset($processingUpload);
  
        }

        if(isset($_FILES["signature"]) && !empty($_FILES["signature"]) && $_FILES["signature"]!=''){

            $processingUpload = new UploadController();

            $uploadResult = $processingUpload->processingUpload($_FILES["signature"]);

            if($uploadResult){
                $lastUploadedId=$processingUpload->getLastUploadedId();

                $parameters['signature']=$lastUploadedId;
            }else{
                $_SESSION['sim-messages']=[['Maaf, gagal upload signature', 0]];
            }
            unset($processingUpload);

        }

        //after got the expected return then record it into database
        $result=$builder->insert('users', $parameters);

        if($result){

            //send verification mail
            $mail = App::get('mail');

            $mail->setFrom('aji@sentranetcomindo.com', 'Mailer');
            $mail->addAddress($parameters['email'], $name);     
            $mail->addReplyTo('aji@sentranetcomindo.com', 'Information');

            $mail->isHTML(true);                                  

            $mail->Subject = 'Confirmation link to activate account';
            $mail->Body    = "To activate your account please go to this <a href=localhost:8000/confirmation?c=$link&u=$email>link</a>.";
            $mail->AltBody = '';

            if(!$mail->send()) {
                $_SESSION['sim-messages']=[['Message could not be sent.','Mailer Error: ' . $mail->ErrorInfo, 0]];
                return false;
            } else {
                $_SESSION['sim-messages']=[['Message has been sent', 1]];
                return true;  
            }
                   
        }

        return false;

    }

    public function userConfirmation(){
        
        //check the status of the user account
        $password=filterUserInput($_POST['password']);
        $confirm=filterUserInput($_POST['c']);
        $email=filterUserInput($_POST['u']);
        $active=0;

        if(!isset($password) || empty($password)){
            redirectWithMessage([["Isi password anda",0]], getLastVisitedPage());
        }

        //allowing a-zA-Z0-9~!@#$
        if(!preg_match("/^[\w~!@#$]*$/",$password)){
            redirectWithMessage([["Password hanya boleh berupa angka dan huruf",0]], getLastVisitedPage());
        }

        //create new object
        $builder = App::get('builder');

        //get all rows from the users table
        $rows = $builder->getAllData('users', 'User');

        //if the record is only one. make it active, since the first user is admin. otherwise set it non active.
        if(count($rows)==1){
            $active=1;
        }

        $toUpdate=[
            'password' => md5(App::get('config')['salt'].$password),
            'active' => $active,
        ];

        $where=[
            'confirmation_link' => $confirm,
            'email' => $email,
        ];
        
        //update
        $result = $builder->update('users', $toUpdate, $where, "&&", 'User');
        
        if(!$result){
            recordLog('Konfirmasi', "Konfirmasi gagal");
            redirectWithMessage([["Maaf, konfirmasi gagal", 0]], getLastVisitedPage());
        }

        recordLog('Konfirmasi pengguna', "Konfirmasi pengguna berhasil");

        //commit
        $builder->save();

        //redirect to index page with message
        redirectWithMessage([["Pengaturan password berhasil", 1]], '/');
        
    }

    public function forgetPassword(){
        $email=filterUserInput($_POST['email']);
        
        if(!isset($email) || empty($email)){
            $_SESSION['sim-messages']=[["Mohon email untuk diisi", 0]];
            redirect("/");exit();
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION['sim-messages']=[["Mohon email diisi dengan benar", 0]];
            redirect("/");exit();
        }
        
        $parameters=[
            '*'
        ];

        $where=[
            'email' => $email,
            'active' => 1,
        ];

        $builder = App::get('builder');

        $result=$builder->getSpecificData("users", $parameters, $where, "and", "User");
        
        if(!$result){
            redirectWithMessage(["Email yang anda masukkan tidak terdaftar atau tidak aktif", 0], '/');
        }

        $resetPassword=uniqid();

        $toUpdate = [
            'password' => md5(App::get('config')['salt'].$resetPassword),
        ];

        $where = [
            'email' => $email,
            'active' => 1,
        ];

        $updateTask = $builder -> update('users', $toUpdate, $where, 'and', 'User');

        recordLog('Lupa password', "User diberi password baru");

        $builder->save();

        //sent email contain new password
        $mail = App::get('mail');

        $mail->setFrom('aji@sentranetcomindo.com', 'Simplify mailer');
        $mail->addAddress($email, $result[0]->name);     
        $mail->addReplyTo('aji@sentranetcomindo.com', 'Information');

        $mail->isHTML(true);                               

        $mail->Subject = 'Your New Password';
        $mail->Body    = "Here is your new password ". $resetPassword. " . You can change it later.";
        $mail->AltBody = '';

        if(!$mail->send()) {
            $_SESSION['sim-messages']=[['Maaf, pesan tidak dapat dikirimkan ke email anda.','Mailer Error: ' . $mail->ErrorInfo, 0]];
        } else {
            $_SESSION['sim-messages']=[['Pesan telah dikirimkan ke email anda. Mohon cek dan reset password anda', 1]];
        }

        //redirect back to index page
        redirect("/");
        exit();

    }

    /*
        reset password
    */
    public function resetPassword(){

        $this->checkSession();

        //checking form requirement
        $data=[];

        $passingRequirement = true;

        foreach($this->placeholderResetPassword as $key => $value){
            if(checkRequirement($value, $key, $_POST[$key])){
                $data[$key]=filterUserInput($_POST[$key]);
            }else{
                $passingRequirement=false;
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $email = $data['email'];
        $password = md5(App::get('config')['salt'].$data['password']);
        $newPassword = $data['new_password'];
        $confirmNewPassword = $data['re_password'];

        if(!isset($email) || empty($email)){
            redirectWithMessage(["Mohon email untuk diisi", 0], '/home');
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            redirectWithMessage(["Mohon email diisi dengan benar", 0], '/home');
        }

        if(!isset($password) || empty($password)){
            redirectWithMessage(["Mohon password untuk diisi", 0], '/home');
        }

        if(!isset($newPassword) || empty($newPassword)){
            redirectWithMessage(["Mohon password untuk diisi", 0], '/home');
        }

        if(!isset($confirmNewPassword) || empty($confirmNewPassword)){
            redirectWithMessage(["Mohon password untuk diisi", 0], '/home');
        }

        if($newPassword != $confirmNewPassword){
            redirectWithMessage(['Password baru yang anda masukkan tidak sama', 0], '/home');
        }

        //allowing a-zA-Z0-9~!@#$
        if(!preg_match("/^[\w~!@#$]*$/",$newPassword) || !preg_match("/^[\w~!@#$]*$/",$confirmNewPassword)){
            redirectWithMessage(["password hanya boleh berupa angka dan huruf",0], '/home');
        }

        $toUpdate=[
            'password' => md5(App::get('config')['salt'].$newPassword),
        ];

        $where=[
            'email' => $email,
            'active' => 1,
        ];
        
        $builder = App::get('builder');
        
        $builder->update('users', $toUpdate, $where, "and", 'User');

        recordLog('Reset password', "Reset password berhasil.");
        
        $builder->save();
        
        redirectWithMessage(["Reset password berhasil.",0], '/home');

    }

    /*
        This function is to toggle the status of the user. 
        @example: when user status is ACTIVE, then goes to NON-ACTIVE and vice versa.
        @return: status and message.
    */
    public function toggleUserStatus(){

        $this->checkSession();

        if(!$this->role->can("update-user")){
            redirectWithMessage([["Anda tidak memiliki hak untuk mendaftarkan user", 0]],'/');
        }
        
        $email=filterUserInput($_POST['email']);

        if(!isset($email) || empty($email)){
            redirectWithMessage([["Mohon email untuk diisi", 0]], getLastVisitedPage());
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            redirectWithMessage([["Mohon email diisi dengan benar", 0]], getLastVisitedPage());
        }

        //get user data based on email
        $parameters=[
            'id',
            'active'
        ];

        $where=[
            'email' => $email
        ];

        $builder=App::get('builder');
        
        $result = $builder->getSpecificData('users', $parameters, $where, 'and', 'User');
        
        //save the data in variable
        $id=$result[0]->id;
        //if status of user is active then make it deactive
        $active=$result[0]->active;
        
        if($active==1){
            $active=0;
        }else{
            $active=1;
        }


        //update status of the user
        $toUpdate=[
            'active' => $active,
            'updated_by' => substr(substr($_SESSION['sim-id'],3), 0, -3)
        ];

        $where=[
            'id' => $id,
            'email' => $_POST['email']
        ];

        $builder->update('users', $toUpdate, $where, 'and', 'User');

        $builder->save();

        redirectWithMessage([["Pengubahan status user berhasil", 1]],  getLastVisitedPage());
    }

}

?>