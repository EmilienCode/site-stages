// On récupère le bouton et le body
const bouton = document.getElementById('leBouton');
const body = document.body;

// On écoute le clic sur le bouton
bouton.addEventListener('click', function() {
    // La méthode 'toggle' ajoute la classe si elle n'est pas là,
    // et l'enlève si elle est déjà là. C'est magique.
    body.classList.toggle('dark-mode');
});