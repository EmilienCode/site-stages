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

        $offres = $this->offresModel->getOffres($parPage, $offset, $metier, $ville, $tri);
        
        $totalOffres = $this->offresModel->countOffres($metier, $ville);
        $totalPages = ceil($totalOffres / $parPage);

        echo $this->twig->render('offres.twig', [
            'offres'      => $offres,
            'currentPage' => $p,
            'totalPages'  => $totalPages,
            'metier'      => $metier,
            'ville'       => $ville,
            'tri'         => $tri
        ]);
    }
}