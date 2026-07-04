<?php
/** @var array $user */ 
?>
<tr>
    <td><?= htmlspecialchars($user['id']); ?></td>
    <td>
        <strong><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></strong><br>
        <small class="text-muted"><?= htmlspecialchars($user['email']); ?></small>
    </td>
    <td><?= htmlspecialchars($user['telephone'] ?? 'N/A'); ?></td>
    <td>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editUser<?= htmlspecialchars($user['id']); ?>">
                Modifier
            </button>

            <form method="POST" action="index.php?action=delete_user" class="m-0" 
            onsubmit="return confirm('Supprimer cet utilisateur ?');">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    Supprimer
                </button>
            </form>
        </div>

        <div class="collapse mt-2" id="editUser<?= htmlspecialchars($user['id']); ?>">
            <div class="card card-body bg-light shadow-sm">
                <form method="POST" action="index.php?action=update_user_admin">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
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