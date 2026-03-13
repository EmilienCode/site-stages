<?php
require_once __DIR__.'/vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);
echo $twig->render('offres.twig');