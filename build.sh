#!/bin/bash

# Script principal de build pour Distrobox
# Usage: ./build.sh [plateforme]

show_help() {
    echo "🏗️  Script de build Distrobox"
    echo ""
    echo "Usage: ./build.sh [option]"
    echo ""
    echo "Options:"
    echo "  windows, win    Build pour Windows"
    echo "  macos, mac      Build pour macOS"
    echo "  linux           Build pour Linux"
    echo "  all             Build pour toutes les plateformes"
    echo "  dev             Build rapide pour développement (plateforme actuelle)"
    echo "  help, -h        Affiche cette aide"
    echo ""
    echo "Exemples:"
    echo "  ./build.sh windows"
    echo "  ./build.sh all"
    echo "  ./build.sh dev"
}

# Vérification des prérequis
if [ ! -f "composer.json" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

# Gestion des paramètres
case "${1:-help}" in
    "windows"|"win")
        echo "🪟 Lancement du build Windows..."
        ./scripts/build/build-windows.sh
        ;;
    "macos"|"mac")
        echo "🍎 Lancement du build macOS..."
        ./scripts/build/build-macos.sh
        ;;
    "linux")
        echo "🐧 Lancement du build Linux..."
        ./scripts/build/build-linux.sh
        ;;
    "all")
        echo "🚀 Lancement du build multi-plateforme..."
        ./scripts/build/build-all.sh
        ;;
    "dev")
        echo "🛠️  Lancement du build de développement..."
        ./scripts/build/build-dev.sh
        ;;
    "help"|"-h"|*)
        show_help
        ;;
esac