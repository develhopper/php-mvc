<?php
spl_autoload_register(function($classname){
	$classname=__DIR__."/".str_replace("\\","/",$classname).".php";
	if(file_exists($classname))
		require_once $classname;
	else
		die("cannot require from $classname [ NOTE FOUND ]");	
});
