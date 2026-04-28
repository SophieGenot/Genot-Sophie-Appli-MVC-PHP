<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/services/TrajetService.php';

class TrajetServiceTest extends TestCase {

    private $pdo;
    private $trajetService;

    protected function setUp(): void {
        // Connexion à la base de test
        $this->pdo = new PDO("mysql:host=localhost;dbname=appklaxon_test", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->trajetService = new TrajetService($this->pdo);
        
        // Nettoyage des tables pour éviter les conflits (Ordre important cause clés étrangères)
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->pdo->exec("DELETE FROM reservations");
        $this->pdo->exec("DELETE FROM trajets");
        $this->pdo->exec("DELETE FROM utilisateurs");
        $this->pdo->exec("DELETE FROM agences");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        // Préparation de données de base nécessaires (Agences et Auteur)
        $this->pdo->exec("INSERT INTO agences (id, nom) VALUES (1, 'Agence A'), (2, 'Agence B')");
        $this->pdo->exec("INSERT INTO utilisateurs (id, nom, prenom, email, telephone, mot_de_passe) 
                          VALUES (10, 'Conducteur', 'Test', 'driver@test.fr', '0600000000', 'hash')");
    }

    public function testCreateTrajetValide(): void {
        $data = [
            'agence_depart_id' => 1,
            'agence_arrivee_id' => 2,
            'gdh_depart' => '2026-12-01 08:00:00',
            'gdh_arrivee' => '2026-12-01 09:00:00',
            'nb_places_total' => 3,
            'auteur_id' => 10
        ];

        $result = $this->trajetService->createTrajet($data);
        $this->assertTrue($result);

        // Vérifie que les places dispos ont bien été initialisées au total
        $trajet = $this->trajetService->getAllTrajetsAvecInfos()[0];
        $this->assertEquals(3, $trajet['places_dispo']);
    }

    public function testCreateTrajetMemeAgenceRefuse(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'agence de départ et d'arrivée doivent être différentes.");

        $data = [
            'agence_depart_id' => 1,
            'agence_arrivee_id' => 1, // ID identique
            'gdh_depart' => '2026-12-01 08:00:00',
            'gdh_arrivee' => '2026-12-01 09:00:00',
            'nb_places_total' => 2,
            'auteur_id' => 10
        ];

        $this->trajetService->createTrajet($data);
    }

    public function testCreateTrajetTropDePlacesRefuse(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Le nombre de places doit être compris entre 1 et 4.");

        $data = [
            'agence_depart_id' => 1,
            'agence_arrivee_id' => 2,
            'gdh_depart' => '2026-12-01 08:00:00',
            'gdh_arrivee' => '2026-12-01 09:00:00',
            'nb_places_total' => 10, // Trop de places
            'auteur_id' => 10
        ];

        $this->trajetService->createTrajet($data);
    }

   public function testReserverPlaceDiminueQuota(): void {
    // 1. Création du trajet (l'auteur ID 10 est déjà créé dans ton setUp)
    $this->trajetService->createTrajet([
        'agence_depart_id' => 1, 'agence_arrivee_id' => 2,
        'gdh_depart' => '2026-12-01 08:00:00', 'gdh_arrivee' => '2026-12-01 09:00:00',
        'nb_places_total' => 2, 'auteur_id' => 10
    ]);
    $trajetId = $this->pdo->lastInsertId();

    // 2. CRÉER l'utilisateur passager avant de réserver (TRÈS IMPORTANT)
    $this->pdo->exec("INSERT INTO utilisateurs (id, nom, prenom, email, telephone, mot_de_passe) 
                      VALUES (11, 'Passager', 'Test', 'passager@test.fr', '0600000011', 'hash')");

    $userPassager = ['id' => 11, 'nom' => 'Passager', 'prenom' => 'Test'];
    
    $result = $this->trajetService->reserverPlace($trajetId, $userPassager);
    $this->assertTrue($result);

    $trajet = $this->trajetService->getTrajetById($trajetId);
    $this->assertEquals(1, $trajet['places_dispo']);
}
}