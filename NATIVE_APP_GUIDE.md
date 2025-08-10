# Guide d'utilisation de l'application Desktop

## Vue d'ensemble
Votre application Laravel a été transformée en application desktop grâce à NativePHP. Elle utilise Electron pour créer une application native multiplateforme.

## Configuration actuelle
- **Framework** : Laravel 10 + NativePHP
- **Base de données** : MySQL/MariaDB (distante)
- **Fenêtre** : 1280x800 (redimensionnable, minimum 1024x768)
- **Environnement** : Local avec base de données distante

## Démarrage de l'application

### Option 1 : Script automatique (recommandé)
```bash
./start-native.sh
```

### Option 2 : Commande directe
```bash
php artisan native:serve --no-interaction
```

### Option 3 : Via Composer (si configuré)
```bash
composer run native:dev
```

## Configuration de la base de données

Assurez-vous que votre fichier `.env` contient les bonnes informations de connexion :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=votre_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

## Fonctionnalités de l'application

L'application conserve toutes ses fonctionnalités originales :

1. **Gestion des stocks** - Inventaire des produits dans plusieurs entrepôts
2. **Transactions de vente** - Système de point de vente avec génération de reçus
3. **Gestion des clients** - Base de données clients et suivi des crédits/débits
4. **Recouvrement** - Suivi des paiements et gestion des comptes
5. **Gestion de caisse** - Opérations quotidiennes de trésorerie
6. **Gestion des utilisateurs** - Système multi-utilisateurs avec rôles

## Compilation pour la production

Pour créer un exécutable distributable :

```bash
# Pour Windows
php artisan native:build --platform=win

# Pour macOS
php artisan native:build --platform=mac

# Pour Linux
php artisan native:build --platform=linux
```

## Problèmes courants

### Erreur TTY dans WSL/Linux
Si vous rencontrez des erreurs TTY, utilisez le script `start-native.sh` fourni.

### Performance
- L'application utilise Electron, elle consommera plus de mémoire qu'une application web classique
- La première ouverture peut être plus lente (chargement des dépendances)

### Base de données
- Assurez-vous que votre serveur de base de données est accessible
- Vérifiez les paramètres de pare-feu si nécessaire

## Développement

Pour le développement en mode desktop :

1. Lancez l'application : `./start-native.sh`
2. Modifiez vos fichiers PHP/Blade normalement
3. Rechargez la fenêtre (Ctrl+R) pour voir les changements

## Personnalisation de la fenêtre

Modifiez `app/Providers/NativeAppServiceProvider.php` pour personnaliser :
- Titre de la fenêtre
- Dimensions
- Icône
- Options d'affichage

```php
Window::open()
    ->title('Votre Titre')
    ->width(1400)
    ->height(900)
    ->icon('chemin/vers/icone.png');
```

## Support

- Documentation NativePHP : https://nativephp.com
- Documentation Laravel : https://laravel.com/docs