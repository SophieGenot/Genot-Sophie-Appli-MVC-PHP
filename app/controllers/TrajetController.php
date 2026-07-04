<?php 
require_once __DIR__ . '/AbstractController.php'; 
require_once __DIR__ . '/../services/TrajetService.php';
require_once __DIR__ . '/../services/AgenceService.php';


class TrajetController extends AbstractController {

    private TrajetService $trajetService;
    private AgenceService $agenceService;

    public function __construct(PDO$pdo) {
        parent::__construct($pdo); 
        $this->trajetService = new TrajetService($this->pdo);
        $this->agenceService = new AgenceService($this->pdo);
    }

    // ------------------------ Page d'accueil ------------------------
    public function listHome() {
        $trajets = $this->trajetService->getAllTrajetsDisponiblesAvecInfos();
        $this->render('home', ['trajets' => $trajets]);
    }

    // ------------------------ Dashboard employé ------------------------
 public function listDashboardEmploye() {
    // 1. Vérification de l'authentification
    $this->checkAuth();
    $userId = $_SESSION['user']['id'];

    // Récupération du filtre de recherche (depuis le formulaire en méthode GET)
    $searchAgenceId = $_GET['search_agence_id'] ?? null;

    // 2. Récupération des données brutes depuis les services
    $trajets = $this->trajetService->getAllTrajetsAvecInfos();
    $agences = $this->agenceService->getAllAgences();
    $notifications = $this->trajetService->getNotificationsForUser($userId);
    $mes_reservations = $this->trajetService->getReservationsByPassenger($userId);

    $mes_trajets = [];
    $autres_trajets = [];

    // 3. Tri et filtrage des trajets
    foreach ($trajets as $trajet) {
        $id_auteur = $trajet['auteur_id'] ?? null;

        if ($id_auteur == $userId) {
            // C'est un trajet créé par l'utilisateur connecté
            $mes_trajets[] = $trajet;
        } elseif (($trajet['places_dispo'] ?? 0) > 0) {
            
            // Logique de filtrage dynamique par agence de départ
            if (empty($searchAgenceId) || $trajet['agence_depart_id'] == $searchAgenceId) {
                $autres_trajets[] = $trajet;
            }
        }
    }

    // 4. UN SEUL ET UNIQUE RENDU FINAL AVEC TOUTES LES DONNÉES
    // Note : On utilise 'dashboard_employe' puisque c'est le nom de l'action qui fonctionne dans ton routeur
    $this->render('dashboard', [
        'mes_trajets'      => $mes_trajets,
        'autres_trajets'   => $autres_trajets,
        'notifications'    => $notifications,
        'mes_reservations' => $mes_reservations,
        'agences'          => $agences // La variable $agences est maintenant bien initialisée et transmise !
    ]);
}
    // ------------------------ Création d'un trajet ------------------------
    public function createTrajet() {
    $this->checkAuth();
    $agences = $this->agenceService->getAllAgences();
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // On prépare le tableau de données à envoyer au service
            $formData = [
                'agence_depart_id' => $_POST['agence_depart_id'],
                'agence_arrivee_id' => $_POST['agence_arrivee_id'],
                'gdh_depart'        => $_POST['gdh_depart'],
                'gdh_arrivee'       => $_POST['gdh_arrivee'],
                'nb_places_total'   => (int)$_POST['nb_places_total'],
                'auteur_id'         => $_SESSION['user']['id']
            ];

            // On appelle le service. S'il y a une erreur, il "saute" directement au catch
            $this->trajetService->createTrajet($formData);
            
            $this->redirect('dashboard_employe');

        } catch (Exception $e) {
            // On récupère le message de l'exception lancée dans le service
            $error = $e->getMessage();
        }
    }
    
    // On affiche la vue avec les agences et l'éventuelle erreur
    require __DIR__ . '/../views/_trajet-form.php';
}

    public function editTrajet(int$trajetId) {
        $this->checkAuth();

        $userId = $_SESSION['user']['id'];
        $trajet = $this->trajetService->getTrajetById($trajetId);

        if ($trajet['auteur_id'] !== $userId && $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('dashboard_employe');
        }

        $agences = $this->agenceService->getAllAgences();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $departId = $_POST['agence_depart_id'];
            $arriveeId = $_POST['agence_arrivee_id'];
            $gdhDepart = $_POST['gdh_depart'];
            $gdhArrivee = $_POST['gdh_arrivee'];
            $nbPlaces = (int)$_POST['nb_places_total'];

            if ($departId == $arriveeId) {
                $error = "L'agence de départ et d'arrivée doivent être différentes.";
            } elseif (strtotime($gdhDepart) >= strtotime($gdhArrivee)) {
                $error = "La date/heure d'arrivée doit être après la date/heure de départ.";
            } elseif ($nbPlaces < 1 || $nbPlaces > 4) {
                $error = "Le nombre de places doit être compris entre 1 et 4.";
            } else {
                $this->trajetService->updateTrajet($trajetId, [
                    'agence_depart_id' => $departId,
                    'agence_arrivee_id' => $arriveeId,
                    'gdh_depart' => $gdhDepart,
                    'gdh_arrivee' => $gdhArrivee,
                    'nb_places_total' => $nbPlaces
                ]);

                if ($_SESSION['user']['role'] === 'admin') {
                    $this->redirect('dashboard_admin');
                } else {
                    $this->redirect('dashboard_employe');
                }
            }
        }

        $this->render('_trajet-form', [
            'trajet' => $trajet,
            'agences' => $agences,
            'error' => $error
        ]);
    }

    public function reserver() {
    if (!isset($_SESSION['user'])) {
        echo "La session est vide ! <br>";
        var_dump($_SESSION); 
        die("Arrêt du script : Pas de session utilisateur.");
    }

    $idTrajet = $_GET['id'] ?? null;

    if ($idTrajet) {
        try {
            $this->trajetService->reserverPlace((int)$idTrajet, $_SESSION['user']);
            
            header('Location: index.php?action=dashboard_employe');
            exit;

        } catch (Exception $e) {
            die("Erreur durant la réservation : " . $e->getMessage());
        }
    } else {
        die("Erreur : Aucun ID de trajet reçu.");
    }
}

