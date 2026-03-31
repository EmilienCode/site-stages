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
        $id_utilisateur = $_SESSION['user_id'] ?? null;
        $offres_favoris = [];
        if ($id_utilisateur) {
            try {
                // Utilisation de OFFRE au singulier
                $sql = "SELECT o.* FROM OFFRE o 
                        JOIN MET_EN_FAVORI f ON o.id_offre = f.id_offre 
                        WHERE f.id_utilisateur = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_utilisateur]);
                $offres_favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erreur Wishlist : " . $e->getMessage());
            }
        }
        echo $twig->render('wishlist.twig', ['offres' => $offres_favoris]);
        break;

    case 'ajouter_wishlist':
        $id_offre = $_GET['id'] ?? null;
        $id_utilisateur = $_SESSION['user_id'] ?? null;
        if ($id_offre && $id_utilisateur) {
            try {
                $check = $pdo->prepare("SELECT * FROM MET_EN_FAVORI WHERE id_utilisateur = ? AND id_offre = ?");
                $check->execute([$id_utilisateur, $id_offre]);
                if ($check->rowCount() == 0) {
                    $ins = $pdo->prepare("INSERT INTO MET_EN_FAVORI (id_utilisateur, id_offre) VALUES (?, ?)");
                    $ins->execute([$id_utilisateur, $id_offre]);
                } else {
                    $del = $pdo->prepare("DELETE FROM MET_EN_FAVORI WHERE id_utilisateur = ? AND id_offre = ?");
                    $del->execute([$id_utilisateur, $id_offre]);
                }
            } catch (PDOException $e) {
                die("Erreur SQL Ajout : " . $e->getMessage());
            }
        }
        header("Location: index.php?page=offres");
        exit();

    case 'candidatures':
        $id_utilisateur = $_SESSION['user_id'] ?? null;
        $mes_candidatures = [];
        if ($id_utilisateur) {
            try {
                // Ajout de o.lieu_offre et c.LM_candidature dans le SELECT
                $sql = "SELECT c.*, o.titre_offre, o.nom_entreprise, o.lieu_offre 
                        FROM CANDIDATURES c 
                        JOIN OFFRE o ON c.id_offre = o.id_offre 
                        WHERE c.id_utilisateur = ? 
                        ORDER BY c.date_candidature DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_utilisateur]);
                $mes_candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erreur Candidatures : " . $e->getMessage());
            }
        }
        echo $twig->render('candidatures.twig', ['candidatures' => $mes_candidatures]);
        break;

    case 'candidatures_pilotes':
        if (!isset($_SESSION['user_id']) || ($_SESSION['id_role'] != 2 && $_SESSION['id_role'] != 3)) {
            header("Location: index.php?page=connexion");
            exit();
        }
        // Ajout de o.lieu_offre et c.LM_candidature dans le SELECT pour le pilote
        $sql = "SELECT c.*, o.titre_offre, o.nom_entreprise, o.lieu_offre, u.nom as nom_etudiant, u.prenom as prenom_etudiant 
                FROM CANDIDATURES c 
                JOIN OFFRE o ON c.id_offre = o.id_offre 
                JOIN UTILISATEUR u ON c.id_utilisateur = u.id_utilisateur 
                ORDER BY c.date_candidature DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $toutes_candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('candidatures_pilotes.twig', ['candidatures' => $toutes_candidatures]);
        break;

    case 'powerpoint':
        echo $twig->render('powerpoint.twig');
        break;
}