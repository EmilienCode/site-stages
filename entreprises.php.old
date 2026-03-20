<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php'; 
require_once __DIR__.'/src/Models/EntrepriseModel.php';
require_once __DIR__.'/src/Controlers/EntrepriseControleur.php';

// Initialisation Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

// 1. On crée le Modèle
$userModel = new EntrepriseModel($pdo);

// 2. On injecte le Modèle et Twig dans le Contrôleur
$controleur = new EntrepriseControleur($userModel, $twig);

// 3. On lance l'action
$controleur->pagination();