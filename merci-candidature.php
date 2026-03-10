<?php
require 'config.php';

if (!isset($_GET['id'])) {
    die("Aucune candidature trouvée.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM Formulaire WHERE id = ?");
$stmt->execute([$id]);
$candidature = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidature) {
    die("Candidature introuvable.");
}
?>

<?php include "includes/header.php"; ?>
<link rel="stylesheet" href="assets/css/stylepostuler.css"/>
<main class="page">

    <section class="card thank-card">
        <div class="thank-icon">✔</div>

        <h1>Merci pour votre candidature !</h1>

        <p>
            Votre candidature a bien été prise en compte.<br>
            L’entreprise analysera votre profil et vous contactera si votre
            candidature correspond au poste.
        </p>
        <div class="card" style="margin-top:20px; text-align:left;">
            <h3><strong>Résumé de votre candidature :</strong></h3>
            <div style="padding-left:20px">
                <ul><li><strong>Nom :</strong> <?= $candidature['Nom'] ?></li>
                <li><strong>Prénom :</strong> <?= $candidature['Prenom'] ?></li>
                <li><strong>Email :</strong> <?= $candidature['Email'] ?></li>
                <li><strong>Téléphone :</strong> <?= $candidature['Tel'] ?></li>
                <li><strong>Lettre :</strong><br><?= nl2br($candidature['LM']) ?></li>
                </ul>
            </div>
        </div>

        <div class="thank-actions">
            <a href="index.html" class="btn btn-secondary">Retour à l’accueil</a>
            <a href="offres.html" class="btn btn-primary">Voir d’autres offres</a>
        </div>
    </section>

</main>

<?php include "includes/footer.php"; ?>
