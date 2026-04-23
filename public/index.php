<?php
/**
 * FRONT CONTROLLER
 * * Point d'entrée unique de l'application "Pas Touche au Klaxon".
 * Responsable de la configuration globale, de l'initialisation des dépendances,
 * et de l'aiguillage (routage) des requêtes vers les contrôleurs appropriés.
 */

// 1. CONFIGURATION ET DEBUGGING
// En phase de développement, permet d'afficher les erreurs PHP pour faciliter le debug.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. INCLUSION DÉPENDANCES
// Chargement de l'autoloader de Composer (pour PHPUnit, PHPStan, etc.)
require_once __DIR__ . '/../vendor/autoload.php';
// Connexion à la base de données (initialise la variable $pdo)
require_once __DIR__ . '/../config/db.php'; 

// 3. INCLUSION CONTROLLERS
// Note : Dans une architecture plus avancée, on utiliserait un Autoloader pour ces classes.
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/TrajetController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';

// 4. DÉMARRAGE SESSION
// Nécessaire pour gérer l'authentification des employés et les messages flash.
session_start();

/**
 * LOGIQUE DE ROUTAGE
 * Analyse du paramètre 'action' passé dans l'URL (ex: index.php?action=login)
 */
$action = $_GET['action'] ?? 'home';

// 5. INSTANCIATION DES CONTRÔLEURS
// Injection de la dépendance $pdo dans chaque contrôleur pour l'accès aux modèles.
$uc = new UserController($pdo);
$tc = new TrajetController($pdo);

/** * Optimisation : On instancie l'AdminController uniquement si une action "admin" est demandée.
 * Cela permet d'économiser des ressources sur les pages publiques.
 */
$ac = null; 
if (strpos($action, 'admin') !== false || $action === 'validate_user' || $action === 'update_user_admin') {
    $ac = new AdminController($pdo);
}

// 6. AIGUILLAGE (SWITCH)
switch ($action) {

    // --- Gestion Utilisateurs (UserController) ---
    case 'login':
        $uc->login();
        break;

    case 'logout':
        $uc->logout();
        break;

    case 'register':
        $uc->register(); 
        break;

    // --- Gestion Trajets (TrajetController) ---
    case 'home':
        // Page d'accueil publique : liste des trajets avec places disponibles.
        $tc->listHome();
        break;

    case 'dashboard_employe':
        // Sécurité : Accès restreint au profil employé.
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employe') {
            header("Location: index.php?action=home");
            exit;
        }
        $tc->listDashboardEmploye();
        break;

    case 'create_trajet':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=home");
            exit;
        }
        $tc->createTrajet();
        break;

    case 'edit_trajet':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=home");
            exit;
        }
        $tc->editTrajet($_GET['id'] ?? null);
        break;

    // --- Gestion Administration (AdminController) ---
    case 'dashboard_admin':
        // L'AdminController gère lui-même sa sécurité dans son constructeur.
        $ac->dashboard();
        break;

    case 'validate_user':
        $ac->validateUser($_GET['id'] ?? null);
        break;

    case 'update_user_admin':
        $ac->updateUserAdmin();
        break;

    // --- Redirection par défaut ---
    // Si l'action est inconnue, retour à la page d'accueil.
    default:
        header("Location: index.php?action=home");
        exit;
}