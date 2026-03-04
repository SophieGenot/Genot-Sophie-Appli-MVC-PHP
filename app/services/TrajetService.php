<?php
require_once __DIR__ . '/../models/Trajet.php';
require_once __DIR__ . '/../models/Agence.php';
require_once __DIR__ . '/../models/User.php';

class TrajetService {
    private $trajetModel;
    private $agenceModel;
    private $userModel;

    public function __construct($pdo) {
        $this->trajetModel = new Trajet($pdo);
        $this->agenceModel = new Agence($pdo);
        $this->userModel = new User($pdo);
    }

    public function getAllTrajetsDisponiblesAvecInfos(): array {
        $trajets = $this->trajetModel->findDisponibles();

        foreach ($trajets as &$t) {
            $t['agence_depart'] = $this->agenceModel->findById($t['agence_depart_id'])['nom'] ?? '';
            $t['agence_arrivee'] = $this->agenceModel->findById($t['agence_arrivee_id'])['nom'] ?? '';
            $user = $this->userModel->findById($t['auteur_id']);
            $t['user_nom'] = $user['nom'] ?? '';
            $t['user_prenom'] = $user['prenom'] ?? '';
            $t['user_email'] = $user['email'] ?? '';
            $t['user_tel'] = $user['telephone'] ?? '';
            $t['places_dispo'] = $t['nb_places_disponibles'] ?? $t['nb_places_total'];
        }

        return $trajets;
    }

    public function getAllTrajetsAvecInfos(): array {
        $trajets = $this->trajetModel->findAll();

        foreach ($trajets as &$t) {
            $t['agence_depart'] = $this->agenceModel->findById($t['agence_depart_id'])['nom'] ?? '';
            $t['agence_arrivee'] = $this->agenceModel->findById($t['agence_arrivee_id'])['nom'] ?? '';
            $user = $this->userModel->findById($t['auteur_id']);
            $t['user']['id'] = $t['auteur_id']; 
            $t['nom'] = $user['nom'] ?? '';
            $t['prenom'] = $user['prenom'] ?? '';
            $t['email'] = $user['email'] ?? '';
            $t['telephone'] = $user['telephone'] ?? '';
            $t['places_dispo'] = $t['nb_places_disponibles'] ?? $t['nb_places_total'];
        }

        return $trajets;
    }

    public function createTrajet(array $data): bool {
        if (
            empty($data['agence_depart_id']) ||
            empty($data['agence_arrivee_id']) ||
            empty($data['gdh_depart']) ||
            empty($data['gdh_arrivee']) ||
            empty($data['nb_places_total']) ||
            empty($data['auteur_id'])
        ) {
            throw new Exception("Données invalides");
        }

        if ($data['nb_places_total'] > 4) {
            throw new Exception("Une voiture ne peut pas dépasser 4 places");
        }

        $data['nb_places_disponibles'] = $data['nb_places_total'];

        return $this->trajetModel->create($data);
    }

    public function getTrajetById($id) {
    $trajet = $this->trajetModel->findById($id); // utilise ton modèle
    if (!$trajet) return null;

    // Ajoute les infos des agences et utilisateur
    $trajet['agence_depart'] = $this->agenceModel->findById($trajet['agence_depart_id'])['nom'] ?? '';
    $trajet['agence_arrivee'] = $this->agenceModel->findById($trajet['agence_arrivee_id'])['nom'] ?? '';
    $user = $this->userModel->findById($trajet['auteur_id']);
    $trajet['user_nom'] = $user['nom'] ?? '';
    $trajet['user_prenom'] = $user['prenom'] ?? '';
    $trajet['user_email'] = $user['email'] ?? '';
    $trajet['user_tel'] = $user['telephone'] ?? '';
    $trajet['places_dispo'] = $trajet['nb_places_disponibles'] ?? $trajet['nb_places_total'];

    return $trajet;
}

    public function deleteTrajet(int $id): bool {
        if (empty($id)) {
            throw new Exception("ID invalide");
        }
        return $this->trajetModel->delete($id);
    }

    public function updateTrajet(int $id, array $data): bool {
        if (empty($id) || empty($data)) {
            throw new Exception("ID ou données invalides");
        }
        return $this->trajetModel->update($id, $data);
    }
}