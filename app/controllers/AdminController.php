<?php
require_once __DIR__ . '/AbstractController.php'; 
require_once __DIR__ . '/../services/TrajetService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/AgenceService.php';

class AdminController extends AbstractController {
    private $trajetService;
    private $userService;
    private $agenceService;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->trajetService = new TrajetService($this->pdo);
        $this->userService = new UserService($this->pdo);
        $this->agenceService = new AgenceService($this->pdo);
    }

    public function dashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('home');
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

        // Utilisation de la méthode render du parent
        $this->render('admin-dashboard', [
            'users' => $users,
            'agences' => $agences,
            'trajets' => $trajets
        ]);
    }
}