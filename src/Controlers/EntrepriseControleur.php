<?php
namespace App\Controlers;
class EntrepriseControleur{
    private $entrepriseModel;
    private $twig;

    public function __construct($entrepriseModel, $twig) {
        $this->entrepriseModel = $entrepriseModel;
        $this->twig = $twig;
    }

    public function pagination(){
        // Logique de pagination (Le Contrôleur décide)
        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($p < 1) $p = 1;
        $parPage = 10;
        $offset = ($p - 1) * $parPage;

        // Récupération des données via le Modèle
        $entreprises = $this->entrepriseModel->getEntreprises($parPage, $offset);

        // On utilise Twig pour afficher la vue
        echo $this->twig->render('entreprises.twig', [
            'entreprises' => $entreprises,
            'page' => $p,
            'parPage' => $parPage
        ]);
    }

}


