#!/bin/bash

# Script de build rapide pour le développement
echo "🚀 Build rapide de Distrobox pour tests..."

# Vérification des prérequis
if [ ! -f "composer.json" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

# Détecter la plateforme actuelle
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    PLATFORM="linux"
    PLATFORM_NAME="Linux 🐧"
elif [[ "$OSTYPE" == "darwin"* ]]; then
    PLATFORM="mac"
    PLATFORM_NAME="macOS 🍎"
elif [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "win32" ]]; then
    PLATFORM="win"
    PLATFORM_NAME="Windows 🪟"
else
    echo "⚠️  Plateforme non détectée, utilisation de Linux par défaut"
    PLATFORM="linux"
    PLATFORM_NAME="Linux (défaut) 🐧"
fi

echo "🎯 Build pour $PLATFORM_NAME"

# Nettoyage léger (garde le cache pour la rapidité)
echo "🧹 Nettoyage léger..."
php artisan config:clear
php artisan view:clear

# Build des assets en mode dev (plus rapide)
echo "🛠️  Build des assets (mode dev)..."
npm run dev

# Sauvegarde de l'environnement
cp .env .env.dev.backup

# Configuration pour build de test
sed -i.bak 's/APP_ENV=local/APP_ENV=production/' .env
sed -i.bak 's/APP_DEBUG=true/APP_DEBUG=false/' .env

# Build pour la plateforme actuelle
echo "📦 Construction du package $PLATFORM_NAME..."
if php artisan native:build $PLATFORM; then
    echo "✅ Build $PLATFORM_NAME réussi!"
    
    # Afficher les résultats
    if [ -d "dist" ]; then
        echo "📁 Fichiers créés dans dist/:"
        ls -la dist/
    fi
    
    echo ""
    echo "🧪 Test rapide:"
    echo "   Testez l'application générée avant utilisation"
    
else
    echo "❌ Échec du build $PLATFORM_NAME"
fi

# Restauration de l'environnement
mv .env.dev.backup .env
rm -f .env.bak

echo "🔄 Environnement de développement restauré"