<?php

/**
 * Classe abstraite AbstractModel
 * Sert de base à tous les modèles de l'application.
 * Elle centralise l'accès à la base de données (PDO) et les méthodes CRUD génériques
 * pour éviter la duplication de code et faciliter la maintenance.
 */
abstract class AbstractModel {
    /** @var PDO Instance de connexion à la base de données */
    protected PDO $pdo; 
    
    /** @var string Nom de la table qui sera défini dans les classes enfants */
    protected string $table; 

    /**
     * Constructeur de la classe.
     * Injecte l'instance PDO pour permettre les requêtes SQL.
     * * @param PDO $pdo Connexion à la base de données.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère l'ensemble des enregistrements de la table.
     * * @return array Liste de toutes les lignes sous forme de tableaux associatifs.
     */
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un enregistrement spécifique par son identifiant unique.
     * * @param int $id L'identifiant de la ligne recherchée.
     * @return array|null Les données de la ligne ou null si aucune correspondance.
     */
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Supprime un enregistrement de la base de données.
     * * @param int $id L'identifiant de la ligne à supprimer.
     * @return bool True si la suppression a été effectuée avec succès.
     */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}