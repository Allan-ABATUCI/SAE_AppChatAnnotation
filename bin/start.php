<?php
require_once __DIR__.'/../vendor/autoload.php';


$server = new  App\WebSocket\Server();

$server->run();
