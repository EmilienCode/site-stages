<?php

class UtilisateurControleur {
    private $userModel;
    private $twig;

    public function __construct($userModel, $twig) {
        $this->userModel = $userModel;
        $this->twig = $twig;
    }

    // Une seule méthode de sécurité flexible
    private function checkAccess($requiredRole) {
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
        // Seul l'admin (3) a le droit de supprimer par exemple
        $this->checkAccess([2, 3]);

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->deleteUser($id);
        }

        header('Location: index.php?page=afficher_utilisateur&success=delete');
        exit();
    }

    public function modifierUtilisateur() {
        // Tout le monde (Pilote=2 ou Admin=3) peut modifier si connecté
        if (!isset($_SESSION['user_id'])) header('Location: login.php');

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
}