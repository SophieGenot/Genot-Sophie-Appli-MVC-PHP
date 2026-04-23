<?php include 'header.php'; ?>
<?php
/** @var array $trajets */
/** @var array $usersToValidate */
?>
<main class="container mt-4">

<?php if (isset($_SESSION['message_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['message_success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['message_success']); ?>
<?php endif; ?>
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
<div class="row mt-4">
    <div class="col-md-6" id="login-section">
        <div class="login-form">
            <form action="index.php?action=login" method="post">
                <fieldset class="border p-3 rounded">
                    <legend class="w-auto px-2">Connexion</legend>
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <button type="submit" class="btn btn-primary mt-2 w-100">Se connecter</button>
                    <hr>
                    <p class="text-center">Nouveau collaborateur ? <br>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="btn-show-register">Créer un compte</button>
                    </p>
                </fieldset>
            </form>
        </div>
    </div>

    <div class="col-md-6" id="register-section" style="display: none;">
        <div class="register-form">
            <form action="index.php?action=register" method="post">
                <fieldset class="border p-3 rounded shadow-sm" style="background-color: #f8f9fa;">
                    <legend class="w-auto px-2">Inscription</legend>
                    <div class="row">
                        <div class="col-6">
                            <label>Nom :</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label>Prénom :</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                    </div>
                    <label class="mt-2">Adresse mail :</label>
                    <input type="email" name="email" class="form-control" required>
                    <label class="mt-2">Téléphone :</label>
                    <input type="tel" name="telephone" class="form-control" required>
                    <label class="mt-2">Mot de passe :</label>
                    <input type="password" name="mot_de_passe" class="form-control" required>
                    
                    <button type="submit" class="btn btn-success mt-3 w-100">Envoyer ma demande</button>
                    <p class="mt-2 text-muted" style="font-size: 0.8rem;">
                        <i>Note : Votre compte sera activé après validation par l'administration.</i>
                    </p>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
   
</main>

<?php include 'footer.php'; ?>