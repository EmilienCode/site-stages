<?php
namespace App\Controlers;

class OffresControleur {
    private $offresModel;
    private $twig;

    public function __construct($offresModel, $twig) {
        $this->offresModel = $offresModel;
        $this->twig = $twig;
    }

    public function pagination() {
        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($p < 1) $p = 1;
        
        $parPage = 12;
        $offset = ($p - 1) * $parPage;

        $metier = $_GET['metier'] ?? '';
        $ville = $_GET['ville'] ?? '';
        $tri = $_GET['tri'] ?? 'recents';
        // NOUVEAU : On récupère la compétence sélectionnée
        $competence_id = $_GET['competence'] ?? ''; 

        // NOUVEAU : On récupère la liste complète des compétences pour le menu
        $competences_list = $this->offresModel->getAllCompetences();

        // On passe $competence_id aux méthodes du modèle
        $offres = $this->offresModel->getOffres($parPage, $offset, $metier, $ville, $tri, $competence_id);
        $totalOffres = $this->offresModel->countOffres($metier, $ville, $competence_id);
        $totalPages = ceil($totalOffres / $parPage);

        echo $this->twig->render('offres.twig', [
            'offres'              => $offres,
            'currentPage'         => $p,
            'totalPages'          => $totalPages,
            'metier'              => $metier,
            'ville'               => $ville,
            'tri'                 => $tri,
            'competences'         => $competences_list, // Envoi de la liste à Twig
            'competence_selected' => $competence_id     // Pour garder la sélection active
        ]);
    }
    public function afficherOffre() {
        if (!isset($_GET['id'])) {
            die("Offre non trouvée");
        }
        $id = $_GET['id'];
        $offre = $this->offresModel->getOffreById($id);
        if (!$offre) {
            die("Offre inexistante");
        }
        $user = $_SESSION['user'] ?? null;
        echo $this->twig->render('postuler.twig', [
            'offre' => $offre, 'user' => $user
        ]);
    }

    public function afficherEntrepriseOffre(){
        // 1. Sécurité : Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion'); // Ou ta page de login
            exit();
        }
        $entreprise = [];
        // On adapte la requête selon le rôle en session
        if ($_SESSION['id_role'] == 3||$_SESSION['id_role'] == 2) {
            $entreprise = $this->offresModel->getAllEntrepriseMinInfos();
            //var_dump($entreprise); die(); //permet d'afficher le resultat de la requete (debut)
        }

        echo $this->twig->render('gestion_entreprise_offre.twig', [
            'entreprise' => $entreprise
        ]);
    }

    public function afficherOffreByNomEntreprise(){
        // 1. Sécurité : Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion'); // Ou ta page de login
            exit();
        }
        $offres = [];
        $nom_entreprise = $_GET['nom'] ?? null;
        // On adapte la requête selon le rôle en session
        if ($_SESSION['id_role'] == 3||$_SESSION['id_role'] == 2) {
            $offres = $this->offresModel->afficherOffreByNomEntrepriseSQL($nom_entreprise);
            //var_dump($entreprise); die(); //permet d'afficher le resultat de la requete (debut)
        }

        echo $this->twig->render('gestion_offres.twig', [
            'offres' => $offres
        ]);
    }

    public function modifierOffre() {
        // Logique de modification d'une offre (similaire à afficherOffre mais avec formulaire)
    }

    public function supprimerOffre() {
        $this->checkAccess([2,3]);

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->offreModel->deleteOffre($id);
        }
        echo $this->twig->render('gestion_offres.twig', [
            'offres' => $offres
        ]);
    }
}