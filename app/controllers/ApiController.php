<?php
namespace app\controllers;

use Core\BaseController;

class ApiController extends BaseController{

    public function api_hello(){
        $this->json(["message"=>"Hello :)"],200);
    }
}