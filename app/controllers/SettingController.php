<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class SettingController{

    private $role, $userId, $roleOfUser;

    public function __construct(){
        $user=Auth::user();
        
        $this->userId=$user[0]->id;

        $this->role = App::get('role');
        
        $this->roleOfUser = $this->role -> getRole($this->userId);

        //dd($roleOfUser);
    }

    public function index(){
        //contain profile settings

        /* if(!$this->role->can('view-dashboard')){
            redirectWithMessage([[ returnMessage()['dashboard']['accessRight']['view'] , 0]], getLastVisitedPage());
        } */

        $context = filterUserInput($_GET['c']);
        
        switch ($context){
            case 'company':
                $this->companySetting();
                break;
            case 'user':
                $this->userSetting();
                break;
            case 'profile':
                $this->profileSetting();
                break;
            case 'form':
                $this->formSetting();
                break;
            case 'permission':
                $this->permissionSetting();
                break;
            default:
                redirectWithMessage([[ returnMessage()['dashboard']['unknown'] , 0]], getLastVisitedPage());
                break;
        }

    }

    public function companySetting(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

    }

    public function profileSetting(){
        $builder = App::get('builder');

        $id = $this->userId;

        //$profile = $builder->getSpecificData('users', ['*'], ['id'=>$this->userId], '', 'User');

        $departments = $builder->getAllData('departments', 'User');

        $profile = $builder->custom("SELECT a.id, a.name, a.email, a.code, a.department as idd,
        b.name as department, c.upload_file as photo, 
        case active when 1 then 'Active' else 'Deactive' end  as active, 
        date_format(a.created_at, '%d %M %Y') as created_at, date_format(a.updated_at, '%d %M %Y') as updated_at 
        FROM users as a 
        INNER JOIN departments as b on a.department=b.id 
        LEFT JOIN upload_files as c on a.photo=c.id where a.id=$id", 'User');

        if(count($profile)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }

        view('/setting/index', compact('profile', 'departments'));
    }

    public function profileUpdate(){
        $builder = App::get('builder');

        $id = $this->userId;

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach(['name' => 'required', 'code' => 'required', 'email' => 'required', 'department' => 'required'] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }


        $updateProfile = $builder->update("users", $data, ['id' => $id], '', 'Setting');

        if(!$updateProfile ){
            recordLog("profile", "Pembaharuan profile gagal");
            redirectWithMessage([["Pembaharuan profile gagal",0]],getLastVisitedPage());
        }

        recordLog("profile", "Pembaharuan profile  berhasil");

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pembaharuan profile berhasil",1]],getLastVisitedPage());

    }

    public function userSetting(){
        /* if(!$this->role->can("view-user")){
            redirectWithMessage([["Anda tidak memiliki hak untuk mendaftarkan user", 0]],'/');
        }*/

        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }
        
        $builder = App::get('builder');

        $departments = $builder->getAllData('departments', 'User');

        //Get list of user account registered
        $users=$builder->custom("SELECT a.id, a.name, a.email, a.code, a.department as idd,
        b.name as department, c.upload_file as photo, 
        case active when 1 then 'Active' else 'Deactive' end  as active, 
        a.active as ida,
        e.name as user_role,
        d.role_id as idr,
        date_format(a.created_at, '%d %M %Y') as created_at, date_format(a.updated_at, '%d %M %Y') as updated_at 
        FROM users as a 
        INNER JOIN departments as b on a.department=b.id 
        INNER JOIN role_user as d on a.id=d.user_id
        INNER JOIN roles as e on d.role_id=e.id
        LEFT JOIN upload_files as c on a.photo=c.id", 'User');

        view('/setting/user', compact('users', 'departments'));

    }

    public function userUpdate(){
        $builder = App::get('builder');

        $id = $this->userId;

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach(['name' => 'required', 'email' => 'required', 'department' => 'required'] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }


        $updateProfile = $builder->update("users", $data, ['id' => $id], '', 'Setting');

        if(!$updateProfile ){
            recordLog("profile", "Pembaharuan profile gagal");
            redirectWithMessage([["Pembaharuan profile gagal",0]],getLastVisitedPage());
        }

        recordLog("profile", "Pembaharuan profile  berhasil");

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pembaharuan profile berhasil",1]],getLastVisitedPage());

    }


}