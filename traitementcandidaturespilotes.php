<?php
// Vérification : Seuls les pilotes (rôle 2)
if (!isset($_SESSION['user_id']) || ($_SESSION['id_role'] != 2)) {
    header("Location: index.php?page=connexion");
    exit();
}

// Requête pour voir TOUTES les candidatures avec les noms des étudiants
$sql = "SELECT c.id_candidature, c.date_candidature, c.CV_candidature, c.LM_candidature,
            o.titre_offre, o.nom_entreprise, 
            u.nom AS nom_etudiant, u.prenom AS prenom_etudiant, u.email AS email_etudiant
        FROM CANDIDATURES c
        JOIN OFFRE o ON c.id_offre = o.id_offre
        JOIN UTILISATEUR u ON c.id_utilisateur = u.id_utilisateur
        WHERE u.id_pilote_referent = :pilote_id
        ORDER BY c.date_candidature DESC;";

$stmt = $pdo->prepare($sql);
$stmt->execute(['pilote_id' => $_SESSION['user_id']]);
$toutes_candidatures = $stmt->fetchAll();

echo $twig->render('candidatures_pilotes.twig', [
    'candidatures' => $toutes_candidatures,
    'session' => $_SESSION,
    'pilote_id' => $_SESSION['user_id']

]);