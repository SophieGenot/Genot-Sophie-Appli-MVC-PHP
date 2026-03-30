<?php
abstract class AbstractController {
    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Affiche une vue
    protected function render(string $view, array $data = []) {
        extract($data); 
        $filePath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($filePath)) {
            require $filePath;
        } else {
            die("La vue '$view' est introuvable.");
        }
    }

    // Redirige vers une action
    protected function redirect(string $action) {
        header("Location: index.php?action=" . $action);
        exit;
    }

    // Vérifie la connexion simple
    protected function checkAuth() {
        if (!isset($_SESSION['user']['id'])) {
            $this->redirect('home');
        }
    }

    // Vérifie le rôle admin
    protected function checkAdmin() {
        $this->checkAuth(); 
        if ($_SESSION['user']['role'] !== 'admin') {
            $this->redirect('home');
        }
    }
}