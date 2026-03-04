<?php
$mdp = 'motdepasse123'; // mot de passe que tu veux utiliser
$hash = password_hash($mdp, PASSWORD_DEFAULT);
echo "Mot de passe : $mdp\n$2y$10$SNyPI6khFMned/sk1RUIc.A/i9crRFK1DxLuJn3O48uTQooyCC1o2 : $hash\n";
?>