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
        // 1. Memcached session handler configuration
        $memcached = new \Memcached();
        $memcached->addServer('localhost', 11211); // Ensure Memcached server is running
        
        $sessionHandler = new MemcachedSessionHandler($memcached);

        // 2. Your core WebSocket application component
        $chatComponent = new Chat();
        
        // 3. Wrap the chat component with WsServer to handle WebSocket protocol
        $wsServer = new WsServer($chatComponent);

        // 4. Wrap the WsServer with HttpServer to handle HTTP requests (including WebSocket handshake)
        $httpServer = new HttpServer($wsServer);

        // 5. Wrap the HttpServer with SessionProvider for session management
        // SessionProvider expects an HttpServerInterface as its first argument
        $handlerWithSessions = new SessionProvider(
            $httpServer, // This is the change! Pass the HttpServer here.
            $sessionHandler
        );

        // 6. Create Ratchet application
        // The first argument is the host name clients will connect to.
        // The second is the port to listen on.
        // The third is the address to bind to (0.0.0.0 for all interfaces).
        $app = new App('localhost', 8081, '0.0.0.0');

        // Route:
        // First arg: Path clients connect to (e.g., ws://localhost:8081/chat)
        // Second arg: The component that handles the route. This is where the fully wrapped component goes.
        // Third arg: Allowed origins (['*'] allows all)
        $app->route('/chat', $handlerWithSessions, ['*']);

        echo "Starting WebSocket server on ws://localhost:8081/chat\n";
        $app->run();
    }
}