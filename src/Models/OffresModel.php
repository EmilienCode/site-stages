<?php
namespace App\Models;
use PDO;
use Exception;

class OffresModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getoffres($limit, $offset) {
        try {
            $sql = "SELECT * FROM OFFRE LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // On log l'erreur au lieu de l'afficher directement ici
            error_log($e->getMessage());
            return [];
        }
    }
}
?>