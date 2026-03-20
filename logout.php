<?php
session_start(); // On rejoint la session actuelle
session_unset(); // On vide toutes les variables de session
session_destroy(); // On détruit la session sur le serveur

// On redirige vers l'accueil
header('Location: index.php');
exit();
?>