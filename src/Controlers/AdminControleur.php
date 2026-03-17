<?php
class AdminControleur {
    private $userModel;
    private $twig;

    public function __construct($userModel, $twig) {
        $this->userModel = $userModel;
        $this->twig = $twig;
    }

    public function afficherUtilisateurs() {
        // Le contrôleur demande au modèle les données
        $utilisateurs = $this->userModel->getAll();

        // Le contrôleur demande à Twig d'afficher la vue avec ces données
        echo $this->twig->render('admin_utilisateur.twig', [
            'users' => $utilisateurs
        ]);
    }

    public function supprimerUtilisateur() {
        // 1. On récupère l'ID passé en paramètre dans l'URL (ex: ?id=5)
        $id = $_GET['id'] ?? null;

        if ($id) {
            // 2. On demande au modèle de supprimer
            $this->userModel->deleteUser($id);
        }

        // 3. On redirige vers la page de gestion pour voir le changement
        header('Location: index.php?page=admin_utilisateur');
        exit();
    }
}