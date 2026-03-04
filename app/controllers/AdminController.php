<?php
require_once __DIR__ . '/../services/TrajetService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/AgenceService.php';

class AdminController {
    private $trajetService;
    private $userService;
    private $agenceService;

    public function __construct($pdo) {
        $this->trajetService = new TrajetService($pdo);
        $this->userService = new UserService($pdo);
        $this->agenceService = new AgenceService($pdo);
    }

    public function dashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?action=home');
            exit;
        }

        // Création agence
        if (isset($_POST['create_agence'])) {
        $this->agenceService->createAgence($_POST['nom_agence']);
        }

        // Modification agence
        if (isset($_POST['update_agence'])) {
        $this->agenceService->updateAgence($_POST['id_modif'], $_POST['nom_modif']);
        }

        // Suppression agence
        if (isset($_POST['delete_agence'])) {
        $this->agenceService->deleteAgence($_POST['delete_agence']);
        }
    
        $users = $this->userService->getAllUsers();
        $agences = $this->agenceService->getAllAgences();
        $trajets = $this->trajetService->getAllTrajetsAvecInfos();

        require __DIR__ . '/../views/admin-dashboard.php';
    }
}