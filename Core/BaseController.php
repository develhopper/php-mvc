<?php
namespace Core;

use Core\handler\Error;

class BaseController{
    protected $method;
    public function __construct()
    {
        
    }

    public function __get($key){
        return $this->$key;
    }

    public function __set($key,$value){
        $this->$key=$value;
    }

    public function view($name,$params=[]){
        $name=str_replace(".","/",$name);
        $path=BASEDIR."/app/views/$name.php";
        if(!file_exists($path)){
            header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
            echo "View Not Found $name";
            exit;
        }
        extract($params);
        include $path;
    }
    
    public function redirect($route){
        header("Location: $route");
        exit;
    }

    public function middleware($name){
        $middleware = $GLOBALS['middlewares'];
        $middleware[$name]::next();
    } 
}