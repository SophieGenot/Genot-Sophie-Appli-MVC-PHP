<?php 
require_once __DIR__ . '/AbstractController.php'; 
require_once __DIR__ . '/../services/TrajetService.php';
require_once __DIR__ . '/../services/AgenceService.php';

class TrajetController extends AbstractController {

    private TrajetService $trajetService;
    private AgenceService $agenceService;

    public function __construct($pdo) {
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
        $this->checkAuth();

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

        $this->render('dashboard', [
            'mes_trajets' => $mes_trajets,
            'autres_trajets' => $autres_trajets
        ]);
    }

    // ------------------------ Création d'un trajet ------------------------
    public function createTrajet() {
        $this->checkAuth();

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
                $this->redirect('dashboard_employe');
            }
        }

        $this->render('_trajet-form', [
            'agences' => $agences,
            'error' => $error
        ]);
    }

    public function editTrajet($trajetId) {
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
}