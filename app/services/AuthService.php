<?php
require_once __DIR__ . '/AbstractService.php';
require_once __DIR__ . '/../models/User.php';

class AuthService extends AbstractService {

    private User $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
    }

    /**
     * Authentifie un utilisateur
     * @param string $email
     * @param string $password
     * @throws Exception
     * @return array|false
     */
    public function login(string $email, string $password) {
        $user = $this->userModel->findByEmail($email);

        // 1. On vérifie si l'utilisateur existe
        if (!$user) {
            return false;
        }

        // 2. On vérifie si le mot de passe est correct
        if (!password_verify($password, $user['mot_de_passe'])) {
            return false;
        }

        // 3. Vérification du compte validé (Amélioration)
        if ((int)$user['is_validated'] !== 1) {
            throw new Exception("Votre compte n'a pas encore été validé par un administrateur.");
        }

        // Si tout est ok, on retourne l'utilisateur
        return $user;
    }
}