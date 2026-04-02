<?php
// On indique que ce fichier renvoie du JSON
header('Content-Type: application/json; charset=utf-8');

// 1. On inclut ton fichier de connexion à la base de données
// (Assure-toi que le chemin vers config.php est correct)
require_once 'config.php'; 

// 2. Dictionnaire de coordonnées de secours
// Comme ta BDD n'a pas de lat/lon, on fait correspondre les noms avec des coordonnées connues.
// /!\ Il faudra ajouter ici les villes et pays qui existent dans ta vraie base de données.
$coordonnees_connues = [
    // --- PAYS ---
    "France" => ["lat" => 46.2276, "lon" => 2.2137],
    "Etats-Unis" => ["lat" => 37.0902, "lon" => -95.7129],
    "Australie" => ["lat" => -25.2744, "lon" => 133.7751],
    "Japon" => ["lat" => 36.2048, "lon" => 138.2529],
    "Canada" => ["lat" => 56.1304, "lon" => -106.3468],
    
    // --- VILLES ---
    "Paris" => ["lat" => 48.8566, "lon" => 2.3522],
    "Lyon" => ["lat" => 45.7640, "lon" => 4.8357],
    "Bordeaux" => ["lat" => 44.8378, "lon" => -0.5792],
    "Orléans" => ["lat" => 47.9029, "lon" => 1.9092],
    "Toulouse" => ["lat" => 43.6047, "lon" => 1.4442],
    "New York" => ["lat" => 40.7128, "lon" => -74.0060],
    "Tokyo" => ["lat" => 35.6895, "lon" => 139.6917],
    "Sydney" => ["lat" => -33.8688, "lon" => 151.2093]
];

try {
    // 3. Récupérer le nombre d'offres par PAYS
    // On fait une jointure entre OFFRE et ENTREPRISE pour récupérer le pays de l'entreprise qui recrute
    $stmtPays = $pdo->query("
        SELECT e.pays_entreprise AS nom, COUNT(o.id_offre) AS nb_offres 
        FROM OFFRE o
        JOIN ENTREPRISE e ON o.nom_entreprise = e.nom_entreprise 
        WHERE e.pays_entreprise IS NOT NULL AND e.pays_entreprise != ''
        GROUP BY e.pays_entreprise
    ");
    $resultatsPays = $stmtPays->fetchAll(PDO::FETCH_ASSOC);

    // 4. Récupérer le nombre d'offres par VILLE
    $stmtVilles = $pdo->query("
        SELECT e.ville_entreprise AS nom, COUNT(o.id_offre) AS nb_offres 
        FROM OFFRE o
        JOIN ENTREPRISE e ON o.nom_entreprise = e.nom_entreprise 
        WHERE e.ville_entreprise IS NOT NULL AND e.ville_entreprise != ''
        GROUP BY e.ville_entreprise
    ");
    $resultatsVilles = $stmtVilles->fetchAll(PDO::FETCH_ASSOC);

    // 5. On prépare les tableaux finaux en ajoutant les coordonnées
    $donnees_pays = [];
    foreach ($resultatsPays as $pays) {
        $nom = ucfirst(strtolower(trim($pays['nom']))); // Nettoyage (ex: "FRANCE" devient "France")
        
        // Si on connait les coordonnées de ce pays dans notre dictionnaire
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
    // En cas d'erreur SQL, on renvoie l'erreur proprement au JavaScript
    http_response_code(500);
    echo json_encode(["erreur" => "Erreur de base de données : " . $e->getMessage()]);
}
?>