<?php
namespace App\Models;

use PDO;
use Exception;

class OffresModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // NOUVELLE MÉTHODE : Récupérer toutes les compétences pour le menu déroulant
    public function getAllCompetences() {
        try {
            $sql = "SELECT id_competence, nom_competence FROM COMPETENCES ORDER BY nom_competence ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // MISE À JOUR : Ajout du paramètre $competence_id et correction de lieu_offre
    public function getOffres($limit, $offset, $metier = '', $ville = '', $tri = 'recents', $competence_id = '') {
        try {
            $sql = "SELECT * FROM OFFRE WHERE 1=1";
            $params = [];

            if (!empty($metier)) {
                $sql .= " AND (titre_offre LIKE :metier OR description_offre LIKE :metier)";
                $params[':metier'] = '%' . $metier . '%';
            }
    
            if (!empty($ville)) {
                $sql .= " AND lieu_offre LIKE :ville"; // Corrigé : lieu_offre au lieu de ville
                $params[':ville'] = '%' . $ville . '%';
            }

            // Nouveau filtre par compétence
            if (!empty($competence_id)) {
                $sql .= " AND id_competence = :competence_id";
                $params[':competence_id'] = $competence_id;
            }

            switch ($tri) {
                case 'anciens': $sql .= " ORDER BY date_offre ASC"; break;
                case 'salaire_asc': $sql .= " ORDER BY remuneration_offre ASC"; break;
                case 'salaire_desc': $sql .= " ORDER BY remuneration_offre DESC"; break;
                case 'recents':
                default: $sql .= " ORDER BY date_offre DESC"; break;
            }

            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
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

    // MISE À JOUR : Ajout du paramètre $competence_id
    public function countOffres($metier = '', $ville = '', $competence_id = '') {
        try {
            $sql = "SELECT COUNT(*) FROM OFFRE WHERE 1=1";
            $params = [];

            if (!empty($metier)) {
                $sql .= " AND (titre_offre LIKE :metier OR description_offre LIKE :metier)";
                $params[':metier'] = '%' . $metier . '%';
            }

            if (!empty($ville)) {
                $sql .= " AND ville LIKE :ville"; // <-- Pareil, vérifie la colonne "ville"
                $params[':ville'] = '%' . $ville . '%';
            }

            // Nouveau filtre par compétence
            if (!empty($competence_id)) {
                $sql .= " AND id_competence = :competence_id";
                $params[':competence_id'] = $competence_id;
            }

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchColumn();

        } catch (Exception $e) {
            error_log($e->getMessage());
            return 0; 
        }
    }

    public function getOffreById($id) {
        $sql = "SELECT * FROM OFFRE WHERE id_offre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllEntrepriseMinInfos() {
        $query = "
            SELECT 
                nom_entreprise, 
                email_entreprise,
                secteur_entreprise, 
                EVALUATION.note_evaluation
            FROM ENTREPRISE LEFT JOIN EVALUATION ON ENTREPRISE.siret_entreprise = EVALUATION.siret_entreprise;
        ";
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function afficherOffreByNomEntrepriseSQL($nom_entreprise){
        $sql = "
            SELECT 
                id_offre, 
                titre_offre,
                description_offre, 
                remuneration_offre, 
                date_offre,
                lieu_offre,
                duree_formation_offre,
                OFFRE.nom_entreprise,
                nombredevues,
                nombredepostulants

            FROM OFFRE LEFT JOIN ENTREPRISE ON OFFRE.nom_entreprise  = ENTREPRISE.nom_entreprise WHERE OFFRE.nom_entreprise = ?;
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nom_entreprise]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteOffre($id){
        $query = "
        DELETE FROM OFFRE WHERE id_offre = :id
        ";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}
