<?php
require_once __DIR__ . '/AbstractModel.php';

/**
 * Modèle User
 * Gère toutes les interactions avec la table 'utilisateurs'.
 * Inclut la gestion de l'authentification et du cycle de validation admin.
 */
class User extends AbstractModel {
    
    /** @var string Nom de la table associée */
    protected string $table = 'utilisateurs';

    /**
     * Recherche un utilisateur par son adresse email.
     * Utilisé principalement lors de la phase de connexion.
     * * @param string $email L'adresse email fournie par l'utilisateur.
     * @return array|null Retourne les données de l'utilisateur ou null s'il n'existe pas.
     */
    /**
 * Exemple de requête préparée avec paramètre nommé pour contrer les injections SQL.
 */
public function findByEmail(string $email): ?array
{
    // On utilise :email au lieu de ?
    $sql = "SELECT * FROM utilisateurs WHERE email = :email";
    
    $stmt = $this->pdo->prepare($sql);
    
    // On passe un tableau associatif à l'execute
    $stmt->execute(['email' => $email]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Retourne le tableau de l'utilisateur ou null s'il n'existe pas
    return $user ?: null;
}

    /**
     * Enregistre un nouvel utilisateur en base de données.
     * Par défaut, un employé est créé avec un statut non validé (is_validated = 0).
     * * @param array $data Tableau contenant les index : nom, prenom, telephone, email, mot_de_passe, role.
     * @return bool True si l'insertion a réussi.
     */
    public function create(array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO utilisateurs (nom, prenom, telephone, email, mot_de_passe, role, is_validated) 
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['telephone'], 
            $data['email'],
            $data['mot_de_passe'],
            $data['role'] ?? 'employe',
            $data['is_validated'] ?? 0
        ]);
    }

    /**
     * Récupère la liste des comptes employés en attente de validation par l'administrateur.
     * * @return array Liste associative des utilisateurs à valider.
     */
    public function findPending(): array {
        $sql = "SELECT id, nom, prenom, email, telephone 
                FROM utilisateurs 
                WHERE role = 'employe' AND is_validated = 0";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Modifie le statut de validation d'un compte utilisateur.
     * * @param int $id L'identifiant de l'utilisateur.
     * @param int $status Le nouveau statut (1 pour validé, 0 pour refusé/en attente).
     * @return bool True si la mise à jour a réussi.
     */
    public function setValidationStatus(int $id, int $status): bool {
        $sql = "UPDATE utilisateurs SET is_validated = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    /**
     * Met à jour les informations personnelles d'un utilisateur.
     * * @param int $id L'identifiant de l'utilisateur à modifier.
     * @param array $data Tableau contenant les nouvelles valeurs (nom, prenom, telephone).
     * @return bool True si la mise à jour a réussi.
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE utilisateurs 
                SET nom = ?, 
                    prenom = ?, 
                    telephone = ? 
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['telephone'],
            $id
        ]);
    }
}