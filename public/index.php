<?php

use App\core\handler\Request;
use App\core\Kernel;

require_once '../bootstrap.php';

$kernel = new Kernel();

$response = $kernel->handle(new Request());

$response->send();
