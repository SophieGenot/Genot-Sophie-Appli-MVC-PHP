<?php
require_once __DIR__ . '/AbstractModel.php';

/**
 * Modèle Trajet
 * Gère la logique de stockage et de récupération des annonces de covoiturage.
 * Centralise les règles d'affichage (places disponibles et tri chronologique).
 */
class Trajet extends AbstractModel {

    /** @var string Nom de la table associée */
    protected string $table = 'trajets';

    /**
     * Récupère TOUS les trajets avec les informations des auteurs et des agences.
     * Requis pour le traitement et le filtrage dans le dashboard employé.
     * * @return array Liste complète des trajets.
     */
    public function findAllAvecInfos(): array {
        // En sélectionnant t.*, on récupère bien t.agence_depart_id nécessaire au filtrage !
        $sql = "SELECT t.*, ad.nom AS agence_depart, aa.nom AS agence_arrivee, 
                       u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                       t.nb_places_disponibles AS places_dispo
                FROM trajets t
                JOIN agences ad ON t.agence_depart_id = ad.id
                JOIN agences aa ON t.agence_arrivee_id = aa.id
                JOIN utilisateurs u ON t.auteur_id = u.id
                ORDER BY t.gdh_depart ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la liste des trajets disponibles pour la page d'accueil.
     * Effectue des jointures pour obtenir le nom des agences et de l'auteur.
     * Filtre les résultats pour n'afficher que les trajets avec des places libres.
     * @return array Liste des trajets triés par date de départ croissante.
     */
    public function findDisponibles(): array {
        $sql = "SELECT t.*, ad.nom AS agence_depart, aa.nom AS agence_arrivee, u.nom AS user_nom 
                FROM trajets t
                JOIN agences ad ON t.agence_depart_id = ad.id
                JOIN agences aa ON t.agence_arrivee_id = aa.id
                JOIN utilisateurs u ON t.auteur_id = u.id
                WHERE t.nb_places_disponibles > 0
                ORDER BY t.gdh_depart ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre un nouveau trajet proposé par un employé.
     * @param array $data Données du trajet (id agences, GDH, places, id auteur).
     * @return bool True si l'insertion en base de données a réussi.
     */
    public function create(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO trajets (agence_depart_id, agence_arrivee_id, gdh_depart, gdh_arrivee, nb_places_total, nb_places_disponibles, auteur_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['agence_depart_id'], 
            $data['agence_arrivee_id'], 
            $data['gdh_depart'], 
            $data['gdh_arrivee'], 
            $data['nb_places_total'], 
            $data['nb_places_disponibles'], 
            $data['auteur_id']
        ]);
    }

    /**
     * Met à jour les informations d'un trajet existant.
     * Permet à l'auteur ou à l'administrateur de modifier les détails du trajet.
     * @param int $id L'identifiant unique du trajet.
     * @param array $data Nouvelles données à appliquer.
     * @return bool True si la mise à jour a été effectuée.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE trajets SET agence_depart_id = ?, agence_arrivee_id = ?, gdh_depart = ?, gdh_arrivee = ?, nb_places_total = ?, nb_places_disponibles = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['agence_depart_id'], 
            $data['agence_arrivee_id'], 
            $data['gdh_depart'], 
            $data['gdh_arrivee'], 
            $data['nb_places_total'], 
            $data['nb_places_disponibles'], 
            $id
        ]);
    }

    /**
     * Met à jour uniquement le nombre de places disponibles (lors d'une réservation/annulation).
     */
    public function updatePlacesDisponibles(int $id, int $nouvellesPlaces): bool {
        $sql = "UPDATE trajets SET nb_places_disponibles = :places WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['places' => $nouvellesPlaces, 'id' => $id]);
    }
}