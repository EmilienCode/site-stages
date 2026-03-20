<?php
// On enlève les "use" car ils font des warnings sans namespace
class OffresModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getOffres($limit, $offset) {
        try {
            $sql = "SELECT * FROM OFFRE LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            // On force le type en INT pour la BDD
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function countOffres() {
        return $this->pdo->query("SELECT COUNT(*) FROM OFFRE")->fetchColumn();
    }
}