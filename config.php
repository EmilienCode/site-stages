<?php
$host = "localhost";
$dbname = "CESITonStage";
$user = "tom";
$pass = "XTTom5145!";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}
?>