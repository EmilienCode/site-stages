<?php
namespace App\Controlers;

class UtilisateurControleur {
    private $userModel;
    private $twig;

    public function __construct($userModel, $twig) {
        $this->userModel = $userModel;
        $this->twig = $twig;
    }

    // Une seule méthode de sécurité flexible
    private function checkAccess($allowedRoles) {
        // Si on a passé un seul chiffre (ex: 3), on le transforme en tableau [3]
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        // On vérifie si l'ID du rôle en session est dans la liste autorisée
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['id_role'], $allowedRoles)) {
            header('Location: acceserror.html');
            exit();
        }
    }

    public function afficherUtilisateurs() {
        // 1. Sécurité : Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion'); // Ou ta page de login
            exit();
        }
        $utilisateurs = [];
        // On adapte la requête selon le rôle en session
        if ($_SESSION['id_role'] == 3) {
            $utilisateurs = $this->userModel->getAll(); // Admin voit tout
        } elseif ($_SESSION['id_role'] == 2) {
            $utilisateurs = $this->userModel->getUserByRole(); // Pilote voit restreint
        }

        echo $this->twig->render('gestion_utilisateur.twig', [
            'users' => $utilisateurs
        ]);
    }

    public function supprimerUtilisateur() {
        
        $this->checkAccess([2,3]);

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->deleteUser($id);
        }

        header('Location: index.php?page=afficher_utilisateur&success=delete');
        exit();
    }

    public function modifierUtilisateur() {
        // Tout le monde (Pilote=2 ou Admin=3) peut modifier si connecté
        if (!isset($_SESSION['user_id'])) header('Location: connexion.php');

        $id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'] ?? '',
                'prenom' => $_POST['prenom'] ?? '',
                'email' => $_POST['email'] ?? '',
                'id_role' => $_POST['id_role'] ?? 1,
                'ville' => $_POST['ville'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'sexe' => $_POST['sexe'] ?? 0,
                'date_naissance' => $_POST['date_naissance'] ?? ''
            ];

            if ($this->userModel->updateUser($id, $data)) {
                header('Location: index.php?page=afficher_utilisateur&success=update');
                exit();
            }
        }

        $userToEdit = $this->userModel->getUserById($id);
        echo $this->twig->render('modifier_utilisateur.twig', [
            'user' => $userToEdit,
            'roles' => $this->userModel->getRoles()
        ]);
    }
    
    public function afficherWishlist() {
        // J'utilise $_SESSION['user_id'] en me basant sur ce que j'ai vu dans ta fonction checkAccess()
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=connexion');
            exit();
        }
        
        // On récupère les offres en passant l'ID de l'utilisateur connecté
        $offresWishlist = $this->userModel->getAllOffreWishlist($_SESSION['user_id']);

        // On envoie la variable sous le nom 'offres' car c'est ce que Twig attend
        echo $this->twig->render('wishlist.twig', [
            'offres' => $offresWishlist
        ]);
    }

    // J'ai renommé cette méthode pour correspondre à ton lien Twig (page=supprimer_wishlist)
    public function supprimerWishlist() { 
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=connexion');
            exit();
        }

        $id_offre = $_GET['id'] ?? null;
        if ($id_offre) {
            // On s'assure de supprimer le favori uniquement pour l'utilisateur en cours !
            $this->userModel->deleteOffreWishlist($id_offre, $_SESSION['user_id']);
        }

        // Retour à la page précédente
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}