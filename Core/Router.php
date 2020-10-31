<?php
namespace Core;

use Core\handler\Session;

class Router{
    private static $instance=null;
    
    private $routes=[
        "GET"=>[],
        "POST"=>[],
        "PUT"=>[],
        "DELETE"=>[]
    ];

    public $prefix;

    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance==null)
            self::$instance=new Router();
        return self::$instance;
    }

    public function register($path,$options=[]){
        $this->applyOptions($options);
        if(file_exists($path)){
            include_once $path;
        }
        else
            die("Router: $path not exists");
    }

    private function applyOptions($options=[]){
        $this->prefix=(isset($options["prefix"]))?$options["prefix"]:"";
        $this->response_type=(isset($options["response_type"]))?$options["response_type"]:"web";
    }

    public function add($method,$route){
        array_push($this->routes[$method],$route);
    }

    public function find($name){
        $reqeust_method=$_SERVER['REQUEST_METHOD'];
        if($reqeust_method=="POST"){
            if(isset($_REQUEST['_method']))
                $reqeust_method=$_REQUEST['_method'];
        }
        $name = rtrim($name, "/");
        if (empty($name))
            $name = "/";
        foreach ($this->routes[$reqeust_method] as $r) {
            if (preg_match("/^" . $r['regex'] . "$/i", $name) && $this->method_check($r,$reqeust_method)){
                Session::set("current_route",$r);
                return $r;
            }
        }
        throw new RouteException("Route no found",404);
    }

    private function method_check($route,$reqeust_method)
    {
        if(strcmp($route['method'],$reqeust_method)==0)
            return true;
        else
            throw new RouteException("Method not Allowed",405);
    }
}