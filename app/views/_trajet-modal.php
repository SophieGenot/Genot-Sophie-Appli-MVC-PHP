<div class="modal fade" id="trajetModal<?= $trajet['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Infos trajet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Proposé par: <?= htmlspecialchars(($trajet['nom'] ?? '') . ' ' . ($trajet['prenom'] ?? '')); ?></p>
                
                <p>Téléphone: <?= htmlspecialchars($trajet['telephone'] ?? 'Non renseigné'); ?></p>
                
                <p>Email: <?= htmlspecialchars($trajet['email'] ?? 'Non renseigné'); ?></p>
                
                <p>Total places: <?= $trajet['nb_places_total']; ?></p>
                
                <p>Places disponibles: <?= $trajet['places_dispo'] ?? $trajet['nb_places_disponibles']; ?></p>
            </div>
        </div>
    </div>
</div>