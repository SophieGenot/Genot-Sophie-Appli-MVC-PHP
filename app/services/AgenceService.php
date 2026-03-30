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
    public function getAllAgences() {
        return $this->agenceModel->findAll();
    }

    // Créer une agence
    public function createAgence($nom) {
        if (empty($nom)) {
            throw new Exception("Le nom de l'agence est requis.");
        }
        return $this->agenceModel->create($nom);
    }

    // Modifier une agence existante
    public function updateAgence($id, $nom) {
        if (empty($id) || empty($nom)) {
            throw new Exception("ID et nom valides requis pour la mise à jour.");
        }
        return $this->agenceModel->update($id, $nom);
    }

    // Supprimer une agence
    public function deleteAgence($id) {
        if (empty($id)) {
            throw new Exception("ID valide requis pour la suppression.");
        }
        return $this->agenceModel->delete($id);
    }
}