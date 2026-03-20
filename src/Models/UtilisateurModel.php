<?php
namespace App\Models;
use PDO;
use Exception;

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

    public function getUserByRole() {
        $query = "
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, r.nom_role 
            FROM UTILISATEUR u 
            LEFT JOIN ROLES r ON u.id_role = r.id_role
            WHERE u.id_role = 1
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

    // 1. Récupérer UN utilisateur avec ses coordonnées
    public function getUserById($id) {
        $query = "
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.id_role,
                   c.ville_coordonnees, c.telephone_coordonnees, c.sexe_coordonnees, c.date_naissance_coordonnees
            FROM UTILISATEUR u
            LEFT JOIN COORDONNEES c ON u.id_utilisateur = c.id_utilisateur
            WHERE u.id_utilisateur = :id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Récupérer la liste des rôles pour le menu déroulant
    public function getRoles() {
        return $this->pdo->query("SELECT id_role, nom_role FROM ROLES")->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Mettre à jour l'utilisateur et ses coordonnées
    public function updateUser($id, $data) {
        try {
            // On utilise une transaction car on modifie deux tables
            $this->pdo->beginTransaction();

            // A. Mise à jour de la table UTILISATEUR
            $queryUser = "UPDATE UTILISATEUR SET nom = :nom, prenom = :prenom, email = :email, id_role = :id_role WHERE id_utilisateur = :id";
            $stmtUser = $this->pdo->prepare($queryUser);
            $stmtUser->execute([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'id_role' => $data['id_role'],
                'id' => $id
            ]);

            // B. Mise à jour ou Insertion dans COORDONNEES
            // On s'assure que date_naissance n'est pas vide (ou on le met à null)
            $dateNaissance = !empty($data['date_naissance']) ? $data['date_naissance'] : null;

            $queryCoord = "
                INSERT INTO COORDONNEES (id_utilisateur, ville_coordonnees, telephone_coordonnees, sexe_coordonnees, date_naissance_coordonnees) 
                VALUES (:id, :ville, :telephone, :sexe, :date_naissance)
                ON DUPLICATE KEY UPDATE 
                ville_coordonnees = VALUES(ville_coordonnees), 
                telephone_coordonnees = VALUES(telephone_coordonnees), 
                sexe_coordonnees = VALUES(sexe_coordonnees), 
                date_naissance_coordonnees = VALUES(date_naissance_coordonnees)
            ";
            $stmtCoord = $this->pdo->prepare($queryCoord);
            $stmtCoord->execute([
                'id' => $id,
                'ville' => $data['ville'],
                'telephone' => $data['telephone'],
                'sexe' => $data['sexe'], // Attention, c'est un int dans ta BDD
                'date_naissance' => $dateNaissance
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            // Idéalement, on log l'erreur ici
            return false;
        }
    }
}
?>