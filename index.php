<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php'; #Cette ligne charge automatiquement toutes les librairies installées avec Composer, notamment Twig.
require_once __DIR__.'/config.php'; 

require_once __DIR__.'/src/Models/EntrepriseModel.php';
require_once __DIR__.'/src/Controlers/EntrepriseControleur.php';

require_once __DIR__.'/src/Models/OffresModel.php';
require_once __DIR__.'/src/Controlers/OffresControleur.php';

require_once __DIR__.'/src/Models/UtilisateurModel.php';
require_once __DIR__.'/src/Controlers/AdminControleur.php';

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
    
    case 'admin_utilisateur':
        // 1. On crée le Modèle
        $userModel = new UtilisateurModel($pdo);

        // 2. On injecte le Modèle et Twig dans le Contrôleur
        $controleur = new AdminControleur($userModel, $twig);

        // 3. On lance l'action
        $controleur->afficherUtilisateurs();
        break;        
}