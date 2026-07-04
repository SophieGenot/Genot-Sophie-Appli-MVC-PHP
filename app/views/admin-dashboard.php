<?php
/** @var array|null $usersToValidate */
/** @var array $agences         */
/** @var array $trajets          */
/** @var array $users       */
?>

<?php include 'header.php'; ?>

<main class="container mt-4 admin-dashboard">
    <h1>Tableau de bord Admin</h1>

    <?php if (count($usersToValidate) > 0): ?>
        <section class="mb-5 p-3 border border-warning rounded bg-light">
            <h2 class="text-warning">
                <i class="bi bi-person-plus-fill"></i> 
                Demandes en attente <span class="badge bg-warning text-dark"><?= count($usersToValidate); ?></span>
            </h2>
            <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Nom / Prénom</th>
                <th>Email</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($usersToValidate):?>
                <?php foreach ($usersToValidate as $u): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($u['nom']); ?></strong> 
                            <?= htmlspecialchars($u['prenom']); ?>
                        </td>
                        <td><?= htmlspecialchars($u['email']); ?></td>
                      
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="index.php?action=validate_user&id=<?= $u['id']; ?>" 
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('Valider ce compte ?');">
                                   Valider
                                </a>
                                
                                <form action="index.php?action=delete_user" method="POST" class="m-0">
                                    <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Refuser et supprimer cette demande ?');">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        Aucune demande en attente de validation.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</section>
    <?php endif; ?>

    <section class="mb-5">
        <h2>Utilisateurs actifs</h2>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Utilisateur</th><th>Téléphone</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <?php if($user['role'] === 'admin' || (isset($user['is_validated']) && $user['is_validated'] == 1)): ?>
                            <?php include __DIR__ . '/_admin-user-row.php'; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <hr class="my-5">

    <section class="mb-5">
        <h2 class="mb-4">Gestion des Trajets</h2>
        <div class="trajet-list">
            <?php foreach($trajets as $trajet): ?>
                <?php 
                    // On adapte les clés pour que la carte affiche le nom du conducteur
                    $trajet['auteur_nom'] = $trajet['user_nom'];
                    $trajet['auteur_prenom'] = $trajet['user_prenom'];
                    $mode = 'admin'; 
                    include __DIR__ . '/_trajet-card.php'; 
                ?>
            <?php endforeach; ?>
        </div>
    </section>

    <hr class="my-5">

    <section>
        <h2>Gestion des Agences</h2>
        <?php include __DIR__ . '/_admin-agences-manager.php'; ?>
    </section>
</main>

<?php include 'footer.php'; ?>