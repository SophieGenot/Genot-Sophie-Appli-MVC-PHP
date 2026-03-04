<?php include 'header.php'; ?>

<main class="container mt-4">
    <h1>Trajets disponibles</h1>

    <!-- Liste des trajets -->
    <div class="trajet-list">
        <?php foreach($trajets as $trajet): ?>
            <?php if($trajet['places_dispo'] > 0): ?>
            <div class="trajet-card">
                <div class="trajet-header"><?= htmlspecialchars($trajet['agence_depart']); ?> → <?= htmlspecialchars($trajet['agence_arrivee']); ?></div>
                <div class="trajet-info">
                    <span>Départ: <?= htmlspecialchars($trajet['gdh_depart']); ?></span>
                    <span>Arrivée: <?= htmlspecialchars($trajet['gdh_arrivee']); ?></span>
                    <span>Places dispo: <?= $trajet['places_dispo']; ?></span>
                </div>
                <?php if(isset($_SESSION['user'])): ?>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#trajetModal<?= $trajet['id']; ?>">Infos</button>
                    <?php if($trajet['auteur_id'] === $_SESSION['user']['id']): ?>
                        <a href="/trajet-form.php?id=<?= $trajet['id']; ?>" class="btn btn-primary">Modifier</a>
                        <a href="/delete-trajet.php?id=<?= $trajet['id']; ?>" class="btn btn-danger">Supprimer</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Modal infos trajet -->
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

            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <?php if(!isset($_SESSION['user'])): ?>
    <div id="login" class="login-form mt-4">
        <form action="index.php?action=login" method="post">
            <fieldset>
                <legend>Connexion</legend>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" class="form-control" required>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <button type="submit" class="btn btn-primary mt-2">Se connecter</button>
            </fieldset>
        </form>
    </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>