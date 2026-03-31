<?php

namespace App\Controlers;

use App\Models\CandidatureModel;

class CandidatureControleur {

    private $model;
    private $twig;

    public function __construct($model, $twig) {
        $this->model = $model;
        $this->twig = $twig;
    }

    public function postuler() {
        if (!isset($_SESSION['user_id'])) {
            die("Vous devez être connecté.");
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $LM = htmlspecialchars($_POST['LM'] ?? '');
            $id_offre = $_POST['id_offre'] ?? null;
            $id_utilisateur = $_SESSION['user_id'];
            if (!$id_offre) {
                die("Offre non spécifiée.");
            }
            $CV = $_FILES['CV'];
            $CVName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($CV["name"]));
            $targetFile = "uploads/" . $CVName;
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $CV["tmp_name"]);
            if ($mime !== "application/pdf") {
                die("Le fichier doit être un PDF.");
            }
            if ($CV["size"] > 2 * 1024 * 1024) {
                die("Fichier trop volumineux.");
            }
            if (move_uploaded_file($CV["tmp_name"], $targetFile)) {
                $id = $this->model->insererCandidature($LM, $targetFile, $id_offre, $id_utilisateur);
                $this->model->incrementerPostulants($id_offre);
                header("Location: index.php?page=merci-candidature&id=" . $id);
                exit();
            } else {
                die("Erreur upload");
            }
        }
    }

    public function afficherMerci($id) {
        $candidature = $this->model->getCandidatureAvecOffre($id);
        echo $this->twig->render('merci-candidature.twig', [
            'candidature' => $candidature
        ]);
    }
}