<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class NotificationController{
    
    private $role, $userId;


    public function __construct(){
        $user=Auth::user();

        $this->userId=$user[0]->id;

        $this->role = App::get('role');

        $this->role->getRole($this->userId);

    }

    public function index(){

        $builder = App::get('builder');

        /* 
        $vacationData = $builder->custom("SELECT a.id, b.name as submitter, b.code,
        a.day_used, 
        date_format(a.created_at, '%d %M %Y') as created_at,
        c.name as requisite,
        d.name as department,
        GROUP_CONCAT(date_format(e.vacation_date, '%d %M %Y') order by e.vacation_date ASC SEPARATOR ', ') as vacation_date
        FROM form_vacation as a 
        inner join users as b on a.submitter=b.id 
        inner join requisite as c on a.requisite=c.id 
        inner join departments as d on b.department=d.id
        inner join vacation_date as e on a.id=e.document_number
        where a.approved_by=$this->userId and a.approved=0 group by a.id order by a.created_at ASC ", 'Document');

        view("notification", compact('vacationData')); 
        */
        
        $thisAccount=substr($_SESSION['sim-id'], 3, -3);
        
        $notificationData = $builder->custom("SELECT a.message, b.name, a.document, a.document_number, a.already_read, date_format(a.created_at, '%d %M %Y') as created_at 
        FROM `notifications` as a 
        inner join documents as b on a.document=b.id 
        where a.for_user=$thisAccount", 'Document');

        view("notification", compact('notificationData'));
    }
}

?>