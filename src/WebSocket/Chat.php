<?php
namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Model_ws;

class Chat implements MessageComponentInterface 
{
    protected $connexionsUtilisateurs;
    private $memcached;

    private $bd;

    public function __construct() 
    {
        $this->connexionsUtilisateurs = [];
        $this->memcached = new \Memcached();
        $this->memcached->addServer('localhost', 11211); // Configurez selon votre environnement
            $this->bd=Model_ws::getModel();
    }

    public function onOpen(ConnectionInterface $connexion) 
    {
        

        // Analyser la chaîne de requête pour le jeton
        $queryString = $connexion->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParameters);

        if (!isset($queryParameters['id'])) {
            echo "erreur : pas d'id en query params";
            var_dump($queryParameters);
            
        }

        $userId = $queryParameters['id'];
        echo "Nouvelle connexion ({$userId}) ouverte. :\n";
        
        
        if (!$userId) {
            var_dump($userId);
        }

        // 2. Récupérer les données de session depuis Memcached
        $sessionData =$this->memcached->get('ws_user_'.$userId);
        
        if (!$sessionData) {
            echo "erreur : Session data : \n";
            var_dump($sessionData);
            echo "Memcached";
            $keys = $this->memcached->getAllKeys();
        }


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
    
        $senderData = $expediteur->sessionData;
        $senderId = $senderData['id'];
        $senderName = $senderData['username'];

        // Get recipient ID from message
        $recipientId = $message['recipient'] ?? null;

        if (!$recipientId) {
            return;
        }

        echo "Message from {$senderName} to {$recipientId}: {$message['content']}\n";
        
        if($messageId=$this->bd->insertMessageWithEmotion($senderId,
            $recipientId,
            $message['content'],
            $message['emotion']))
        
        // Send to recipient if connected
        if (isset($this->connexionsUtilisateurs[$recipientId])) {
            $recipientConn = $this->connexionsUtilisateurs[$recipientId];
            $recipientConn->send(json_encode([
                'content' => $message['content'],
                'sender' => $senderId,
                'senderName' => $senderName,
                'msgid' => $messageId,
            ]));
        } else {
            echo "User {$recipientId} is offline\n";
            // Option: stocker le message pour une livraison ultérieure
        }
    }

    public function onClose(ConnectionInterface $connexion) 
    {
        $userId = $connexion->sessionData['user_id'];

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