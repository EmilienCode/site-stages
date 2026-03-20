<?php
session_start(); //Obligatoire pour lire les infos de connexion
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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
        // 1. On crée le Modèle
        $entrepriseModel = new EntrepriseModel($pdo);

        // 2. On injecte le Modèle et Twig dans le Contrôleur
        $controleur = new EntrepriseControleur($entrepriseModel, $twig);

        // 3. On lance l'action
        $controleur->pagination();
        break;
    
    case 'offres':
        // 1. On crée le Modèle
        $offresModel = new OffresModel($pdo);

        // 2. On injecte le Modèle et Twig dans le Contrôleur
        $controleur = new OffresControleur($offresModel, $twig);

        // 3. On lance l'action
        $controleur->pagination();
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

}