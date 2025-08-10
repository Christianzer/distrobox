#!/bin/bash

# Script de build pour Windows
echo "🏗️  Construction de Distrobox pour Windows..."

# Vérification des prérequis
if [ ! -f "composer.json" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
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

# Copie de l'icône Windows
echo "🎨 Configuration de l'icône Windows..."
if [ -f "resources/icons/windows/app.ico" ]; then
    mkdir -p storage/app/native/
    cp resources/icons/windows/app.ico storage/app/native/icon.ico
    echo "✅ Icône Windows copiée"
else
    echo "⚠️  Icône Windows non trouvée, génération automatique..."
    bash scripts/generate-icons.sh
fi

# Build NativePHP pour Windows
echo "🎯 Construction du package Windows..."
php artisan native:build win

# Restauration de l'environnement de développement
echo "🔄 Restauration de l'environnement de développement..."
mv .env.backup .env

echo "✅ Build Windows terminé!"
echo "📁 L'exécutable se trouve dans: dist/"
echo "📋 Fichiers créés:"
ls -la dist/ 2>/dev/null || echo "   Vérifiez le dossier de sortie de NativePHP"

echo ""
echo "🚀 Instructions de distribution:"
echo "   1. Testez l'exécutable avant distribution"
echo "   2. Créez un installateur avec NSIS ou Inno Setup si nécessaire"
echo "   3. Signez l'exécutable pour éviter les avertissements Windows"