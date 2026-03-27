<?php
namespace App\Controlers;

class EntrepriseControleur {
    private $entrepriseModel;
    private $twig;

    public function __construct($entrepriseModel, $twig) {
        $this->entrepriseModel = $entrepriseModel;
        $this->twig = $twig;
    }

    public function pagination() {
        // Paramètres de base
        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($p < 1) $p = 1;
        $parPage = 10;
        $offset = ($p - 1) * $parPage;

        // Récupération des filtres depuis l'URL (GET)
        $nom = $_GET['nom'] ?? '';
        $taille = $_GET['taille'] ?? '';
        $secteur = $_GET['secteur'] ?? '';

        // Récupération des données
        $entreprises = $this->entrepriseModel->getEntreprises($parPage, $offset, $nom, $taille, $secteur);
        $totalEntreprises = $this->entrepriseModel->countEntreprises($nom, $taille, $secteur);
        $totalPages = ceil($totalEntreprises / $parPage);
        $secteurs_list = $this->entrepriseModel->getAllSecteurs();

        // Rendu de la vue
        echo $this->twig->render('entreprises.twig', [
            'entreprises'      => $entreprises,
            'currentPage'      => $p,
            'totalPages'       => $totalPages,
            'nom_search'       => $nom,
            'taille_selected'  => $taille,
            'secteur_selected' => $secteur,
            'secteurs_list'    => $secteurs_list
        ]);
    }
}