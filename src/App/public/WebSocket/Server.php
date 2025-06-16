<?php

namespace websocket;

use Ratchet\App;
use websocket\Chat;
class Server
{
    public function run()
    {
        
        $handler = new Chat();
        $app = new App('localhost', 8081, '0.0.0.0');
        $app->route('/chat', $handler, ['*']);
        $app->run();
    }
}