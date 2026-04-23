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
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['telephone']) || empty($data['mot_de_passe'])) {
            throw new Exception("Données utilisateur incomplètes");
        }

        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

        return $this->userModel->create($data);
    }

    public function getUsersPendingValidation(): array {
        return $this->userModel->findPending();
    }
    
    public function validateUser(int $id): bool {
        return $this->userModel->setValidationStatus($id, 1);
    }

    // Mettre à jour un utilisateur
    public function updateUser(int $id, array $data): bool {
        // On prépare un tableau propre pour le Model
        $cleanData = [
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'telephone' => $data['telephone']
        ];

        return $this->userModel->update($id, $cleanData);
    }

    // Supprimer un utilisateur
    public function deleteUser(int $id): bool {
        return $this->userModel->delete($id);
    }
}