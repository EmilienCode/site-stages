<?php include "includes/header.php"; ?>
<link rel="stylesheet" href="assets/css/stylecontact.css"/>
    <main class="page">
        
        <section class="card form-card">
            <h3>NOUS CONTACTER</h3>
            <input type="hidden" name="type_formulaire" value="contact">
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
                    <input type="email" id="email" name="Email" placeholder="zizouzidane@gmail.com" required>
                </div>
                
                <div class="form-group">
                    <label>Raison de votre demande</label>

                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" name="raison" value="compte" required>
                            Problème de compte / connexion
                        </label>

                        <label class="radio-item">
                            <input type="radio" name="raison" value="offre">
                            Problème avec une offre de stage
                        </label>

                        <label class="radio-item">
                            <input type="radio" name="raison" value="entreprise">
                            Problème avec une entreprise
                        </label>

                        <label class="radio-item">
                            <input type="radio" name="raison" value="candidature">
                            Question sur une candidature
                        </label>

                        <label class="radio-item">
                            <input type="radio" name="raison" value="technique">
                            Problème technique / bug
                        </label>

                        <label class="radio-item">
                            <input type="radio" name="raison" value="autre" id="raison-autre">
                            Autre
                        </label>
                </div>

                <div class="form-group" id="precision-autre" style="display: none;">
                        <label for="autre-detail">Veuillez préciser</label>
                        <input
                            type="text"
                            id="autre-detail"
                            name="raison"
                            placeholder="Précisez la raison de votre demande"
                            required>
                </div>
                <div class="form-group">
                    <label for="lm">Détails</label>
                    <textarea id="lm" name="Details" placeholder="Expliquez plus en détails la raison de votre demande..." required></textarea>
                </div>

                <div class="form-actions">
                    <a href="#" class="btn btn-secondary">Annuler</a>
                    <button type="submit"  class="btn btn-primary">Envoyer</button>
                </div>

            </form>
            <script>
                const radios = document.querySelectorAll('input[name="raison"]');
                const autreBloc = document.getElementById('precision-autre');
                const autreInput = document.getElementById('autre-detail');

                radios.forEach(radio => {
                    radio.addEventListener('change', () => {
                        if (radio.value === 'autre' && radio.checked) {
                            autreBloc.style.display = 'block';
                            autreInput.required = true;
                        } else {
                            autreBloc.style.display = 'none';
                            autreInput.required = false;
                            autreInput.value = '';
                        }
                    });
                });
            </script>
        </section>
    </main>
<?php include "includes/footer.php"; ?>