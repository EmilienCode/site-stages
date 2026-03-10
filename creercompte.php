<?php include "includes/header.php"; ?>

    <main class="connexion-container">
    <section class="connexion-card">
        <header class="connexion-header">
            <h1>Créer un compte</h1>
            <p>Créer un compte étudiant pour plus de fonctionnalitées</p>
        </header>

        <form class="connexion-form" action="connexion.html" onsubmit="return checkPassword()">
            <div class="form-row">
                <div class="form-line">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" placeholder="Zidane">
                </div>

                <div class="form-line">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" placeholder="Zinédine">
                </div>
            </div>

            <div class="form-line">
                <label for="username">Email</label>
                <input
                    type="email"
                    id="username"
                    placeholder="zizouzidane@gmail.com"
                    required
                />
            </div>

            <div class="form-line">
                <label for="password">Définissez un mot de passe   </label>
                <input
                    type="password"
                    id="password"
                    minlength="8" 
                    pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$" 
                    placeholder="••••••••"
                    required
                /> <!-- (?=.*[A-Z]) → au moins 1 majuscule
                        (?=.*\d) → au moins 1 chiffre
                        (?=.*[^A-Za-z0-9]) → au moins 1 caractère spécial
                        {8,} → 8 caractères minimum-->
                <br>      
                <small class="password-hint">
                     8 caractères minimum, <strong>1 majuscule</strong>, <strong>1 chiffre</strong> et <strong>1 caractère spécial</strong>
                </small>
            </div>

            <div class="form-line">
                <label for="password">Confirmez votre mot de passe</label>
                <input
                    type="password"
                    id="confirm"
                    minlength="8"
                    pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                    placeholder="••••••••"
                    required
                />
                <span id="error" style="color:#914941;"></span><br><br>
            </div>

            <button type="submit" class="btn-primary btn-full">
                Créer un compte
            </button>
        </form>
        <script>
            function checkPassword() {
                const pass = document.getElementById("password").value;
                const confirm = document.getElementById("confirm").value;
                const error = document.getElementById("error");

                if (pass !== confirm) {
                error.textContent = "⚠️ Les mots de passe ne correspondent pas !";
                return false; // empêche l'envoi
                }

                error.textContent = "";
                return true; // autorise l'envoi
            }
        </script>

        <div class="connexion-links">
            <a href="connexion.html">← Retour</a>
        </div>
    </section>
    </main>

<?php include "includes/footer.php"; ?>
