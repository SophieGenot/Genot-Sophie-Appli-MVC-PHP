<?php
require_once __DIR__ . '/AbstractController.php'; 
require_once __DIR__ . '/../services/TrajetService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/AgenceService.php';

/**
 * Contrôleur AdminController
 * Gère les fonctionnalités d'administration : gestion des agences,
 * modération des utilisateurs et supervision des trajets.
 * Ce contrôleur est protégé par une vérification stricte du rôle 'admin'.
 */
class AdminController extends AbstractController {
    /** @var TrajetService Service de gestion des trajets */
    private TrajetService $trajetService;
    /** @var UserService Service de gestion des utilisateurs */
    private UserService $userService;
    /** @var AgenceService Service de gestion du référentiel agences */
    private AgenceService $agenceService;

    /**
     * Constructeur
     * Initialise les services nécessaires et sécurise l'accès au rôle administrateur.
     * * @param PDO $pdo Instance de connexion à la base de données.
     */
    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
   
        // Sécurité renforcée : redirection immédiate si l'utilisateur n'est pas admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('home');
            exit;
        }

        $this->trajetService = new TrajetService($this->pdo);
        $this->userService = new UserService($this->pdo);
        $this->agenceService = new AgenceService($this->pdo);
    }

    /**
     * Affiche le tableau de bord de l'administrateur.
     * Traite également les formulaires de gestion des agences (CRUD rapide).
     * * @return void Rendu de la vue admin-dashboard avec les données globales.
     */
    public function dashboard() {
        // Traitement des actions sur les agences
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

    /**
     * Valide l'inscription d'un employé pour lui donner accès aux fonctionnalités.
     * * @param int $id Identifiant de l'utilisateur à valider.
     */
    public function validateUser(int $id) {
        if ($id) {
            $this->userService->validateUser($id);
            $_SESSION['message_success'] = "Utilisateur validé.";
        }
        $this->redirect('dashboard_admin');
    }

    /**
     * Met à jour les informations d'un utilisateur depuis l'interface admin.
     * Inclut une vérification de la conformité du numéro de téléphone.
     * * @return void Redirection vers le dashboard.
     */
    public function updateUserAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            
            $id = (int)$_POST['user_id'];
            $tel = $_POST['telephone'] ?? '';

            // Appel d'une règle métier statique pour la validation du format
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

    /**
     * Supprime définitivement un utilisateur de la base de données.
     * Sécurité : Empêche l'administrateur de supprimer son propre compte en session.
     * * @param int $id Identifiant de l'utilisateur à supprimer.
     */
    public function deleteUser(int $id) {
        if ($id) {
            if ($id == $_SESSION['user']['id']) {
                $_SESSION['message_error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            } else {
                if ($this->userService->deleteUser((int)$id)) {
                    $_SESSION['message_success'] = "Utilisateur supprimé avec succès.";
                } else {
                    $_SESSION['message_error'] = "Erreur lors de la suppression.";
                }
            }
        }
        $this->redirect('dashboard_admin');
    }
}