<?php
require_once __DIR__ . '/AbstractModel.php';

class Agence extends AbstractModel {

    protected string $table = 'agences';
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Créer une nouvelle agence
    public function create(string $nom): bool {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (nom) VALUES (?)");
        return $stmt->execute([$nom]);
    }

    // Mettre à jour une agence
    public function update(int $id, string $nom): bool {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET nom = ? WHERE id = ?");
        return $stmt->execute([$nom, $id]);
    }
}