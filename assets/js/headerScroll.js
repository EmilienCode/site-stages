document.addEventListener("DOMContentLoaded", () => {

    let lastScroll = 0;
    const header = document.querySelector(".nav");

    if (!header) return; // sécurité

    window.addEventListener("scroll", () => {

        // seulement en mobile
        if (window.innerWidth > 768) {
            header.classList.remove("hide");
            return;
        }

        const currentScroll = window.scrollY;

        if (currentScroll > lastScroll && currentScroll > 100) {
            // scroll vers le bas → cache
            header.classList.add("hide");
        } else {
            // scroll vers le haut → affiche
            header.classList.remove("hide");
        }

        lastScroll = currentScroll;
    });

});