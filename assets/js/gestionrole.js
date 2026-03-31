document.addEventListener('DOMContentLoaded', function() {
    const roleRadios = document.querySelectorAll('.role-selector');
    const sectionPilote = document.getElementById('section-pilote');

    function togglePiloteSection() {
        const selectedRadio = document.querySelector('.role-selector:checked');
        
        if (selectedRadio && sectionPilote) {
            const selectedRole = selectedRadio.value;
            
            // Si le rôle est Étudiant (1), on affiche, sinon on cache
            if (selectedRole === "1") {
                sectionPilote.style.display = 'block';
            } else {
                sectionPilote.style.display = 'none';
                // Reset de la sélection du pilote par sécurité
                const selectElement = sectionPilote.querySelector('select');
                if (selectElement) selectElement.value = "";
            }
        }
    }

    // Écouteur sur les boutons radio (pour l'admin)
    roleRadios.forEach(radio => {
        radio.addEventListener('change', togglePiloteSection);
    });

    // État initial au chargement
    if (roleRadios.length > 0) {
        togglePiloteSection();
    }
});