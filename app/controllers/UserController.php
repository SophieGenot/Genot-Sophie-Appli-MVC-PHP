<?php
require_once __DIR__ . '/AbstractController.php'; 
require_once __DIR__ . '/../services/AuthService.php';

class UserController extends AbstractController {

    private $authService;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->authService = new AuthService($this->pdo);
    }

    // ------------------------ Connexion ------------------------
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->authService->login($email, $password);

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom']
                ];

                if ($user['role'] === 'admin') {
                    $this->redirect('dashboard_admin');
                } else {
                    $this->redirect('dashboard_employe');
                }
            } else {
                $_SESSION['login_error'] = "Email ou mot de passe incorrect";
                
                header('Location: index.php?action=home#login'); 
                exit;
            }
        }
    }

    // ------------------------ Déconnexion ------------------------
    public function logout() {
        session_destroy();
        $this->redirect('home');
    }
}