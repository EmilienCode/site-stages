<?php
class UtilisateurModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $query = "
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, r.nom_role 
            FROM UTILISATEUR u 
            LEFT JOIN ROLES r ON u.id_role = r.id_role
            ORDER BY u.id_utilisateur DESC
        ";
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser($id) {
        $query = "
        DELETE FROM UTILISATEUR WHERE id_utilisateur = :id
        ";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}
?>