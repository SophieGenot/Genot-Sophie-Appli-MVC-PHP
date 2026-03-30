<?php
require_once __DIR__ . '/AbstractModel.php';

class User extends AbstractModel {
    protected string $table = 'utilisateurs';

    // findAll() et findById() sont héritées du parent !

    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

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

    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE utilisateurs SET " . implode(", ", $fields) . " WHERE id = ?";
        return $this->pdo->prepare($sql)->execute($values);
    }
}