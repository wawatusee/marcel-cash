document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('comptesForm');
    const totalSpan = document.getElementById('total');

    // Fonction pour calculer le total
    function calculerTotal() {
        let total = 0;
        const inputs = form.querySelectorAll('input[type="number"]');

        inputs.forEach(input => {
            const quantite = parseFloat(input.value) || 0;
            const valeur = parseFloat(input.dataset.valeur) || 0;
            total += quantite * valeur;
        });

        totalSpan.textContent = total.toFixed(2) + ' €';
        console.log("Total mis à jour :", total); // Pour débogage
    }

    // Écouter les changements dans les champs (saisie manuelle)
    form.addEventListener('input', function(e) {
        if (e.target.type === 'number') {
            calculerTotal();
        }
    });

    // Gérer les boutons d'incrémentation
    document.querySelectorAll('.round-increment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const champ = this.dataset.champ;
            const pas = parseFloat(this.dataset.pas);
            const input = form.querySelector(`input[name="${champ}"]`);

            if (input) {
                const isDecimal = input.step === '0.01';
                let currentValue = isDecimal ? parseFloat(input.value) || 0 : parseInt(input.value) || 0;
                input.value = isDecimal ? (currentValue + pas).toFixed(2) : currentValue + pas;

                // Déclencher l'événement 'input' pour recalculer le total
                input.dispatchEvent(new Event('input'));
            }
        });
    });

    // Calcul initial au chargement
    calculerTotal();
});