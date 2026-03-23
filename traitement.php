<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';
session_start(); //Démarre la session pour accéder aux données utilisateur

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $LM = htmlspecialchars($_POST['LM'] ?? '');
    // htmlspecialchars() permet d’éviter les injections HTML / XSS
    $id_offre = $_POST['id_offre'] ?? null;
    // Récupère l'id de l'offre à laquelle l'utilisateur postule 
    // ?? null permet d'éviter une erreur si la variable n'existe pas
    if (!$id_offre) {
        die("Offre non spécifiée."); //si aucune offre n'est envoyée => erreur
    }
    $LM = htmlspecialchars($_POST['LM']);
    // Upload CV
    $CV = $_FILES['CV'];
    $CVName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($CV["name"]));
    $targetDir = "uploads/";
    $targetFile = $targetDir . $CVName;

    // Vérification type MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    // FILEINFO_MIME_TYPE permet de récupérer le type réel (ex: application/pdf)
    $mime = finfo_file($finfo, $CV["tmp_name"]); 
    //Analyse le fichier temporaire uploadé pour connaître son vrai type  (et non celui envoyé par le navigateur, qui peut être falsifié)
    if ($mime !== "application/pdf") {
        die("Le fichier doit être un PDF.");
    }

    if ($CV["size"] > 2 * 1024 * 1024) {
        die("Fichier trop volumineux.");
    }

    if (move_uploaded_file($CV["tmp_name"], $targetFile)) {

        $sql = "INSERT INTO CANDIDATURES (LM_candidature, CV_candidature, id_offre)
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$LM, $targetFile, $id_offre]);
        $id = $pdo->lastInsertId();
        header("Location: merci-candidature.php?id=" . $id);
        exit();

    } else {
        echo "Erreur upload : ";
        print_r(error_get_last());
    }

}
?>