<?php
require_once __DIR__ . '/AbstractController.php'; 
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/UserService.php'; 

/**
 * Contrôleur UserController
 * Gère le cycle de vie des comptes utilisateurs : inscription, connexion et déconnexion.
 * Assure la transition entre les formulaires publics et les espaces privés (dashboards).
 */
class UserController extends AbstractController {

    /** @var AuthService Service dédié à l'authentification */
    private $authService;
    /** @var UserService Service dédié à la gestion des comptes */
    private $userService; 

    /**
     * Constructeur
     * * @param PDO $pdo Injection de la connexion à la base de données.
     */
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->authService = new AuthService($this->pdo);
        $this->userService = new UserService($this->pdo);
    }

    /**
     * Gère la tentative de connexion d'un utilisateur.
     * Vérifie la validité de l'email et authentifie l'utilisateur via le service.
     * Initialise la session utilisateur et redirige selon le rôle (admin ou employé).
     * * @return void
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validation du format de l'email via une règle métier centralisée
            if (!AbstractService::isEmailValid($email)) {
                $_SESSION['login_error'] = "Format d'email invalide.";
                $this->redirect('home#login');
                exit;
            }

            try {
                $user = $this->authService->login($email, $password);

                if ($user) {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'telephone' => $user['telephone'] ?? ''
                    ];

                    // Routage dynamique selon le rôle utilisateur
                    if ($user['role'] === 'admin') {
                        $this->redirect('dashboard_admin');
                    } else {
                        $this->redirect('dashboard_employe');
                    }
                }
            } catch (Exception $e) {
                // Gestion des erreurs d'authentification (mot de passe faux, compte non validé)
                $_SESSION['login_error'] = $e->getMessage();
                header('Location: index.php?action=home#login'); 
                exit;
            }
        }
    }

    /**
     * Traite le formulaire d'inscription des nouveaux employés.
     * Applique des contrôles de conformité sur l'email et le téléphone.
     * Envoie la demande au service pour création en attente de validation.
     * * @return void
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!AbstractService::isEmailValid($_POST['email'] ?? '')) {
                    throw new Exception("L'adresse email n'est pas valide.");
                }

                if (!empty($_POST['telephone']) && !AbstractService::isPhoneValid($_POST['telephone'])) {
                    throw new Exception("Le numéro de téléphone doit contenir 10 chiffres.");
                }

                $success = $this->userService->createUser($_POST);

                if ($success) {
                    $_SESSION['message_success'] = "Demande envoyée ! Un administrateur doit valider votre compte avant votre première connexion.";
                    $this->redirect('home');
                }
            } catch (Exception $e) {
                $_SESSION['register_error'] = $e->getMessage();
                header('Location: index.php?action=home#register-section');
                exit;
            }
        }
    }

    /**
     * Détruit la session en cours et redirige l'utilisateur vers la page d'accueil.
     * * @return void
     */
    public function logout() {
        session_destroy();
        $this->redirect('home');
    }
}