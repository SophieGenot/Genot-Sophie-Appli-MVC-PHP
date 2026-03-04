<?php
class User {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer tous les utilisateurs
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM utilisateurs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Chercher un utilisateur par ID
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Chercher un utilisateur par email
    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Créer un nouvel utilisateur
    public function create(array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO utilisateurs (nom, prenom, telephone, email, mot_de_passe, role) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['telephone'] ?? null,
            $data['email'],
            $data['mot_de_passe'],
            $data['role'] ?? 'employe'
        ]);
    }

    // Mettre à jour un utilisateur
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE utilisateurs SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    // Supprimer un utilisateur
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}