<?php
namespace App\Models;

use PDO;
use Exception;

class OffresModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // On ajoute les paramètres optionnels pour les filtres et le tri
    public function getOffres($limit, $offset, $metier = '', $ville = '', $tri = 'recents') {
        try {
            // Le 1=1 permet d'ajouter dynamiquement des AND par la suite
            $sql = "SELECT * FROM OFFRE WHERE 1=1";
            $params = [];

            // 1. Filtre par métier/compétence
            if (!empty($metier)) {
                $sql .= " AND (titre_offre LIKE :metier OR description_offre LIKE :metier)";
                $params[':metier'] = '%' . $metier . '%';
            }

            // 2. Filtre par ville
            if (!empty($ville)) {
                $sql .= " AND ville LIKE :ville"; // <-- Vérifie que ta colonne s'appelle bien "ville"
                $params[':ville'] = '%' . $ville . '%';
            }

            // 3. Tri
            switch ($tri) {
                case 'anciens':
                    $sql .= " ORDER BY date_offre ASC";
                    break;
                case 'salaire_asc':
                    $sql .= " ORDER BY remuneration_offre ASC";
                    break;
                case 'salaire_desc':
                    $sql .= " ORDER BY remuneration_offre DESC";
                    break;
                case 'recents':
                default:
                    $sql .= " ORDER BY date_offre DESC";
                    break;
            }

            // 4. Pagination
            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);

            // On bind les paramètres de recherche s'il y en a
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, \PDO::PARAM_STR);
            }

            // On bind la pagination
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // On doit aussi mettre à jour le count pour que la pagination s'adapte à la recherche !
    public function countOffres($metier = '', $ville = '') {
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

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, \PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchColumn();

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return 0; // On retourne 0 si erreur
        }
    }
}