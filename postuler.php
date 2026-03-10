<?php include "includes/header.php"; ?>

<main class="page">

    <section class="top-links">
        <a href="offres.html">← Retour</a>
    </section>

    <section class="card offer-card">
        <div class="offer-header">
            <h1>Stage Exemple</h1>
            <span class="badge">Nouveau</span>
        </div>

        <h2>Entreprise Exemple </h2>

        <div class="offer-meta">
            <span>📍 Lieu</span>
            <span>⏳ Durée</span>
            <span>💻 Domaine</span>
            <span>🧑‍🎓 Formation requise</span>
        </div>
    </section>

    <section class="card form-card">
        <h3>Votre candidature</h3>

        <form class="form" action="traitement.php" method="POST" enctype="multipart/form-data">

            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="Nom" placeholder="Zidane" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="Prenom" placeholder="Zinédine" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="Email" placeholder="zizou.zinedine@gmail.com" required>
            </div>
            
            <div class="form-group">
                <label for="tel">Numéro de téléphone</label>
                <input type="tel" id="tel" name="Tel" placeholder="ex : 0606060606" required>
            </div>

            <div class="form-group">
                <label for="lm">Lettre de motivation (recommandée)</label>
                <textarea id="lm" name="LM" placeholder="Expliquez pourquoi ce stage vous intéresse..."></textarea>
            </div>

            <div class="form-group file-group">
                <label for="cv">CV (PDF)</label>
                <input type="file" id="cv" name="CV" accept="application/pdf" required>
                <span class="file-hint">Format PDF – 2 Mo max</span>
            </div>

            <div class="form-actions">
                <a href="offres.html" class="btn btn-secondary">Annuler</a>
                <button type="submit"  class="btn btn-primary">Envoyer ma candidature</button>
            </div>

        </form>
        <script>
            document.getElementById("cv").addEventListener("change", function() {
                const file = this.files[0];
                if (file) {
                    const maxSize = 2 * 1024 * 1024; // 2 Mo

                    // Vérification du type
                    if (file.type !== "application/pdf") {
                        alert("Le fichier doit être au format PDF.");
                        this.value = "";
                        return;
                    }

                    // Vérification de la taille
                    if (file.size > maxSize) {
                        alert("Le fichier dépasse la taille maximale de 2 Mo.");
                        this.value = "";
                    }
                }
            });
        </script>
    </section>

</main>

<?php include "includes/footer.php"; ?>