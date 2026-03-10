<?php include "includes/header.php"; ?>
<link rel="stylesheet" href="assets/css/style.css"/>
    <main>
        <section class="Section">
            <div class="InsideSection">
                <div style="opacity: 1;transform: none;">
                    <h1 class="TxtBlack animated-title-glitch">Trouvez votre <span class="TxtBlue">stage idéal</span> ou le candidat parfait.</h1>
                    <p class="ParaLight">La plateforme dédiée aux étudiants et aux entreprises pour faciliter la recherche de stages et d'alternances. Publiez votre profil ou parcourez les offres.</p>
                    <div class="BoxRechercher">
                        <div class="input1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svgLoop" aria-hidden="true">
                                <path d="m21 21-4.34-4.34"></path>
                                <circle cx="11" cy="11" r="8"></circle>
                            </svg>
                            <input type="text" placeholder="Métier, compétence..." class="inputTXT">
                        </div>
                        <div class="input2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svgLoop" aria-hidden="true">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <input type="text" placeholder="Ville ou région" class="inputTXT">
                        </div>
                        <div>
                            <button class="RechercherBTN">
                                Rechercher
                            </button>
                        </div>
                    </div>
                    <div class="barrePopulaire">
                        <span class="populaire">Populaire :</span>
                        <span class="populaireTXT">Développement Web</span>
                        <span class="populaireTXT">Marketing</span>
                        <span class="populaireTXT">Design UX</span>
                    </div>
                </div>
                <div class="planete">
                </div>
            </div>
        </section>
        <section class="SectionOffre">
            <div class="SectionTrie">
                <div class="divTrie">
                    <button class="btnTrie">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logoTrie" aria-hidden="true">
                            <path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z"></path>
                        </svg>
                        Filtres
                    </button>
                    <button class="btnTrie">
                        Trier par : Récents
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logoTrie" aria-hidden="true">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    </main>
<?php include "includes/footer.php"; ?>