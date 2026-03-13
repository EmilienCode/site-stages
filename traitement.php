<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $Nom = htmlspecialchars($_POST['Nom']);
    $Prenom = htmlspecialchars($_POST['Prenom']);
    $Email = htmlspecialchars($_POST['Email']);
    $Tel = htmlspecialchars($_POST['Tel']);
    $LM = htmlspecialchars($_POST['LM']);
    $raison = htmlspecialchars($_POST['raison']);
    $Details = htmlspecialchars($_POST['Details']);
    // Upload CV
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

    if (move_uploaded_file($CV["tmp_name"], $targetFile)) {

        $sql = "INSERT INTO Formulaire (Nom, Prenom, Email, Tel, LM, DestinationCV)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$Nom, $Prenom, $Email, $Tel, $LM, $targetFile]);

        $id = $pdo->lastInsertId();
        header("Location: merci-candidature.php?id=" . $id);
        exit();

    } else {
        echo "Erreur upload : ";
        print_r(error_get_last());
    }
}
if ($type === "contact") {

    $Nom = htmlspecialchars($_POST['Nom']);
    $Prenom = htmlspecialchars($_POST['Prenom']);
    $Email = htmlspecialchars($_POST['Email']);
    $raison = htmlspecialchars($_POST['Raison']);
    $Details = htmlspecialchars($_POST['Details']);

    $sql = "INSERT INTO Contact (Nom, Prenom, Email, Raison, Details)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$Nom, $Prenom, $Email, $Raison, $Details]);

    $id = $pdo->lastInsertId();
    header("Location: merci-candidature.php?id=" . $id);
    exit();

}
?>