<?php
// On démarre la session au tout début
session_start();

// Affichage des erreurs pour le débogage (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['type']) && $_POST['type'] === "COMPTE") {

    // 1. Récupération et nettoyage des données de base
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $ville = trim($_POST["ville"]);
    $telephone = trim($_POST["telephone"]);
    $sexe = $_POST["sexe"]; // 0 ou 1
    
    // 2. Traitement sécurisé de la date
    $date_input = trim($_POST['date_naissance']);
    $date_obj = DateTime::createFromFormat('d/m/Y', $date_input);

    // On vérifie si la date est valide et correspond au format jj/mm/aaaa
    if (!$date_obj || $date_obj->format('d/m/Y') !== $date_input) {
        header("Location: index.php?page=creercompte&error=date_format");
        exit;
    }
    
    // Format de stockage standard pour MySQL : YYYY-MM-DD
    $date_naissance_sql = $date_obj->format('Y-m-d');

    try {
        // Configuration PDO pour lancer des exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Début de la transaction pour garantir l'intégrité (tout ou rien)
        $pdo->beginTransaction();

        // 3. Insertion dans la table UTILISATEUR
        $sql1 = "INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, id_role)
                 VALUES (?, ?, ?, ?, 1)"; // 1 = rôle Etudiant par défaut

        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([$nom, $prenom, $email, $password]);

        // récupérer l'id utilisateur créé
        $id_utilisateur = $pdo->lastInsertId();

        // 5. Insertion dans la table COORDONNEES
        $sql2 = "INSERT INTO COORDONNEES 
                (ville_coordonnees, telephone_coordonnees, sexe_coordonnees, date_naissance_coordonnees, id_utilisateur)
                VALUES (?, ?, ?, ?, ?)";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            $ville, 
            $telephone, 
            $sexe, 
            $date_naissance_sql, 
            $id_utilisateur
        ]);

        // Si tout est bon, on valide définitivement en base de données
        $pdo->commit();

        // 6. Connexion automatique après inscription
        $_SESSION['user_id'] = $id_utilisateur;
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $_SESSION['id_role'] = 2;

        // Redirection vers l'accueil avec un message de succès
        header("Location: index.php?success=welcome");
        exit;

    } 
    catch (Exception $e) {
        // En cas d'erreur, on annule tout ce qui a été fait dans la transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Gestion de l'erreur "Duplicate entry" (Email déjà utilisé)
        if ($e->getCode() == 23000) {
            header("Location: index.php?page=creercompte&error=email_taken");
        } else {
            // En développement, on affiche l'erreur. En production, préférez un message générique.
            echo "Erreur lors de l'inscription : " . $e->getMessage();
        }
        exit;
    }
} else {
    // Si on tente d'accéder au fichier sans passer par le formulaire POST
    header("Location: index.php?page=creercompte");
    exit;
}
?>