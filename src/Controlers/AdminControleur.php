<?php
class AdminControleur {
    private $pdo;
    private $twig;

    public function __construct($pdo, $twig) {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }

    public function afficherUtilisateurs() {
        // La logique de récupération des données (Modèle)
        $query = "
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, r.nom_role 
            FROM UTILISATEUR u 
            LEFT JOIN ROLES r ON u.id_role = r.id_role
            ORDER BY u.id_utilisateur DESC
        ";
        $stmt = $this->pdo->query($query);
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Appel de la vue avec les données
        echo $this->twig->render('admin_utilisateur.twig', [
            'users' => $utilisateurs
        ]);
    }
}?>