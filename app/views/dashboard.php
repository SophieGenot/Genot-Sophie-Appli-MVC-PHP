<?php include 'header.php'; ?>

<main class="container mt-4">
    <h1>Mon tableau de bord</h1>

    <!-- ------------------------ Mes trajets ------------------------ -->
    <h2>Mes trajets</h2>
<?php if (!empty($mes_trajets)): ?>
    <div class="trajet-list">
        <?php foreach($mes_trajets as $trajet): ?>
            <div class="trajet-card">
                <div class="trajet-header"><?= htmlspecialchars($trajet['agence_depart']); ?> → <?= htmlspecialchars($trajet['agence_arrivee']); ?></div>
                <div class="trajet-info">
                    <span>Départ: <?= htmlspecialchars($trajet['gdh_depart']); ?></span>
                    <span>Arrivée: <?= htmlspecialchars($trajet['gdh_arrivee']); ?></span>
                    <span>Places dispo: <?= $trajet['places_dispo']; ?></span>
                </div>
                 <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#trajetModal<?= $trajet['id']; ?>">Infos</button>
                <?php $trajetModal = $trajet; require __DIR__ . '/_trajet-modal.php'; ?>
                <a href="index.php?action=edit_trajet&id=<?= $trajet['id']; ?>" class="btn btn-primary">Modifier</a>
                <a href="index.php?action=delete_trajet&id=<?= $trajet['id']; ?>" class="btn btn-danger">Supprimer</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Vous n'avez aucun trajet en cours.</p>
<?php endif; ?>

    <!-- ------------------------ Autres trajets disponibles ------------------------ -->
    <h2>Autres trajets disponibles</h2>
    <?php if (!empty($autres_trajets)): ?>
        <div class="trajet-list">
            <?php foreach($autres_trajets as $trajet): ?>
                <div class="trajet-card">
                    <div class="trajet-header"><?= htmlspecialchars($trajet['agence_depart']); ?> → <?= htmlspecialchars($trajet['agence_arrivee']); ?></div>
                    <div class="trajet-info">
                        <span>Départ: <?= htmlspecialchars($trajet['gdh_depart']); ?></span>
                        <span>Arrivée: <?= htmlspecialchars($trajet['gdh_arrivee']); ?></span>
                        <span>Places dispo: <?= $trajet['places_dispo']; ?></span>
                    </div>
                     <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#trajetModal<?= $trajet['id']; ?>">Infos</button>
                     <?php $trajetModal = $trajet; require __DIR__ . '/_trajet-modal.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucun autre trajet disponible pour le moment.</p>
    <?php endif; ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>