<?php
require_once __DIR__.'/vendor/autoload.php'; #Cette ligne charge automatiquement toutes les librairies installées avec Composer, notamment Twig.
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates'); #DIR  représente le dossier du fichier actuel donc cette ligne dit que les fichiers Twig sont dans le dossier templates
$twig = new \Twig\Environment($loader);
#Cette ligne crée l’environnement Twig.

#C’est l’objet principal qui va :

    #lire les templates

    #interpréter Twig

    #générer le HTML final

echo $twig->render('index.twig'); #affiche la page index.twig