<?php
namespace App\WebSocket;

use Ratchet\App;
use Ratchet\Http\HttpServer; // NEW: Import HttpServer
use Ratchet\WebSocket\WsServer; // NEW: Import WsServer
use Ratchet\Session\SessionProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use App\WebSocket\Chat;  // Make sure this namespace is correct

class Server
{
    public function run()
    {
        $app = new App('localhost', 8081, '0.0.0.0');

        // Route:
        // First arg: Path clients connect to (e.g., ws://localhost:8081/chat)
        // Second arg: The component that handles the route. This is where the fully wrapped component goes.
        // Third arg: Allowed origins (['*'] allows all)
        $app->route('/chat', new Chat(), ['*']);

        echo "Starting WebSocket server on ws://localhost:8081/chat\n";
        $app->run();
    }
}