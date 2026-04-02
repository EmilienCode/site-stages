<?php
namespace App\Controlers;

use DateTime;
use Exception;

class UtilisateurControleur {
    private $userModel;
    private $twig;

    public function __construct($userModel, $twig) {
        $this->userModel = $userModel;
        $this->twig = $twig;
    }

    // Vérification flexible des accès par rôle
    protected function checkAccess($allowedRoles) {
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['id_role'], $allowedRoles)) {
            header('Location: acceserror.html');
            exit();
        }
    }

    // Affiche la liste des utilisateurs selon les droits
    public function afficherUtilisateurs() {
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion');
            exit();
        }

        $search = isset($_GET['nom']) ? trim($_GET['nom']) : '';
        $utilisateurs = [];

        if ($_SESSION['id_role'] == 3) {
            // L'Admin (3) voit les étudiants (1) et les pilotes (2)
            $utilisateurs = $this->userModel->getUsersByRoles([1, 2], $search); 
        } elseif ($_SESSION['id_role'] == 2) {
            // Le Pilote (2) voit uniquement les étudiants (1)
            $utilisateurs = $this->userModel->getUsersByRoles([1], $search); 
        }

        $success = $_GET['success'] ?? null;

        echo $this->twig->render('gestion_utilisateur.twig', [
            'users' => $utilisateurs,
            'nom_search' => $search,
            'success' => $success
        ]);
    }

    // Affiche le formulaire de création (nécessaire pour envoyer la liste des pilotes à l'admin)
    public function afficherFormCreation() {
        $this->checkAccess([2, 3]);
        
        $pilotes = [];
        // Si c'est un admin qui crée, on va chercher la liste des pilotes pour le menu déroulant
        if ($_SESSION['id_role'] == 3) {
            $pilotes = $this->userModel->getAllPilotes();
        }

        echo $this->twig->render('creercompte.twig', [
            'pilotes' => $pilotes
        ]);
    }
    // Supprime un utilisateur
    public function supprimerUtilisateur() {
        $this->checkAccess([2, 3]);
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->deleteUser($id);
        }
        header('Location: index.php?page=afficher_utilisateur&success=delete');
        exit();
    }
    // modifie un utilisateur
    public function modifierUtilisateur() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=connexion');
            exit();
        }

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
        // Récupération des infos actuelles pour pré-remplir le formulaire
        $userToEdit = $this->userModel->getUserById($id);
        if (!$userToEdit) {
            header('Location: index.php?page=afficher_utilisateur');
            exit();
        }
        // Sécurité : Un pilote ne peut modifier que les étudiants, un admin peut tout modifier
        if ($_SESSION['id_role'] == 2 && $userToEdit['id_role'] != 1) {
            die("Accès refusé : Vous n'avez pas l'autorisation de modifier ce compte.");
        }

        echo $this->twig->render('modifier_utilisateur.twig', [
            'user' => $userToEdit,
            'roles' => $this->userModel->getRoles()
        ]);
    }
    // Traite le formulaire d'inscription d'un nouvel utilisateur
    public function registerUtilisateur() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['type']) || $_POST['type'] !== "COMPTE") {
            header("Location: index.php?page=creercompte");
            exit;
        }

        // Nettoyage et Validation
        $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
        $date_input = trim($_POST['date_naissance']);
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_input);
        $errors = DateTime::getLastErrors();
        // Validation de base + vérification des domaines jetables
        if (!$email || !$date_obj || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
            header("Location: index.php?page=creercompte&error=invalid_data");
            exit;
        }

        // Vérification domaines jetables
        $emailDomain = strtolower(explode('@', $email)[1] ?? '');
        $blockedDomains = ["yopmail.com", "mailinator.com", "tempmail.com"];
        if (in_array($emailDomain, $blockedDomains)) { 
            header("Location: index.php?page=creercompte&error=email_temp");
            exit;
        }

        // --- LOGIQUE D'ASSIGNATION DU PILOTE ---
        $id_pilote_referent = null;
        if ($_SESSION['id_role'] == 2) {
            // Si c'est un pilote qui crée, l'étudiant lui est assigné automatiquement
            $id_pilote_referent = $_SESSION['user_id'];
        } elseif ($_SESSION['id_role'] == 3) {
            // Si c'est un admin, on récupère le pilote choisi dans le menu déroulant (peut être nul)
            $id_pilote_referent = !empty($_POST['id_pilote']) ? (int)$_POST['id_pilote'] : null;
        }

        // Préparation des données
        $userData = [
            'nom'            => strtoupper(trim($_POST["nom"])),
            'prenom'         => ucfirst(strtolower(trim($_POST["prenom"]))),
            'email'          => $email,
            'password'       => password_hash($_POST["password"], PASSWORD_DEFAULT),
            'ville'          => ucfirst(strtolower(trim($_POST["ville"]))),
            'telephone'      => trim($_POST["telephone"]),
            'sexe'           => $_POST["sexe"],
            'date_naissance' => $date_obj->format('Y-m-d'),
            'id_pilote'      => $id_pilote_referent // Passage de l'ID du pilote
        ];
        // Tentative d'inscription et gestion des erreurs (ex: email déjà pris)
        try {
            $this->userModel->inscrireEtudiant($userData);
            header("Location: index.php?page=afficher_utilisateur&success=created");
            exit;
        } catch (\PDOException $e) {
            if ((string)$e->getCode() === '23000') {
                header("Location: index.php?page=creercompte&error=email_taken");
                exit;
            }
            die("Erreur SQL : " . $e->getMessage());
        }
    }
    // Affiche la wishlist de l'utilisateur connecté
    public function afficherWishlist() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=connexion');
            exit();
        }
        $offresWishlist = $this->userModel->getAllOffreWishlist($_SESSION['user_id']);
        echo $this->twig->render('wishlist.twig', ['offres' => $offresWishlist]);
    }
    // Ajoute une offre à la wishlist de l'utilisateur connecté
    public function supprimerWishlist() {
        // Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=connexion');
            exit();
        }
        $id_offre = $_GET['id'] ?? null;
        // Si un ID d'offre est fourni, on le supprime de la wishlist de l'utilisateur
        if ($id_offre) {
            $this->userModel->deleteOffreWishlist($id_offre, $_SESSION['user_id']);
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}