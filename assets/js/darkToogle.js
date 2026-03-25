import { updateEarthTheme } from "./index.js";
// On récupère la checkbox et le body
const toggleCheckbox = document.getElementById('btn-theme-toogle');
const body = document.body;

// 1. Au chargement : on vérifie le thème sauvegardé dans le navigateur
const themeSauvegarde = localStorage.getItem('theme');

// On applique le bon thème ET on met le switch dans la bonne position
if (themeSauvegarde === 'dark') {
    body.classList.add('theme-dark');
    toggleCheckbox.checked = true;  // On "coche" visuellement le switch
} else {
    toggleCheckbox.checked = false; // On s'assure qu'il est "décoché"
}

// 2. À l'utilisation : on écoute le changement d'état du switch
// Pour une checkbox, on utilise l'événement 'change' (plus fiable que 'click')
toggleCheckbox.addEventListener('change', function() {
    
    // Si le switch vient d'être activé (coché)
    if (this.checked) {
        body.classList.add('theme-dark');
        localStorage.setItem('theme', 'dark');
        updateEarthTheme(true);
    } 
    // Si le switch vient d'être désactivé (décoché)
    else {
        body.classList.remove('theme-dark');
        localStorage.setItem('theme', 'light');
        updateEarthTheme(false);
    }
});
updateEarthTheme(toggleCheckbox.checked);