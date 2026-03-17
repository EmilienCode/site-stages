<?php
$host = "127.0.0.1";
$dbname = "CESITonStage.fr";
$user = "tom";
$pass = "MeilleurGroupe26!";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}
?>