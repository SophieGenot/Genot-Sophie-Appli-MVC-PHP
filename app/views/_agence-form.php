<?php
// Si $agence existe, on est en modification, sinon création
$editMode = isset($agence);
?>
<form method="POST" class="mb-3">
    <div class="mb-3">
        <label for="nom" class="form-label">Nom de l'agence</label>
        <input type="text" name="nom" id="nom" class="form-control"
               value="<?= htmlspecialchars($agence['nom'] ?? ''); ?>" required>
    </div>

    <button type="submit" name="<?= $editMode ? 'update_agence' : 'create_agence'; ?>"
            class="btn btn-primary">
        <?= $editMode ? 'Mettre à jour' : 'Créer'; ?>
    </button>
</form>