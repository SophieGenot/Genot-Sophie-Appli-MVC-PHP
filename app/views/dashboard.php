<?php include 'header.php'; ?>

<main class="container mt-4">
    <h1>Mon tableau de bord</h1>
    <section>
        <h2>Mes trajets</h2>
        <?php if (!empty($mes_trajets)): ?>
            <div class="trajet-list">
                <?php foreach($mes_trajets as $trajet): ?>
                    <?php $mode = 'mes_trajets'; include __DIR__ . '/_trajet-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Vous n'avez aucun trajet en cours.</p>
        <?php endif; ?>
    </section>

    <section class="mt-5">
        <h2 class="mb-4">Mes réservations (en tant que passager)</h2>
        <?php if (!empty($mes_reservations)): ?>
            <div class="trajet-list">
                <?php foreach ($mes_reservations as $trajet): ?>
                    <?php $mode = 'reservation'; include __DIR__ . '/_trajet-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-light border text-muted">
                Vous n'avez aucune réservation en cours.
            </div>
        <?php endif; ?>
    </section>
<section>
     <form method="GET" action="index.php" class="row g-3 mb-4 align-items-end bg-light p-3 border rounded shadow-sm">
    <input type="hidden" name="action" value="dashboard_employe">

    <div class="col-md-8">
        <label for="search_agence" class="form-label fw-bold">Filtrer par agence de départ :</label>
        
        <select name="search_agence_id" id="search_agence" class="form-select">
            <option value="">--- Toutes les agences ---</option>
            <?php foreach($agences as $agence): ?>
                <option value="<?= htmlspecialchars($agence['id']); ?>" <?= (isset($_GET['search_agence_id']) && $_GET['search_agence_id'] == $agence['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($agence['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100">Rechercher</button>
        <?php if (!empty($_GET['search_agence_id'])): ?>
            <a href="index.php?action=dashboard" class="btn btn-outline-secondary">Réinitialiser</a>
        <?php endif; ?>
    </div>
</form>
    </section>
    <section>
        <h2 class="mt-5">Autres trajets disponibles</h2>
        <?php if (!empty($autres_trajets)): ?>
            <div class="trajet-list">
                <?php foreach($autres_trajets as $trajet): ?>
                    <?php $mode = 'autres'; include __DIR__ . '/_trajet-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun autre trajet disponible pour le moment.</p>
        <?php endif; ?>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>