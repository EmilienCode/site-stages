<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '/var/www/cesitonstage.fr/site-stages/vendor/autoload.php';


require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Models/OffresModel.php';
require_once __DIR__ . '/src/Controlers/OffresControleur.php';


use App\Controlers\OffresControleur;


$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

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

    case 'offres':
        $model = new OffresModel($pdo); 
        $controleur = new OffresControleur($model, $twig);
        $controleur->pagination();
        break;
}