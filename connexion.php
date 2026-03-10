<?php include "includes/header.php"; ?>
<link rel="stylesheet" href="assets/css/stylelogin.css"/>
    <main class="connexion-container">
    <section class="connexion-card">
        <header class="connexion-header">
            <h1>Connexion</h1>
            <p>Accédez à votre espace personnel</p>
        </header>

        <form class="connexion-form">
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
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    placeholder="••••••••"
                    required
                />
            </div>

            <button type="submit" class="btn-primary btn-full">
                Se connecter
            </button>
        </form>

        <div class="connexion-links">
            <a href="#">Mot de passe oublié ?</a>
            <a href="creercompte.html">Créer un compte étudiant</a>
        </div>
    </section>
    </main>

<?php include "includes/footer.php"; ?>
