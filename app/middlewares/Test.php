<?php
namespace app\middlewares;

use Core\Middleware; 
use Core\handler\Request;

class Test extends Middleware{

    public static function next(Request $request){
        echo "Hello from test Middleware <br>";
    }

}