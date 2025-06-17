<?
namespace websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface 
{
    protected $clients;
    protected $connexionsUtilisateurs;

    public function __construct() 
    {
        $this->clients = new \SplObjectStorage;
        $this->connexionsUtilisateurs = [];
    }

    public function onOpen(ConnectionInterface $connexion) 
    {
        // Get session data
        $session = $connexion->Session;
        $userData = $session->get('user');

        // Verify authentication
        if (!$userData || !isset($userData['id'])) {
            $connexion->close();
            return;
        }

        $userId = $userData['id'];
        $username = $userData['username'] ?? 'Anonymous';

        // Store connection with user metadata
        $this->clients->attach($connexion, [
            'userId' => $userId,
            'username' => $username
        ]);

        // Register for quick access
        $this->connexionsUtilisateurs[$userId] = $connexion;

        echo "User {$username} ({$userId}) connected\n";
    }

    public function onMessage(ConnectionInterface $expediteur, $message) 
    {
        $message = json_decode($message, true);
        
        if (!$message || !isset($message['content'])) {
            return;
        }

        // Get sender info from session
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
            // Option: Store message in database for later delivery
        }
    }

    public function onClose(ConnectionInterface $connexion) 
    {
        $infos = $this->clients[$connexion];
        $userId = $infos['userId'];

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