<?php

namespace App\Models;
use PDO;

/**
 * Classe Model - Gestionnaire principal de la base de données
 * 
 * Implémente le design pattern Singleton pour garantir une seule instance de connexion DB.
 * Fournit des méthodes pour interagir avec les utilisateurs, leurs statuts et les messages.
 * 
 * @package App\Models
 */
class Model_ws extends Model
{
    /**
     * Instance PDO pour la connexion à la base de données
     * @var PDO
     */
    private $bd;

    /**
     * Instance unique de la classe Model (Singleton)
     * @var Model|null
     */
    private static $instance = null;

    /**
     * Constructeur privé pour implémenter le pattern Singleton
     * 
     * Établit la connexion à la base de données avec les paramètres suivants :
     * - Mode d'erreur : ERRMODE_EXCEPTION
     * - Encodage : UTF-8
     * 
     * @throws \PDOException Si la connexion à la base échoue
     */
    private function __construct()
    {
       
        require_once "../src/Auth/credentials.php";
        $this->bd = new PDO($dsn, $login, $mdp);
        $this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->bd->query("SET names 'utf8'");
    }
    public static function getModel()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }   
        return self::$instance;
    }
    /* Insère un message dans une conversation entre deux utilisateurs avec son annotation émotionnelle.
 * Si la conversation n'existe pas, elle est créée automatiquement.
 * 
 * @param int $sender_id ID de l'utilisateur qui envoie le message
 * @param int $user2_id ID du deuxième utilisateur
 * @param string $message Contenu du message à envoyer
 * @param string $emotion Emotion à annoter ('joie', 'colère', 'tristesse', 'surprise', 'dégoût', 'peur')
 * @return boolean true su la transaction est effectué ou false en cas d'échec
 */
public function insertMessageWithEmotion($sender_id, $user2_id, $message, $emotion)
{
    try {
        // Vérifier les deux id utilisateurs
        if (!ctype_digit((string)$sender_id) || !ctype_digit((string)$user2_id)) {
           return false;
        } elseif ($sender_id == $user2_id) {
        return false;
        }  
        
        // Vérifier que l'émotion est valide
        $validEmotions = ['joie', 'colère', 'tristesse', 'surprise', 'dégoût', 'peur'];
        if (!in_array($emotion, $validEmotions)) {
            return false;
        }
        
        // Commencer une transaction
        $this->bd->beginTransaction();
        
        // Vérifier si la conversation existe déjà
        $req = $this->bd->prepare("
            SELECT conversation_id FROM Conversation 
            WHERE (user_1_id = :user1 AND user_2_id = :user2)
            OR (user_1_id = :user2 AND user_2_id = :user1)
        ");
        $req->bindValue(':user1', min($sender_id, $user2_id), PDO::PARAM_INT);
        $req->bindValue(':user2', max($sender_id, $user2_id), PDO::PARAM_INT);
        $req->execute();
        
        $conversation_id = $req->fetchColumn();
        
        // Si la conversation n'existe pas, la créer
        if (!$conversation_id) {
            $req = $this->bd->prepare("
                INSERT INTO Conversation (user_1_id, user_2_id) 
                VALUES (:user1, :user2)
            ");
            $req->bindValue(':user1', min($sender_id, $user2_id), PDO::PARAM_INT);
            $req->bindValue(':user2', max($sender_id, $user2_id), PDO::PARAM_INT);
            $req->execute();
            
            $conversation_id = $this->bd->lastInsertId();
        }
        
        // Insérer le message
        $req = $this->bd->prepare("
            INSERT INTO Message (conversation_id, sender_id, receiver_id, content)
            VALUES (:conversation_id, :sender_id, :receiver_id, :content)
        ");
        $req->bindValue(':conversation_id', $conversation_id, PDO::PARAM_INT);
        $req->bindValue(':sender_id', $sender_id, PDO::PARAM_INT);
        $req->bindValue(':receiver_id', $user2_id, PDO::PARAM_INT);
        $req->bindValue(':content', $message, PDO::PARAM_STR);
        $req->execute();
        
        $message_id = $this->bd->lastInsertId();
        
        // Insérer l'annotation émotionnelle
        $req = $this->bd->prepare("
            INSERT INTO Annotation (message_id, annotator_id, emotion)
            VALUES (:message_id, :annotator_id, :emotion)
        ");
        $req->bindValue(':message_id', $message_id, PDO::PARAM_INT);
        $req->bindValue(':annotator_id', $sender_id, PDO::PARAM_INT);
        $req->bindValue(':emotion', $emotion, PDO::PARAM_STR);
        $req->execute();
        
        $annotation_id = $this->bd->lastInsertId();
        
        // Valider la transaction
        $this->bd->commit();
         
        return $message_id;
        
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $this->bd->rollBack();
        error_log("Erreur lors de l'insertion du message et de l'annotation: " . $e->getMessage());
        return false;
    }
}

}