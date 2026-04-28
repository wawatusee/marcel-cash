<?php
// Lire le fichier types.json
function lireTypes() {
    $fichier = 'data/types.json';
    if (!file_exists($fichier)) {
        die("Le fichier types.json est introuvable.");
    }
    $contenu = file_get_contents($fichier);
    return json_decode($contenu, true);
}

// Lire le fichier config.json
function lireConfig() {
    $fichier = 'data/config.json';
    if (!file_exists($fichier)) {
        // Créer le fichier avec des valeurs par défaut
        $configDefault = ['pas_incrementation' => [1, 5, 10]];
        file_put_contents($fichier, json_encode($configDefault, JSON_PRETTY_PRINT));
        return $configDefault;
    }
    $contenu = file_get_contents($fichier);
    return json_decode($contenu, true);
}

// Sauvegarder les comptes du jour
function sauvegarderComptes($date, $donnees) {
    $dossier = 'data/comptes';
    if (!file_exists($dossier)) {
        mkdir($dossier, 0755, true);
    }
    $fichier = "$dossier/{$date}.json";
    file_put_contents($fichier, json_encode($donnees, JSON_PRETTY_PRINT));
}

// Lister tous les fichiers de comptes
function listerComptes() {
    $dossier = 'data/comptes';
    $fichiers = [];
    if (file_exists($dossier)) {
        $elements = scandir($dossier);
        foreach ($elements as $element) {
            if ($element !== '.' && $element !== '..' && pathinfo($element, PATHINFO_EXTENSION) === 'json') {
                $fichiers[] = $element;
            }
        }
        // Trier par date (du plus récent au plus ancien)
        rsort($fichiers);
    }
    return $fichiers;
}
?>