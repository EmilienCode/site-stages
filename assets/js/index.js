document.addEventListener("DOMContentLoaded", () => {
    const popup = document.querySelector(".container-cookie");
    const acceptBtn = document.querySelector(".accept-btn");
    const rejectBtn = document.querySelector(".reject-btn");

    // Masque la popup si un des boutons est cliqué
    const closePopup = () => {
        popup.style.display = "none";
    };

    acceptBtn.addEventListener("click", closePopup);
    rejectBtn.addEventListener("click", closePopup);
});