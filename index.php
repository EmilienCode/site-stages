<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/vendor/autoload.php'; #Cette ligne charge automatiquement toutes les librairies installées avec Composer, notamment Twig.
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates'); #DIR  représente le dossier du fichier actuel donc cette ligne dit que les fichiers Twig sont dans le dossier templates
$twig = new \Twig\Environment($loader);
#Cette ligne crée l’environnement Twig.

#C’est l’objet principal qui va :

    #lire les templates

    #interpréter Twig

    #générer le HTML final


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