<?php
// Affichage des erreurs pour le dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des dépendances et connexion PDO
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php'; 

// Controllers
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/TrajetController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';

session_start();

$action = $_GET['action'] ?? 'home';

switch ($action) {

    case 'login':
        $uc = new UserController($pdo);
        $uc->login();
        break;

    case 'logout':
        $uc = new UserController($pdo);
        $uc->logout();
        break;

    case 'home':
        $tc = new TrajetController($pdo);
        $tc->listHome();
        break;

    case 'dashboard_employe':
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employe') {
        header("Location: index.php?action=home");
        exit;
        }
        $tc = new TrajetController($pdo);
        $tc->listDashboardEmploye();
        break;

    case 'dashboard_admin':
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: index.php?action=home");
            exit;
        }
        $ac = new AdminController($pdo);
        $ac->dashboard();
        break;

    case 'create_trajet':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=home");
            exit;
        }
        $tc = new TrajetController($pdo);
        $tc->createTrajet();
        break;

    case 'edit_trajet':
        if (!isset($_SESSION['user'])) {
        header("Location: index.php?action=home");
        exit;
        }
        $tc = new TrajetController($pdo);
        $tc->editTrajet($_GET['id']);
        break;

    default:

        header("Location: index.php?action=home");
        exit;
}