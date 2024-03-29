<?php

namespace Core;

use Core\handler\Error;
use Core\handler\Request;
use Core\exceptions\RouteException;

class Kernel
{
    private $router;
    private $controller;
    private $method;
    private $params = [];
    private $route;
    private $response;
    private $request;
    private $middlewares;
    public function __construct()
    {
        include __DIR__."/kernel/kernel_functions.php";
        include __DIR__."/kernel/kernel_configs.php";
        $this->router=Router::getInstance();
        $this->middlewares=$middlewares;
    }
    
    public function handle(Request $request){
        session_start();
        try{
            $this->route = $this->router->find($request->url);
            $this->request=$request;
            $this->Routemiddleware();
            RegisterProvider::register($request);
        }catch(RouteException $e){
            Error::send($e->code);
        }
        
        $this->setController();
        array_push($this->params,$request);
        $response=new Response();
        $response->controller=$this->controller;
        $response->method=$this->method;
        $response->params=$this->params;
        $response->route=$this->route;
        return $response;
    }

    private function Routemiddleware(){
        $middles=$this->route['middleware'];
        if(is_string($middles)){
            $middleware=$this->middlewares[$middles];
            $middleware::next($this->request);    
        }else if(is_array($middles)){
            foreach($middles as $m){
                $middleware = $this->middlewares[$m];
                $middleware::next($this->request);
            }
        }
    }

    private function setController()
    {
        $this->controller = "app\\controllers\\" . $this->route['controller'];
        $this->method = $this->route['function'];
        $this->setParams();
    }
    private function setParams()
    {
        $url_parts = explode("/", $this->request->url);
        $parts = explode("\/", $this->route['regex']);
        foreach ($parts as $key => $p) {
            if ($p == "(.+)")
                array_push($this->params, $url_parts[$key]);
        }
    }
    }
