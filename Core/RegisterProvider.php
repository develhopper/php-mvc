<?php
namespace App\core;
class RegisterProvider{

    public static function register(){
        include 'providers.php';
        foreach ($providers as $provider) {
            call_user_func([$provider, "boot"]);
        }
    }
}