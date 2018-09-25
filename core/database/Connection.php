<?php

class Connection{

    public static function connect($config){
        try{
            $pdo=new PDO($config['host'].";dbname=".$config['database'],$config['username'],$config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }catch(PDOException $e){
            die($e->getMessage());
        }   
    }

}

?>