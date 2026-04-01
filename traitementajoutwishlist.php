<?php
$id_offre = $_GET['id'] ?? null;
$id_utilisateur = $_SESSION['user_id'] ?? null;

if ($id_offre && $id_utilisateur) {
    try {
        $check = $pdo->prepare("SELECT * FROM MET_EN_FAVORI WHERE id_utilisateur = ? AND id_offre = ?");
        $check->execute([$id_utilisateur, $id_offre]);
        
        if ($check->rowCount() == 0) {
            $ins = $pdo->prepare("INSERT INTO MET_EN_FAVORI (id_utilisateur, id_offre) VALUES (?, ?)");
            $ins->execute([$id_utilisateur, $id_offre]);
        } else {
            $del = $pdo->prepare("DELETE FROM MET_EN_FAVORI WHERE id_utilisateur = ? AND id_offre = ?");
            $del->execute([$id_utilisateur, $id_offre]);
        }
    } catch (PDOException $e) {
        die("Erreur SQL Ajout : " . $e->getMessage());
    }
}

header("Location: index.php?page=offres");
exit();