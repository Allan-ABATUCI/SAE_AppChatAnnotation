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

}