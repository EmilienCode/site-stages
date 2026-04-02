<?php
$id_utilisateur = $_SESSION['user_id'] ?? null;
$mes_candidatures = [];
// Si l'utilisateur est connecté, on récupère ses candidatures
if ($id_utilisateur) {
    try {
        $sql = "SELECT c.*, o.titre_offre, o.nom_entreprise, o.lieu_offre 
                FROM CANDIDATURES c 
                JOIN OFFRE o ON c.id_offre = o.id_offre 
                WHERE c.id_utilisateur = ? 
                ORDER BY c.date_candidature DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_utilisateur]);
        $mes_candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        die("Erreur Candidatures : " . $e->getMessage());
    }
}

echo $twig->render('candidatures.twig', ['candidatures' => $mes_candidatures]);