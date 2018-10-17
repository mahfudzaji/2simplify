<?php

namespace App\Core;

use App\Core\App;

class Auth{

    public static function user(){
        
        if(!isset($_SESSION['sim-isLogin'])||$_SESSION['sim-isLogin']==false||empty($_SESSION)){
            /* header("Location: /");
            exit; */
            redirectWithMessage([['Mohon login terlebih dahulu', 0]], '/');
        }
        
        $verifyingEmail=password_verify(App::get('config')['salt'].$_SESSION['sim-email'], $_SESSION['sim-forVerify']);
        
        if(!$verifyingEmail){
            redirectWithMessage("[[email anda tidak terverifikasi, 0]]", '/');
        }

        $parameters=[
            'id',
            'name',
            'email'
        ];

        $where=[
            'email'=>$_SESSION['sim-email']
        ];

        $checkUserEmailForAuthentication=App::get('builder')->getSpecificData('users',$parameters, $where,'','User');
        
        if(count($checkUserEmailForAuthentication)==0){
            redirectWithMessage([['Maaf anda tidak memiliki hak akses masuk halaman ini', 0]], getLastVisitedPage());
        }

        return $checkUserEmailForAuthentication;
    }

}

?>