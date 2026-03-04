<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/services/AgenceService.php';

class AgenceServiceTest extends TestCase {

    private PDO $pdo;
    private AgenceService $agenceService;

    protected function setUp(): void {
        // Connexion à la base de test
        $this->pdo = new PDO("mysql:host=localhost;dbname=appklaxon_test", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Commencer une transaction pour isoler chaque test
        $this->pdo->beginTransaction();

        $this->agenceService = new AgenceService($this->pdo);

        // Créer données nécessaires pour FK si besoin
        $this->pdo->exec("DELETE FROM trajets"); // éviter conflit FK
    }

    protected function tearDown(): void {
        // Rollback pour revenir à l'état initial après chaque test
        $this->pdo->rollBack();
    }

    public function testCreateAgence(): void {
        $result = $this->agenceService->createAgence("TestVille");
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM agences");
        $count = $stmt->fetchColumn();
        $this->assertEquals(1, $count);
    }

    public function testUpdateAgence(): void {
        $this->agenceService->createAgence("AncienneVille");
        $stmt = $this->pdo->query("SELECT id FROM agences LIMIT 1");
        $id = $stmt->fetchColumn();

        $result = $this->agenceService->updateAgence($id, "NouvelleVille");
        $this->assertTrue($result);

        $stmt = $this->pdo->prepare("SELECT nom FROM agences WHERE id = ?");
        $stmt->execute([$id]);
        $nom = $stmt->fetchColumn();
        $this->assertEquals("NouvelleVille", $nom);
    }

    public function testDeleteAgence(): void {
        $this->agenceService->createAgence("VilleASupprimer");
        $stmt = $this->pdo->query("SELECT id FROM agences LIMIT 1");
        $id = $stmt->fetchColumn();

        $result = $this->agenceService->deleteAgence($id);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM agences");
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);
    }

    public function testCreateDuplicateAgence(): void {
        // Vérifie qu’on ne peut pas créer deux agences avec le même nom
        $this->agenceService->createAgence("VilleUnique");

        $this->expectException(PDOException::class);
        $this->agenceService->createAgence("VilleUnique");
    }
}