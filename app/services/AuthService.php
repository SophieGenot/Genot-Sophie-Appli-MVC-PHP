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
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['mot_de_passe'])) {
            return false;
        }
        if ($user['role'] !== 'admin' && (int)$user['is_validated'] !== 1) {
            throw new Exception("Votre compte n'a pas encore été validé par 
            un administrateur.");
        }
        return $user;
    }
}