<?php
/**
 * CONFIGURATION DE LA BASE DE DONNÉES
 * Initialise l'instance PDO (PHP Data Objects) utilisée par l'ensemble des modèles.
 * Ce fichier centralise les accès au serveur de données.
 */

$host = '127.0.0.1';
$db   = 'appklaxon';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

/**
 * DSN (Data Source Name)
 * Définit le type de driver, l'adresse de l'hôte, le nom de la base et l'encodage.
 */
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

/**
 * OPTIONS PDO
 * Ces réglages garantissent la sécurité et la fiabilité du projet :
 * * - ERRMODE_EXCEPTION : PHPStan pourra analyser les erreurs potentielles 
 * car PDO lèvera des exceptions en cas de requête invalide.
 * - DEFAULT_FETCH_MODE : Simplifie le code en renvoyant par défaut des tableaux associatifs.
 * - EMULATE_PREPARES : Force l'utilisation des requêtes préparées natives de MySQL 
 * pour une protection maximale contre les injections SQL.
 */
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      
    PDO::ATTR_EMULATE_PREPARES   => false,                 
];

try {
    /**
     * Instanciation de l'objet PDO
     * Cette variable $pdo sera ensuite injectée dans les constructeurs des contrôleurs.
     */
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    /**
     * En cas d'erreur de connexion, on arrête le script pour éviter 
     * des erreurs de type "Variable $pdo inexistante" plus loin.
     */
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}