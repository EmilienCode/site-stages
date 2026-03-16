<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/src/Models/UtilisateurModel.php';
require_once __DIR__.'/src/Controlers/AdminControleur.php';

// Initialisation Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

// Initialisation (Comme d'habitude)
$userModel = new UtilisateurModel($pdo);
$controleur = new AdminControleur($userModel, $twig);

// Lancement de l'action de suppression
$controleur->supprimerUtilisateur();
?>