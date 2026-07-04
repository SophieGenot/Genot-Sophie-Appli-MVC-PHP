<?php
/** @var array $trajet */ 
/** @var string $mode */
?>
<?php
// On récupère l'ID selon la source de données
$id_affichage = $trajet['id'] ?? $trajet['trajet_id'] ?? null;
?>

<div class="trajet-card">
    <div class="trajet-header">
        <?= htmlspecialchars($trajet['agence_depart'] ?? 'N/C'); ?> → <?= htmlspecialchars($trajet['agence_arrivee'] ?? 'N/C'); ?>
    </div>
    
    <div class="trajet-info">
        <?php if (!empty($trajet['auteur_prenom'])): ?>
            <span>Conducteur: <strong><?= htmlspecialchars($trajet['auteur_prenom'] . ' ' . $trajet['auteur_nom']); ?></strong></span>
        <?php endif; ?>

        <span>Départ: <?= htmlspecialchars(AbstractService::formatDateFr($trajet['gdh_depart'] ?? '')); ?></span>
        
        <?php if (!empty($trajet['gdh_arrivee'])): ?>
            <span>Arrivée: <?= htmlspecialchars(AbstractService::formatDateFr($trajet['gdh_arrivee'])); ?></span>
        <?php endif; ?>

        <?php if (isset($trajet['places_dispo'])): ?>
            <span>Places dispo: <?= htmlspecialchars($trajet['places_dispo']); ?></span>
        <?php endif; ?>
    </div>

    <div class="mt-3 d-flex gap-2">
        
        <?php if ($mode === 'mes_trajets'): ?>
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#trajetModal<?= 
                htmlspecialchars($id_affichage); ?>">Infos</button>
            <a href="index.php?action=edit_trajet&id=<?= htmlspecialchars($id_affichage); ?>" 
            class="btn btn-primary">Modifier</a>
            
            <form action="index.php?action=delete_trajet" method="POST" class="m-0" onsubmit="
            return confirm('Supprimer votre trajet ?');">
                <input type="hidden" name="trajet_id" value="<?= htmlspecialchars($id_affichage); ?>">
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>

        <?php elseif ($mode === 'reservation'): ?>
            <form action="index.php?action=annuler_reservation" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler votre place ?');" class="w-100">
                <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($trajet['reservation_id']); ?>">
                <button type="submit" class="btn btn-danger w-100">Annuler la réservation</button>
            </form>

        <?php elseif ($mode === 'admin'): ?>
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#trajetModal<?= 
                htmlspecialchars($id_affichage); ?>">Infos</button>
            
            <form action="index.php?action=delete_trajet" method="POST" class="m-0" onsubmit="
            return confirm('Supprimer ce trajet ?');">
                <input type="hidden" name="trajet_id" value="<?= htmlspecialchars($id_affichage); ?>">
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    Supprimer
                </button>
            </form>

        <?php elseif ($mode === 'autres'): ?>
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#trajetModal<?= htmlspecialchars($id_affichage); ?>">Infos</button>
            <?php if (($trajet['places_dispo'] ?? 0) > 0): ?>
                <a href="index.php?action=reserver&id=<?= htmlspecialchars($id_affichage); ?>" class="btn btn-success" onclick="return confirm('Réserver une place ?')">Réserver</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>Complet</button>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<?php 
if ($mode !== 'reservation') {
    require __DIR__ . '/_trajet-modal.php'; 
}
?>