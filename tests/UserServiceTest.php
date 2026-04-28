<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/services/UserService.php';

class UserServiceTest extends TestCase {

    private $pdo;
    private $userService;

  protected function setUp(): void {
    $this->pdo = new PDO("mysql:host=localhost;dbname=appklaxon_test", "root", "");
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->userService = new UserService($this->pdo);
    
    // Désactiver les clés étrangères pour vider proprement les tables
    $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $this->pdo->exec("DELETE FROM trajets");
    $this->pdo->exec("DELETE FROM utilisateurs");
    $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
}
        
      
    public function testCreateUser(): void {
        $data = [
            'nom' => 'Test',
            'prenom' => 'Utilisateur',
            'email' => 'test.user@email.fr',
            'telephone' => '0601020304', // Obligatoire selon ton service
            'mot_de_passe' => 'password123'
        ];

        $result = $this->userService->createUser($data);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM utilisateurs");
        $this->assertEquals(1, $stmt->fetchColumn());
    }

    public function testGetUserByEmail(): void {
        $data = [
            'nom' => 'Test', 'prenom' => 'Email', 'email' => 'find@email.fr',
            'telephone' => '0102030405', 'mot_de_passe' => 'pass'
        ];
        $this->userService->createUser($data);

        $user = $this->userService->getUserByEmail('find@email.fr');
        $this->assertNotNull($user);
        $this->assertEquals('Test', $user['nom']);
    }

    public function testUpdateUser(): void {
        // 1. Création initiale
        $data = [
            'nom' => 'Old', 'prenom' => 'Name', 'email' => 'update@test.fr',
            'telephone' => '0600000000', 'mot_de_passe' => 'pass'
        ];
        $this->userService->createUser($data);
        $id = $this->pdo->lastInsertId();

        // 2. Mise à jour (On respecte les clés attendues par ton service)
        $updateData = [
            'nom' => 'New',
            'prenom' => 'Name',
            'telephone' => '0700000000'
        ];
        
        $result = $this->userService->updateUser($id, $updateData);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT nom FROM utilisateurs WHERE id=$id");
        $this->assertEquals('New', $stmt->fetchColumn());
    }

    public function testDeleteUser(): void {
        // On crée deux utilisateurs : un à supprimer et un qui simule l'admin actuel
        $this->userService->createUser([
            'nom' => 'A', 'prenom' => 'B', 'email' => 'a@test.fr',
            'telephone' => '0600000001', 'mot_de_passe' => 'p'
        ]);
        $idToDelete = $this->pdo->lastInsertId();

        $this->userService->createUser([
            'nom' => 'Admin', 'prenom' => 'C', 'email' => 'admin@test.fr',
            'telephone' => '0600000002', 'mot_de_passe' => 'p'
        ]);
        $currentAdminId = $this->pdo->lastInsertId();

        // On passe les deux arguments demandés par ton service
        $result = $this->userService->deleteUser($idToDelete, $currentAdminId);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE id=$idToDelete");
        $this->assertEquals(0, $stmt->fetchColumn());
    }
}