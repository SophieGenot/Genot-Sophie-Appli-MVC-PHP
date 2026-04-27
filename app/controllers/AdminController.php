<?php
require_once __DIR__ . '/AbstractController.php'; 
require_once __DIR__ . '/../services/TrajetService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/AgenceService.php';

class AdminController extends AbstractController {
    private TrajetService $trajetService;
    private UserService $userService;
    private AgenceService $agenceService;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
   
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('home');
            exit;
        }

        $this->trajetService = new TrajetService($this->pdo);
        $this->userService = new UserService($this->pdo);
        $this->agenceService = new AgenceService($this->pdo);
    }

    public function dashboard() {
        if (isset($_POST['create_agence'])) {
            $this->agenceService->createAgence($_POST['nom_agence']);
        }
        if (isset($_POST['update_agence'])) {
            $this->agenceService->updateAgence((int)$_POST['id_modif'], $_POST['nom_modif']);
        }
        if (isset($_POST['delete_agence'])) {
            $this->agenceService->deleteAgence($_POST['delete_agence']);
        }
    
        $this->render('admin-dashboard', [
            'users' => $this->userService->getAllUsers(),
            'usersToValidate' => $this->userService->getUsersPendingValidation(), 
            'agences' => $this->agenceService->getAllAgences(),
            'trajets' => $this->trajetService->getAllTrajetsAvecInfos()
        ]);
    }

    public function validateUser(int $id) {
        if ($id) {
            $this->userService->validateUser($id);
            $_SESSION['message_success'] = "Utilisateur validé.";
        }
        $this->redirect('dashboard_admin');
    }

    public function updateUserAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $id = (int)$_POST['user_id'];
            $tel = $_POST['telephone'] ?? '';

            if (!AbstractService::isPhoneValid($tel)) {
               $_SESSION['message_error'] = "Le numéro est incorrect. Il doit contenir exactement 10 chiffres.";
               $this->redirect('dashboard_admin');
               exit;
            }

            $data = [
                'nom'       => $_POST['nom'],
                'prenom'    => $_POST['prenom'],
                'telephone' => $tel,
            ];

            if ($this->userService->updateUser($id, $data)) {
                $_SESSION['message_success'] = "Utilisateur mis à jour !";
            }
        }
        $this->redirect('dashboard_admin');
    }

    public function deleteUser() {
        // Sécurité : vérification que la requête est bien en POST
        $id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

        if ($id) {
            if ($id == $_SESSION['user']['id']) {
                $_SESSION['message_error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            } else {
                if ($this->userService->deleteUser($id)) {
                    $_SESSION['message_success'] = "Utilisateur supprimé.";
                } else {
                    $_SESSION['message_error'] = "Erreur lors de la suppression.";
                }
            }
        }
        $this->redirect('dashboard_admin');
    }

    public function deleteTrajetAdmin() {
        $id = isset($_POST['trajet_id']) ? (int)$_POST['trajet_id'] : null;
        
        if ($id) {
            if ($this->trajetService->deleteTrajet($id)) {
                $_SESSION['message_success'] = "Trajet supprimé avec succès.";
            } else {
                $_SESSION['message_error'] = "Erreur lors de la suppression du trajet.";
            }
        }
        $this->redirect('dashboard_admin');
    }
}