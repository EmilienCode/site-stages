<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $LM = htmlspecialchars($_POST['LM']);
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

        $sql = "INSERT INTO CANDIDATURES (LM_candidature, CV_candidature)
                VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$LM, $targetFile]);

        $id = $pdo->lastInsertId();
        header("Location: merci-candidature.php?id=" . $id);
        exit();

    } else {
        echo "Erreur upload : ";
        print_r(error_get_last());
    }
}
?>