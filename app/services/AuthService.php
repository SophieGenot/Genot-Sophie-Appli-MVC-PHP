<?php
require_once __DIR__ . '/AbstractService.php';
require_once __DIR__ . '/../models/User.php';

class AuthService extends AbstractService {

    private User $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['mot_de_passe'])) {
            return false;
        }

        return $user;
    }
}