<?php
// On démarre la session au tout début
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['type']) && $_POST['type'] === "COMPTE") {

    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $ville = $_POST["ville"];
    $telephone = $_POST["telephone"];
    $sexe = $_POST["sexe"];
    
    $date = DateTime::createFromFormat('d/m/Y', $_POST['date_naissance']);
    if (!$date) {
        // Au lieu de die(), on pourrait rediriger avec une erreur
        header("Location: index.php?page=creercompte&error=date_format");
        exit;
    }
    $date = $date->format('Y-m-d');

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // 1. Insertion utilisateur
        $sql = "INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, id_role)
                VALUES (?, ?, ?, ?, 2)"; // On force le rôle 2 (Étudiant) par exemple

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $password]);

        // 2. Récupérer l'id utilisateur créé
        $id_utilisateur = $pdo->lastInsertId();

        // 3. Insertion coordonnées
        $sql2 = "INSERT INTO COORDONNEES 
        (ville_coordonnees, telephone_coordonnees, sexe_coordonnees, date_naissance_coordonnees, id_utilisateur)
        VALUES (?, ?, ?, ?, ?)";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$ville, $telephone, $sexe, $date, $id_utilisateur]);

        $pdo->commit();

        // --- CONNEXION AUTOMATIQUE ---
        // On remplit la session avec les infos qu'on vient d'utiliser
        $_SESSION['user_id'] = $id_utilisateur;
        $_SESSION['user_nom'] = $nom;
        $_SESSION['id_role'] = 2; // Le rôle défini plus haut

        // Redirection vers l'accueil
        header("Location: index.php?success=welcome");
        exit;

    } 
    catch (Exception $e) {
        $pdo->rollBack();
        
        // Gestion spécifique si l'email existe déjà (Erreur 23000)
        if ($e->getCode() == 23000) {
            header("Location: index.php?page=creercompte&error=email_taken");
        } else {
            echo "Erreur : " . $e->getMessage();
        }
        exit;
    }
}
?>