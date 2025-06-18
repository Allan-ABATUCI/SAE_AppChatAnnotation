<?php
namespace websocket;

use Ratchet\App;
use Ratchet\Session\SessionProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use websocket\Chat;

class Server
{
    public function run()
    {
        // 1. Configuration du gestionnaire de sessions Memcached
        $memcached = new \Memcached();
        $memcached->addServer('localhost', 11211); // Connexion au serveur Memcached
        
        $sessionHandler = new MemcachedSessionHandler($memcached);

        // 3. Encapsulation du Chat avec le gestionnaire de sessions
        $chatHandler = new Chat();
        $handlerAvecSessions = new SessionProvider(
            $chatHandler,
            $sessionHandler
        );

        // 4. Création de l'application Ratchet
        $app = new App('localhost', 8081, '0.0.0.0');
        $app->route('/chat', $handlerAvecSessions, ['*']); // Toutes les origines autorisées
        $app->run();
    }
}