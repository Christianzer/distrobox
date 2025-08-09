# CLAUDE.md

Ce fichier fournit des conseils à Claude Code (claude.ai/code) lors du travail avec le code de ce dépôt.

## Vue d'ensemble du projet

Il s'agit d'une application Laravel 8 de gestion d'entreprise pour gérer l'inventaire, les ventes et les transactions financières. L'application comprend des modules pour :

- **Gestion des stocks** : Inventaire des produits dans plusieurs entrepôts
- **Transactions de vente** : Système de point de vente avec génération de reçus
- **Gestion des clients** : Base de données clients et suivi des crédits/débits
- **Recouvrement** : Suivi des paiements et gestion des comptes
- **Gestion de caisse** : Opérations quotidiennes de trésorerie
- **Gestion des utilisateurs** : Système multi-utilisateurs avec accès basé sur les rôles

## Commandes de développement

### Commandes PHP/Laravel
```bash
# Installer les dépendances
composer install

# Exécuter les migrations de base de données
php artisan migrate

# Générer la clé d'application
php artisan key:generate

# Démarrer le serveur de développement
php artisan serve

# Exécuter les tests
vendor/bin/phpunit
# ou
php artisan test

# Vider le cache de l'application
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Générer les fichiers autoload
composer dump-autoload
```

### Commandes Frontend/Assets
```bash
# Installer les dépendances Node.js
npm install

# Compiler les assets pour le développement
npm run dev

# Surveiller les fichiers et recompiler lors des changements
npm run watch

# Compiler les assets pour la production
npm run production
```

## Vue d'ensemble de l'architecture

### Structure de la base de données
L'application utilise plusieurs tables clés :
- `client` - Informations clients avec codes uniques (format CL)
- `transaction` - Transactions de vente principales avec codes générés (formats AU/RU/EC/DC/DP/RE)
- `produits` - Catalogue des produits
- `entrepots` - Gestion des entrepôts/emplacements
- `users` - Authentification et rôles des utilisateurs
- `paiement` - Suivi des paiements (versements/sorties)

### Contrôleurs clés et fonctionnalités
- **TransactionController** : Gère les ventes, achats, reçus et rapports quotidiens
- **RecouvrementController** : Gère les recouvrements, soldes de comptes et messagerie
- **StockController** : Gestion d'inventaire et attribution d'entrepôt
- **CaissesController** : Opérations de trésorerie et réconciliation quotidienne
- **ClientController** : Opérations CRUD clients et nettoyage de base de données

### Fonctions d'aide (app/helpers.php)
Contient des fonctions utilitaires spécifiques au métier :
- `genererCode()` - Génère des codes de transaction uniques par type
- `generateUniqueCode()` - Crée des codes clients avec format mois/année
- `convertirMontantEnLettres()` - Convertit les montants en texte français
- `formatNumber()` - Formatage des nombres pour l'affichage
- Aides à la traduction pour la localisation française

### Génération de PDF
Utilisation intensive des bibliothèques PDF pour la génération de documents :
- DOMPDF, MPDF, TCPDF, FPDF/FPDI pour divers types de documents
- Impression de reçus (`recu.blade.php`, `versement.blade.php`)
- Rapports quotidiens de point de vente (`point.blade.php`)

### Structure du Frontend
- Template d'administration basé sur Bootstrap (SB Admin 2)
- Templates Blade avec AJAX pour les mises à jour dynamiques
- DataTables pour la présentation des données
- Style personnalisé dans le répertoire `/public/front/`

## Logique métier clé

### Codes de transaction
- **AU** : Achats (Achat UV)
- **RU** : Retours (Retour UV)
- **EC** : Encaissements (Encaissement)
- **DC** : Décaissements (Décaissement)
- **DP** : Dépenses
- **RE** : Recouvrement

### Format des codes clients
`CL{MM}{YYYY}SM{XXXXXX}` - Mois, Année, numéro séquentiel

### Authentification et Middleware
- Middleware personnalisé `Activation` pour le statut utilisateur
- Authentification Laravel standard avec gestion de session
- Protection des routes via les groupes middleware `auth` et `activation`

## Notes de développement

### Exigences de base de données
Nécessite MySQL/MariaDB avec charset approprié pour la gestion du texte français.

### Téléchargements de fichiers
Stockage des photos dans `/public/photos/` avec chiffrement pour les documents sensibles.

### Localisation
Implémentation de la langue française avec fonctions personnalisées de formatage des dates/nombres.

### Tests
Configuration PHPUnit disponible mais couverture de tests minimale actuellement implémentée.