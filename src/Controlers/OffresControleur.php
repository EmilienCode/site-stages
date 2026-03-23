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
        echo $this->twig->render('postuler.twig', [
            'offre' => $offre
        ]);
    }
}