<?php
session_start(); // 1. AJOUT : Obligatoire pour lire les infos de connexion
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

// 2. LA LIGNE MAGIQUE À PLACER ICI :
// Elle donne accès à la variable "session" dans TOUS tes fichiers .twig
$twig->addGlobal('session', $_SESSION); 

$page = $_GET['page'] ?? 'accueil';

switch ($page) {
    case 'accueil':
        echo $twig->render('index.twig');
        break;

    case 'offres':
        echo $twig->render('offres.twig');
        break;
    
    case 'confidentialite':
        echo $twig->render('confidentialite.twig');
        break;
    
    case 'mentions-legales':
        echo $twig->render('mentions-legales.twig');
        break;
    
    case 'entreprises':
        echo $twig->render('entreprises.twig');
        break;
}