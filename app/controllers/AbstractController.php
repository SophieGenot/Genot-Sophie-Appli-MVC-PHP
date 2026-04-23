<?php

/**
 * Classe abstraite AbstractController
 * Fournit les outils de base pour tous les contrôleurs de l'application.
 * Gère le rendu des vues, les redirections et la sécurité des accès (Auth/Admin).
 */
abstract class AbstractController {
    /** @var PDO Instance de connexion à la base de données */
    protected PDO $pdo;

    /**
     * Constructeur
     * * @param PDO $pdo Injection de la connexion à la base de données.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Affiche une vue en lui passant des données.
     * * @param string $view Nom du fichier de vue (sans l'extension .php).
     * @param array $data Tableau associatif des données à transmettre à la vue.
     */
    protected function render(string $view, array $data = []) {
        // Transforme les clés du tableau en variables utilisables dans la vue
        extract($data); 
        
        $filePath = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($filePath)) {
            require $filePath;
        } else {
            die("Erreur critique : La vue '$view' est introuvable dans le dossier views.");
        }
    }

    /**
     * Effectue une redirection HTTP vers une action précise.
     * * @param string $action Nom de l'action cible (ex: 'home', 'login').
     */
    protected function redirect(string $action) {
        header("Location: index.php?action=" . $action);
        exit;
    }

    /**
     * Sécurité : Vérifie si l'utilisateur est authentifié.
     * Redirige vers l'accueil si aucune session utilisateur n'est active.
     */
    protected function checkAuth() {
        if (!isset($_SESSION['user']['id'])) {
            $this->redirect('home');
        }
    }

    /**
     * Sécurité : Vérifie si l'utilisateur possède le rôle 'admin'.
     * Cumule la vérification d'authentification et de rôle.
     */
    protected function checkAdmin() {
        $this->checkAuth(); 
        if ($_SESSION['user']['role'] !== 'admin') {
            $this->redirect('home');
        }
    }
}