<?php

namespace App\Core;

use App\Controllers;
use Exception;

class Router{

    protected $links=[
        'POST'=>[],
        'GET'=>[],
    ];
    
    public function getLink($uri, $requestType){

        $checkUri= array_key_exists($uri,$this->links[$requestType]);
        
        if($checkUri){
            return $this->process($this->links[$requestType][$uri]);
        }

        redirect('/404');
        exit();

        //throw new Exception("No route");
    }

    public function get($uri,$controller){
        return $this->links['GET'][$uri]=$controller;
    }

    public function post($uri,$controller){
        return $this->links['POST'][$uri]=$controller;
    }

    protected function process($uri){
 
        $explodeUri=explode('@', $uri);

        $class=$explodeUri[0];
        $method=$explodeUri[1];
        
        $class="App\\Controllers\\{$class}";
        $obj=new $class;
        
        if(!method_exists($obj, $method)){
            throw new Exception("No method exist");
        }
        return $obj->$method();
        
    }
}


?>