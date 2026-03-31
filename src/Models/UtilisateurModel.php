<?php
namespace App\Models;

use PDO;
use Exception;

class UtilisateurModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les utilisateurs avec recherche par nom/prénom
     */
    public function getAll($search = '') {
        $query = "
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, r.nom_role 
            FROM UTILISATEUR u 
            LEFT JOIN ROLES r ON u.id_role = r.id_role
        ";
        
        $params = [];
        if (!empty($search)) {
            $query .= " WHERE u.nom LIKE :search OR u.prenom LIKE :search";
            $params['search'] = '%' . $search . '%';
        }
        
        $query .= " ORDER BY u.id_utilisateur DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère uniquement les utilisateurs ayant le rôle Pilote (ID 2)
     */
    public function getAllPilotes() {
        $query = "SELECT id_utilisateur, nom, prenom FROM UTILISATEUR WHERE id_role = 2 ORDER BY nom ASC";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les offres en favoris d'un utilisateur
     */
    public function getAllOffreWishlist($id_utilisateur) {
        $query = "SELECT o.id_offre, o.titre_offre, o.description_offre, o.remuneration_offre, 
                         o.domaine_requis_offre, o.date_offre, o.lieu_offre, o.duree_formation_offre,
                         e.nom_entrprise 
                  FROM OFFRE o
                  JOIN MET_EN_FAVORI m ON o.id_offre = m.id_offre
                  JOIN ENTREPRISE e ON o.siret_entreprise = e.siret_entreprise
                  WHERE m.id_utilisateur = :id_utilisateur";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_utilisateur' => $id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteOffreWishlist($id_offre, $id_utilisateur) {
        $query = "DELETE FROM MET_EN_FAVORI 
                  WHERE id_offre = :id_offre AND id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id_offre' => $id_offre,
            'id_utilisateur' => $id_utilisateur
        ]);
    }

    public function deleteUser($id) {
        $query = "DELETE FROM UTILISATEUR WHERE id_utilisateur = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function getUserById($id) {
        $query = "
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.id_role, u.id_pilote_referent,
                   c.ville_coordonnees, c.telephone_coordonnees, c.sexe_coordonnees, c.date_naissance_coordonnees
            FROM UTILISATEUR u
            LEFT JOIN COORDONNEES c ON u.id_utilisateur = c.id_utilisateur
            WHERE u.id_utilisateur = :id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRoles() {
        return $this->pdo->query("SELECT id_role, nom_role FROM ROLES")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersByRoles(array $roles, $search = '') {
        $rolesList = implode(',', array_map('intval', $roles)); 
        
        $query = "
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.id_role, r.nom_role 
            FROM UTILISATEUR u 
            LEFT JOIN ROLES r ON u.id_role = r.id_role
            WHERE u.id_role IN ($rolesList)
        ";
        
        $params = [];
        if (!empty($search)) {
            $query .= " AND (u.nom LIKE :search OR u.prenom LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        
        $query .= " ORDER BY u.id_utilisateur DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        try {
            $this->pdo->beginTransaction();

            $queryUser = "UPDATE UTILISATEUR SET nom = :nom, prenom = :prenom, email = :email, id_role = :id_role WHERE id_utilisateur = :id";
            $stmtUser = $this->pdo->prepare($queryUser);
            $stmtUser->execute([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'id_role' => $data['id_role'],
                'id' => $id
            ]);

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
                'sexe' => $data['sexe'],
                'date_naissance' => $dateNaissance
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * INSCRIPTION ETUDIANT (Version Corrigée avec Pilote Référent)
     */
    public function inscrireEtudiant($data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertion UTILISATEUR (5 paramètres : nom, prenom, email, mdp, id_pilote)
            // On force l'id_role à 1 directement dans le SQL
            $sql1 = "INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, id_role, id_pilote_referent) 
                     VALUES (?, ?, ?, ?, 1, ?)";
            
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->execute([
                $data['nom'], 
                $data['prenom'], 
                $data['email'], 
                $data['password'],
                $data['id_pilote'] // <--- L'ID du pilote (peut être null)
            ]);

            $id_utilisateur = $this->pdo->lastInsertId();

            // 2. Insertion COORDONNEES (5 paramètres)
            $sql2 = "INSERT INTO COORDONNEES 
                    (ville_coordonnees, telephone_coordonnees, sexe_coordonnees, date_naissance_coordonnees, id_utilisateur)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([
                $data['ville'], 
                $data['telephone'], 
                $data['sexe'], 
                $data['date_naissance'], 
                $id_utilisateur
            ]);

            $this->pdo->commit();
            return $id_utilisateur;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}