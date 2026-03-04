<?php
$host = '127.0.0.1';
$db   = 'appklaxon';
$user = 'root'; // par défaut XAMPP
$pass = '';     // mot de passe XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erreurs visibles
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // vrais prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>