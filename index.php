<?php
require_once 'includes/functions.php';

// Lire les types et la config
$types = lireTypes();
$config = lireConfig();
$pasIncrementation = $config['pas_incrementation'] ?? [1, 5, 10];

$pieces = $types['pieces'];
$billets = $types['billets'];
$bancontact = $types['bancontact'] ?? [];

$dateDuJour = date('Y-m-d');
$fichierComptes = "data/comptes/{$dateDuJour}.json";

$comptesDuJour = [];
if (file_exists($fichierComptes)) {
    $comptesDuJour = json_decode(file_get_contents($fichierComptes), true);
}

// Charger un compte spécifique si on consulte un jour précédent
$dateConsultation = $_GET['date'] ?? null;
if ($dateConsultation && file_exists("data/comptes/{$dateConsultation}.json")) {
    $comptesDuJour = json_decode(file_get_contents("data/comptes/{$dateConsultation}.json"), true);
    $dateDuJour = $dateConsultation;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Comptes de caisse - Boulangerie</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h1>Comptes de caisse
        <?= $dateConsultation ? "du " . date('d/m/Y', strtotime($dateConsultation)) : "du " . date('d/m/Y') ?>
    </h1>
    <div class="admin-button-container">
        <a href="admin.php" class="admin-button">Admin</a>
    </div>
    <!-- Lien pour revenir à aujourd'hui -->
    <?php if ($dateConsultation): ?>
        <p><a href="index.php">Retour à aujourd'hui</a></p>
    <?php endif; ?>

    <form id="comptesForm" method="post" action="index.php<?= $dateConsultation ? "?date={$dateConsultation}" : "" ?>">
        <h2>Pièces</h2>
        <?php foreach ($pieces as $piece): ?>
            <div class="calculator-field">
                <div class="increment-buttons">
                    <?php foreach ($pasIncrementation as $pas): ?>
                        <button type="button" class="round-increment-btn"
                            data-champ="pieces[<?= htmlspecialchars($piece['nom']) ?>]" data-pas="<?= $pas ?>">
                            +<?= $pas ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="field-input">
                    <label><?= htmlspecialchars($piece['nom']) ?></label>
                    <input type="number" name="pieces[<?= htmlspecialchars($piece['nom']) ?>]"
                        value="<?= $comptesDuJour['pieces'][$piece['nom']] ?? 0 ?>" data-valeur="<?= $piece['valeur'] ?>"
                        min="0">
                </div>
            </div>
        <?php endforeach; ?>

        <h2>Billets</h2>
        <?php foreach ($billets as $billet): ?>
            <div class="calculator-field">
                <div class="increment-buttons">
                    <?php foreach ($pasIncrementation as $pas): ?>
                        <button type="button" class="round-increment-btn"
                            data-champ="billets[<?= htmlspecialchars($billet['nom']) ?>]" data-pas="<?= $pas ?>">
                            +<?= $pas ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="field-input">
                    <label><?= htmlspecialchars($billet['nom']) ?></label>
                    <input type="number" name="billets[<?= htmlspecialchars($billet['nom']) ?>]"
                        value="<?= $comptesDuJour['billets'][$billet['nom']] ?? 0 ?>" data-valeur="<?= $billet['valeur'] ?>"
                        min="0">
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Bancontact -->
        <?php if (!empty($bancontact)): ?>
            <h2>Paiements par carte (Bancontact)</h2>
            <?php foreach ($bancontact as $bc): ?>
                <div class="calculator-field">
                    <div class="increment-buttons">
                        <?php foreach ($pasIncrementation as $pas): ?>
                            <button type="button" class="round-increment-btn bancontact-btn"
                                data-champ="bancontact[<?= htmlspecialchars($bc['nom']) ?>]" data-pas="<?= $pas ?>">
                                +<?= $pas ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="field-input">
                        <label><?= htmlspecialchars($bc['nom']) ?> (€)</label>
                        <input type="number" name="bancontact[<?= htmlspecialchars($bc['nom']) ?>]"
                            value="<?= $comptesDuJour['bancontact'][$bc['nom']] ?? 0 ?>" data-valeur="1" min="0" step="0.01"
                            placeholder="0.00">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <p class="total-estime">
            Total estimé : <strong><span id="total">0.00 €</span></strong>
        </p> <button type="submit" name="enregistrer">Enregistrer les comptes</button>
    </form>

    <?php
    // Traitement du formulaire
    if (isset($_POST['enregistrer'])) {
        $donnees = [
            'date' => $dateDuJour,
            'pieces' => $_POST['pieces'] ?? [],
            'billets' => $_POST['billets'] ?? [],
            'bancontact' => $_POST['bancontact'] ?? [],
            'total' => 0.0
        ];

        // Calculer le total
        foreach ($donnees['pieces'] as $nom => $quantite) {
            $valeur = array_column($pieces, 'valeur', 'nom')[$nom] ?? 0;
            $donnees['total'] += $quantite * $valeur;
        }
        foreach ($donnees['billets'] as $nom => $quantite) {
            $valeur = array_column($billets, 'valeur', 'nom')[$nom] ?? 0;
            $donnees['total'] += $quantite * $valeur;
        }
        foreach ($donnees['bancontact'] as $nom => $montant) {
            $donnees['total'] += (float) $montant;
        }

        // Sauvegarder
        sauvegarderComptes($dateDuJour, $donnees);
        echo "<p class='success'>Comptes enregistrés pour le {$dateDuJour} !</p>";
    }
    ?>

    <!-- Liste des comptes précédents -->
    <h2>Comptes précédents</h2>
    <ul class="liste-comptes">
        <?php
        $fichiersComptes = listerComptes();
        foreach ($fichiersComptes as $fichier): ?>
            <li>
                <a href="index.php?date=<?= urlencode($fichier) ?>">
                    <?= date('d/m/Y', strtotime($fichier)) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <script src="assets/js/script.js"></script>
</body>

</html>