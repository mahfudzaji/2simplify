<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;

class HomeController{

    private $role,$userId,$roleOfUser;
    
    public function __CONSTRUCT(){
        $user=Auth::user();
        $this->userId=Auth::user()[0]->id;
        $this->role = App::get('role'); 
        $this->roleOfUser = $this->role->getRole($this->userId);

    }

    public function index(){

        if($this->role->hasRole("superadmin")){

            $builder = App::get('builder');

            //Get list of user account registered
            $users=$builder->getAllData('users','User');

            $roleOfUser = $this->roleOfUser;

            $departments = $builder->getAllData('departments', 'User');

            $currentDate=date('Y-m-d');
            $startDate=date('Y-m-d', strtotime("-2 days"));

            //dd("select activity, date_format(created_at, '%d %M %Y') as created_at from daily_activities where created_at between '$startDate' and '$currentDate'");
            
            $activities = $builder->custom("select group_concat(activity separator '<br>') as activity, date_format(created_at, '%d %M %Y') as created_at from daily_activities where date_format(created_at, '%Y-%m-%d') between '$startDate' and '$currentDate' group by date_format(created_at, '%Y-%m-%d')", 'Internal');
            
            $events = $builder->getSpecificData('events', ['*'], ['created_by' => $this->userId ], '', 'Internal');

            $checkIfAnyCompanyRegistered=App::get('builder')->getAllData('companies', 'User');

            if(!$checkIfAnyCompanyRegistered){

                $provinces = $builder->getAllData('provinces', 'Partner');
    
                view('register_first_company', compact('provinces'));
            }else{
                
                view('home', compact('users', 'roleOfUser', 'departments', 'activities', 'events'));
            
            }

        }else{

            view('home', compact('roleOfUser'));
        
        }
    }

    public function activityCreate(){
        if(!$this->role->can("create-activity")){
            //https://paulund.co.uk/use-php-to-detect-an-ajax-request//
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([["Anda tidak memiliki hak untuk membuat activity", 0]], getLastVisitedPage());
            }
        }

        $builder = App::get('builder');

        $data = [
            'activity' => filterUserInput($_POST['activity']),
            'created_by' => substr($_SESSION['sim-id'], 3, -3)
        ];

        $insertToDailyActivities = $builder->insert('daily_activities', $data);

        ///dd($insertToDailyActivities);

        if(!$insertToDailyActivities){
            recordLog('Daily activity', 'Membuat catatan aktifitas gagal.' );
            redirectWithMessage([[ 'Membuat catatan aktifitas gagal.' , 0]],getLastVisitedPage());
            exit();
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ 'Membuat catatan aktifitas berhasil.' , 1]],getLastVisitedPage());

    }

    public function eventCreate(){
        if(!$this->role->can("create-event")){
            //https://paulund.co.uk/use-php-to-detect-an-ajax-request//
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage(["Anda tidak memiliki hak untuk membuat data event", 0], getLastVisitedPage());
            }
        }

        $builder = App::get('builder');

        $data = [
            'event' => filterUserInput($_POST['event']),
            'event_date' => filterUserInput($_POST['event_date']),
            'created_by' => substr($_SESSION['sim-id'], 3, -3),
            'updated_by' => substr($_SESSION['sim-id'], 3, -3)
        ];

        $insertToEvents = $builder->insert('events', $data);

        if(!$insertToEvents){
            recordLog('Event', 'Membuat data event gagal.' );
            redirectWithMessage([[ 'Membuat data event gagal.' , 0]],getLastVisitedPage());
            exit();
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ 'Membuat data event berhasil.' , 1]],getLastVisitedPage());
    }

    public function eventUpdate(){
        if(!$this->role->can("update-event")){
            //https://paulund.co.uk/use-php-to-detect-an-ajax-request//
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage(["Anda tidak memiliki hak untuk membuat data event", 0], getLastVisitedPage());
            }
        }

        $builder = App::get('builder');

        $id = filterUserInput($_POST['eid']);

        $data = [
            'event' => filterUserInput($_POST['event']),
            'event_date' => filterUserInput($_POST['event_date']),
            'updated_by' => substr($_SESSION['sim-id'], 3, -3)
        ];

        $updateEvent = $builder->update('events', $data, ['id' => $id], '', 'Internal');

        if(!$updateEvent){
            recordLog('Event', 'Memperbaharui data event gagal.' );
            redirectWithMessage([[ 'Memperbaharui data event gagal.' , 0]],getLastVisitedPage());
            exit();
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ 'Memperbaharui data event berhasil.' , 1]],getLastVisitedPage());
    }

}
?>