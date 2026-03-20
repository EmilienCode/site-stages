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
        // Logique de pagination
        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($p < 1) $p = 1;
        
        $parPage = 12;
        $offset = ($p - 1) * $parPage;

        // Récupération des données via le Modèle
        $offres = $this->offresModel->getOffres($parPage, $offset);
        $totalOffres = $this->offresModel->countOffres();
        $totalPages = ceil($totalOffres / $parPage);

        // On utilise Twig pour afficher la vue
        echo $this->twig->render('offres.twig', [
            'offres'      => $offres,
            'currentPage' => $p,
            'totalPages'  => $totalPages
        ]);
    }

}


