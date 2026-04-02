<?php
$id_utilisateur = $_SESSION['user_id'] ?? null;
$offres_favoris = [];
// Si l'utilisateur est connecté, on récupère ses offres en favoris
if ($id_utilisateur) {
    try {
        $sql = "SELECT o.* FROM OFFRE o 
                JOIN MET_EN_FAVORI f ON o.id_offre = f.id_offre 
                WHERE f.id_utilisateur = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_utilisateur]);
        $offres_favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        die("Erreur Wishlist : " . $e->getMessage());
    }
}

echo $twig->render('wishlist.twig', ['offres' => $offres_favoris]);