<?php

namespace websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface 
{
    protected $clients;
    protected $connexionsUtilisateurs; // Tableau associatif [userId => Connexion]

    public function __construct() 
    {
        $this->clients = new \SplObjectStorage;
        $this->connexionsUtilisateurs = [];
    }

    public function onOpen(ConnectionInterface $connexion) 
    {
        // Récupère les paramètres de connexion (userId et dest)
        $queryString = $connexion->httpRequest->getUri()->getQuery();
        parse_str($queryString, $params);
        
        $userId = $params['userId'] ?? null;
        $dest = $params['dest'] ?? null;

        // Vérification des paramètres obligatoires
        if (!$userId || !$dest) {
            $connexion->close();
            return;
        }

        // Stockage de la connexion avec ses métadonnées
        $this->clients->attach($connexion, [
            'userId' => $userId,
            'dest' => $dest
        ]);

        // Enregistrement de la connexion pour accès rapide
        $this->connexionsUtilisateurs[$userId] = $connexion;

        echo "Utilisateur {$userId} connecté (discute avec {$dest})\n";
    }

    public function onMessage(ConnectionInterface $expediteur, $message) 
    {
        $message = json_decode($message, true);
        
        // Validation du message
        if (!$message || !isset($message['message'])) {
            return;
        }

        // Récupère les infos de l'expéditeur
        $infosExpediteur = $this->clients[$expediteur];
        $userIdExpediteur = $infosExpediteur['userId'];
        $userIdDestinataire = $infosExpediteur['dest'];

        echo "Message de {$userIdExpediteur} à {$userIdDestinataire}: {$message['message']}\n";

        // Envoi direct au destinataire s'il est connecté
        if (isset($this->connexionsUtilisateurs[$userIdDestinataire])) {
            $connexionDestinataire = $this->connexionsUtilisateurs[$userIdDestinataire];
            $connexionDestinataire->send(json_encode([
                'message' => $message['message'],
                'emotion' => $message['emotion'] ?? null,
                'expediteur' => $userIdExpediteur
            ]));
        } else {
            echo "L'utilisateur {$userIdDestinataire} est déconnecté\n";
            // Optionnel: Stocker le message en base pour envoi ultérieur
        }
    }

    public function onClose(ConnectionInterface $connexion) 
    {
        $infos = $this->clients[$connexion];
        $userId = $infos['userId'];

        // Nettoyage
        unset($this->connexionsUtilisateurs[$userId]);
        $this->clients->detach($connexion);

        echo "Utilisateur {$userId} déconnecté\n";
    }

    public function onError(ConnectionInterface $connexion, \Exception $e) 
    {
        echo "Erreur: {$e->getMessage()}\n";
        $connexion->close();
    }
}