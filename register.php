<?php
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
        die("Format de date incorrect !");
    }
    $date = $date->format('Y-m-d');

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // insertion utilisateur
        $sql = "INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $password]);

        // récupérer l'id utilisateur créé
        $id_utilisateur = $pdo->lastInsertId();

        // insertion coordonnées
        $sql2 = "INSERT INTO COORDONNEES 
        (ville_coordonnees, telephone_coordonnees, sexe_coordonnees, date_naissance_coordonnees, id_utilisateur)
        VALUES (?, ?, ?, ?, ?)";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$ville, $telephone, $sexe, $date, $id_utilisateur]);

        $pdo->commit();
        header("Location: connexion.php");
        exit;
    } 
    catch (Exception $e) {

        $pdo->rollBack();
        echo "Erreur : " . $e->getMessage();

    }

}
?>