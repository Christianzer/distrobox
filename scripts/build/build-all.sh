#!/bin/bash

# Script de build pour toutes les plateformes
echo "🚀 Construction de Distrobox pour toutes les plateformes..."

# Vérification des prérequis
if [ ! -f "composer.json" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

# Dossier de build avec timestamp
BUILD_DIR="builds/$(date +%Y%m%d_%H%M%S)"
echo "📁 Création du dossier de build: $BUILD_DIR"
mkdir -p "$BUILD_DIR"

# Fonction pour logger les builds
log_build() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$BUILD_DIR/build.log"
}

log_build "Début du build multi-plateforme"

# Préparation commune
echo "🛠️  Préparation commune..."
log_build "Nettoyage des caches"
php artisan cache:clear
php artisan config:clear  
php artisan view:clear

log_build "Installation des dépendances"
composer install --optimize-autoloader --no-dev
npm run production

# Sauvegarde de l'environnement original
cp .env .env.original

# Build Windows
echo ""
echo "🪟 Build Windows..."
log_build "Début build Windows"
cp .env.original .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env

if php artisan native:build win 2>&1 | tee -a "$BUILD_DIR/build.log"; then
    log_build "Build Windows: SUCCÈS"
    if [ -d "dist" ]; then
        mkdir -p "$BUILD_DIR/windows"
        cp -r dist/* "$BUILD_DIR/windows/" 2>/dev/null || true
    fi
else
    log_build "Build Windows: ÉCHEC"
fi

# Build macOS  
echo ""
echo "🍎 Build macOS..."
log_build "Début build macOS"
cp .env.original .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
echo -e "\nNATIVEPHP_MACOS_BUNDLE_ID=com.distrobox.app" >> .env
echo "NATIVEPHP_MACOS_CATEGORY=public.app-category.business" >> .env

if php artisan native:build mac 2>&1 | tee -a "$BUILD_DIR/build.log"; then
    log_build "Build macOS: SUCCÈS"
    if [ -d "dist" ]; then
        mkdir -p "$BUILD_DIR/macos"
        cp -r dist/* "$BUILD_DIR/macos/" 2>/dev/null || true
    fi
else
    log_build "Build macOS: ÉCHEC"
fi

# Build Linux
echo ""
echo "🐧 Build Linux..."
log_build "Début build Linux"
cp .env.original .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
echo -e "\nNATIVEPHP_LINUX_CATEGORY=Office" >> .env

if php artisan native:build linux 2>&1 | tee -a "$BUILD_DIR/build.log"; then
    log_build "Build Linux: SUCCÈS"
    if [ -d "dist" ]; then
        mkdir -p "$BUILD_DIR/linux"
        cp -r dist/* "$BUILD_DIR/linux/" 2>/dev/null || true
        
        # Création du fichier .desktop pour Linux
        cat > "$BUILD_DIR/linux/distrobox.desktop" << EOF
[Desktop Entry]
Name=Distrobox
Comment=Application de gestion d'entreprise
Exec=./distrobox
Icon=distrobox
Type=Application
Categories=Office;Finance;
StartupNotify=true
EOF
    fi
else
    log_build "Build Linux: ÉCHEC"
fi

# Restauration de l'environnement original
mv .env.original .env

# Restauration des dépendances de développement
composer install
npm run dev

# Création du rapport de build
echo ""
echo "📊 Création du rapport de build..."
cat > "$BUILD_DIR/README.md" << EOF
# Build Distrobox - $(date '+%Y-%m-%d %H:%M:%S')

## Informations de build
- **Date**: $(date)
- **Version**: $(grep '"version"' package.json 2>/dev/null || echo "Non définie")
- **Commit**: $(git rev-parse --short HEAD 2>/dev/null || echo "Non disponible")
- **Branch**: $(git branch --show-current 2>/dev/null || echo "Non disponible")

## Plateformes buildées

### Windows 🪟
- Dossier: \`windows/\`
- Exécutable: \`distrobox.exe\` (si présent)

### macOS 🍎  
- Dossier: \`macos/\`
- Application: \`Distrobox.app\` (si présente)

### Linux 🐧
- Dossier: \`linux/\`
- Exécutable: \`distrobox\` (si présent)
- Fichier desktop: \`distrobox.desktop\`

## Instructions de distribution

### Windows
1. Testez l'exécutable
2. Signez avec un certificat de code
3. Créez un installateur (NSIS/Inno Setup)

### macOS
1. Testez l'application .app
2. Signez avec un certificat Apple Developer
3. Notariez l'application
4. Créez un .dmg

### Linux
1. Testez l'exécutable
2. Créez des packages (.deb, .rpm, AppImage)
3. Installez le fichier .desktop

## Logs de build
Consultez \`build.log\` pour les détails du processus de build.
EOF

log_build "Build multi-plateforme terminé"

echo "✅ Build multi-plateforme terminé!"
echo "📁 Tous les builds sont dans: $BUILD_DIR"
echo "📋 Structure créée:"
find "$BUILD_DIR" -type f | head -20

echo ""
echo "🎯 Prochaines étapes:"
echo "   1. Testez chaque build sur sa plateforme cible"
echo "   2. Signez les exécutables si nécessaire"
echo "   3. Créez des installateurs pour la distribution"
echo "   4. Consultez $BUILD_DIR/README.md pour plus de détails"