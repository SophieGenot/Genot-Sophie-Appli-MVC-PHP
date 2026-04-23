<?php
require_once __DIR__ . '/AbstractModel.php';

/**
 * Modèle Agence
 * Gère le référentiel des implantations géographiques (villes).
 * Ce modèle est principalement utilisé par l'administrateur pour la gestion
 * des points de départ et d'arrivée des trajets.
 */
class Agence extends AbstractModel {

    /** @var string Nom de la table associée */
    protected string $table = 'agences';

    /**
     * Récupère la liste de toutes les agences.
     * Les agences sont triées par nom pour faciliter la lecture dans les formulaires.
     * * @return array Liste associative des agences.
     */
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre une nouvelle agence dans le référentiel.
     * Cette action est réservée au profil Administrateur.
     * * @param string $nom Le nom de la ville ou de l'agence.
     * @return bool True si l'insertion a réussi.
     */
    public function create(string $nom): bool {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (nom) VALUES (?)");
        return $stmt->execute([$nom]);
    }

    /**
     * Met à jour le nom d'une agence existante.
     * * @param int $id L'identifiant unique de l'agence.
     * @param string $nom Le nouveau nom à attribuer.
     * @return bool True si la mise à jour a réussi.
     */
    public function update(int $id, string $nom): bool {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET nom = ? WHERE id = ?");
        return $stmt->execute([$nom, $id]);
    }
}