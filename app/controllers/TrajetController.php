<?php 
require_once __DIR__ . '/../services/TrajetService.php';
require_once __DIR__ . '/../services/AgenceService.php';

class TrajetController {

    private TrajetService $trajetService;
    private AgenceService $agenceService;

    public function __construct($pdo) {
        $this->trajetService = new TrajetService($pdo);
        $this->agenceService = new AgenceService($pdo);
    }

    // ------------------------ Page d'accueil ------------------------
    public function listHome() {
        $trajets = $this->trajetService->getAllTrajetsDisponiblesAvecInfos();
        require __DIR__ . '/../views/home.php';
    }

    // ------------------------ Dashboard employé ------------------------
    public function listDashboardEmploye() {
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=home");
            exit;
        }

        // Récupération de tous les trajets avec infos complètes
        $trajets = $this->trajetService->getAllTrajetsAvecInfos();

        $mes_trajets = [];
        $autres_trajets = [];

        foreach ($trajets as $trajet) {
         
            $trajet['user']['id'] = $trajet['auteur_id'] ?? null;

            if ($trajet['user']['id'] === $_SESSION['user']['id']) {
                $mes_trajets[] = $trajet;
            } elseif (($trajet['places_dispo'] ?? 0) > 0) {
                $autres_trajets[] = $trajet;
            }
        }

        require __DIR__ . '/../views/dashboard.php';
    }

    // ------------------------ Création d'un trajet ------------------------
    public function createTrajet() {
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=home");
            exit;
        }

        $userId = $_SESSION['user']['id'];
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
                $this->trajetService->createTrajet([
                    'agence_depart_id' => $departId,
                    'agence_arrivee_id' => $arriveeId,
                    'gdh_depart' => $gdhDepart,
                    'gdh_arrivee' => $gdhArrivee,
                    'nb_places_total' => $nbPlaces,
                    'auteur_id' => $userId
                ]);
                header("Location: index.php?action=dashboard_employe");
                exit;
            }
        }

        require __DIR__ . '/../views/_trajet-form.php';
    }

    public function editTrajet($trajetId) {
        if (!isset($_SESSION['user'])) {
        header("Location: index.php?action=home");
        exit;
    }

    $userId = $_SESSION['user']['id'];
    $trajet = $this->trajetService->getTrajetById($trajetId);

    // Vérifie que l'utilisateur est l'auteur du trajet ou un admin
    if ($trajet['auteur_id'] !== $userId && $_SESSION['user']['role'] !== 'admin') {
        header("Location: index.php?action=dashboard_employe");
        exit;
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

            // Redirection vers le dashboard
            if ($_SESSION['user']['role'] === 'admin') {
                header("Location: index.php?action=dashboard_admin");
            } else {
                header("Location: index.php?action=dashboard_employe");
            }
            exit;
        }
    }

    require __DIR__ . '/../views/_trajet-form.php';
}
    }
