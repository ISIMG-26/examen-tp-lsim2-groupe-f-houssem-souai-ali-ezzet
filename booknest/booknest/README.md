# BookNest — Librairie en ligne

Mini-projet Web — LSIM 2 · Technologies & Programmation Web · 2025-2026

---

## 👥 Membres du groupe

| Prénom | Nom | Section |
|--------|-----|---------|
| *(à compléter)* | *(à compléter)* | Groupe ? |

---

## 📖 Description du projet

**BookNest** est une application e-commerce de librairie en ligne développée en HTML, CSS, JavaScript natif et PHP natif avec une base de données MySQL.

Elle permet :
- de parcourir un catalogue de livres par catégorie
- de rechercher des livres en temps réel (AJAX)
- de gérer un panier (ajout, modification de quantité, suppression)
- de s'inscrire / se connecter
- de passer des commandes et consulter son historique
- d'administrer les livres, commandes et utilisateurs (rôle admin)

---

## 🗂 Structure du projet

```
booknest/
├── index.php          ← Page d'accueil / catalogue
├── auth.php           ← Connexion & Inscription
├── cart.php           ← Panier
├── orders.php         ← Historique des commandes
├── admin.php          ← Panel d'administration
├── css/
│   └── style.css      ← Feuille de style externe
├── js/
│   └── main.js        ← JavaScript externe
├── back/
│   ├── config.php     ← Configuration BDD + helpers
│   ├── header.php     ← Header commun
│   ├── footer.php     ← Footer commun
│   ├── logout.php     ← Déconnexion
│   ├── search.php     ← AJAX : recherche livres
│   ├── check_email.php← AJAX : vérification email
│   ├── order.php      ← Traitement commandes
│   ├── save_book.php  ← Ajout / modification livre
│   ├── delete_book.php← Suppression livre
│   ├── update_order.php← Mise à jour statut commande
│   └── delete_order.php← Suppression commande
├── database/
│   └── script.sql     ← Script SQL complet
└── images/            ← Dossier images (facultatif)
```

---

## ⚙️ Installation locale (XAMPP / WAMP)

1. Copier le dossier `booknest/` dans `htdocs/` (XAMPP) ou `www/` (WAMP)
2. Ouvrir **phpMyAdmin**
3. Créer une base `booknest` et importer `database/script.sql`
4. Vérifier les identifiants dans `back/config.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');      // mot de passe MySQL
   define('DB_NAME', 'booknest');
   ```
5. Accéder à : `http://localhost/booknest/`

### Compte admin par défaut
- **Email :** `admin@booknest.tn`
- **Mot de passe :** `password` *(changer en production)*

> ⚠️ Le hash par défaut dans le SQL correspond au mot de passe `password`.  
> Pour en générer un nouveau : `<?php echo password_hash('VotreMotDePasse', PASSWORD_DEFAULT); ?>`

---

## ✅ Checklist des fonctionnalités

### Partie 1 — Structure HTML
- [x] Au minimum 3 pages interconnectées (index, auth, cart, orders, admin)
- [x] Balises sémantiques : `<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`, `<aside>`
- [x] Menu de navigation fonctionnel sur toutes les pages

### Partie 2 — CSS externe
- [x] Fichier `css/style.css` externe
- [x] Cohérence graphique (palette, typographie Playfair Display + DM Sans)
- [x] Mise en page structurée (grid, flexbox, responsive)

### Partie 3 — JavaScript DOM
- [x] `getElementById`, `querySelector`, `querySelectorAll`
- [x] Modification dynamique du contenu (rendu panier, résultats AJAX)
- [x] Ajout / suppression d'éléments (articles panier, notifications)
- [x] Gestion d'événements (click, input, submit, change)
- [x] Fonctionnalités interactives : panier dynamique, filtres catégories, accordéon commandes, modal

### Partie 4 — Validation formulaires JS
- [x] Champs obligatoires vérifiés
- [x] Validation email, mot de passe (format), téléphone, prix
- [x] Messages d'erreur affichés sous chaque champ
- [x] Blocage de la soumission si données invalides

### Partie 5 — AJAX
- [x] `fetch()` utilisé dans `search.php` et `check_email.php`
- [x] Mise à jour sans rechargement : résultats de recherche, vérification email inscription
- [x] Debounce sur la recherche (350 ms)

### Partie 6 — PHP Back-end
- [x] `$_POST` et `$_GET` utilisés
- [x] Traitement complet des formulaires (connexion, inscription, commande)
- [x] Scripts distincts par fonctionnalité (`order.php`, `save_book.php`, etc.)
- [x] Contenu dynamique généré (catalogue, commandes, admin)

### Partie 7 — Base de données MySQL
- [x] 4 tables liées : `utilisateurs`, `livres`, `commandes`, `commande_details`
- [x] SELECT (catalogue, commandes, recherche)
- [x] INSERT (inscription, ajout livre, commande)
- [x] UPDATE (modifier livre, statut commande)
- [x] DELETE (supprimer livre, supprimer commande)

### Partie 8 — Qualité
- [x] Design éditorial soigné, palette cohérente crème/marron/rouge
- [x] Navigation intuitive avec menu sticky et breadcrumbs
- [x] Interface responsive (mobile, tablette, desktop)

### Soumission GitHub
- [x] Projet déposé via GitHub Classroom
- [ ] Commits réguliers (à faire pendant le développement)
- [x] README complet

---

## 🧩 Répartition des tâches

| Tâche | Membre |
|-------|--------|
| Structure HTML & pages | *(à compléter)* |
| CSS & design | *(à compléter)* |
| JavaScript DOM & panier | *(à compléter)* |
| PHP back-end & BDD | *(à compléter)* |
| AJAX (recherche, email) | *(à compléter)* |
| Admin panel | *(à compléter)* |

---

## 🛠 Technologies utilisées

- **Front-end :** HTML5, CSS3, JavaScript ES6+ (natif, sans framework)
- **Back-end :** PHP 8+ natif (sans Laravel ni autre framework)
- **Base de données :** MySQL via PDO
- **Polices :** Google Fonts (Playfair Display, DM Sans)
- **Serveur local :** XAMPP / WAMP
