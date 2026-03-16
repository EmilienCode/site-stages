<?php 
// 1. FORCE L'AFFICHAGE DES ERREURS POUR COMPRENDRE LE CRASH
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "config.php";
include "includes/header.php"; 
?>

<link rel="stylesheet" href="assets/css/styleentreprises.css"/>
<h2 class="entreprisestitre">ENTREPRISES</h2>

<?php
function getEntreprises($limit, $offset) {
    global $pdo; 
    try {
        $sql = "SELECT * FROM ENTREPRISE LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Si la requête échoue, on affiche l'erreur sans couper la page
        echo "<p style='color:red;'>Erreur SQL : " . $e->getMessage() . "</p>";
        return [];
    }
}

$p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($p < 1) $p = 1;
$parPage = 10;
$offset = ($p - 1) * $parPage;

// On récupère les données
$entreprises = getEntreprises($parPage, $offset);
?>

<div class="entreprises-container">
    <?php if (empty($entreprises)): ?>
        <p>Aucune entreprise trouvée.</p>
    <?php else: ?>
        <?php foreach($entreprises as $e): 
            $e = array_change_key_case($e, CASE_LOWER); 
        ?>
            <div class="entreprise-card">
                <div class="card-body-content">
                    
                    <div class="card-header-simple">
                        <h3 class="entreprise-nom"><?= htmlspecialchars($e['nom_entreprise'] ?? 'Nom inconnu') ?></h3>
                        <p class="ville-label">📍 <?= htmlspecialchars($e['ville_entreprise'] ?? 'N/A') ?></p>
                    </div>

                    <div class="details-cache">
                        <div class="details-content">
                            <p class="description">
                                <strong>À propos :</strong><br>
                                <?= nl2br(htmlspecialchars($e['description_entreprise'] ?? 'Aucune description.')) ?>
                            </p>

                            <div class="info-grid">
                                <div class="info-item"><strong>🆔 SIRET :</strong> <?= htmlspecialchars($e['siret_entreprise'] ?? '/') ?></div>
                                <div class="info-item"><strong>📞 Tel :</strong> <?= htmlspecialchars($e['telephone_entreprise'] ?? '/') ?></div>
                                <div class="info-item"><strong>📧 Mail :</strong> <?= htmlspecialchars($e['email_entreprise'] ?? '/') ?></div>
                                <div class="info-item"><strong>📏 Taille :</strong> <?= htmlspecialchars($e['taille_entreprise'] ?? '/') ?></div>
                                <div class="info-item"><strong>💼 Secteur :</strong> <?= htmlspecialchars($e['secteur_entreprise'] ?? '/') ?></div>
                                <div class="info-item"><strong>🌍 Site Web :</strong> <?= htmlspecialchars($e['site_web_entreprise'] ?? '/') ?></div>
                            </div>

                            <div class="links-footer">
                                                               
                                <?php if(!empty($e['site_web_entreprise'])): ?>
                                    <a href="<?= htmlspecialchars($e['site_web_entreprise']) ?>" target="_blank" class="pagination-btn">Site Web</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="pagination">
    <?php if($p > 1): ?>
        <a href="?p=<?= $p - 1 ?>" class="pagination-btn">Précédent</a>
    <?php endif; ?>
    
    <?php if(count($entreprises) >= $parPage): ?>
        <a href="?p=<?= $p + 1 ?>" class="pagination-btn">Suivant</a>
    <?php endif; ?>
</div>

<script src="assets/js/scroll-animation.js"></script>
<?php include "includes/footer.php"; ?>