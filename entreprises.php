<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styleentreprises.css"/>
    <link rel="stylesheet" href="assets/css/styleAnimationLogoNavBarre.css"/>
    <link rel="stylesheet" href="assets/css/styleToogleThemeMode.css"/>
    <link rel="stylesheet" href="assets/css/styleDarkMode.css">
    <meta charset="utf-8" />
    <title>CESITonStage - Entreprises</title>
    <link
    href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Sonsie+One"
    rel="stylesheet"
    type="text/css" />
    <!-- Les trois lignes ci‑dessous sont un correctif pour que la sémantique
        HTML5 fonctionne correctement avec les anciennes versions de
        Internet Explorer-->
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
    <header>
        <nav class="nav">
            <div class="navInside">
                <div class="NomLogoNav">
                    <div class="LogoNav">
                        <a href="index.html"><svg xmlns="http://www.w3.org/2000/svg"  alt="Logo plateforme" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="LOGO" aria-hidden="true"> 
                            <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            <rect width="20" height="14" x="2" y="6" rx="2"></rect>
                        </svg></a> 
                    </div>
                    <div class="NomNav">
                        <a class = "logoAcc" href= index.html> CESITonStage </a> 
                    </div>
                </div>
            <div class="BtnNavOutside">
                    <a href="index.html" class="BtnNavInside">Accueil</a>
                    <a href="offres.html" class="BtnNavInside">Offres</a>
                    <a href="entreprises.php" class="BtnNavInside">Entreprise</a>
            </div>
                <div class="logoDroitNav">
                    <button class="NotifBtn WishlistBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svgNotif" aria-hidden="true">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.78-8.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        <span class="wishlist-text">Wishliste</span>
                    </button>
                    <button class="NotifBtn NotificationBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svgNotif" aria-hidden="true">
                            <path d="M10.268 21a2 2 0 0 0 3.464 0"></path>
                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"></path>
                        </svg>
                        <span class="notif-text">Notifications</span>
                    </button>
                    <div class="CompteBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svgCompte" aria-hidden="true">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <!-- From Uiverse.io by Madflows --> 
                    <div class="toggle-switch">
                        <label class="switch-label">
                            <input type="checkbox" id="btn-theme-toogle">
                            <span class="slider"></span>
                        </label>
                    </div>  
                </div>
            </div>
        </nav>
    </header>
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
    <?php foreach($entreprisesPage as $e): ?>
        <div class="entreprise-card">
            <h3 class="entreprise-nom"><?= htmlspecialchars($e['nom']) ?></h3>
            <p><strong>Secteur :</strong> <?= htmlspecialchars($e['secteur']) ?></p>
            <p><strong>Ville :</strong> <?= htmlspecialchars($e['ville']) ?></p>
        </div>
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

    <footer>
        <div class="footerIn">
            <div class="footerlogonom">
                <div class="logofooter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svglogofooter" aria-hidden="true">
                        <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        <rect width="20" height="14" x="2" y="6" rx="2"></rect>
                    </svg>
                </div>
                <span class="nomfooter">CESITONSTAGE</span>
            </div>
            <div class="navfooter">
                <a href="mentions-legales.html" class="BtnNavInside">Mentions légales</a>
                <a href="confidentialite.html" class="BtnNavInside">Confidentialité</a>
                <a href="contact.html" class="BtnNavInside">Contact</a>
            </div>
            <p style="color: var(--color-light-gray); font-size: 0.875rem;">© 2026 CESITonStage from Crazy Industries. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="assets/js/darkToogle.js"></script>
</body>
</html>