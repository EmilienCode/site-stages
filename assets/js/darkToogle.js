// On récupère la checkbox et le body
const toggleCheckbox = document.getElementById('btn-theme-toogle');
const body = document.body;

// 1. Au chargement : on vérifie le thème sauvegardé
const themeSauvegarde = localStorage.getItem('theme');

if (themeSauvegarde === 'dark') {
    body.classList.add('theme-dark');
    toggleCheckbox.checked = true;
} else {
    toggleCheckbox.checked = false;
}

// 2. À l'utilisation : on écoute le changement d'état du switch
toggleCheckbox.addEventListener('change', function() {
    
    if (this.checked) {
        body.classList.add('theme-dark');
        localStorage.setItem('theme', 'dark');
        // On diffuse un événement global pour dire "On passe en mode sombre"
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark: true } }));
    } else {
        body.classList.remove('theme-dark');
        localStorage.setItem('theme', 'light');
        // On diffuse un événement global pour dire "On passe en mode clair"
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark: false } }));
    }
});