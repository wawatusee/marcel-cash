
// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('comptesForm');
    const totalSpan = document.getElementById('total');

    // Fonction pour calculer le total
    function updateTotal() {
        let total = 0;
        const inputs = form.querySelectorAll('input[type="number"]');
        inputs.forEach(input => {
            const quantity = parseFloat(input.value) || 0;
            const value = parseFloat(input.dataset.valeur) || 0;
            total += quantity * value;
        });
        totalSpan.textContent = total.toFixed(2) + ' €';
    }

    // Écouter les changements dans les inputs
    form.addEventListener('input', updateTotal);

    // Écouter les clics sur les boutons d'incrémentation
    document.querySelectorAll('.round-increment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const fieldName = this.dataset.champ;
            const step = parseFloat(this.dataset.pas);
            const input = form.querySelector(`input[name="${fieldName}"]`);
            if (input) {
                let currentValue = parseFloat(input.value) || 0;
                input.value = currentValue + step;
                updateTotal(); // Mise à jour immédiate
            }
        });
    });

    // Calcul initial
    updateTotal();
});
