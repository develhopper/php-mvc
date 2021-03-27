<?php

use Core\Route;

Route::get("/","IndexController@index");

Route::put("test","IndexController@index");

Route::get("test","IndexController@index","test");
