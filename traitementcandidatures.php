<?php
// Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=connexion");
    exit();
}

$user_id = $_SESSION['user_id'];

// Requête pour récupérer les candidatures avec les infos de l'offre et de l'entreprise
$sql = "SELECT c.id_candidature, c.date_candidature, c.CV_candidature, 
               o.titre_offre, e.nom_entreprise, o.lieu_offre
        FROM CANDIDATURES c
        JOIN OFFRES o ON c.id_offre = o.id_offre
        JOIN ENTREPRISE e ON o.id_entreprise = e.id_entreprise
        WHERE c.id_utilisateur = ?
        ORDER BY c.date_candidature DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$mes_candidatures = $stmt->fetchAll();

// On envoie à Twig
echo $twig->render('traitementcandidatures.twig', [
    'candidatures' => $mes_candidatures,
    'session' => $_SESSION
]);