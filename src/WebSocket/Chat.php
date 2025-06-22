<?php
namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Models\Model;

class Chat implements MessageComponentInterface 
{
    protected $connexionsUtilisateurs;
    private $memcached;

    public function __construct() 
    {
        $this->connexionsUtilisateurs = [];
        $this->memcached = new \Memcached();
        $this->memcached->addServer('localhost', 11211); // Configurez selon votre environnement
    }

    public function onOpen(ConnectionInterface $connexion) 
    {
        echo "Nouvelle connexion ({$connexion->resourceId}) ouverte.\n";

        // Analyser la chaîne de requête pour le jeton
        $queryString = $connexion->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParameters);

        if (!isset($queryParameters['token'])) {
            $connexion->close();
            return;
        }

        $authToken = $queryParameters['token'];

        // 1. Récupérer l'ID de session depuis Memcached
        $sessionId = $this->memcached->get('ws_auth_token:' . $authToken);
        
        if (!$sessionId) {
            $connexion->close();
            return;
        }

        // 2. Récupérer les données de session depuis Memcached
        $sessionData = $this->memcached->get('session:' . $sessionId);
        
        if (!$sessionData || !isset($sessionData['user']['id'])) {
            $connexion->close();
            return;
        }

        $userId = $sessionData['user']['id'];

        // Stocker les données de session dans la connexion pour un accès ultérieur
        $connexion->sessionData = $sessionData;

        // Enregistrer la connexion
        $this->connexionsUtilisateurs[$userId] = $connexion;

        echo "User {$userId} connected\n";
    }

    public function onMessage(ConnectionInterface $expediteur, $message) 
    {
        $message = json_decode($message, true);
        
        if (!$message || !isset($message['content'])) {
            return;
        }

        // Récupérer les données de session depuis la connexion
        if (!isset($expediteur->sessionData)) {
            return;
        }

        $senderData = $expediteur->sessionData['user'];
        $senderId = $senderData['id'];
        $senderName = $senderData['username'];

        // Get recipient ID from message
        $recipientId = $message['recipient'] ?? null;

        if (!$recipientId) {
            return;
        }

        echo "Message from {$senderName} to {$recipientId}: {$message['content']}\n";

        // Send to recipient if connected
        if (isset($this->connexionsUtilisateurs[$recipientId])) {
            $recipientConn = $this->connexionsUtilisateurs[$recipientId];
            $recipientConn->send(json_encode([
                'content' => $message['content'],
                'sender' => $senderId,
                'senderName' => $senderName,
                'timestamp' => time()
            ]));
        } else {
            echo "User {$recipientId} is offline\n";
            // Option: stocker le message pour une livraison ultérieure
        }
    }

    public function onClose(ConnectionInterface $connexion) 
    {
        if (!isset($connexion->sessionData)) {
            return;
        }

        $userId = $connexion->sessionData['user']['id'];

        if (isset($this->connexionsUtilisateurs[$userId])) {
            unset($this->connexionsUtilisateurs[$userId]);
            echo "User {$userId} disconnected\n";
        }
    }

    public function onError(ConnectionInterface $connexion, \Exception $e) 
    {
        echo "Error: {$e->getMessage()}\n";
        $connexion->close();
    }
}