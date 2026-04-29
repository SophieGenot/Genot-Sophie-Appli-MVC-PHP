<?php
/**
 * FRONT CONTROLLER
 */

// 1. CONFIGURATION ET DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. INCLUSION DÉPENDANCES
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php'; 

// 3. INCLUSION CONTROLLERS
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/TrajetController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
/** @var PDO $pdo */
// 4. DÉMARRAGE SESSION
session_start();

/**
 * LOGIQUE DE ROUTAGE
 */
$action = $_GET['action'] ?? 'home';

// 5. INSTANCIATION DES CONTRÔLEURS
$uc = new UserController($pdo);
$tc = new TrajetController($pdo);

$ac = null; 
$adminActions = ['validate_user', 'update_user_admin', 'delete_user'];

if (strpos($action, 'admin') !== false || in_array($action, $adminActions)) {
    $ac = new AdminController($pdo);
}

// 6. AIGUILLAGE (SWITCH)
switch ($action) {

    // --- Gestion Utilisateurs ---
    case 'login':
        $uc->login();
        break;

    case 'logout':
        $uc->logout();
        break;

    case 'register':
        $uc->register(); 
        break;

    // --- Gestion Trajets ---
    case 'home':
        $tc->listHome();
        break;

    case 'dashboard_employe':
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employe') {
            header("Location: index.php?action=home");
            exit;
        }
        $tc->listDashboardEmploye();
        break;

    case 'reserver':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=home#login");
            exit;
        }
        $tc->reserver();
        break;

    case 'annuler_reservation':
      $tc->annulerReservation();
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

    case 'delete_trajet':
    $tc->deleteTrajet(); 
    break;

    // --- Gestion Administration (AdminController) ---
    case 'dashboard_admin':
        $ac->dashboard();
        break;

    case 'validate_user':
        $ac->validateUser($_GET['id'] ?? null);
        break;

    case 'update_user_admin':
        $ac->updateUserAdmin();
        break;

    case 'delete_user':
        $ac->deleteUser(); 
        break;

    // --- Redirection par défaut ---
    default:
        header("Location: index.php?action=home");
        exit;
}