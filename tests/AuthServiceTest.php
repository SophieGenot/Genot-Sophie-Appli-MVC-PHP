<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/services/AuthService.php';
require_once __DIR__ . '/../app/services/UserService.php';

class AuthServiceTest extends TestCase {
    private $pdo;
    private $authService;
    private $userService;

   protected function setUp(): void {
    $this->pdo = new PDO("mysql:host=localhost;dbname=appklaxon_test", "root", "");
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->authService = new AuthService($this->pdo);
    $this->userService = new UserService($this->pdo);
    
    // Désactiver les clés étrangères pour vider proprement
    $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $this->pdo->exec("DELETE FROM trajets"); // On vide les trajets d'abord
    $this->pdo->exec("DELETE FROM utilisateurs");
    $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
}

    public function testLoginSuccess(): void {
        // 1. On crée un utilisateur et on le VALIDE manuellement en base
        $this->userService->createUser([
            'nom' => 'Nom', 'prenom' => 'Prenom', 'email' => 'ok@test.fr',
            'telephone' => '0600000000', 'mot_de_passe' => 'password123'
        ]);
        $id = $this->pdo->lastInsertId();
        $this->pdo->exec("UPDATE utilisateurs SET is_validated = 1 WHERE id = $id");

        // 2. Test de connexion réussie
        $user = $this->authService->login('ok@test.fr', 'password123');

        $this->assertNotFalse($user);
        $this->assertEquals('ok@test.fr', $user['email']);
    }

    public function testLoginWrongPassword(): void {
        $this->userService->createUser([
            'nom' => 'Nom', 'prenom' => 'Prenom', 'email' => 'wrong@test.fr',
            'telephone' => '0600000000', 'mot_de_passe' => 'secret'
        ]);

        // Tentative avec mauvais mot de passe -> Doit retourner false
        $result = $this->authService->login('wrong@test.fr', 'mauvais_pass');
        $this->assertFalse($result);
    }

    public function testLoginAccountNotValidatedThrowsException(): void {
        // On crée un utilisateur mais on ne le valide PAS (is_validated restera à 0)
        $this->userService->createUser([
            'nom' => 'Awaiting', 'prenom' => 'User', 'email' => 'pending@test.fr',
            'telephone' => '0600000000', 'mot_de_passe' => 'password123'
        ]);

        // On s'attend à ce que le service lève une Exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Votre compte n'a pas encore été validé par un administrateur.");

        $this->authService->login('pending@test.fr', 'password123');
    }
}