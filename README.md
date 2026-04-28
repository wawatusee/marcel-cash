# 🍞 Gestion de Caisse pour Boulangerie
*Application web pour suivre les comptes de caisse (pièces, billets, Bancontact) en temps réel.*

---

## 📌 Résumé

Cette application permet aux utilisateurs de :
- **Enregistrer** les quantités de pièces, billets, et paiements Bancontact.
- **Visualiser** le total de la caisse mis à jour automatiquement.
- **Consulter** l'historique des comptes précédents par date.
- **Gérer** les comptes via une interface admin sécurisée.

---

## 📂 Structure du Projet

```bash
boulangerie/
├── index.php          # Page principale (saisie des comptes)
├── admin.php          # Interface d'administration
├── assets/
│   ├── css/
│   │   └── style.css  # Styles (boutons ronds, design responsive)
│   └── js/
│       └── script.js  # Logique JS (calcul du total, écouteurs d'événements)
├── data/
│   ├── comptes/       # Fichiers JSON des comptes (ex: 2026-04-27.json)
│   ├── config.json    # Configuration (mot de passe admin, pas d'incrémentation)
│   └── types.json     # Types de pièces/billets (noms, valeurs)
└── includes/
    └── functions.php  # Fonctions PHP communes (ex: listerComptes())
```

---

## 🛠 Fonctionnalités Clés

### 1. Saisie des Comptes

- Champs dynamiques pour pièces, billets, et Bancontact.
- Boutons d'incrémentation (`+1`, `+5`, `+10`) pour ajouter rapidement des quantités.
- Calcul automatique du total en temps réel via JavaScript.

**Code clé — `script.js` :**
```javascript
function updateTotal() {
    let total = 0;
    document.querySelectorAll('input[type="number"]').forEach(input => {
        const quantity = parseFloat(input.value) || 0;
        const value = parseFloat(input.dataset.valeur) || 0;
        total += quantity * value;
    });
    document.getElementById('total').textContent = total.toFixed(2) + ' €';
}
```

### 2. Historique des Comptes

- Liste des comptes précédents (format `JJ/MM/AAAA`).
- Chargement d'un compte existant via paramètre URL (`?date=2026-04-27.json`).

**Code clé — `index.php` :**
```php
$fichiersComptes = listerComptes(); // Fonction dans includes/functions.php
foreach ($fichiersComptes as $fichier) {
    $dateStr = str_replace('.json', '', $fichier);
    $date = DateTime::createFromFormat('Y-m-d', $dateStr);
    echo '<li><a href="index.php?date=' . urlencode($fichier) . '">' . $date->format('d/m/Y') . '</a></li>';
}
```

### 3. Interface Admin

- Protégée par mot de passe (stocké dans `config.json`).
- Accès aux comptes précédents et gestion des données.

**Code clé — `admin.php` :**
```php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
```

### 4. Design Responsive

- Boutons ronds (style "calculette").
- Champs alignés à droite pour une lecture claire.
- Adapté mobile/desktop.

**Code clé — `style.css` :**
```css
.round-increment-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: #2196F3;
    color: white;
    border: none;
    cursor: pointer;
}
.calculator-field {
    display: flex;
    align-items: center;
    gap: 15px;
}
```

---

## 📝 Exemples de Données

### Fichier de Configuration — `data/config.json`
```json
{
    "mot_de_passe_admin": "monmotdepasse",
    "pas_incrementation": [1, 5, 10]
}
```

### Fichier de Comptes — `data/comptes/2026-04-27.json`
```json
{
    "pieces": {
        "10cts": 5,
        "20cts": 10
    },
    "billets": {
        "5euros": 2,
        "10euros": 1
    },
    "bancontact": {
        "Total Bancontact": 150.50
    },
    "total": 158.50,
    "date": "2026-04-27"
}
```

---

## 🚀 Améliorations Possibles (Backlog)

| Priorité | Fonctionnalité | Description |
|----------|----------------|-------------|
| ⭐⭐⭐ | Sauvegarde automatique | Sauvegarder les comptes toutes les X minutes (éviter les pertes de données). |
| ⭐⭐⭐ | Sécurité renforcée | Hacher le mot de passe admin et utiliser des sessions sécurisées. |
| ⭐⭐ | Export PDF/CSV | Générer un rapport des comptes (via jsPDF). |
| ⭐⭐ | Boutons de décrémentation | Ajouter des boutons `-1`, `-5`, `-10`. |
| ⭐ | Graphiques des recettes | Afficher un historique visuel (Chart.js). |
| ⭐ | Historique des modifications | Stocker les anciennes versions pour annuler des changements. |

---

## 🔧 Installation et Configuration

### 1. Installation

Copier le dossier `boulangerie/` sur un serveur local (XAMPP, WAMP) ou distant, puis vérifier les permissions :

```bash
chmod 755 data/comptes/  # Le dossier des comptes doit être accessible en écriture
```

### 2. Configuration

Modifier `data/config.json` pour :
- Définir le mot de passe admin.
- Adapter les pas d'incrémentation (`[1, 5, 10]`).

### 3. Test

Ouvrir `index.php` dans un navigateur et vérifier :
- Les boutons d'incrémentation.
- La mise à jour automatique du total.
- L'affichage des comptes précédents.

### 4. Débogage

Activer les erreurs PHP si nécessaire :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
Utiliser la console du navigateur (`F12`) pour inspecter les erreurs JavaScript.

---

## ⚠️ Points d'Attention

**Sauvegardes :**
- Toujours sauvegarder le dossier `data/comptes/` avant toute modification majeure.
- Les fichiers JSON sont critiques : une corruption peut entraîner une perte de données.

**Sécurité :**
- Ne pas exposer le projet sur un serveur public sans protection (risque d'accès aux fichiers JSON).
- Éviter de stocker des mots de passe en clair — utiliser `password_hash()`.

**Compatibilité :**
- Testé sur Chrome, Firefox, Edge.
- Responsive validé sur mobile (iOS/Android) et tablette.

---

## 📬 Contact

- **Développeur initial :** Kieran Labarrère
- Ce README peut être complété pour refléter les évolutions du projet.

*Dernière mise à jour : 28/04/2026*
