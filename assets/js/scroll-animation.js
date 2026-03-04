document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll('.entreprise-card');

    const observerOptions = {
        threshold: 0.1, // Déclenche quand 10% de la carte est visible
        rootMargin: "0px 0px -30px 0px" // Petite marge pour ne pas déclencher trop tôt en bas
    };

    const observer = new IntersectionObserver((entries, observer) => {
        // On filtre pour ne garder QUE les cartes qui sont en train d'entrer dans l'écran
        const cartesVisibles = entries.filter(entry => entry.isIntersecting);
        
        cartesVisibles.forEach((entry, index) => {
            // L'effet cascade : on multiplie l'index par 150 millisecondes
            // Carte 1 : 0ms | Carte 2 : 150ms | Carte 3 : 300ms, etc.
            setTimeout(() => {
                entry.target.classList.add('is-visible');
            }, index * 150); 
            
            // On arrête d'observer cette carte pour que l'animation ne se joue qu'une fois
            observer.unobserve(entry.target);
        });
    }, observerOptions);

    // On observe toutes les cartes
    cards.forEach(card => observer.observe(card));
});