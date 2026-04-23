/**
 * SCRIPTS FRONT-END - Application "Pas Touche au Klaxon"
 * Gère les interactions dynamiques, les validations de formulaires avant envoi,
 * et l'amélioration de l'expérience utilisateur (UX).
 */
document.addEventListener('DOMContentLoaded', () => {

    // --- 1. GESTION DU FORMULAIRE D'INSCRIPTION (Toggle) ---
    const btnRegister = document.getElementById('btn-show-register');
    const registerSection = document.getElementById('register-section');

    if (btnRegister && registerSection) {
        const formInscription = registerSection.querySelector('form');
        const telInput = formInscription.querySelector('input[name="telephone"]');

        // Permet d'afficher ou masquer le formulaire d'inscription sur l'accueil
        btnRegister.addEventListener('click', function() {
            if (registerSection.style.display === 'none' || registerSection.style.display === '') {
                registerSection.style.display = 'block';
                this.innerText = "Réduire le formulaire";
                this.classList.replace('btn-outline-secondary', 'btn-secondary');
            } else {
                registerSection.style.display = 'none';
                this.innerText = "Créer un compte";
                this.classList.replace('btn-secondary', 'btn-outline-secondary');
            }
        });

        // Validation du format téléphone en temps réel (Regex)
        formInscription.addEventListener('submit', (e) => {
            const telRegex = /^(0)[1-9](\d{2}){4}$/; 
            
            if (telInput.value !== "" && !telRegex.test(telInput.value)) {
                e.preventDefault(); // Bloque l'envoi vers PHP si le format est mauvais
                alert("Le numéro de téléphone n'est pas valide (ex: 0612345678)");
                telInput.classList.add('is-invalid'); // Feedback visuel Bootstrap
                telInput.focus();
            }
        });
    }

    // --- 2. VALIDATION DU FORMULAIRE DE TRAJET ---
    const formTrajet = document.querySelector('form[action*="trajet"]');
    
    if (formTrajet) {
        const departInput = formTrajet.querySelector('input[name="gdh_depart"]');
        const arriveeInput = formTrajet.querySelector('input[name="gdh_arrivee"]');

        // Empêche de poster un trajet qui finit avant d'avoir commencé
        formTrajet.addEventListener('submit', (e) => {
            const dateDepart = new Date(departInput.value);
            const dateArrivee = new Date(arriveeInput.value);

            if (dateArrivee <= dateDepart) {
                e.preventDefault(); 
                alert("L'heure d'arrivée doit être après l'heure de départ !");
                arriveeInput.classList.add('is-invalid'); 
            }
        });

        // --- 3. DYNAMISME DES AGENCES (Empêcher départ = arrivée) ---
        const selectDepart = formTrajet.querySelector('select[name="agence_depart_id"]');
        const selectArrivee = formTrajet.querySelector('select[name="agence_arrivee_id"]');

        if (selectDepart && selectArrivee) {
            const updateOptions = () => {
                const departId = selectDepart.value;
                const arriveeId = selectArrivee.value;
               
                // Désactive l'agence sélectionnée dans l'autre menu pour éviter les doublons
                Array.from(selectArrivee.options).forEach(option => {
                    option.disabled = (option.value !== "" && option.value === departId);
                });

                Array.from(selectDepart.options).forEach(option => {
                    option.disabled = (option.value !== "" && option.value === arriveeId);
                });
            };

            selectDepart.addEventListener('change', updateOptions);
            selectArrivee.addEventListener('change', updateOptions);
            
            updateOptions(); // Appel initial
        }
    }
});

/**
 * Bascule l'affichage d'une ligne de tableau entre le mode "lecture" et "édition".
 * Utilisé principalement dans le tableau de bord Administrateur.
 * @param {HTMLElement} btn - Le bouton de modification cliqué.
 */
function toggleEdit(btn) {
    const row = btn.closest('tr');
    
    const viewElements = row.querySelectorAll('.view-mode');
    const editElements = row.querySelectorAll('.edit-mode');
    const btnEdit = row.querySelector('.btn-edit');

    viewElements.forEach(el => el.classList.toggle('d-none'));
    editElements.forEach(el => el.classList.toggle('d-none'));
    
    if(btnEdit) {
        btnEdit.classList.toggle('d-none');
    }
}