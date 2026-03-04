<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pas touche au klaxon</title>
    <link rel="stylesheet" href="assets/css/custom.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/icon/klaxon.png">
</head>
<body>
<header>
    <div class="app-name">
        <a href="index.php?action=home" class="text-white text-decoration-none">Pas touche au klaxon</a>
    </div>
    <div class="user-info">
        <?php if(!isset($_SESSION['user'])): ?>
            <a href="index.php?action=home#login" class="btn btn-light">Connexion</a>
        <?php elseif($_SESSION['user']['role'] === 'admin'): ?>
            <nav class="d-flex gap-2">
                <a href="index.php?action=logout" class="btn btn-danger">Déconnexion</a>
            </nav>
        <?php else: ?>
            <a href="index.php?action=create_trajet" class="btn btn-light">Créer un trajet</a>
            <span><?= htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']); ?></span>
            <a href="index.php?action=logout" class="btn btn-danger">Déconnexion</a>
        <?php endif; ?>
    </div>
</header>