#!/bin/bash

# Script de build pour Linux
echo "🏗️  Construction de Distrobox pour Linux..."

# Vérification des prérequis
if [ ! -f "composer.json" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

echo "✅ Environnement Linux détecté: $(lsb_release -d 2>/dev/null | cut -f2 || uname -s)"

# Vérification des dépendances système
echo "🔍 Vérification des dépendances système..."
missing_deps=()

if ! command -v node &> /dev/null; then
    missing_deps+=("nodejs")
fi

if ! command -v npm &> /dev/null; then
    missing_deps+=("npm")
fi

if [ ${#missing_deps[@]} -gt 0 ]; then
    echo "❌ Dépendances manquantes: ${missing_deps[*]}"
    echo "   Installez avec: sudo apt install ${missing_deps[*]} (Ubuntu/Debian)"
    echo "   ou: sudo yum install ${missing_deps[*]} (CentOS/RHEL)"
    echo "   ou: sudo pacman -S ${missing_deps[*]} (Arch Linux)"
    exit 1
fi

# Nettoyage des caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Installation/mise à jour des dépendances
echo "📦 Installation des dépendances..."
composer install --optimize-autoloader --no-dev
npm run production

# Configuration de l'environnement de production
echo "⚙️  Configuration pour la production..."
cp .env .env.backup
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env

# Configuration spécifique Linux dans .env
echo "" >> .env
echo "# Configuration Linux" >> .env
echo "NATIVEPHP_LINUX_CATEGORY=Office" >> .env

# Build NativePHP pour Linux
echo "🎯 Construction du package Linux..."
php artisan native:build linux

# Restauration de l'environnement de développement
echo "🔄 Restauration de l'environnement de développement..."
mv .env.backup .env

echo "✅ Build Linux terminé!"
echo "📁 L'application se trouve dans: dist/"
echo "📋 Fichiers créés:"
ls -la dist/ 2>/dev/null || echo "   Vérifiez le dossier de sortie de NativePHP"

echo ""
echo "🚀 Instructions de distribution:"
echo "   1. Testez l'exécutable avant distribution"
echo "   2. Créez un .deb package pour Ubuntu/Debian:"
echo "      - Utilisez dpkg-deb ou des outils comme fpm"
echo "   3. Créez un .rpm package pour CentOS/RHEL/Fedora:"
echo "      - Utilisez rpmbuild ou fpm"
echo "   4. Créez un AppImage pour une compatibilité universelle:"
echo "      - Utilisez appimagetool"
echo "   5. Pour Arch Linux, créez un PKGBUILD"

# Création d'un fichier .desktop pour l'intégration système
echo ""
echo "📄 Création du fichier .desktop..."
cat > dist/distrobox.desktop << EOF
[Desktop Entry]
Name=Distrobox
Comment=Application de gestion d'entreprise
Exec=./distrobox
Icon=distrobox
Type=Application
Categories=Office;Finance;
StartupNotify=true
EOF

echo "✅ Fichier .desktop créé dans dist/distrobox.desktop"