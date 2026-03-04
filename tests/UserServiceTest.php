<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../config/db_test.php';
require_once __DIR__ . '/../app/services/UserService.php';

class UserServiceTest extends TestCase {

    private $pdo;
    private $userService;

    protected function setUp(): void {
        $this->pdo = new PDO("mysql:host=localhost;dbname=appklaxon_test", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->userService = new UserService($this->pdo);

        // Nettoyer la table pour un test propre
        $this->pdo->exec("DELETE FROM utilisateurs");
    }

    public function testCreateUser(): void {
        $data = [
            'nom' => 'Test',
            'prenom' => 'Utilisateur',
            'email' => 'test.user@email.fr',
            'mot_de_passe' => 'password123',
            'role' => 'employe'
        ];

        $result = $this->userService->createUser($data);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM utilisateurs");
        $count = $stmt->fetchColumn();
        $this->assertEquals(1, $count);
    }

    public function testGetUserByEmail(): void {
        $data = [
            'nom' => 'Test',
            'prenom' => 'Email',
            'email' => 'email.user@email.fr',
            'mot_de_passe' => 'password123',
            'role' => 'employe'
        ];
        $this->userService->createUser($data);

        $user = $this->userService->getUserByEmail('email.user@email.fr');
        $this->assertNotNull($user);
        $this->assertEquals('Test', $user['nom']);
    }

    public function testUpdateUser(): void {
        $data = [
            'nom' => 'Old',
            'prenom' => 'Name',
            'email' => 'update.user@email.fr',
            'mot_de_passe' => 'oldpassword',
            'role' => 'employe'
        ];
        $this->userService->createUser($data);

        $stmt = $this->pdo->query("SELECT id FROM utilisateurs LIMIT 1");
        $id = $stmt->fetchColumn();

        $updateData = ['nom' => 'New', 'mot_de_passe' => 'newpassword'];
        $result = $this->userService->updateUser($id, $updateData);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT nom FROM utilisateurs WHERE id=$id");
        $nom = $stmt->fetchColumn();
        $this->assertEquals('New', $nom);
    }

    public function testDeleteUser(): void {
        $data = [
            'nom' => 'Delete',
            'prenom' => 'Me',
            'email' => 'delete.user@email.fr',
            'mot_de_passe' => 'password',
            'role' => 'employe'
        ];
        $this->userService->createUser($data);

        $stmt = $this->pdo->query("SELECT id FROM utilisateurs LIMIT 1");
        $id = $stmt->fetchColumn();

        $result = $this->userService->deleteUser($id);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM utilisateurs");
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);
    }
}