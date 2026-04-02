<?php
namespace App\Models;

use PDO;
use Exception;

class EntrepriseModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    // Récupère la liste des entreprises avec pagination et filtres
    public function getEntreprises($limit, $offset, $nom = '', $taille = '', $secteur = '') {
        try {
            $sql = "SELECT * FROM ENTREPRISE WHERE 1=1 AND est_active_entreprise = 1";
            $params = [];
            // Ajout de conditions dynamiques selon les filtres
            if (!empty($nom)) {
                $sql .= " AND nom_entreprise LIKE :nom";
                $params[':nom'] = '%' . $nom . '%';
            }
            if (!empty($taille)) {
                $sql .= " AND taille_entreprise = :taille";
                $params[':taille'] = $taille;
            }
            if (!empty($secteur)) {
                $sql .= " AND secteur_entreprise = :secteur";
                $params[':secteur'] = $secteur;
            }

            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    // Compte le nombre total d'entreprises selon les mêmes filtres (pour la pagination)
    public function countEntreprises($nom = '', $taille = '', $secteur = '') {
        try {
            $sql = "SELECT COUNT(*) FROM ENTREPRISE WHERE 1=1 AND est_active_entreprise = 1";
            $params = [];
            // Ajout de conditions dynamiques selon les filtres
            if (!empty($nom)) {
                $sql .= " AND nom_entreprise LIKE :nom";
                $params[':nom'] = '%' . $nom . '%';
            }
            if (!empty($taille)) {
                $sql .= " AND taille_entreprise = :taille";
                $params[':taille'] = $taille;
            }
            if (!empty($secteur)) {
                $sql .= " AND secteur_entreprise = :secteur";
                $params[':secteur'] = $secteur;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
    // Récupère la liste de tous les secteurs d'activité distincts pour le menu déroulant
    public function getAllSecteurs() {
        try {
            $sql = "SELECT DISTINCT secteur_entreprise FROM ENTREPRISE WHERE secteur_entreprise IS NOT NULL AND secteur_entreprise != '' ORDER BY secteur_entreprise ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    // Récupère les détails d'une entreprise par son SIRET
    public function getEntrepriseBySiret($siret) {
        try {
            // On prépare la requête avec l'étoile (*) pour tout récupérer
            $query = "SELECT * FROM ENTREPRISE WHERE siret_entreprise = :siret";
            $stmt = $this->pdo->prepare($query);

            // On lie le paramètre et on exécute
            $stmt->execute(['siret' => $siret]);

            // On utilise fetch() pour avoir un tableau simple (une seule ligne)
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Erreur getEntrepriseBySiret : " . $e->getMessage());
            return false;
        }
    }
    // Récupère la liste de toutes les entreprises (sans pagination ni filtres)
    public function getAllEntreprise() {
        $sql = "
            SELECT 
                ENTREPRISE.siret_entreprise, 
                email_entreprise, 
                telephone_entreprise, 
                site_web_entreprise, 
                logo_entreprise, 
                date_inscription_entreprise, 
                nom_entreprise, 
                description_entreprise, 
                adresse_entreprise, 
                secteur_entreprise, 
                taille_entreprise, 
                linkedin_entreprise, 
                code_postal_entreprise, 
                ville_entreprise, 
                pays_entreprise, 
                est_active_entreprise,
                EVALUATION.note_evaluation
            FROM ENTREPRISE LEFT JOIN EVALUATION ON ENTREPRISE.siret_entreprise = EVALUATION.siret_entreprise;
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    // Supprime une entreprise
    public function deleteEntreprise($id) {
        $query = "
        DELETE FROM ENTREPRISE WHERE siret_entreprise = :id
        ";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
    // Affiche les détails d'une entreprise
    public function updateEntreprise($siret, $data) {
        try {
            // Une seule table à modifier : pas besoin de transaction ici
            $query = "
                UPDATE ENTREPRISE 
                SET 
                    nom_entreprise = :nom, 
                    email_entreprise = :email, 
                    telephone_entreprise = :tel, 
                    site_web_entreprise = :site, 
                    logo_entreprise = :logo, 
                    description_entreprise = :descr, 
                    adresse_entreprise = :addr, 
                    secteur_entreprise = :secteur, 
                    taille_entreprise = :taille, 
                    linkedin_entreprise = :linkedin, 
                    code_postal_entreprise = :cp, 
                    ville_entreprise = :ville, 
                    pays_entreprise = :pays, 
                    est_active_entreprise = :active
                WHERE siret_entreprise = :siret
            ";

            $stmt = $this->pdo->prepare($query);
            
            return $stmt->execute([
                'nom'      => $data['nom_entreprise'],
                'email'    => $data['email_entreprise'],
                'tel'      => $data['telephone_entreprise'],
                'site'     => $data['site_web_entreprise'],
                'logo'     => $data['logo_entreprise'],
                'descr'    => $data['description_entreprise'],
                'addr'     => $data['adresse_entreprise'],
                'secteur'  => $data['secteur_entreprise'],
                'taille'   => $data['taille_entreprise'],
                'linkedin' => $data['linkedin_entreprise'],
                'cp'       => $data['code_postal_entreprise'],
                'ville'    => $data['ville_entreprise'],
                'pays'     => $data['pays_entreprise'],
                'active'   => $data['est_active_entreprise'], // 0 ou 1 (tinyint)
                'siret'    => $siret
            ]);

        } catch (Exception $e) {
            // Log de l'erreur pour le debug
            error_log("Erreur updateEntreprise : " . $e->getMessage());
            return false;
        }
    }
    // Affiche les détails d'une entreprise
    public function inscrireEntreprise($data) {
        try {
            $query = "
                INSERT INTO ENTREPRISE (
                    siret_entreprise, 
                    email_entreprise, 
                    telephone_entreprise, 
                    site_web_entreprise, 
                    logo_entreprise, 
                    date_inscription_entreprise, 
                    nom_entreprise, 
                    description_entreprise, 
                    adresse_entreprise, 
                    secteur_entreprise, 
                    taille_entreprise, 
                    linkedin_entreprise, 
                    code_postal_entreprise, 
                    ville_entreprise, 
                    pays_entreprise, 
                    est_active_entreprise
                ) VALUES (
                    :siret, :email, :tel, :site, :logo, NOW(), :nom, :descr, 
                    :addr, :secteur, :taille, :linkedin, :cp, :ville, :pays, :active
                )
            ";

            $stmt = $this->pdo->prepare($query);

            return $stmt->execute([
                'siret'    => $data['siret_entreprise'],
                'email'    => $data['email_entreprise'],
                'tel'      => $data['telephone_entreprise'],
                'site'     => $data['site_web_entreprise'],
                'logo'     => $data['logo_entreprise'],
                'nom'      => $data['nom_entreprise'],
                'descr'    => $data['description_entreprise'],
                'addr'     => $data['adresse_entreprise'],
                'secteur'  => $data['secteur_entreprise'],
                'taille'   => $data['taille_entreprise'],
                'linkedin' => $data['linkedin_entreprise'],
                'cp'       => $data['code_postal_entreprise'],
                'ville'    => $data['ville_entreprise'],
                'pays'     => $data['pays_entreprise'],
                'active'   => $data['est_active_entreprise']
            ]);

        } catch (Exception $e) {
            // On propage l'exception pour que le contrôleur puisse attraper le code 23000 (SIRET en double)
            throw $e;
        }
    }
}