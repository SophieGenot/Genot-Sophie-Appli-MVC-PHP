<?php
class Agence {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer toutes les agences
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM agences ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une agence par ID
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM agences WHERE id = ?");
        $stmt->execute([$id]);
        $agence = $stmt->fetch(PDO::FETCH_ASSOC);
        return $agence ?: null;
    }

    // Créer une nouvelle agence
    public function create(string $nom): bool {
        $stmt = $this->pdo->prepare("INSERT INTO agences (nom) VALUES (?)");
        return $stmt->execute([$nom]);
    }

    // Mettre à jour une agence
    public function update(int $id, string $nom): bool {
        $stmt = $this->pdo->prepare("UPDATE agences SET nom = ? WHERE id = ?");
        return $stmt->execute([$nom, $id]);
    }

    // Supprimer une agence
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM agences WHERE id = ?");
        return $stmt->execute([$id]);
    }
}