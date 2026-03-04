<?php include 'header.php'; ?>

<main class="container mt-4 admin-dashboard">
    <h1>Tableau de bord Admin</h1>

    <!-- ------------------- UTILISATEURS ------------------- -->
    <h2>Utilisateurs</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= $user['id']; ?></td>
                <td><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td><?= $user['role']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ------------------- TRAJETS ------------------- -->
    <h2>Trajets</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Départ</th><th>Arrivée</th><th>Places dispo</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($trajets as $trajet): ?>
            <tr>
                <td><?= $trajet['id']; ?></td>
                <td><?= htmlspecialchars($trajet['agence_depart']); ?></td>
                <td><?= htmlspecialchars($trajet['agence_arrivee']); ?></td>
                <td><?= $trajet['nb_places_disponibles']; ?></td>
                <td>
                    <form method="get" style="display:inline;">
                        <input type="hidden" name="delete_trajet" value="<?= $trajet['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ------------------- AGENCES ------------------- -->
    <h2>Agences</h2> 

<!-- Formulaire création agence -->
<form method="POST" class="mb-4">
    <div class="mb-3">
        <label for="nom" class="form-label">Nom de l'agence</label>
        <input type="text" name="nom" id="nom" class="form-control" required>
    </div>
    <button type="submit" name="create_agence" class="btn btn-primary">Créer</button>
</form>

<!-- Liste des agences -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agences as $agence): ?>
            <tr>
                <td><?= $agence['id']; ?></td>
                <td><?= htmlspecialchars($agence['nom']); ?></td>
                <td>
                    <!-- Bouton Modifier : ouvre le formulaire inline -->
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#editAgence<?= $agence['id']; ?>">Modifier</button>
                    
                    <!-- Formulaire collapsible -->
                    <div class="collapse mt-2" id="editAgence<?= $agence['id']; ?>">
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="id_modif" value="<?= $agence['id']; ?>">
                            <input type="text" name="nom_modif" value="<?= htmlspecialchars($agence['nom']); ?>" class="form-control form-control-sm" required>
                            <button type="submit" name="update_agence" class="btn btn-success btn-sm">Valider</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#editAgence<?= $agence['id']; ?>">Annuler</button>
                        </form>
                    </div>

                    <!-- Bouton Supprimer -->
                    <form method="POST" style="display:inline-block;">
                        <button type="submit" name="delete_agence" value="<?= $agence['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cette agence ?');">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</main>

<?php include 'footer.php'; ?>