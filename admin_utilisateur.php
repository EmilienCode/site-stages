<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/src/Controlers/AdminControleur.php';

// Initialisation Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

// Lancement du contrôleur
$controleur = new AdminControleur($pdo, $twig);
$controleur->afficherUtilisateurs();
?>