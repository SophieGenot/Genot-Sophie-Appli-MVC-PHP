<?php include 'header.php'; ?> 

<main class="container mt-4">
    <h1><?= isset($trajet) ? 'Modifier le trajet' : 'Créer un nouveau trajet'; ?></h1>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="index.php?action=<?= isset($trajet) ? 'edit_trajet&id=' . $trajet['id'] : 'create_trajet'; ?>" method="POST">
        <!-- Infos utilisateur préremplies -->
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['nom']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['prenom']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['email']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['telephone'] ?? ''); ?>" readonly>
        </div>

        <!-- Sélection des agences -->
        <div class="mb-3">
            <label class="form-label">Agence de départ</label>
            <select name="agence_depart_id" class="form-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach($agences as $agence): ?>
                    <option value="<?= $agence['id']; ?>" <?= isset($trajet) && $trajet['agence_depart_id'] == $agence['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($agence['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Agence d'arrivée</label>
            <select name="agence_arrivee_id" class="form-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach($agences as $agence): ?>
                    <option value="<?= $agence['id']; ?>" <?= isset($trajet) && $trajet['agence_arrivee_id'] == $agence['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($agence['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Dates et places -->
        <div class="mb-3">
            <label class="form-label">Date et heure de départ</label>
            <input type="datetime-local" name="gdh_depart" class="form-control" required
                   value="<?= isset($trajet) ? date('Y-m-d\TH:i', strtotime($trajet['gdh_depart'])) : ''; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Date et heure d'arrivée</label>
            <input type="datetime-local" name="gdh_arrivee" class="form-control" required
                   value="<?= isset($trajet) ? date('Y-m-d\TH:i', strtotime($trajet['gdh_arrivee'])) : ''; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre de places</label>
            <input type="number" name="nb_places_total" class="form-control" min="1" max="4" required
                   value="<?= isset($trajet) ? $trajet['nb_places_total'] : ''; ?>">
        </div>

        <button type="submit" class="btn btn-success"><?= isset($trajet) ? 'Mettre à jour le trajet' : 'Créer le trajet'; ?></button>
        <a href="index.php?action=dashboard_employe" class="btn btn-secondary">Annuler</a>
    </form>
</main>

<?php include 'footer.php'; ?>