<?php
namespace App\Controlers;

class OffresControleur extends UtilisateurControleur{
    private $offresModel;
    private $twig;

    public function __construct($offresModel, $twig) {
        $this->offresModel = $offresModel;
        $this->twig = $twig;
    }
    // Affiche la liste des offres avec pagination, filtres et tri
    public function pagination() {
        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($p < 1) $p = 1;
        
        $parPage = 12;
        $offset = ($p - 1) * $parPage;

        $metier = $_GET['metier'] ?? '';
        $ville = $_GET['ville'] ?? '';
        $tri = $_GET['tri'] ?? 'recents';
        // On récupère la compétence sélectionnée
        $competence_id = $_GET['competence'] ?? ''; 

        // On récupère la liste complète des compétences pour le menu
        $competences_list = $this->offresModel->getAllCompetences();

        // On passe $competence_id aux méthodes du modèle
        $offres = $this->offresModel->getOffres($parPage, $offset, $metier, $ville, $tri, $competence_id);
        $totalOffres = $this->offresModel->countOffres($metier, $ville, $competence_id);
        $totalPages = ceil($totalOffres / $parPage);

        echo $this->twig->render('offres.twig', [
            'offres'              => $offres,
            'currentPage'         => $p,
            'totalPages'          => $totalPages,
            'metier'              => $metier,
            'ville'               => $ville,
            'tri'                 => $tri,
            'competences'         => $competences_list, // Envoi de la liste à Twig
            'competence_selected' => $competence_id     // Pour garder la sélection active
        ]);
    }
    // Affiche les offres
    public function afficherOffre() {
        if (!isset($_GET['id'])) {
            die("Offre non trouvée");
        }
        $id = $_GET['id'];
        $offre = $this->offresModel->getOffreById($id);
        if (!$offre) {
            die("Offre inexistante");
        }
        $user = $_SESSION['user'] ?? null;
        echo $this->twig->render('postuler.twig', [
            'offre' => $offre, 'user' => $user
        ]);
    }
    // Affiche les offres d'une entreprise
    public function afficherEntrepriseOffre(){
        // Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion'); // Ou ta page de login
            exit();
        }
        $entreprise = [];
        // On adapte la requête selon le rôle en session
        if ($_SESSION['id_role'] == 3||$_SESSION['id_role'] == 2) {
            $entreprise = $this->offresModel->getAllEntrepriseMinInfos();
            //var_dump($entreprise); die(); //permet d'afficher le resultat de la requete (debut)
        }

