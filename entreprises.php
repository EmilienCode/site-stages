<?php include "includes/header.php"; ?>
<link rel="stylesheet" href="assets/css/styleentreprises.css"/>
    <h2 class="entreprisestitre">ENTREPRISES</h2>
    <?php
    $entreprises = [
    ['nom' => 'TechCorp', 'secteur' => 'Technologie', 'ville' => 'Paris'],
    ['nom' => 'FinSoft', 'secteur' => 'Finance', 'ville' => 'Londres'],
    ['nom' => 'TechMedia', 'secteur' => 'Technologie', 'ville' => 'Genève'],
    ['nom' => 'NexusSystems', 'secteur' => 'BTP', 'ville' => 'Milan'],
    ['nom' => 'BrightPartner', 'secteur' => 'Technologie', 'ville' => 'Madrid'],
    ['nom' => 'NexusMedia', 'secteur' => 'Énergie', 'ville' => 'New York'],
    ['nom' => 'PrimeLabs', 'secteur' => 'Assurance', 'ville' => 'Marseille'],
    ['nom' => 'SwiftGroup', 'secteur' => 'Technologie', 'ville' => 'Bruxelles'],
    ['nom' => 'QuantumGroup', 'secteur' => 'Transport', 'ville' => 'Munich'],
    ['nom' => 'NexusLogic', 'secteur' => 'Tourisme', 'ville' => 'Barcelone'],
    ['nom' => 'BluePoint', 'secteur' => 'BTP', 'ville' => 'Montréal'],
    ['nom' => 'BrightPartner', 'secteur' => 'Transport', 'ville' => 'Montréal'],
    ['nom' => 'QuantumPoint', 'secteur' => 'Tourisme', 'ville' => 'Toulouse'],
    ['nom' => 'NetMedia', 'secteur' => 'BTP', 'ville' => 'Genève'],
    ['nom' => 'GlobalGroup', 'secteur' => 'Assurance', 'ville' => 'Marseille'],
    ['nom' => 'FutureLabs', 'secteur' => 'BTP', 'ville' => 'Londres'],
    ['nom' => 'FutureIndustries', 'secteur' => 'Santé', 'ville' => 'Tokyo'],
    ['nom' => 'TechSolutions', 'secteur' => 'Finance', 'ville' => 'Milan'],
    ['nom' => 'BlueHub', 'secteur' => 'Finance', 'ville' => 'Paris'],
    ['nom' => 'ZenithIndustries', 'secteur' => 'Immobilier', 'ville' => 'Paris'],
    ['nom' => 'ZenithPartner', 'secteur' => 'Agroalimentaire', 'ville' => 'Rome'],
    ['nom' => 'SoftMedia', 'secteur' => 'Assurance', 'ville' => 'Barcelone'],
    ['nom' => 'AlphaInc', 'secteur' => 'Éducation', 'ville' => 'Bruxelles'],
    ['nom' => 'NetPoint', 'secteur' => 'Assurance', 'ville' => 'Genève'],
    ['nom' => 'OmegaGroup', 'secteur' => 'Assurance', 'ville' => 'New York'],
    ['nom' => 'NovaMedia', 'secteur' => 'Énergie', 'ville' => 'Montréal'],
    ['nom' => 'NovaFlow', 'secteur' => 'Aéronautique', 'ville' => 'Lyon'],
    ['nom' => 'GreenVentures', 'secteur' => 'Immobilier', 'ville' => 'Milan'],
    ['nom' => 'SmartSystems', 'secteur' => 'Environnement', 'ville' => 'Barcelone'],
    ['nom' => 'SwiftPartner', 'secteur' => 'Agroalimentaire', 'ville' => 'Tokyo'],
    ['nom' => 'AlphaWorks', 'secteur' => 'Aéronautique', 'ville' => 'Milan'],
    ['nom' => 'SwiftWorks', 'secteur' => 'Éducation', 'ville' => 'Lille'],
    ['nom' => 'FutureCorp', 'secteur' => 'Santé', 'ville' => 'Munich'],
    ['nom' => 'BlueVentures', 'secteur' => 'Éducation', 'ville' => 'Toulouse'],
    ['nom' => 'SoftLogic', 'secteur' => 'Assurance', 'ville' => 'Nice'],
    ['nom' => 'TechHub', 'secteur' => 'Éducation', 'ville' => 'Paris'],
    ['nom' => 'FutureVentures', 'secteur' => 'Finance', 'ville' => 'Marseille'],
    ['nom' => 'SoftLogic', 'secteur' => 'Transport', 'ville' => 'Lyon'],
    ['nom' => 'OmegaLabs', 'secteur' => 'BTP', 'ville' => 'Tokyo'],
    ['nom' => 'CloudPartner', 'secteur' => 'Immobilier', 'ville' => 'Nantes'],
    ['nom' => 'NexusWorks', 'secteur' => 'Immobilier', 'ville' => 'Nice'],
    ['nom' => 'GreenCorp', 'secteur' => 'Finance', 'ville' => 'New York'],
    ['nom' => 'ZenithMedia', 'secteur' => 'BTP', 'ville' => 'Marseille'],
    ['nom' => 'ZenithHub', 'secteur' => 'Tourisme', 'ville' => 'Barcelone'],
    ['nom' => 'SmartSolutions', 'secteur' => 'Technologie', 'ville' => 'Genève'],
    ['nom' => 'FutureFlow', 'secteur' => 'Transport', 'ville' => 'Munich'],
    ['nom' => 'GreenSystems', 'secteur' => 'Immobilier', 'ville' => 'Nantes'],
    ['nom' => 'NetFlow', 'secteur' => 'Immobilier', 'ville' => 'New York'],
    ['nom' => 'OmegaLabs', 'secteur' => 'Assurance', 'ville' => 'Munich'],
    ['nom' => 'QuantumFlow', 'secteur' => 'Technologie', 'ville' => 'Barcelone'],
    ['nom' => 'ZenithHub', 'secteur' => 'Transport', 'ville' => 'Toulouse'],
    ['nom' => 'ApexInc', 'secteur' => 'Assurance', 'ville' => 'Munich'],
    ];
    // Nombre d'entreprises par page
    $parPage = 10;
    // Récupérer la page depuis l'URL, par défaut page 1
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    // Calculer l'index de départ
    $start = ($page - 1) * $parPage;
    // Extraire les entreprises à afficher sur cette page
    $entreprisesPage = array_slice($entreprises, $start, $parPage);
    ?>
    <div class="entreprises-container">
    <?php $delay = 0; ?>
    <?php foreach($entreprisesPage as $e): ?>
        <div class="entreprise-card">
            <h3 class="entreprise-nom"><?= htmlspecialchars($e['nom']) ?></h3>
            <p><strong>Secteur :</strong> <?= htmlspecialchars($e['secteur']) ?></p>
            <p><strong>Ville :</strong> <?= htmlspecialchars($e['ville']) ?></p>
        </div>
        <?php 
        $delay += 100; // Ajoute 100ms de délai entre chaque carte
        if($delay > 500) $delay = 0; // Réinitialise pour ne pas avoir un délai trop long sur les dernières cartes
        ?>
    <?php endforeach; ?>
    </div>

    <div class="pagination">
    <?php if($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="pagination-btn">Précédent</a>
    <?php endif; ?>
    <?php if($start + $perPage < count($entreprises)): ?>
        <a href="?page=<?= $page + 1 ?>" class="pagination-btn">Suivant</a>
    <?php endif; ?>
    </div>
<script src="assets/js/scroll-animation.js"></script>
<?php include "includes/footer.php"; ?>
