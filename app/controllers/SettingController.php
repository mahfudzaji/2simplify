<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class SettingController{

    private $role, $userId;

    public function __construct(){
        $user=Auth::user();
        
        $this->userId=$user[0]->id;

        $this->role = App::get('role');
        
        $this->role -> getRole($this->userId);
    }

    public function index(){
        //contain profile settings

        /* if(!$this->role->can('view-dashboard')){
            redirectWithMessage([[ returnMessage()['dashboard']['accessRight']['view'] , 0]], getLastVisitedPage());
        } */

        $context = filterUserInput($_GET['c']);
        
        switch ($context){
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

    public function profileSetting(){
        $builder = App::get('builder');

        $id = $this->userId;

        //$profile = $builder->getSpecificData('users', ['*'], ['id'=>$this->userId], '', 'User');

        $profile = $builder->custom("SELECT a.id, a.name, a.email, a.code, 
        b.name as department, c.upload_file as photo, 
        case active when 1 then 'Active' else 'Deactive' end  as active, 
        date_format(a.created_at, '%d %M %Y') as created_at, date_format(a.updated_at, '%d %M %Y') as updated_at 
        FROM users as a 
        INNER JOIN departments as b on a.department=b.id 
        LEFT JOIN upload_files as c on a.photo=c.id where a.id=$id", 'User');

        if(count($profile)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }

        view('/setting/index', compact('profile'));
    }

    public function userSetting(){
        if(!$this->role->can("view-user")){
            redirectWithMessage([["Anda tidak memiliki hak untuk mendaftarkan user", 0]],'/');
        }

        $builder = App::get('builder');

        //Get list of user account registered
        $users=$builder->getAllData('users','User');

        view('/setting/user', compact('users'));

    }
}