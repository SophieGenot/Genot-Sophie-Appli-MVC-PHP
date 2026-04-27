<?php
/** @var array $trajet */
?>

<div class="modal fade" id="trajetModal<?= htmlspecialchars($trajet['id']); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Infos trajet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Proposé par:</strong> <?= htmlspecialchars(($trajet['user_prenom'] ?? '') . ' ' . ($trajet['user_nom'] ?? '')); ?></p>
                
                <p><strong>Téléphone:</strong> <?= htmlspecialchars($trajet['user_tel'] ?? 'Non renseigné'); ?></p>
                
                <p><strong>Email:</strong> <?= htmlspecialchars($trajet['user_email'] ?? 'Non renseigné'); ?></p>
                
                <hr>
                
                <p>Total places: <?= htmlspecialchars($trajet['nb_places_total']); ?></p>
                
                <p>Places disponibles: <?= htmlspecialchars($trajet['places_dispo'] ?? $trajet['nb_places_disponibles']); ?></p>
            </div>
        </div>
    </div>
</div>