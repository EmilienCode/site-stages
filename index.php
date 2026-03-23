<?php
session_start(); //Obligatoire pour lire les infos de connexion
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php'; 

use App\Models\UtilisateurModel;
use App\Models\EntrepriseModel;
use App\Models\OffresModel;
use App\Controlers\UtilisateurControleur;
use App\Controlers\EntrepriseControleur;
use App\Controlers\OffresControleur;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

// Elle donne accès à la variable "session" dans TOUS tes fichiers .twig
$twig->addGlobal('session', $_SESSION); 
$userRole = $_SESSION['role'] ?? null;

$page = $_GET['page'] ?? 'accueil';

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
        $controleur->pagination();
        break;

    case 'postuler':

        $offresModel = new OffresModel($pdo);
        $controleur = new OffresControleur($offresModel, $twig);
        $controleur->afficherOffre();
        break;
    
    case 'afficher_utilisateur':
    case 'modifier_utilisateur':
    case 'supprimer_utilisateur':

        $userModel = new UtilisateurModel($pdo);
        $controleur = new UtilisateurControleur($userModel, $twig);

        // On appelle la méthode correspondante à la page
        if ($page === 'afficher_utilisateur') $controleur->afficherUtilisateurs();
        if ($page === 'modifier_utilisateur') $controleur->modifierUtilisateur();
        if ($page === 'supprimer_utilisateur') $controleur->supprimerUtilisateur();
        break;

    case 'connexion':
        echo $twig->render('connexion.twig');
        break;
    
    case 'creercompte':
        echo $twig->render('creercompte.twig');
        break;

    case 'contact':
        echo $twig->render('contact.twig');
        break;

    case 'merci-candidature':
        echo $twig->render('merci-candidature.twig');
        break;
        
    case 'wishlist':
        echo $twig->render('wishlist.twig');
        break;
}