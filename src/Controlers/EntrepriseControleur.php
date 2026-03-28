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
        
    public function afficherEntreprises() {
        // 1. Sécurité : Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion'); // Ou ta page de login
            exit();
        }
        $entreprise = [];
        // On adapte la requête selon le rôle en session
        if ($_SESSION['id_role'] == 3||$_SESSION['id_role'] == 2) {
            $entreprise = $this->entrepriseModel->getAllEntreprise(); 
            //var_dump($entreprise); die(); //permet d'afficher le resultat de la requete (debut)
        }

        echo $this->twig->render('gestion_entreprise.twig', [
            'entreprise' => $entreprise
        ]);
    }

    public function supprimerEntreprise() {
        
        $this->checkAccess([2,3]);

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->entrepriseModel->deleteEntreprise($id);
        }

        header('Location: index.php?page=afficher_entreprise&success=delete');
        exit();
    }

    public function modifierEntreprise() {
        // Sécurité : Vérification du rôle (Admin=3 ou Pilote=2)
        if (!isset($_SESSION['id_role']) || ($_SESSION['id_role'] != 3 && $_SESSION['id_role'] != 2)) {
            header('Location: index.php?page=connexion');
            exit();
        }

        // On récupère le SIRET depuis l'URL (ex: index.php?page=modifier_entreprise&id=3061389...)
        $siret = $_GET['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Préparation des données avec les noms exacts de la BDD
            $data = [
                'nom_entreprise'         => $_POST['nom_entreprise'] ?? '',
                'email_entreprise'       => $_POST['email_entreprise'] ?? '',
                'telephone_entreprise'   => $_POST['telephone_entreprise'] ?? '',
                'site_web_entreprise'    => $_POST['site_web_entreprise'] ?? '',
                'description_entreprise' => $_POST['description_entreprise'] ?? '',
                'adresse_entreprise'     => $_POST['adresse_entreprise'] ?? '',
                'code_postal_entreprise' => $_POST['code_postal_entreprise'] ?? '',
                'ville_entreprise'       => $_POST['ville_entreprise'] ?? '',
                'pays_entreprise'        => $_POST['pays_entreprise'] ?? '',
                'secteur_entreprise'     => $_POST['secteur_entreprise'] ?? '',
                'taille_entreprise'      => $_POST['taille_entreprise'] ?? '',
                'linkedin_entreprise'    => $_POST['linkedin_entreprise'] ?? '',
                'est_active_entreprise'  => isset($_POST['est_active_entreprise']) ? 1 : 0
            ];

            // Appel au modèle Entreprise (et non UserModel)
            if ($this->entrepriseModel->updateEntreprise($siret, $data)) {
                header('Location: index.php?page=gestion_entreprises&success=update');
                exit();
            }
        }

        // Récupération des infos actuelles pour pré-remplir le formulaire
        $entrepriseToEdit = $this->entrepriseModel->getEntrepriseBySiret($siret);

        echo $this->twig->render('modifier_entreprise.twig', [
            'entreprise' => $entrepriseToEdit
        ]);
    }

    public function registerEntreprise() {
        // 1. Sécurité : Vérifier si l'utilisateur est connecté et autorisé (Pilote/Admin)
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['id_role'], [2, 3])) {
            header('Location: index.php?page=connexion');
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // 2. Nettoyage et Validation des données obligatoires
            $siret = trim($_POST["siret_entreprise"] ?? "");
            $email = filter_var(trim($_POST["email_entreprise"] ?? ""), FILTER_VALIDATE_EMAIL);
            $nom   = trim($_POST["nom_entreprise"] ?? "");

            // Validation minimale (SIRET 14 chiffres, Email valide, Nom présent)
            if (strlen($siret) !== 14 || !$email || empty($nom)) {
                header("Location: index.php?page=creerentreprise&error=invalid_data");
                exit;
            }

            // 3. Préparation du tableau de données (mappage avec ta BDD)
            $entrepriseData = [
                'siret_entreprise'       => $siret,
                'email_entreprise'       => $email,
                'telephone_entreprise'   => trim($_POST["telephone_entreprise"] ?? ""),
                'site_web_entreprise'    => trim($_POST["site_web_entreprise"] ?? ""),
                'logo_entreprise'        => "default_logo.png", // À remplacer par la logique d'upload
                'nom_entreprise'         => $nom,
                'description_entreprise' => trim($_POST["description_entreprise"] ?? ""),
                'adresse_entreprise'     => trim($_POST["adresse_entreprise"] ?? ""),
                'secteur_entreprise'     => trim($_POST["secteur_entreprise"] ?? ""),
                'taille_entreprise'      => trim($_POST["taille_entreprise"] ?? ""),
                'linkedin_entreprise'    => trim($_POST["linkedin_entreprise"] ?? ""),
                'code_postal_entreprise' => trim($_POST["code_postal_entreprise"] ?? ""),
                'ville_entreprise'       => trim($_POST["ville_entreprise"] ?? ""),
                'pays_entreprise'        => trim($_POST["pays_entreprise"] ?? ""),
                'est_active_entreprise'  => isset($_POST['est_active_entreprise']) ? 1 : 0
            ];

            try {
                // 4. Appel au modèle EntrepriseModel
                $success = $this->entrepriseModel->inscrireEntreprise($entrepriseData);

                if ($success) {
                    header("Location: index.php?page=afficher_entreprise&success=created");
                } else {
                    header("Location: index.php?page=creerentreprise&error=insert_failed");
                }
                exit;

            } catch (Exception $e) {
                // Gestion de l'erreur SIRET déjà existant (Duplicate entry)
                if ($e->getCode() == 23000) {
                    header("Location: index.php?page=creerentreprise&error=siret_exists");
                } else {
                    error_log($e->getMessage());
                    die("Erreur lors de la création de l'entreprise.");
                }
                exit;
            }
        }
    }
}