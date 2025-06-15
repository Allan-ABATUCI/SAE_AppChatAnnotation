<?php
require_once 'vendor/autoload.php';

use App\public\WebSocket\Server;

$server = new Server();
$server->run();
