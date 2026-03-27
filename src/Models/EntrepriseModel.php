<?php
namespace App\Models;

use PDO;
use Exception;

class EntrepriseModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getEntreprises($limit, $offset, $nom = '', $taille = '', $secteur = '') {
        try {
            $sql = "SELECT * FROM ENTREPRISE WHERE 1=1";
            $params = [];

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

    public function countEntreprises($nom = '', $taille = '', $secteur = '') {
        try {
            $sql = "SELECT COUNT(*) FROM ENTREPRISE WHERE 1=1";
            $params = [];

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
}