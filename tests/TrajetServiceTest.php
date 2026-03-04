<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/services/TrajetService.php';

class TrajetServiceTest extends TestCase {

    private PDO $pdo;
    private TrajetService $trajetService;

    protected function setUp(): void {
        // Connexion à la base de test
        $this->pdo = new PDO("mysql:host=localhost;dbname=appklaxon_test", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Commencer une transaction pour isoler chaque test
        $this->pdo->beginTransaction();

        $this->trajetService = new TrajetService($this->pdo);

        // Insérer données nécessaires pour FK
        $this->pdo->exec("INSERT INTO agences (id, nom) VALUES (1, 'Agence A'), (2, 'Agence B')");
        $this->pdo->exec("INSERT INTO utilisateurs (id, nom, prenom, email) VALUES (1, 'Doe', 'John', 'john@test.com')");
    }

    protected function tearDown(): void {
        // Rollback pour revenir à l'état initial après chaque test
        $this->pdo->rollBack();
    }

    public function testCreateTrajet(): void {
        $data = [
            'agence_depart_id' => 1,
            'agence_arrivee_id' => 2,
            'gdh_depart' => '2026-03-01 08:00:00',
            'gdh_arrivee' => '2026-03-01 12:00:00',
            'nb_places_total' => 4,
            'nb_places_disponibles' => 4,
            'auteur_id' => 1
        ];

        $result = $this->trajetService->createTrajet($data);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM trajets");
        $count = $stmt->fetchColumn();
        $this->assertEquals(1, $count);
    }

    public function testDeleteTrajet(): void {
        $data = [
            'agence_depart_id' => 1,
            'agence_arrivee_id' => 2,
            'gdh_depart' => '2026-03-01 08:00:00',
            'gdh_arrivee' => '2026-03-01 12:00:00',
            'nb_places_total' => 4,
            'nb_places_disponibles' => 4,
            'auteur_id' => 1
        ];

        $this->trajetService->createTrajet($data);

        $stmt = $this->pdo->query("SELECT id FROM trajets LIMIT 1");
        $id = $stmt->fetchColumn();

        $result = $this->trajetService->deleteTrajet($id);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM trajets");
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);
    }

    public function testCreateTrajetWithInvalidData(): void {
        $this->expectException(Exception::class);

        $data = [
            'agence_depart_id' => 1,
            'agence_arrivee_id' => 1, // même agence -> doit échouer
            'gdh_depart' => '2026-03-01 12:00:00',
            'gdh_arrivee' => '2026-03-01 08:00:00', // arrivée avant départ -> doit échouer
            'nb_places_total' => 5, // trop de places
            'nb_places_disponibles' => 5,
            'auteur_id' => 1
        ];

        $this->trajetService->createTrajet($data);
    }
}