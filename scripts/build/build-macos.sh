#!/bin/bash

# Script de build pour macOS
echo "🏗️  Construction de Distrobox pour macOS..."

# Vérification des prérequis
if [ ! -f "composer.json" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

# Vérification de l'environnement macOS (si exécuté sur macOS)
if [[ "$OSTYPE" == "darwin"* ]]; then
    echo "✅ Environnement macOS détecté"
    
    # Vérification de Xcode Command Line Tools
    if ! command -v xcode-select &> /dev/null; then
        echo "⚠️  Xcode Command Line Tools requis pour la signature de l'app"
        echo "   Installez avec: xcode-select --install"
    fi
else
    echo "⚠️  Build cross-platform depuis $(uname -s)"
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
sed -i.bak 's/APP_ENV=local/APP_ENV=production/' .env
sed -i.bak 's/APP_DEBUG=true/APP_DEBUG=false/' .env

# Configuration spécifique macOS dans .env
echo "" >> .env
echo "# Configuration macOS" >> .env
echo "NATIVEPHP_MACOS_BUNDLE_ID=com.distrobox.app" >> .env
echo "NATIVEPHP_MACOS_CATEGORY=public.app-category.business" >> .env

# Build NativePHP pour macOS
echo "🎯 Construction du package macOS..."
php artisan native:build mac

# Restauration de l'environnement de développement
echo "🔄 Restauration de l'environnement de développement..."
mv .env.backup .env
rm -f .env.bak

echo "✅ Build macOS terminé!"
echo "📁 L'application se trouve dans: dist/"
echo "📋 Fichiers créés:"
ls -la dist/ 2>/dev/null || echo "   Vérifiez le dossier de sortie de NativePHP"

echo ""
echo "🚀 Instructions de distribution:"
echo "   1. Testez l'application .app avant distribution"
echo "   2. Signez l'application avec vos certificats de développeur Apple:"
echo "      codesign --deep --force --verify --verbose --sign \"Developer ID Application: Votre Nom\" dist/Distrobox.app"
echo "   3. Notariez l'application pour macOS Catalina+"
echo "   4. Créez un .dmg avec des outils comme create-dmg ou Disk Utility"