 <div class="modal fade" id="trajetModal<?= $trajet['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Infos trajet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Proposé par: <?= htmlspecialchars($trajet['user_nom'] . ' ' . $trajet['user_prenom']); ?></p>
                            <p>Téléphone: <?= htmlspecialchars($trajet['user_tel']); ?></p>
                            <p>Email: <?= htmlspecialchars($trajet['user_email']); ?></p>
                            <p>Total places: <?= $trajet['nb_places_total']; ?></p>
                        </div>
                    </div>
                </div>
            </div>