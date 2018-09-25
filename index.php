<?php
session_start();

require 'vendor/autoload.php';
require 'core/bootstrap.php';

use App\Core\Router;
use App\Core\Request;

//buat object
$router=new Router();

//daftarkan semua route kedalam array links
require 'app/route.php'; 


//ambil uri sesuai data yang ada dalam array request method
$router->getLink(Request::uri(), Request::method());


?>

