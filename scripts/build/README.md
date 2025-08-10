# Scripts de Build Distrobox

Ce dossier contient tous les scripts nécessaires pour construire l'application Distrobox pour différentes plateformes.

## 📁 Structure des scripts

- `build-windows.sh` - Build pour Windows (génère .exe)
- `build-macos.sh` - Build pour macOS (génère .app)
- `build-linux.sh` - Build pour Linux (génère exécutable)
- `build-all.sh` - Build pour toutes les plateformes
- `build-dev.sh` - Build rapide pour développement

## 🚀 Utilisation

### Script principal (recommandé)
```bash
# Depuis la racine du projet
./build.sh [plateforme]
```

### Scripts individuels
```bash
# Build Windows
./scripts/build/build-windows.sh

# Build macOS
./scripts/build/build-macos.sh

# Build Linux
./scripts/build/build-linux.sh

# Build toutes plateformes
./scripts/build/build-all.sh

# Build rapide développement
./scripts/build/build-dev.sh
```

## ⚙️ Configuration

Modifiez `build.config.json` à la racine pour personnaliser :
- Informations de l'application
- Paramètres de signature
- Options de packaging
- Configuration de distribution

## 🔧 Prérequis

### Généraux
- PHP 8.1+
- Composer
- Node.js & NPM
- NativePHP installé

### Windows
- Aucun prérequis supplémentaire pour le build de base
- Pour la signature : certificat de code Windows
- Pour l'installateur : NSIS ou Inno Setup

### macOS
- Xcode Command Line Tools (pour la signature)
- Certificat Apple Developer (pour signature et notarisation)
- `create-dmg` (pour créer des DMG)

### Linux
- Outils de packaging selon la distribution :
  - `dpkg-deb` ou `fpm` pour .deb
  - `rpmbuild` ou `fpm` pour .rpm
  - `appimagetool` pour AppImage

## 📦 Outputs

### Structure de sortie
```
dist/                     # Build simple plateforme
builds/YYYYMMDD_HHMMSS/  # Build multi-plateforme
├── windows/
├── macos/
├── linux/
├── build.log
└── README.md
```

### Types de fichiers générés

**Windows:**
- `distrobox.exe` - Exécutable principal
- Dossiers de ressources Electron

**macOS:**
- `Distrobox.app` - Bundle d'application macOS
- Dossiers de ressources

**Linux:**
- `distrobox` - Exécutable Linux
- `distrobox.desktop` - Fichier d'intégration bureau
- Dossiers de ressources

## 🔒 Signature et Distribution

### Windows
```bash
# Signature (si certificat disponible)
signtool sign /f certificate.p12 /p password /t http://timestamp.url dist/distrobox.exe

# Création installateur NSIS
makensis installer.nsi
```

### macOS
```bash
# Signature
codesign --deep --force --verify --verbose --sign "Developer ID Application: Votre Nom" dist/Distrobox.app

# Notarisation
xcrun altool --notarize-app --primary-bundle-id "com.distrobox.app" --username "email" --password "app-password" --file dist/Distrobox.app

# Création DMG
create-dmg --volname "Distrobox" --window-size 600 400 dist/Distrobox.dmg dist/Distrobox.app
```

### Linux
```bash
# Package .deb
fpm -s dir -t deb -n distrobox -v 1.0.0 --description "Application de gestion d'entreprise" dist/=/opt/distrobox

# Package .rpm  
fpm -s dir -t rpm -n distrobox -v 1.0.0 --description "Application de gestion d'entreprise" dist/=/opt/distrobox

# AppImage
appimagetool dist/ distrobox.AppImage
```

## 🐛 Dépannage

### Erreurs communes

**"Target class [Fruitcake\Cors\HandleCors] does not exist"**
```bash
composer require fruitcake/laravel-cors
```

**"TTY mode requires /dev/tty to be read/writable" (WSL)**
```bash
php artisan native:serve --no-interaction
```

**Build fails avec erreur Electron**
```bash
# Réinstaller les dépendances Electron
cd vendor/nativephp/electron
npm install
```

**Permissions denied sur les scripts**
```bash
chmod +x scripts/build/*.sh
chmod +x build.sh
```

## 📝 Logs et Debugging

- Logs de build : `builds/[timestamp]/build.log`
- Logs NativePHP : Vérifiez la sortie console
- Debug mode : Activez `APP_DEBUG=true` temporairement

## 🔄 Mise à jour des Scripts

Ces scripts sont conçus pour être facilement modifiables. N'hésitez pas à :
- Ajouter des étapes de validation
- Intégrer avec vos outils CI/CD
- Personnaliser les configurations par environnement
- Ajouter des hooks pre/post-build

## 📞 Support

Pour des problèmes spécifiques :
- Consultez la documentation NativePHP
- Vérifiez les logs d'erreur
- Testez sur la plateforme cible avant distribution