public function deleteTrajet() {
    $this->checkAuth(); // Sécurité : vérifie que l'utilisateur est connecté
    
    $id = isset($_POST['trajet_id']) ? (int)$_POST['trajet_id'] : null;
    $userId = $_SESSION['user']['id'];
    $userRole = $_SESSION['user']['role'];

    if ($id) {
        try {
            // 1. Récupérer le trajet pour vérifier les droits
            $trajet = $this->trajetService->getTrajetById($id);

            if ($trajet) {
                // 2. Autoriser si c'est l'auteur OU si c'est un admin
                if ($trajet['auteur_id'] == $userId || $userRole === 'admin') {
                    if ($this->trajetService->deleteTrajet($id)) {
                        $_SESSION['message_success'] = "Le trajet a été supprimé avec succès.";
                    } else {
                        $_SESSION['message_error'] = "Erreur lors de la suppression en base de données.";
                    }
                } else {
                    $_SESSION['message_error'] = "Action non autorisée : vous n'êtes pas l'auteur de ce trajet.";
                }
            } else {
                $_SESSION['message_error'] = "Trajet introuvable.";
            }
        } catch (Exception $e) {
            $_SESSION['message_error'] = "Erreur : " . $e->getMessage();
        }
    }

    // 3. REDIRECTION STRICTE
    if ($userRole === 'admin') {
        $this->redirect('dashboard_admin');
    } else {
        $this->redirect('dashboard_employe');
    }
    exit;
}

public function annulerReservation() {
    $resId = isset($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : null;

    if ($resId) {
        try {
            if ($this->trajetService->annulerUnePlace($resId)) {
                $_SESSION['message_success'] = "Votre réservation a été annulée.";
            }
        } catch (Exception $e) {
            $_SESSION['message_error'] = "Erreur : " . $e->getMessage();
        }
    }

    $this->redirect('dashboard_employe');
}
}