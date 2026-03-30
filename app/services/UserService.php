<?php
require_once __DIR__ . '/AbstractService.php';
require_once __DIR__ . '/../models/User.php';

class UserService extends AbstractService {
    private User $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
    }

    // Récupérer tous les utilisateurs
    public function getAllUsers(): array {
        return $this->userModel->findAll();
    }

    // Récupérer un utilisateur par ID
    public function getUserById(int $id): ?array {
        return $this->userModel->findById($id);
    }

    // Récupérer un utilisateur par email
    public function getUserByEmail(string $email): ?array {
        return $this->userModel->findByEmail($email);
    }

    // Créer un nouvel utilisateur
    public function createUser(array $data): bool {
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['mot_de_passe'])) {
            throw new Exception("Données utilisateur incomplètes");
        }

        // Sécurité : Hashage du mot de passe
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

        return $this->userModel->create($data);
    }

    // Mettre à jour un utilisateur
    public function updateUser(int $id, array $data): bool {
        if (!empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        } else {
            unset($data['mot_de_passe']);
        }
        return $this->userModel->update($id, $data);
    }

    // Supprimer un utilisateur
    public function deleteUser(int $id): bool {
        return $this->userModel->delete($id);
    }
}