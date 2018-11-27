<?php

use App\Core\App;
use App\Core\Role;
use App\Core\Request;

App::bind('config', require 'config.php');

App::bind('builder', new QueryBuilder(
    Connection::connect(App::get('config')['connection'])
));

App::bind('role', new Role());

App::bind('mail', new Mail());

function returnMessage(){
    $message=require 'Messages.php';
    return $message[$_SESSION['sim-lang']];
}

function printData($file, $contents=[]){
    extract($contents);
    return require "app/resources/views/layouts/print/$file.html";
}

function view($file, $contents=[]){
    extract($contents);
    setLastVisitedPage($_SERVER['REQUEST_URI']);
    return require "app/resources/views/layouts/$file.view.php";
}

function makeItShort($input, $length){
    if(strlen($input)>$length){
        return substr(ucfirst($input),0, $length)."...";
    }else{
        return ucfirst($input);
    }
}

function filterUserInput($input){
    if(is_array($input)){
        $toReturn=[];
        for($i=0;$i<count($input);$i++){
            array_push($toReturn, goFilteringUserInput($input[$i]));
        }
        return $toReturn;
    }else{
        return goFilteringUserInput($input);
    }
}

function goFilteringUserInput($input){
    $string=trim($input);
    $string=htmlspecialchars($input);
    $string=stripslashes($input);
    return $string;
}

function convertToRoman($num){
    $roman=array(
        1000 => 'M',
        900 => 'CM',
        500 => 'D',
        400 => 'CD',
        100 => 'C',
        90 => 'XC',
        50 => 'L',
        40 => 'XL',
        10 => 'X',
        9 => 'IX',
        5 => 'V',
        4 => 'IV',
        1 => 'I',
    );

    $result='';
    
    foreach($roman as $r => $n){
        while($num>=$r){
            $base=floor($num/$r);
            while($base>0){
                $result.=$roman[$r];
                $base-=1;
            }
            $num=$num%$r;
        }
    }
    return $result;
}

function isEmpty($input){

    $toReturn = false;

    if(is_array($input)){
        for($i=0;$i<count($input);$i++){
            if(empty($input[$i]) && $input[$i]=='' && !isset($input[$i])){
                $toReturn = true;
            }
        }
    }else{
        if(empty($input) && $input=='' && !isset($input)){
            $toReturn = true;
        }
    }

    return $toReturn;
}

function maxDataInAPage(){
    return App::get('config')['maxDataInAPage'];
}

function maxPages(){
    return App::get('config')['maxPages'];
}

function redirect($url){
    return header("Location:".$url);
}

function redirectWithMessage($messages, $redirect){
    $_SESSION['sim-messages']=$messages;
    redirect($redirect);
    exit();
}

function pagination($pages){
    $toReturn = "<div><ul class='pagination'>";
    $toReturn2 = "";

    if($pages>maxPages()){
        $toReturn2 = "<li><div class='btn-group'>
                <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                    Next <span class='caret'></span>
                </button>
                <ul class='dropdown-menu'>";
    }
    

    //Pagination will appear when need more pages to show all the data 
    //Get the query url and then explode it using delimiter & to make it an array
    $url=explode('&', parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)); 

    for($i=1; $i<=$pages; $i++){                             
        $newUrl='';
        if(count($url)>1){
                        
            if(!isset($_GET['p'])){
                if($i==1){
                    array_push($url, "p=1");
                }     
            }

            foreach($url as $u){      
                $u=explode('=', $u);
                if($u[0]=='p'){
                    $u[1]=$i;
                }
                $u=implode('=', $u);
                $newUrl.=$u.'&';   
            }

            $newUrl=parse_url($_SERVER['REQUEST_URI'])['path'].'?'.trim($newUrl,'&');
        
        }else{
            $newUrl=parse_url($_SERVER['REQUEST_URI'])['path'].'?p='.$i;
        }

        $pClass=''; 
        //Give class active to the pagination link
        if(isset($_GET['p'])){
            if($i==$_GET['p']){
                $pClass='active';
            }
        }else{
            if($i==1){
                $pClass='active';
            }
        }

        if($i<=maxPages()){
            
            $toReturn .= "<li class=".$pClass."><a href=".$newUrl.">".$i."</a></li>";
        
        }else{
            
            $toReturn2 .= "<li class=".$pClass."><a href=".$newUrl." >".$i."</a></li>";
    
        }
    }

    if($pages>maxPages()){
        $toReturn2.= "</ul></div></li>";
    }
    
    $toReturn.=$toReturn2."</ul></div>";

    return $toReturn;
    
}

function setLastVisitedPage($page){
    $_SESSION['sim-lastVisitedPage']=$page;
}

function getLastVisitedPage(){
    return $_SESSION['sim-lastVisitedPage'];
}

function setSearchPage(){
    $_SESSION['sim-lastSearchPage'] = $_SERVER['REQUEST_URI'];
}

function getSearchPage(){
    return $_SESSION['sim-lastSearchPage'];
}

function dd($param){
    return die(var_dump($param));
}

function checkRequirement($requirements, $placeholder, $data){

    $flag=true;

    $require=explode('|', $requirements);
    
    for($i=0; $i<count($require); $i++){
        if($require[$i]=='required'){          
            if($data==null || !isset($data) || $data==''){
                array_push($_SESSION['sim-messages'], ["kolom $placeholder harus diisi", 0]);
                $flag=false;
            }
        }

        if($require[$i]=='email'){
            if($data==null  || !isset($data) || $data==''){
                array_push($_SESSION['sim-messages'], ["kolom $placeholder harus diisi dengan format email", 0]);
                $flag=false;
            }
        }
    } 
    
    return $flag;
}

function makeFirstLetterUpper($words){
    $firstLetter= strtoupper($words[0]);
    return $firstLetter.substr($words, 1);
}

function toDownload($formData, $dataColumn){
    $dataToDownload = [];
    for($i=0; $i<count($formData); $i++){
        $thisData = [];
        foreach($dataColumn as $column){
            $thisData[$column] = (!empty($formData[$i]->$column)&&$formData[$i]->$column!=null&&$formData[$i]->$column!='')?$formData[$i]->$column:'-';
        }
        array_push($dataToDownload, $thisData);
    }

    return $dataToDownload;
}


function recordLog($context, $activity){
      
    $id=substr($_SESSION['sim-id'],3,-3);
    $user=$_SESSION['sim-name'];

    //record log to database
    
    $builder= App::get('builder');

    $insertLog=$builder->insert('activity_history',[
        'user'=>$id,
        'context'=>$context,
        'activity'=>$activity
        ]
    ); 
    
    //record log to file

    date_default_timezone_set("Asia/Jakarta");
    $time= date("d-m-Y h:i:sa", time());
    $file=fopen("log/Activity.log","a");
    fwrite($file, "$time\t$context\t$user\t$activity\r\n");
    fclose($file);
    
}

function formatRupiah($value){
    return strrev(wordwrap(strrev($value), 3, '.', true));
}

/*
$app=[];

$app['config']=require 'config.php';

//connect to database
$pdo=Connection::connect($app['config']['connection']);

//query builder
$app['builder']=new QueryBuilder($pdo);

*/