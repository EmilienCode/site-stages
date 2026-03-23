<?php
session_start();
require_once 'config.php'; // On récupère $pdo

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // On cherche l'utilisateur
    $query = $pdo->prepare("SELECT * FROM UTILISATEUR WHERE email = :email");
    $query->execute(['email' => $email]);
    $user = $query->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // SUCCESS : On crée la session
        $_SESSION['user_id'] = $user['id_utilisateur'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['id_role'] = $user['id_role'];

        header('Location: index.php?success=1');
        exit();
    } else {
        // ERREUR : Identifiants incorrects
        header('Location: index.php?page=connexion&error=1');
        exit();
    }
}
?>