<?php
use Core\Router;
/** Register Middlwares Here */
$middlewares=[
    "test"=>app\middlewares\Test::class
];
/** Register Routes Here */ 
$router=Router::getInstance();
$router->register(BASEDIR."/routes/web.php",["csrf"=>true]);
$router->register(BASEDIR."/routes/api.php",["prefix"=>"api/"]);

if (getenv('DEBUG') == true) {
    ini_set('display_errors', 'on');
    ini_set('log_errors', 'on');
    ini_set('display_startup_errors', 'on');
    ini_set('error_reporting', E_ALL);
}