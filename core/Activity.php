<?php

namespace App\Core;

class Activity{

    /*
        Format:
        recorded_at|context|user|activity
    */

    public function recordLog($user, $context, $activity){
      
        //record log to database
        $builder= App::get('builder');

        $insertLog=$builder->insert('activity_history',[
            'user'=>$user,
            'context'=>$context,
            'activity'=>$activity]
        ); 

        //record log to file

        date_default_timezone_set("Asia/Jakarta");
        $time= date("d-m-Y h:i:sa", time());
        $file=fopen("Activity.log",'a');
        fwrite($file, "$time\t$context\t$user\t$activity\r\n");
        $fclose($file);
      
    }


}

?>