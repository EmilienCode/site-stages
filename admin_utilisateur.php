<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php'; 
require_once __DIR__.'/src/Models/UtilisateurModel.php';
require_once __DIR__.'/src/Controlers/AdminControleur.php';

// Initialisation Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

// 1. On crée le Modèle
$userModel = new UtilisateurModel($pdo);

// 2. On injecte le Modèle et Twig dans le Contrôleur
$controleur = new AdminControleur($userModel, $twig);

// 3. On lance l'action
$controleur->afficherUtilisateurs();