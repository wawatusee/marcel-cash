document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('comptesForm');
    const totalSpan = document.getElementById('total');

    // Calculer le total à chaque changement
    function calculerTotal() {
        let total = 0;
        const inputs = form.querySelectorAll('input[type="number"]');

        inputs.forEach(input => {
            const quantite = parseFloat(input.value) || 0;
            const valeur = parseFloat(input.dataset.valeur);
            total += quantite * valeur;
        });

        totalSpan.textContent = total.toFixed(2) + ' €';
    }

    // Écouter les changements dans les champs
    form.addEventListener('input', calculerTotal);

    // Gérer les boutons d'incrémentation
    document.querySelectorAll('.btn-increment').forEach(button => {
        button.addEventListener('click', function() {
            const champ = this.dataset.champ;
            const pas = parseFloat(this.dataset.pas);
            const input = form.querySelector(`input[name="${champ}"]`);
            if (input) {
                // Si c'est un champ Bancontact (ou autre champ décimal), on ajoute le pas directement
                if (input.step === '0.01') {
                    input.value = (parseFloat(input.value) || 0) + pas;
                } else {
                    input.value = (parseInt(input.value) || 0) + pas;
                }
                input.dispatchEvent(new Event('input')); // Déclencher le recalcul
            }
        });
    });

    // Calcul initial au chargement
    calculerTotal();
});