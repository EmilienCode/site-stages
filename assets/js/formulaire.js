function checkForm() {
    const pass = document.getElementById("password").value;
    const confirm = document.getElementById("confirm").value;
    const email = document.getElementById("username").value;
    const ville = document.getElementById("ville").value;
    const date = document.getElementById("naissance").value;

    const error = document.getElementById("error");
    const errorEmail = document.getElementById("error-email");
    const errorDomain = document.getElementById("error-domain");
    const errorVille = document.getElementById("error-ville");
    const errorDate = document.getElementById("error-date");
    let valid = true;

    //  PASSWORD CHECK 
    if (pass !== confirm) {
        error.textContent = "⚠️ Les mots de passe ne correspondent pas !";
        document.getElementById("confirm").scrollIntoView({ behavior: "smooth", block: "center" });
        valid = false;
    } else {
        error.textContent = "";
    }

    //  EMAIL CHECK 
    const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
    const blockedDomains = [
        "tempmail.com",
        "10minutemail.com",
        "guerrillamail.com",
        "mailinator.com",
        "yopmail.com"
    ];
    const emailDomain = email.split("@")[1]?.toLowerCase();
    // email.split("@") → sépare l'email en 2 parties : [nom, domaine]
    // [1] → récupère la partie domaine (ex: "gmail.com")
    // ?. → évite une erreur si l'email ne contient pas de "@"
    // toLowerCase() → met tout en minuscule pour éviter les différences (Gmail.com ≠ gmail.com)
    if (!emailRegex.test(email)) {
        errorEmail.textContent = "⚠️ Veuillez entrer un email valide !";
        document.getElementById("username").scrollIntoView({ behavior: "smooth", block: "center" });
        valid = false;
    }
    else if (blockedDomains.includes(emailDomain)) {
        errorEmail.textContent = "⚠️ Les emails temporaires ne sont pas autorisés !";
        document.getElementById("username").scrollIntoView({ behavior: "smooth", block: "center" });
        valid = false;
    }
    else {
        errorEmail.textContent = "";
    }


    const villeRegex = /^[a-zA-ZÀ-ÿ\s-]+$/;
    if (!villeRegex.test(ville)) {
        errorVille.textContent = "⚠️ La ville ne doit contenir que des lettres !";
        document.getElementById("ville").scrollIntoView({ behavior: "smooth", block: "center" });
        valid = false;
    } else {
        errorVille.textContent = "";
    }
    // DATE CHECK 
    // On vérifie que la date est au format jj/mm/aaaa et qu'elle est valide (ex: pas de 31/02/2020) et pas dans le futur
    const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    const match = date.match(dateRegex);
    const now = new Date(); // date actuelle pour comparer et éviter les dates de naissance dans le futur
    if (!match) { // si la date ne correspond pas au format
        errorDate.textContent = "⚠️ Veuillez entrer une date au format jj/mm/aaaa !";
        document.getElementById("naissance").scrollIntoView({ behavior: "smooth", block: "center" }); // on scroll vers le champ date pour attirer l'attention de l'utilisateur
        valid = false;
    } else {
        const day = parseInt(match[1], 10); // match[1] → jour, match[2] → mois, match[3] => année et le parseInt(..., 10) convertit la chaîne en nombre entier en base 10
        const month = parseInt(match[2], 10);
        const year = parseInt(match[3], 10);

        const dateObj = new Date(year, month - 1, day); 

        if (
            dateObj.getFullYear() !== year ||
            dateObj.getMonth() !== month - 1 || // en JavaScript, les mois sont indexés de 0 (janvier) à 11 (décembre), donc on soustrait 1 pour comparer correctement
            dateObj.getDate() !== day
        ) {
            errorDate.textContent = "⚠️ Date invalide !";
            document.getElementById("naissance").scrollIntoView({ behavior: "smooth", block: "center" });
            valid = false;
        }
        else if (dateObj > now) {
            errorDate.textContent = "⚠️ La date ne peut pas être dans le futur !";
            document.getElementById("naissance").scrollIntoView({ behavior: "smooth", block: "center" });
            valid = false;
        }
        else {
            errorDate.textContent = "";
        }
    }
    return valid;
}
window.addEventListener("DOMContentLoaded", () => {
// Gestion des erreurs de validation côté serveur
const url = new URL(window.location.href);
const error = url.searchParams.get("error");
const nomInput = document.getElementById("nom");
nomInput.addEventListener("blur", () => {
    nomInput.value = nomInput.value.toUpperCase(); // Mettre le nom en majuscules automatiquement au moment où l'utilisateur quitte le champ
});
// on aurait pu aussi faire en temps réel
//  nomInput.addEventListener("input", () => {
//    nomInput.value = nomInput.value.toUpperCase();
//});


if (error) {
    let target = null;
    switch(error) {
        case "invalid_data":
            target = document.getElementById("naissance");
            break;

        case "ville_invalid":
            target = document.getElementById("ville");
            break;

        case "email_temp":
            target = document.getElementById("username");
            break;

        case "email_taken":
            target = document.getElementById("username");
            document.getElementById("error-email").textContent =
                "⚠️ Cette adresse email est déjà utilisée";
            break;
                    }
    if (target) {
        target.scrollIntoView({ behavior: "smooth", block: "center" });
        target.focus();
    }
}
});