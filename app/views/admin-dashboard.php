<?php include 'header.php'; ?>
<?php
/** @var array $users */
/** @var array $trajets */
/** @var array $agences */
?>
<main class="container mt-4 admin-dashboard">
    <h1>Tableau de bord Admin</h1>

    <?php if (!empty($usersToValidate)): ?>
        <section class="mb-5 p-3 border border-warning rounded bg-light">
            <h2 class="text-warning">
                <i class="bi bi-person-plus-fill"></i> 
                Demandes d'inscription en attente 
                <span class="badge bg-warning text-dark"><?= count($usersToValidate); ?></span>
            </h2>
            <div class="table-responsive">
                <table class="table table-hover align-middle mt-3">
                    <thead class="table-warning">
                        <tr>
                            <th>Nom & Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usersToValidate as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']); ?></td>
                            <td><?= htmlspecialchars($u['email']); ?></td>
                            <td><?= htmlspecialchars($u['telephone'] ?? 'Non renseigné'); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="index.php?action=validate_user&id=<?= $u['id']; ?>" class="btn btn-success btn-sm">Valider</a>
                                    <form method="POST" action="index.php?action=delete_user" class="m-0">
                                        <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Refuser cette demande ?');">Refuser</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

    <h2 class="mt-5">Utilisateurs actifs</h2>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark"> <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <?php if($user['role'] === 'admin' || (isset($user['is_validated']) && $user['is_validated'] == 1)): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td>
                            <strong><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($user['email']); ?></small>
                        </td>
                        <td><?= htmlspecialchars($user['telephone'] ?? 'N/A'); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editUser<?= $user['id']; ?>">
                                    Modifier
                                </button>

                                <form method="POST" action="index.php?action=delete_user" class="m-0" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Supprimer
                                    </button>
                                </form>
                            </div>

                            <div class="collapse mt-2" id="editUser<?= $user['id']; ?>">
                                <div class="card card-body bg-light shadow-sm">
                                    <form method="POST" action="index.php?action=update_user_admin">
                                        <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="text" name="nom" class="form-control form-control-sm" value="<?= htmlspecialchars($user['nom']); ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="prenom" class="form-control form-control-sm" value="<?= htmlspecialchars($user['prenom']); ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="telephone" class="form-control form-control-sm" value="<?= htmlspecialchars($user['telephone'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn btn-sm btn-success w-100">OK</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <hr class="my-5">

    <h2>Gestion des Trajets</h2>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Nom Prénom</th> <th>Téléphone</th><th>Départ</th><th>Arrivée</th><th>Places dispo</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($trajets as $trajet): ?>
                <tr>
                    <td><?= htmlspecialchars($trajet['user_nom'] . ' ' . $trajet['user_prenom']); ?></td>
                    <td><?= htmlspecialchars($user['telephone'] ?? 'N/A'); ?></td>
                    <td><?= htmlspecialchars($trajet['agence_depart']); ?></td>
                    <td><?= htmlspecialchars($trajet['agence_arrivee']); ?></td>
                    <td><?= $trajet['nb_places_disponibles']; ?></td>
                    <td>
                        <form method="get" class="m-0">
                            <input type="hidden" name="action" value="delete_trajet">
                            <input type="hidden" name="id" value="<?= $trajet['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce trajet ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <hr class="my-5">

    <h2>Gestion des Agences</h2> 
    <form method="POST" class="mb-4 bg-light p-3 border rounded shadow-sm">
        <div class="row align-items-end g-3">
            <div class="col-md-4">
                <label for="nom" class="form-label">Nouvelle agence</label>
                <input type="text" name="nom_agence" id="nom" class="form-control" placeholder="Nom de l'agence" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="create_agence" class="btn btn-primary w-100">Ajouter</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th><th>Nom</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agences as $agence): ?>
                    <tr>
                        <td><?= $agence['id']; ?></td>
                        <td><?= htmlspecialchars($agence['nom']); ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#editAgence<?= $agence['id']; ?>">Modifier</button>
                                <form method="POST" class="m-0">
                                    <button type="submit" name="delete_agence" value="<?= $agence['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette agence ?');">Supprimer</button>
                                </form>
                            </div>
                            <div class="collapse mt-2" id="editAgence<?= $agence['id']; ?>">
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="id_modif" value="<?= $agence['id']; ?>">
                                    <input type="text" name="nom_modif" value="<?= htmlspecialchars($agence['nom']); ?>" class="form-control form-control-sm" required>
                                    <button type="submit" name="update_agence" class="btn btn-success btn-sm">OK</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'footer.php'; ?>