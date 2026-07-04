<?php
require_once __DIR__ . '/AbstractService.php';
require_once __DIR__ . '/../models/User.php';

class UserService extends AbstractService {
    private User $userModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
    }

    public function getAllUsers(): array {
        return $this->userModel->findAll();
    }

    public function getUserById(int $id): ?array {
        return $this->userModel->findById($id);
    }

    public function getUserByEmail(string $email): ?array {
        return $this->userModel->findByEmail($email);
    }

    public function createUser(array $data): bool {
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) ||
         empty($data['telephone']) || empty($data['mot_de_passe'])) {
            throw new Exception("Données utilisateur incomplètes.");
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

    public function updateUser(int $id, array $data): bool {
        // Validation du téléphone déportée dans le Service
        if (isset($data['telephone']) && !AbstractService::isPhoneValid($data['telephone'])) {
            throw new Exception("Le numéro est incorrect. Il doit contenir exactement 10 chiffres.");
        }

        $cleanData = [
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'telephone' => $data['telephone'] ?? ''
        ];

        return $this->userModel->update($id, $cleanData);
    }

    public function deleteUser(int $id, int $currentAdminId): bool {
        if ($id === $currentAdminId) {
            throw new Exception("Sécurité : Vous ne pouvez pas supprimer votre propre compte administrateur.");
        }
        return $this->userModel->delete($id);
    }
}