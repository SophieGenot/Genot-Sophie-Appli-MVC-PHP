<?php

require_once __DIR__ . '/../services/AuthService.php';

class UserController {

    private $authService;

    public function __construct($pdo) {
        $this->authService = new AuthService($pdo);
        if (session_status() === PHP_SESSION_NONE) {
        }
    }

    // ------------------------ Connexion ------------------------
    public function login() {
        // Vérifie si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Tente de connecter l'utilisateur
            $user = $this->authService->login($email, $password);

            if ($user) {
                // Stocke toutes les infos utiles dans la session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom']
                ];

                // Redirection selon rôle
                if ($user['role'] === 'admin') {
                    header('Location: index.php?action=dashboard_admin');
                } else {
                    header('Location: index.php?action=dashboard_employe');
                }
                exit;
            } else {
                // Message d'erreur affiché sur la home
                $_SESSION['login_error'] = "Email ou mot de passe incorrect";
                header('Location: index.php?action=home#login'); // renvoie à la section login
                exit;
            }
        }
    }

    // ------------------------ Déconnexion ------------------------
    public function logout() {
    session_destroy();
    header('Location: index.php?action=home');
    exit;
}
}