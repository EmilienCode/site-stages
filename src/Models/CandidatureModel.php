<?php

namespace App\Models;

class CandidatureModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    // Insère une nouvelle candidature dans la base de données
    public function insererCandidature($LM, $CV, $id_offre, $id_utilisateur) {
        $sql = "INSERT INTO CANDIDATURES 
                (LM_candidature, CV_candidature, id_offre, id_utilisateur)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$LM, $CV, $id_offre, $id_utilisateur]);
        return $this->pdo->lastInsertId();
    }
    // Incrémente le nombre de postulants pour une offre donnée
    public function incrementerPostulants($id_offre) {
        $sql = "UPDATE OFFRE 
                SET nombredepostulants = nombredepostulants + 1 
                WHERE id_offre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_offre]);
    }
    // Récupère les détails d'une candidature avec les informations de l'offre associée
    public function getCandidatureAvecOffre($id) {
        $sql = "SELECT c.*, o.titre_offre, o.nom_entreprise 
                FROM CANDIDATURES c
                JOIN OFFRE o ON c.id_offre = o.id_offre
                WHERE c.id_candidature = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}