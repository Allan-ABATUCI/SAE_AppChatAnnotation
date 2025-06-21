<?php
namespace App\WebSocket;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Models\Model;
class Chat implements MessageComponentInterface 
{

    protected $connexionsUtilisateurs;
    private $bd;

    public function __construct() 
    {
        
        $this->connexionsUtilisateurs = [];

    }

    public function onOpen(ConnectionInterface $connexion) 
    {
        // Get session data
        $session = $connexion->Session;
        $userId = $session->get('id');

        // Verify authentication
        if (!$userId || !isset($userData['id'])) {
            $connexion->close();
            return;
        }

        // enregistrer la connexion
        $this->connexionsUtilisateurs[$userId] = $connexion;

        echo "User {$userId} connected\n";
    }

    public function onMessage(ConnectionInterface $expediteur, $message) 
    {
        $message = json_decode($message, true);
        
        if (!$message || !isset($message['content'])) {
            return;
        }

        // info session
        $senderSession = $expediteur->Session;
        $senderData = $senderSession->get('user');
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
            //peut être envoyé un message pour dire qu'il est offline
        }
    }

    public function onClose(ConnectionInterface $connexion) 
    {
        
        $userId = $connexion->Session['id'];

        unset($this->connexionsUtilisateurs[$userId]);
        $this->clients->detach($connexion);

        echo "User {$userId} disconnected\n";
    }

    public function onError(ConnectionInterface $connexion, \Exception $e) 
    {
        echo "Error: {$e->getMessage()}\n";
        $connexion->close();
    }
}