<?php
session_start(); // Indispensable pour gérer la connexion
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php'; // On suppose que $pdo est défini ici

use App\Models\UtilisateurModel;
use App\Models\EntrepriseModel;
use App\Models\OffresModel;
use App\Controlers\UtilisateurControleur;
use App\Controlers\EntrepriseControleur;
use App\Controlers\OffresControleur;
use App\Models\CandidatureModel;
use App\Controlers\CandidatureControleur;

// --- INITIALISATION TWIG ---
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

// Donne accès à la variable "session" dans TOUS tes fichiers .twig
$twig->addGlobal('session', $_SESSION); 
$userRole = $_SESSION['id_role'] ?? null;

$page = $_GET['page'] ?? 'accueil';
$action = $_GET['action'] ?? null;

if ($action) {
    switch ($action) {
        case 'inscription_entreprise':
            $entrepriseModel = new EntrepriseModel($pdo);
            $controleur = new EntrepriseControleur($entrepriseModel, $twig);
            $controleur->registerEntreprise();
            exit; 

        case 'inscription_user':
            $userModel = new UtilisateurModel($pdo);
            $controleur = new UtilisateurControleur($userModel, $twig);
            $controleur->registerUtilisateur();
            exit;
    }
}

switch ($page) {
    case 'accueil':
        echo $twig->render('index.twig');
        break;
    
    case 'confidentialite':
        echo $twig->render('confidentialite.twig');
        break;
    
    case 'mentions-legales':
        echo $twig->render('mentions-legales.twig');
        break;
    
    case 'entreprises':
        $entrepriseModel = new EntrepriseModel($pdo);
        $controleur = new EntrepriseControleur($entrepriseModel, $twig);
        $controleur->pagination();
        break;
    
    case 'offres':
        $offresModel = new OffresModel($pdo);
        $controleur = new OffresControleur($offresModel, $twig);

        $favorisIds = [];
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT id_offre FROM MET_EN_FAVORI WHERE id_utilisateur = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $favorisIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        $twig->addGlobal('favoris', $favorisIds);

        $controleur->pagination();
        break;

    case 'postuler':
        $id_offre = $_GET['id'] ?? null;
        $sqlVue = "UPDATE OFFRE SET nombredevues = nombredevues + 1 WHERE id_offre = ?";
        $stmtVue = $pdo->prepare($sqlVue);
        $stmtVue->execute([$id_offre]);
        $offresModel = new OffresModel($pdo);
        $controleur = new OffresControleur($offresModel, $twig);
        $controleur->afficherOffre();
        break;

    case 'candidature':
        $model = new CandidatureModel($pdo);
        $controleur = new CandidatureControleur($model, $twig);
        $controleur->postuler();
        break;
    
    case 'afficher_utilisateur':
    case 'modifier_utilisateur':
    case 'supprimer_utilisateur':
        $userModel = new UtilisateurModel($pdo);
        $controleur = new UtilisateurControleur($userModel, $twig);
        if ($page === 'afficher_utilisateur') $controleur->afficherUtilisateurs();
        if ($page === 'modifier_utilisateur') $controleur->modifierUtilisateur();
        if ($page === 'supprimer_utilisateur') $controleur->supprimerUtilisateur();
        break;
    
    case 'afficher_entreprise':
    case 'modifier_entreprise':
    case 'supprimer_entreprise':

        $entrepriseModel = new EntrepriseModel($pdo);
        $controleur = new EntrepriseControleur($entrepriseModel, $twig);

        // On appelle la méthode correspondante à la page
        if ($page === 'afficher_entreprise') $controleur->afficherEntreprises();
        if ($page === 'modifier_entreprise') $controleur->modifierEntreprise();
        if ($page === 'supprimer_entreprise') $controleur->supprimerEntreprise();
        break;
    
    case 'afficher_entreprise_offre':
    case 'modifier_offre':
    case 'supprimer_offre':
    case 'afficher_offre':
        $offresModel = new OffresModel($pdo);
        $controleur = new OffresControleur($offresModel, $twig);
        if ($page === 'afficher_entreprise_offre') $controleur->afficherEntrepriseOffre();
        if ($page === 'modifier_offre') $controleur->modifierOffre();
        if ($page === 'supprimer_offre') $controleur->supprimerOffre();
        if ($page === 'afficher_offre') $controleur->afficherOffreByNomEntreprise();
        break;
    
    case 'connexion':
        echo $twig->render('connexion.twig');
        break;
    
    case 'creercompte':
        $userModel = new UtilisateurModel($pdo);
        $controleur = new UtilisateurControleur($userModel, $twig);
        $controleur->afficherFormCreation(); // Cette méthode gère le render avec les pilotes
        break;
    
    case 'creerentreprise':
        echo $twig->render('creerentreprise.twig');
        break;
    
    case 'creeroffre':
        $offresModel = new OffresModel($pdo);
        $controleur = new OffresControleur($offresModel, $twig);
        $controleur->afficherFormulaireCreation();
        break;

    case 'contact':
        echo $twig->render('contact.twig');
        break;

    case 'merci-candidature':
        $id = $_GET['id'] ?? null;
        if (!$id) { die("Candidature introuvable"); }
        $model = new CandidatureModel($pdo);
        $controleur = new CandidatureControleur($model, $twig);
        $controleur->afficherMerci($id);
        break;

    case 'wishlist':
        require_once __DIR__ . '/traitementwishlist.php';
        break;

    case 'ajouter_wishlist':
        require_once __DIR__ . '/traitementajoutwishlist.php';
        break;

    case 'candidatures':
        require_once __DIR__ . '/traitementcandidatures.php';
        break;

    case 'candidatures_pilotes':
        require_once __DIR__ . '/traitementcandidaturespilotes.php';
        break;


    case 'powerpoint':
        echo $twig->render('powerpoint.twig');
        break;
}