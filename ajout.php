<?php
if ($page === 'wishlist') {
    $id_offre = $_GET['id'] ?? null;
    $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;

    if ($id_offre && $id_utilisateur) {
        $check = $bdd->prepare("SELECT * FROM MET_EN_FAVORI WHERE id_utilisateur = ? AND id_offre = ?");
        $check->execute([$id_utilisateur, $id_offre]);

        if ($check->rowCount() == 0) {
            $ins = $bdd->prepare("INSERT INTO MET_EN_FAVORI (id_utilisateur, id_offre) VALUES (?, ?)");
            $ins->execute([$id_utilisateur, $id_offre]);
        }
    }
    header("Location: index.php?page=offres");
    exit();
}
?>