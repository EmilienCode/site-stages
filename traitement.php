<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $Nom = htmlspecialchars($_POST['Nom']);
    $Prénom = htmlspecialchars($_POST['Prénom']);
    $Email = htmlspecialchars($_POST['Email']);
    $Tel = htmlspecialchars($_POST['Tel']);
    $LM = htmlspecialchars($_POST['LM']);

    // Gestion upload PDF
    $CV = $_FILES['CV'];
    $CVName = time() . "_" . basename($CV["name"]);
    $targetDir = "uploads/";
    $targetFile = $targetDir . $CVName;

    if ($CV["type"] != "application/pdf") {
        die("Le fichier doit être un PDF.");
    }

    if ($CV["size"] > 2 * 1024 * 1024) {
        die("Fichier trop volumineux.");
    }

    move_uploaded_file($CV["tmp_name"], $targetFile);

    // Insertion en BDD
    $sql = "INSERT INTO candidatures (Nom, Prénom, Email, Tel, LM, CV)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$Nom, $Prénom, $Email, $Tel, $LM, $CVName]);

    // Redirection avec ID
    $id = $pdo->lastInsertId();
    header("Location: merci-candidature.php?id=" . $id);
    exit();
}
?>