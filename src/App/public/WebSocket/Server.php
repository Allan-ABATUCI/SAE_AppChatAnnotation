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
        
        $sessionHandler = new MemcachedSessionHandler($memcached, [
            'prefix' => 'ws_sess_', // Préfixe des clés de session
            'expiretime' => 86400   // Durée de vie des sessions (24h)
        ]);

        // 2. Options de configuration des sessions
        $sessionOptions = [
            'name' => 'WS_SESSION',      // Nom du cookie de session
            'cookie_lifetime' => 86400,   // Durée de vie du cookie
            'gc_maxlifetime' => 86400,    // Durée avant garbage collection
            'cookie_httponly' => true,    // Cookie inaccessible en JavaScript
            'cookie_secure' => false      // À passer à true en production avec HTTPS
        ];

        // 3. Encapsulation du Chat avec le gestionnaire de sessions
        $chatHandler = new Chat();
        $handlerAvecSessions = new SessionProvider(
            $chatHandler,
            $sessionHandler,
            $sessionOptions
        );

        // 4. Création de l'application Ratchet
        $app = new App('localhost', 8081, '0.0.0.0');
        $app->route('/chat', $handlerAvecSessions, ['*']); // Toutes les origines autorisées
        $app->run();
    }
}