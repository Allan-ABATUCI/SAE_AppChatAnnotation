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
class Model
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
        include "src/App/Auth/credentials.php";
        $this->bd = new PDO($dsn, $login, $mdp);
        $this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->bd->query("SET names 'utf8'");
    }

    /**
     * Récupère l'instance unique de la classe Model
     * 
     * @return Model L'instance unique
     */
    public static function getModel()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }   
        return self::$instance;
    }

    /**
     * Récupère les utilisateurs en ligne
     * 
     * @return array Tableau associatif des utilisateurs en ligne avec :
     *               - user_id
     *               - username
     *               - email
     */
    public function getOnlineUsers()
    {
        $sql = "SELECT Users.user_id, Users.username, Users.email 
            FROM UserStatus 
            JOIN Users ON UserStatus.user_id = Users.user_id 
            WHERE is_online = TRUE";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le dernier message entre deux utilisateurs
     * 
     * @param int|string $id ID du destinataire
     * @param int|string $id_sender ID de l'expéditeur
     * @return array|false Tableau associatif du message ou false si aucun message
     */
    public function getLastMessage($id, $id_sender)
    {
        $req = $this->bd->prepare("SELECT * FROM Message WHERE receiver_id=:id and sender_id=:ids ORDER BY created_at desc limit 1");
        $req->bindValue(':ids', $id_sender, PDO::PARAM_STR);
        $req->bindValue(':id', $id, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un utilisateur par email et mot de passe hashé
     * 
     * @param string $email Email de l'utilisateur
     * @return array|false Tableau associatif des données utilisateur ou false si non trouvé
     */
    public function getUser($email)
    {
        $req = $this->bd->prepare("SELECT * FROM Users WHERE email = :email");
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si un utilisateur existe avec cet email
     * 
     * @param string $email Email à vérifier
     * @return bool true si l'utilisateur existe
     */
    public function UserExists($email)
    {
        $req = $this->bd->prepare("SELECT COUNT(*) FROM Users WHERE email = :email");
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->execute();
        return $req->fetchColumn() > 0;
    }

    /**
     * Crée un nouvel utilisateur
     * 
     * @param string $username Nom d'utilisateur
     * @param string $email Email de l'utilisateur
     * @param string $mdp Mot de passe hashé
     * @return string ID du nouvel utilisateur créé
     */
    public function createUser($username, $email, $mdp)
    {
        $req = $this->bd->prepare("INSERT INTO Users (username, email, password_hash) VALUES (:username, :email, :hashedPassword)");
        $req->bindValue(':username', $username, PDO::PARAM_STR);
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->bindValue(':hashedPassword', $mdp, PDO::PARAM_STR);
        $req->execute();
        return $this->bd->lastInsertId();
    }

    /**
     * Met à jour le statut en ligne d'un utilisateur
     * 
     * Crée un enregistrement si l'utilisateur n'a pas encore de statut
     * 
     * @param int $userId ID de l'utilisateur
     * @param bool $isOnline Statut (true = en ligne, false = hors ligne)
     * @return void
     */
    public function updateUserStatus($userId, $isOnline)
    {
        $checkSql = "SELECT COUNT(*) FROM UserStatus WHERE user_id = :user_id";
        $checkStmt = $this->bd->prepare($checkSql);
        $checkStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $checkStmt->execute();
        $recordExists = $checkStmt->fetchColumn() > 0;

        if ($recordExists) {
            $sql = "UPDATE UserStatus SET is_online = :is_online WHERE user_id = :user_id";
            $stmt = $this->bd->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':is_online', $isOnline, PDO::PARAM_BOOL);
            $stmt->execute();
        } else {
            $sql = "INSERT INTO UserStatus (user_id, is_online) VALUES (:user_id, :is_online)";
            $stmt = $this->bd->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':is_online', $isOnline, PDO::PARAM_BOOL);
            $stmt->execute();
        }
    }

    /**
     * Méthode réservée pour la gestion des conversations
     * 
     * @todo Implémenter cette fonctionnalité
     */
    public function getConversation() {}
}