<?php
// Vérification : Seuls les pilotes (rôle 2) ou admins (rôle 3) peuvent accéder
if (!isset($_SESSION['user_id']) || ($_SESSION['id_role'] != 2 && $_SESSION['id_role'] != 3)) {
    header("Location: index.php?page=connexion");
    exit();
}

// Requête pour voir TOUTES les candidatures avec les noms des étudiants
$sql = "SELECT c.id_candidature, c.date_candidature, c.CV_candidature, c.LM_candidature,
               o.titre_offre, e.nom_entreprise, 
               u.nom AS nom_etudiant, u.prenom AS prenom_etudiant, u.email AS email_etudiant
        FROM CANDIDATURES c
        JOIN OFFRES o ON c.id_offre = o.id_offre
        JOIN ENTREPRISE e ON o.id_entreprise = e.id_entreprise
        JOIN UTILISATEUR u ON c.id_utilisateur = u.id_utilisateur
        ORDER BY c.date_candidature DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$toutes_candidatures = $stmt->fetchAll();

echo $twig->render('candidatures_pilotes.twig', [
    'candidatures' => $toutes_candidatures,
    'session' => $_SESSION
]);