        echo $this->twig->render('gestion_entreprise_offre.twig', [
            'entreprise' => $entreprise
        ]);
    }
    // Affiche les offres d'une entreprise par nom
    public function afficherOffreByNomEntreprise(){
        // Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion'); // Ou ta page de login
            exit();
        }
        $offres = [];
        $nom_entreprise = $_GET['nom'] ?? null;
        // On adapte la requête selon le rôle en session
        if ($_SESSION['id_role'] == 3||$_SESSION['id_role'] == 2) {
            $offres = $this->offresModel->afficherOffreByNomEntrepriseSQL($nom_entreprise);
            //var_dump($entreprise); die(); //permet d'afficher le resultat de la requete (debut)
        }

        echo $this->twig->render('gestion_offres.twig', [
            'offres' => $offres,
            'nom_entreprise' => $nom_entreprise
        ]);
    }
    // Affiche les offres d'une entreprise par ID
    public function afficherOffreById(){
        // Si la session a sauté, on dégage vers le login (ou accueil)
        if (!isset($_SESSION['id_role'])) {
            header('Location: index.php?page=connexion'); // Ou ta page de login
            exit();
        }
        $offres = [];
        $id_offre = $_GET['id'] ?? null;
        // On adapte la requête selon le rôle en session
        if ($_SESSION['id_role'] == 3||$_SESSION['id_role'] == 2) {
            $offres = $this->offresModel->afficherOffreByIdSQL($id);
            //var_dump($entreprise); die(); //permet d'afficher le resultat de la requete (debut)
        }

        echo $this->twig->render('gestion_offres.twig', [
            'offres' => $offres,
            'nom_entreprise' => $nom_entreprise
        ]);
    
    }
    // Supprime une offre
    public function supprimerOffre() {
        // Vérification des droits
        $this->checkAccess([2,3]);

        $id = $_GET['id'] ?? null;

        if ($id) {
            // On récupère les infos de l'offre AVANT de la détruire
            $offre = $this->offresModel->getOffreById($id);

            if ($offre) {
                // On sauvegarde le nom de l'entreprise pour la redirection
                $nom_entreprise = $offre['nom_entreprise'];

                // On supprime l'offre
                $this->offresModel->deleteOffre($id);

                // n redirige vers la liste des offres de CETTE entreprise
                // urlencode() sécurise le nom dans l'URL (remplace les espaces par %20 etc.)
                header('Location: index.php?page=afficher_offre&nom=' . urlencode($nom_entreprise) . '&success=delete');
                exit(); 
            }
        }

        // Si l'ID est invalide ou l'offre introuvable, on renvoie à la page globale
        header('Location: index.php?page=afficher_entreprise_offre&error=notfound');
        exit();
    }

    public function afficherFormulaireCreation() {
        // On récupère la liste des entreprises
        $entreprises = $this->offresModel->getAllNomsEntreprises();

        $competences = $this->offresModel->getAllCompetences(); 

        // On envoie tout à la vue Twig
        echo $this->twig->render('creeroffre.twig', [
            'entreprises' => $entreprises,
            'competences' => $competences
        ]);
    }

    public function modifierOffre() {
        // Vérification du rôle (Admin=3 ou Pilote=2)
        if (!isset($_SESSION['id_role']) || ($_SESSION['id_role'] != 3 && $_SESSION['id_role'] != 2)) {
            header('Location: index.php?page=connexion');
            exit();
        }

        // On récupère le id depuis l'URL (ex: index.php?page=modifier_entreprise&id=3061389...)
        $id = $_GET['id'] ?? null;
        // Si pas d'ID, on redirige vers la liste globale
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $remuneration = $_POST['remuneration_offre'] ?? '';
            if ($remuneration === '') $remuneration = null;

            $competence = $_POST['id_competence'] ?? '';
            if ($competence === '') $competence = null;

            // Préparation des données avec les noms exacts de la BDD
            $data = [
                'titre_offre'         => $_POST['titre_offre'] ?? '',
                'description_offre'       => $_POST['description_offre'] ?? '',
                'remuneration_offre'   => $_POST['remuneration_offre'] ?? '',
                'lieu_offre' => $_POST['lieu_offre'] ?? '',
                'duree_formation_offre'     => $_POST['duree_formation_offre'] ?? '',
                'domaine_requis_offre' => $_POST['domaine_requis_offre'] ?? '',
                'nom_entreprise'       => $_POST['nom_entreprise'] ?? '',
                'id_competence'        => $_POST['id_competence'] ?? '',
            ];

            // Appel au modèle Entreprise (et non UserModel)
            if ($this->offresModel->updateOffre($id, $data)) {
                $offre = $this->offresModel->getOffreById($id);
                if ($offre) {
                    // On sauvegarde le nom de l'entreprise pour la redirection
                    $nom_entreprise = $offre['nom_entreprise'];
                    header('Location: index.php?page=afficher_offre&nom=' . urlencode($nom_entreprise) . '&success=update');
                    exit();
                }
            }
        }

        // Récupération des infos actuelles pour pré-remplir le formulaire
        $offreToEdit = $this->offresModel->getOffreById($id);
        // On récupère la liste des entreprises
        $entreprises = $this->offresModel->getAllNomsEntreprises();

        $competences = $this->offresModel->getAllCompetences(); 
        //var_dump($offreToEdit);
        //die();
        echo $this->twig->render('modifier_offre.twig', [
            'offre' => $offreToEdit,
            'entreprises' => $entreprises,
            'competences' => $competences
        ]);
    }
}