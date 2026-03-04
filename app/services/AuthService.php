<?php

require_once __DIR__ . '/../models/User.php';

class AuthService {

    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
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