<?php
// On indique que ce fichier renvoie du JSON
header('Content-Type: application/json; charset=utf-8');

// 1. On inclut ton fichier de connexion à la base de données
require_once 'config.php'; 

// 2. Dictionnaire de coordonnées
// Ajout de TOUTES les villes présentes dans ta table OFFRE
$coordonnees_connues = [
    // --- PAYS ---
    "France" => ["lat" => 46.2276, "lon" => 2.2137],
    "Etats-unis" => ["lat" => 37.0902, "lon" => -95.7129],
    "Australie" => ["lat" => -25.2744, "lon" => 133.7751],
    "Japon" => ["lat" => 36.2048, "lon" => 138.2529],
    "Canada" => ["lat" => 56.1304, "lon" => -106.3468],
    
    // --- VILLES DE TA BASE DE DONNÉES ---
    "Paris" => ["lat" => 48.8566, "lon" => 2.3522], // Centre
    
    // BANLIEUE PARISIENNE (Coordonnées artificiellement écartées pour la 3D)
    "Clichy" => ["lat" => 49.60, "lon" => 2.20],      // Poussé vers le Nord
    "Levallois" => ["lat" => 49.30, "lon" => 1.70],   // Poussé vers le Nord-Ouest
    "Courbevoie" => ["lat" => 49.00, "lon" => 1.40],  // Poussé vers l'Ouest
    "Boulogne" => ["lat" => 48.60, "lon" => 1.40],    // Poussé vers le Sud-Ouest
    "Guyancourt" => ["lat" => 48.20, "lon" => 1.10],  // Poussé vers le Sud-Ouest lointain
    "Massy" => ["lat" => 47.90, "lon" => 2.10],       // Poussé vers le Sud
    "Ivry" => ["lat" => 48.20, "lon" => 3.20],        // Poussé vers le Sud-Est
    "Montreuil" => ["lat" => 48.80, "lon" => 3.30],   // Poussé vers l'Est
    
    // --- PROVINCE ---
    "Lille" => ["lat" => 50.6292, "lon" => 3.0573],
    "Lyon" => ["lat" => 45.7640, "lon" => 4.8357],
    "Bordeaux" => ["lat" => 44.8378, "lon" => -0.5792],
    "Toulouse" => ["lat" => 43.6047, "lon" => 1.4442],
    "Clermont-ferrand" => ["lat" => 45.7772, "lon" => 3.0870],
    "Pau" => ["lat" => 43.2951, "lon" => -0.3708],
    
    // --- AUTRES VILLES ---
    "Orléans" => ["lat" => 47.9029, "lon" => 1.9092],
    "New york" => ["lat" => 40.7128, "lon" => -74.0060],
    "Tokyo" => ["lat" => 35.6895, "lon" => 139.6917],
    "Sydney" => ["lat" => -33.8688, "lon" => 151.2093]
];

try {
    // 3. Récupérer le nombre d'offres par PAYS (On garde l'entreprise pour le pays)
    $stmtPays = $pdo->query("
        SELECT e.pays_entreprise AS nom, COUNT(o.id_offre) AS nb_offres 
        FROM OFFRE o
        JOIN ENTREPRISE e ON o.nom_entreprise = e.nom_entreprise 
        WHERE e.pays_entreprise IS NOT NULL AND e.pays_entreprise != ''
        GROUP BY e.pays_entreprise
    ");
    $resultatsPays = $stmtPays->fetchAll(PDO::FETCH_ASSOC);

    // 4. CORRECTION : Récupérer le nombre d'offres par VILLE (On utilise o.lieu_offre)
    $stmtVilles = $pdo->query("
        SELECT o.lieu_offre AS nom, COUNT(o.id_offre) AS nb_offres 
        FROM OFFRE o
        WHERE o.lieu_offre IS NOT NULL AND o.lieu_offre != ''
        GROUP BY o.lieu_offre
    ");
    $resultatsVilles = $stmtVilles->fetchAll(PDO::FETCH_ASSOC);

    // 5. On prépare les tableaux finaux en ajoutant les coordonnées
    $donnees_pays = [];
    foreach ($resultatsPays as $pays) {
        $nom = ucfirst(strtolower(trim($pays['nom']))); 
        
        if (isset($coordonnees_connues[$nom])) {
            $donnees_pays[] = [
                "nom" => $nom,
                "offres" => (int)$pays['nb_offres'],
                "lat" => $coordonnees_connues[$nom]['lat'],
                "lon" => $coordonnees_connues[$nom]['lon']
            ];
        }
    }

    $donnees_villes = [];
    foreach ($resultatsVilles as $ville) {
        $nom = ucfirst(strtolower(trim($ville['nom'])));
        
        if (isset($coordonnees_connues[$nom])) {
            $donnees_villes[] = [
                "nom" => $nom,
                "offres" => (int)$ville['nb_offres'],
                "lat" => $coordonnees_connues[$nom]['lat'],
                "lon" => $coordonnees_connues[$nom]['lon']
            ];
        }
    }

    // 6. On assemble et on renvoie le tout au format JSON
    echo json_encode([
        "pays" => $donnees_pays,
        "villes" => $donnees_villes
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erreur" => "Erreur de base de données : " . $e->getMessage()]);
}
?>