<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;
use PHPMailer;

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

class PageController{

    /*
        This is index page. 
        it will redirected to index page that there is login form
    */
    public function index(){
        
        /*
            check if any user registered. if none, redirect to register page.
        */
        $checkIfAnyAccountRegistered=App::get('builder')->getAllData('users', 'User');

        $recaptcha = App::get('config')['recaptcha']['development'];

        if(!$checkIfAnyAccountRegistered){

            $builder = App::get('builder');

            $departments = $builder->getAllData('departments', 'User');

            view('register',['firstUser'=>true, 'message'=>'Anda adalah calon user pertama. Anda akan didaftarkan sebagai admin', 'departments' => $departments]);
            
        }else{
            if(!isset($_SESSION['sim-isLogin'])||$_SESSION['sim-isLogin']==false||empty($_SESSION)){
                view('index', compact('recaptcha')); 
            }else{

                redirect('home');
                exit;

            }
            
        }

    }

    public function login(){
        
        view('login');
    
    }   

    public function forget(){

        view('need_email_confirmation');        
    
    }

    public function reset(){
    
        view('reset');
    
    }

    public function register(){
        
        $role=App::get('role');
        
        $userId=Auth::user()[0]->id;

        $role->getRole($userId);

        $builder = App::get('builder');

        $departments = $builder->getAllData('departments', 'User');

        if(!$role->hasRole('superadmin')){

            redirectWithMessage(['Anda tidak memiliki hak akses ke halaman tersebut', 0], "home");
            //redirectWithMessage(['Maaf, anda tidak memiliki hak akses', 0], getLastVisitedPage());
        }
        
        view('register', ['firstUser'=>false, 'message'=>'', 'departments' => $departments]);
        
    }

    public function userConfirmation(){
        //check the status of the user account
        $confirm=filterUserInput($_GET['c']);
        $email=filterUserInput($_GET['u']);

        $parameters=[
           '*',
        ];

        $where=[
            'confirmation_link' => $confirm,
            'email' => $email,
        ];

        $result = App::get('builder')->getSpecificData('users', $parameters, $where, 'and', 'User');
        
        //if already active then give the message that says this account is active
        //otherwise make it active
        if(!$result){
            redirectWithMessage([['Operasi gagal atau data tidak ditemukan',0]],'/');
        }

        if($result[0]->active==1){
            //redirect to change/update password
            redirectWithMessage([['Akun ini sudah pernah diaktifkan. Silakan apabila ingin mengubah password',0]] ,'reset');
        }

        view('set_password');
    }

    //DEFAULT PARAMETER
    public function parameterShow(){
        $builder = App::get('builder');
        $parameters = $builder->getAllData('default_parameter', 'Internal');
  
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode($parameters);
            exit();
        }else{
            redirect(getLastVisitedPage());
        }

    }

    //ERROR REDIRECT
    //404

    public function pageNotFound(){
        view('404');
    }

    //TESTING
    public function testing(){
        $key= Key::createNewRandomKey();
        $file = fopen("key.txt", "w") or die("Unable to open file!");
        $cipher = $key->saveToAsciiSafeString();
        fwrite($file, $cipher);
        fclose($file);
    }

    function loadEncryptionKeyFromConfig(){
        $file=fopen('key.txt', 'r') or die("salah");
        $key=fread($file,filesize("key.txt"));
        fclose($file);
        return Key::loadFromAsciiSafeString($key);
    }

    function loadCipherText(){
        $file=fopen('ciphertext.txt', 'r') or die("salah");
        $ciphertext=fread($file,filesize("ciphertext.txt"));
        fclose($file);
        return $ciphertext;
    }

    public function saveCipherText(){
       
        $key = $this->loadEncryptionKeyFromConfig();
        $cipherText=Crypto::encrypt('my name mahfudz aji wicaksono', $key);
        $file=fopen('ciphertext.txt', 'w');
        fwrite($file, $cipherText);
        fclose($file);
    }

    public function showCipherText(){
        $key=$this->loadEncryptionKeyFromConfig();
        $cipherText=$this->loadCipherText();

        try{
            $secret_data=Crypto::decrypt($cipherText, $key);
        }catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
            // An attack! Either the wrong key was loaded, or the ciphertext has
            // changed since it was created -- either corrupted in the database or
            // intentionally modified by Eve trying to carry out an attack.

            // ... handle this case in a way that's suitable to your application ...
        }
        echo $secret_data;
    }

}

?>