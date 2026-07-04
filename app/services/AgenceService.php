<?php 
require_once __DIR__ . '/AbstractService.php'; 
require_once __DIR__ . '/../models/Agence.php';

class AgenceService extends AbstractService {
    private Agence $agenceModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->agenceModel = new Agence($this->pdo);
    }

    // Récupérer toutes les agences
    public function getAllAgences(): array {
        return $this->agenceModel->findAll();
    }

    // Créer une agence
    public function createAgence(string $name): bool {
        // CORRECTION : On teste bien $name
        if (empty($name)) {
            throw new Exception("Le nom de l'agence est requis.");
        }
        return $this->agenceModel->create($name);
    }

    // Modifier une agence existante
    public function updateAgence(int $id, string $name): bool {
        // CORRECTION : On teste bien $name et on a typé int et string
        if (empty($id) || empty($name)) {
            throw new Exception("ID et nom valides requis pour la mise à jour.");
        }
        return $this->agenceModel->update($id, $name);
    }

    // Supprimer une agence
    public function deleteAgence(int $id): bool {
        // CORRECTION : On a typé int ici aussi
        if (empty($id)) {
            throw new Exception("ID valide requis pour la suppression.");
        }
        return $this->agenceModel->delete($id);
    }
}