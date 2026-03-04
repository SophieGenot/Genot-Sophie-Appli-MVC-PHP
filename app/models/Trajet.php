<?php
class Trajet {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer tous les trajets disponibles (places > 0)
    public function findDisponibles(): array {
        $sql = "
            SELECT 
                t.*,
                ad.nom AS agence_depart,
                aa.nom AS agence_arrivee,
                u.nom AS user_nom,
                u.prenom AS user_prenom,
                u.email AS user_email,
                u.telephone AS user_tel
            FROM trajets t
            JOIN agences ad ON t.agence_depart_id = ad.id
            JOIN agences aa ON t.agence_arrivee_id = aa.id
            JOIN utilisateurs u ON t.auteur_id = u.id
            WHERE t.nb_places_disponibles > 0
            ORDER BY t.gdh_depart ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les trajets (pour dashboard)
    public function findAll(): array {
        $sql = "
            SELECT 
                t.*,
                ad.nom AS agence_depart,
                aa.nom AS agence_arrivee,
                u.nom AS user_nom,
                u.prenom AS user_prenom,
                u.email AS user_email,
                u.telephone AS user_tel
            FROM trajets t
            JOIN agences ad ON t.agence_depart_id = ad.id
            JOIN agences aa ON t.agence_arrivee_id = aa.id
            JOIN utilisateurs u ON t.auteur_id = u.id
            ORDER BY t.gdh_depart ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un trajet par ID
    public function findById(int $id): ?array {
        $sql = "
            SELECT 
                t.*,
                ad.nom AS agence_depart,
                aa.nom AS agence_arrivee,
                u.nom AS nom,
                u.prenom AS prenom,
                u.email AS email,
                u.telephone AS telephone
            FROM trajets t
            JOIN agences ad ON t.agence_depart_id = ad.id
            JOIN agences aa ON t.agence_arrivee_id = aa.id
            JOIN utilisateurs u ON t.auteur_id = u.id
            WHERE t.id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        return $trajet ?: null;
    }

    // Créer un trajet
    public function create(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO trajets (
                agence_depart_id,
                agence_arrivee_id,
                gdh_depart,
                gdh_arrivee,
                nb_places_total,
                nb_places_disponibles,
                auteur_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
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

    // Modifier un trajet
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE trajets SET
                agence_depart_id = ?,
                agence_arrivee_id = ?,
                gdh_depart = ?,
                gdh_arrivee = ?,
                nb_places_total = ?,
                nb_places_disponibles = ?
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

    // Supprimer un trajet
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM trajets WHERE id = ?");
        return $stmt->execute([$id]);
    }
}