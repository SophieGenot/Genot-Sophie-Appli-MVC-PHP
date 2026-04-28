<?php
require_once __DIR__ . '/AbstractService.php';
require_once __DIR__ . '/../models/Trajet.php';
require_once __DIR__ . '/../models/Agence.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Reservation.php';

class TrajetService extends AbstractService {
    private Trajet $trajetModel;
    private Agence $agenceModel;
    private User $userModel;
    private Reservation $reservationModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->trajetModel = new Trajet($this->pdo);
        $this->agenceModel = new Agence($this->pdo);
        $this->userModel = new User($this->pdo);
        $this->reservationModel = new Reservation($this->pdo);
    }

    public function getAllTrajetsDisponiblesAvecInfos(): array {
        $trajets = $this->trajetModel->findDisponibles();
        foreach ($trajets as &$t) {
            $this->enrichirInfosTrajet($t);
        }
        return $trajets;
    }

    public function getAllTrajetsAvecInfos(): array {
        $trajets = $this->trajetModel->findAll();
        foreach ($trajets as &$t) {
            $this->enrichirInfosTrajet($t);
        }
        return $trajets;
    }

    private function enrichirInfosTrajet(array &$t): void {
        $t['agence_depart'] = $this->agenceModel->findById($t['agence_depart_id'])['nom'] ?? '';
        $t['agence_arrivee'] = $this->agenceModel->findById($t['agence_arrivee_id'])['nom'] ?? '';
        $user = $this->userModel->findById($t['auteur_id']);
        $t['user_nom'] = $user['nom'] ?? '';
        $t['user_prenom'] = $user['prenom'] ?? '';
        $t['user_email'] = $user['email'] ?? '';
        $t['user_tel'] = $user['telephone'] ?? '';
        $t['places_dispo'] = $t['nb_places_disponibles'] ?? $t['nb_places_total'];
    }

    public function getReservationsByPassenger(int $userId) {
        return $this->reservationModel->findReservationsByPassenger($userId);
    }

   public function createTrajet(array $data): bool {
    // 1. Vérification des champs vides
    if (empty($data['agence_depart_id']) || empty($data['agence_arrivee_id']) || 
        empty($data['gdh_depart']) || empty($data['gdh_arrivee']) || 
        empty($data['nb_places_total']) || empty($data['auteur_id'])) {
        throw new Exception("Tous les champs sont obligatoires.");
    }

    // 2. Logique agences (Déplacé du Controller)
    if ($data['agence_depart_id'] == $data['agence_arrivee_id']) {
        throw new Exception("L'agence de départ et d'arrivée doivent être différentes.");
    }

    // 3. Logique dates (Déplacé du Controller)
    if (strtotime($data['gdh_depart']) >= strtotime($data['gdh_arrivee'])) {
        throw new Exception("La date d'arrivée doit être postérieure au départ.");
    }

    // 4. Logique places
    if ($data['nb_places_total'] < 1 || $data['nb_places_total'] > 4) {
        throw new Exception("Le nombre de places doit être compris entre 1 et 4.");
    }

    // Si tout est OK, on prépare la donnée pour le modèle
    $data['nb_places_disponibles'] = $data['nb_places_total'];
    return $this->trajetModel->create($data);
}

    public function getTrajetById(int $id) {
        $trajet = $this->trajetModel->findById($id);
        if (!$trajet) return null;
        $this->enrichirInfosTrajet($trajet);
        return $trajet;
    }

    public function reserverPlace(int $idTrajet, array $userConnecte): bool {
        $trajet = $this->getTrajetById($idTrajet);
        if (!$trajet || $trajet['places_dispo'] <= 0) {
            throw new Exception("Réservation impossible : plus de places disponibles.");
        }

        try {
            $this->pdo->beginTransaction();
            $nouveauNb = $trajet['places_dispo'] - 1;
            $this->trajetModel->updatePlacesDisponibles($idTrajet, $nouveauNb);

            $nomPassager = $userConnecte['prenom'] . " " . $userConnecte['nom'];
            $message = "L'employé $nomPassager a réservé une place sur votre trajet " . 
                       $trajet['agence_depart'] . " → " . $trajet['agence_arrivee'] . ".";
            
            $this->reservationModel->create($idTrajet, (int)$userConnecte['id'], $message);
            return $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getNotificationsForUser(int $userId): array {
        return $this->reservationModel->findNotificationsForUser($userId);
    }

    public function deleteTrajet(int $id): bool {
        if (empty($id)) throw new Exception("ID invalide");
        return $this->trajetModel->delete($id);
    }

    public function updateTrajet(int $id, array $data): bool {
        if (empty($id) || empty($data)) throw new Exception("ID ou données invalides");
        if (isset($data['nb_places_total'])) {
             $data['nb_places_disponibles'] = $data['nb_places_total'];
        }
        return $this->trajetModel->update($id, $data);
    }

    public function annulerUnePlace(int $reservationId): bool {
        $res = $this->reservationModel->findById($reservationId);
        if (!$res) throw new Exception("Réservation introuvable.");

        $idTrajet = $res['trajet_id'];
        $trajet = $this->trajetModel->findById($idTrajet);
        
        try {
            $this->pdo->beginTransaction();
            $nouveauNb = $trajet['nb_places_disponibles'] + 1;
            $this->trajetModel->updatePlacesDisponibles($idTrajet, $nouveauNb);
            $result = $this->reservationModel->delete($reservationId);
            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}