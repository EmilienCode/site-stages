<?php
namespace App\Controlers;
use DateTime;
class UtilisateurControleur {
    private $userModel;
    private $twig;

    public function __construct($userModel, $twig) {
        $this->userModel = $userModel;
        $this->twig = $twig;
    }

    // Une seule méthode de sécurité flexible
    protected function checkAccess($allowedRoles) {
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
        if (!isset($_SESSION['user_id'])) {
            header('Location: connexion.php');
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

        $userToEdit = $this->userModel->getUserById($id);
        if (!$userToEdit) {
        header('Location: index.php?page=afficher_utilisateur');
        exit();
    }

    // Un pilote (2) essaie de modifier un compte qui n'est pas étudiant (1)
    if ($_SESSION['id_role'] == 2 && $userToEdit['id_role'] != 1) {
        die("Accès refusé : Vous n'avez pas l'autorisation de modifier ce compte.");
    }
    // Un admin (3) essaie de modifier autre chose que 1 ou 2 (sauf s'il a le droit de se modifier lui-même)
    if ($_SESSION['id_role'] == 3 && !in_array($userToEdit['id_role'], [1, 2, 3])) {
        die("Accès refusé.");
    }
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

    public function registerUtilisateur() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['type']) || $_POST['type'] !== "COMPTE") {
            header("Location: index.php?page=creercompte");
            exit;
        }

        // 1. Nettoyage et Validation
        $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);

        $date_input = trim($_POST['date_naissance']);
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_input);
        $errors = DateTime::getLastErrors();

        if (
            !$email ||
            !$date_obj ||
            $errors['warning_count'] > 0 ||
            $errors['error_count'] > 0
        ) {
            header("Location: index.php?page=creercompte&error=invalid_data");
            exit;
        }

        $now = new DateTime();
        if ($date_obj > $now) {
            header("Location: index.php?page=creercompte&error=future_date");
            exit;
        }
        
        $emailDomain = strtolower(explode('@', $email)[1] ?? '');
        $blockedDomains = [
            "tempmail.com",
            "10minutemail.com",
            "guerrillamail.com",
            "mailinator.com",
            "yopmail.com"
        ];

        if (in_array($emailDomain, $blockedDomains)) {
            header("Location: index.php?page=creercompte&error=email_temp");
            exit;
        }

        // 2. Préparation des données pour le modèle
        $userData = [
            'nom'            => strtoupper(trim($_POST["nom"])), // on met le nom en majuscules pour une meilleure présentation et uniformité
            'prenom'         => ucfirst(strtolower(trim($_POST["prenom"]))), // on met la première lettre en majuscule et le reste en minuscule pour une meilleure présentation et stockage 
            'email'          => $email,
            'password'       => password_hash($_POST["password"], PASSWORD_DEFAULT),
            'ville'          => ucfirst(strtolower(trim($_POST["ville"]))), // même traitement que pour le prénom pour une meilleure présentation et un meilleur stockage
            'telephone'      => trim($_POST["telephone"]),
            'sexe'           => $_POST["sexe"],
            'date_naissance' => $date_obj->format('Y-m-d') // Format SQL
        ];

        try {
            // 3. Appel au modèle
            $id_utilisateur = $this->userModel->inscrireEtudiant($userData);

            header("Location: index.php?page=afficher_utilisateur&success=created");
            exit;

        } catch (\PDOException $e) {
            if ((string)$e->getCode() === '23000') {
                // Code d'erreur 23000 = violation de contrainte d'unicité (ex: email déjà pris)
                header("Location: index.php?page=creercompte&error=email_taken");
                // On redirige vers la page de création de compte avec une erreur spécifique
                exit;
            }

            die("Erreur SQL : " . $e->getMessage());
            //cette ligne est surtout utile en développement pour comprendre ce qui a mal tourné. En production, il vaudrait mieux logguer l'erreur dans un fichier plutôt que de l'afficher à l'utilisateur.
        }
    }
}