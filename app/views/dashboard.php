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