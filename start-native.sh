#!/bin/bash

# Script pour lancer l'application NativePHP sans problème de TTY
export ELECTRON_DISABLE_SANDBOX=1
export ELECTRON_NO_ATTACH_CONSOLE=1

# Aller dans le répertoire de l'application
cd /home/tooparkzer/distrobox

# Vérifier si Electron est installé
if [ ! -d "vendor/nativephp/electron/node_modules" ]; then
    echo "Installation des dépendances Electron..."
    cd vendor/nativephp/electron
    npm install --silent --no-interaction
    cd ../../../
fi

echo "🚀 Démarrage de l'application Distrobox..."
echo "💡 Une fenêtre va s'ouvrir automatiquement..."

# Lancer l'application NativePHP
php artisan native:serve --no-interaction --no-ansi