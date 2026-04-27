<?php
/** @var array $agences */
?>

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
                    <td><?= htmlspecialchars($agence['id']); ?></td>
                    <td><?= htmlspecialchars($agence['nom']); ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#editAgence<?= htmlspecialchars($agence['id']); ?>">Modifier</button>
                            
                            <form method="POST" class="m-0">
                                <button type="submit" name="delete_agence" value="<?= htmlspecialchars($agence['id']); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette agence ?');">Supprimer</button>
                            </form>
                        </div>
                        
                        <div class="collapse mt-2" id="editAgence<?= htmlspecialchars($agence['id']); ?>">
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="id_modif" value="<?= htmlspecialchars($agence['id']); ?>">
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