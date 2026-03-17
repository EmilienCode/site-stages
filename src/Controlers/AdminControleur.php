<?php
class AdminControleur {
    private $userModel;
    private $twig;

    public function __construct($userModel, $twig) {
        $this->userModel = $userModel;
        $this->twig = $twig;
    }

    private function checkAdmin() {
        // On vérifie si la session existe ET si le rôle est bien 3
        if (!isset($_SESSION['user_id']) || $_SESSION['id_role'] != 3) {
            // Si pas admin, on redirige vers l'accueil ou login
            header('Location: acceserror.html');
            exit();
        }
    }

    public function afficherUtilisateurs() {
        //check admin
        $this->checkAdmin();

        // Le contrôleur demande au modèle les données
        $utilisateurs = $this->userModel->getAll();

        // Le contrôleur demande à Twig d'afficher la vue avec ces données
        echo $this->twig->render('admin_utilisateur.twig', [
            'users' => $utilisateurs
        ]);
    }

    public function supprimerUtilisateur() {
        //check admin
        $this->checkAdmin();

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