<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/services/AgenceService.php';

class AgenceServiceTest extends TestCase {

    private $pdo;
    private $agenceService;

    protected function setUp(): void {
        // Connexion à la base de test
        $this->pdo = new PDO("mysql:host=localhost;dbname=appklaxon_test", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->agenceService = new AgenceService($this->pdo);
        
        // Nettoyage radical avant chaque test pour éviter les doublons
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->pdo->exec("DELETE FROM agences");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function testCreateAgence(): void {
        $nom = "Agence Paris";
        
        $result = $this->agenceService->createAgence($nom);
        $this->assertTrue($result);

        // Vérification en base
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM agences");
        $this->assertEquals(1, $stmt->fetchColumn());
    }

    public function testUpdateAgence(): void {
        // 1. Création
        $this->agenceService->createAgence("Ancien Nom");
        $id = $this->pdo->lastInsertId();

        // 2. Modification
        $result = $this->agenceService->updateAgence($id, "Nouveau Nom");
        $this->assertTrue($result);

        // 3. Vérification
        $stmt = $this->pdo->prepare("SELECT nom FROM agences WHERE id = ?");
        $stmt->execute([$id]);
        $this->assertEquals("Nouveau Nom", $stmt->fetchColumn());
    }

    public function testDeleteAgence(): void {
        // 1. Création
        $this->agenceService->createAgence("Agence a supprimer");
        $id = $this->pdo->lastInsertId();

        // 2. Suppression
        $result = $this->agenceService->deleteAgence($id);
        $this->assertTrue($result);

        // 3. Vérification
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM agences");
        $this->assertEquals(0, $stmt->fetchColumn());
    }

    public function testCreateAgenceEmptyNameThrowsException(): void {
        // On vérifie que ton Service lève bien une exception si le nom est vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Le nom de l'agence est requis.");

        $this->agenceService->createAgence("");
    }
}