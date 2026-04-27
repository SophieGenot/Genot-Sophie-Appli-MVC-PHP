<?php

class Reservation {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère une réservation par son ID
     * Nécessaire pour identifier le trajet associé avant annulation
     */
    public function findById(int $id) {
        $sql = "SELECT * FROM reservations WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une réservation
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM reservations WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function create(int $trajetId, int $passagerId, string $message): bool {
        $sql = "INSERT INTO reservations (trajet_id, passager_id, message_notification) 
                VALUES (:t, :p, :m)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            't' => $trajetId,
            'p' => $passagerId,
            'm' => $message
        ]);
    }

    // Pour afficher les notifications sur le dashboard du conducteur
    public function findNotificationsForUser(int $userId) {
        $sql = "SELECT r.*, t.agence_depart_id, t.agence_arrivee_id
                FROM reservations r
                JOIN trajets t ON r.trajet_id = t.id
                WHERE t.auteur_id = :u 
                ORDER BY r.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findReservationsByPassenger(int $passengerId) {
        $sql = "SELECT 
                    r.id AS reservation_id,
                    ad.nom AS agence_depart,    
                    aa.nom AS agence_arrivee,  
                    t.gdh_depart,
                    t.gdh_arrivee,
                    t.id AS trajet_id,
                    u.nom AS auteur_nom,
                    u.prenom AS auteur_prenom
                FROM reservations r
                JOIN trajets t ON r.trajet_id = t.id
                JOIN utilisateurs u ON t.auteur_id = u.id
                JOIN agences ad ON t.agence_depart_id = ad.id
                JOIN agences aa ON t.agence_arrivee_id = aa.id
                WHERE r.passager_id = :p";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['p' => $passengerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}