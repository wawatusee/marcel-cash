<?php
require_once 'includes/functions.php';

// Mot de passe admin
$motDePasseAdmin = 'passeLeOinj';

// Vérifier l'authentification
session_start();
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    if (isset($_POST['password']) && $_POST['password'] === $motDePasseAdmin) {
        $_SESSION['admin'] = true;
    } else {
        if (isset($_POST['password'])) {
            echo "<p style='color: red;'>Mot de passe incorrect.</p>";
        }
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Connexion Admin</title>
        </head>

        <body>
            <h1>Connexion Admin</h1>
            <form method="post">
                <label>Mot de passe : <input type="password" name="password" required></label>
                <button type="submit">Se connecter</button>
            </form>
        </body>

        </html>
        <?php
        exit;
    }
}

// Traiter la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Lire les types et la config
$types = lireTypes();
$config = lireConfig();
$pasIncrementation = $config['pas_incrementation'] ?? [1, 5, 10];

// Traiter les modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_piece'])) {
        $nom = htmlspecialchars($_POST['nom']);
        $valeur = (float) $_POST['valeur'];
        $types['pieces'][] = ['nom' => $nom, 'valeur' => $valeur];
    } elseif (isset($_POST['ajouter_billet'])) {
        $nom = htmlspecialchars($_POST['nom']);
        $valeur = (float) $_POST['valeur'];
        $types['billets'][] = ['nom' => $nom, 'valeur' => $valeur];
    } elseif (isset($_POST['ajouter_bancontact'])) {
        $nom = htmlspecialchars($_POST['nom']);
        $types['bancontact'][] = ['nom' => $nom, 'valeur' => 1.00];
    } elseif (isset($_POST['supprimer'])) {
        $type = $_POST['type'];
        $index = (int) $_POST['index'];
        array_splice($types[$type], $index, 1);
    } elseif (isset($_POST['mettre_a_jour_pas'])) {
        // Mettre à jour les pas d'incrémentation
        $config['pas_incrementation'] = array_map('intval', explode(',', $_POST['pas_incrementation']));
        file_put_contents('data/config.json', json_encode($config, JSON_PRETTY_PRINT));
        header('Location: admin.php');
        exit;
    }

    // Sauvegarder les modifications des types
    file_put_contents('data/types.json', json_encode($types, JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin - Gestion des types et paramètres</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h1>Administration</h1>
    <p>
        <a href="index.php" class="retour-caisse-button">Retour à la caisse</a>
    </p>
    <p><a href="admin.php?logout=1">Déconnexion</a></p>

    <!-- Paramétrage des pas d'incrémentation -->
    <h2>Paramétrer les pas d'incrémentation</h2>
    <form method="post">
        <label>
            Pas d'incrémentation (séparés par des virgules) :
            <input type="text" name="pas_incrementation" value="<?= implode(',', $pasIncrementation) ?>"
                placeholder="Ex: 1,5,10" required>
        </label>
        <button type="submit" name="mettre_a_jour_pas">Mettre à jour</button>
    </form>

    <!-- Ajouter une pièce -->
    <h2>Ajouter une pièce</h2>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label>
        <label>Valeur (€) : <input type="number" step="0.01" name="valeur" required></label>
        <button type="submit" name="ajouter_piece">Ajouter</button>
    </form>

    <!-- Ajouter un billet -->
    <h2>Ajouter un billet</h2>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label>
        <label>Valeur (€) : <input type="number" step="0.01" name="valeur" required></label>
        <button type="submit" name="ajouter_billet">Ajouter</button>
    </form>

    <!-- Ajouter un type Bancontact -->
    <h2>Ajouter un type de paiement Bancontact</h2>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label>
        <button type="submit" name="ajouter_bancontact">Ajouter</button>
    </form>

    <!-- Liste des pièces -->
    <h2>Pièces existantes</h2>
    <ul>
        <?php foreach ($types['pieces'] as $index => $piece): ?>
            <li>
                <?= htmlspecialchars($piece['nom']) ?> (<?= $piece['valeur'] ?> €)
                <form method="post" style="display: inline;">
                    <input type="hidden" name="type" value="pieces">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="supprimer">Supprimer</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Liste des billets -->
    <h2>Billets existants</h2>
    <ul>
        <?php foreach ($types['billets'] as $index => $billet): ?>
            <li>
                <?= htmlspecialchars($billet['nom']) ?> (<?= $billet['valeur'] ?> €)
                <form method="post" style="display: inline;">
                    <input type="hidden" name="type" value="billets">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="supprimer">Supprimer</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Liste des types Bancontact -->
    <h2>Les paiements bancontact, champs libre</h2>
    <ul>
        <?php foreach ($types['bancontact'] ?? [] as $index => $bc): ?>
            <li>
                <?= htmlspecialchars($bc['nom']) ?>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="type" value="bancontact">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="supprimer">Supprimer</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>