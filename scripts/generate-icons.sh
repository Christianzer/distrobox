#!/bin/bash

# Script de génération d'icônes pour NativePHP
# Génère les icônes pour Windows, macOS et Linux à partir du logo source

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SOURCE_LOGO="public/front/logoEmaster.png"
ICONS_DIR="resources/icons"
TEMP_DIR="temp_icons"

# Vérifier si ImageMagick est installé
check_imagemagick() {
    if ! command -v magick &> /dev/null && ! command -v convert &> /dev/null; then
        echo -e "${RED}❌ ImageMagick n'est pas installé.${NC}"
        echo -e "${YELLOW}Installation sur Ubuntu/Debian:${NC} sudo apt install imagemagick"
        echo -e "${YELLOW}Installation sur macOS:${NC} brew install imagemagick"
        echo -e "${YELLOW}Installation sur Windows:${NC} Télécharger depuis https://imagemagick.org/script/download.php"
        exit 1
    fi
    echo -e "${GREEN}✅ ImageMagick détecté${NC}"
}

# Créer les dossiers nécessaires
create_directories() {
    echo -e "${BLUE}📁 Création des dossiers...${NC}"
    mkdir -p "$ICONS_DIR"/{windows,macos,linux}
    mkdir -p "$TEMP_DIR"
}

# Vérifier que le logo source existe
check_source_logo() {
    if [ ! -f "$SOURCE_LOGO" ]; then
        echo -e "${RED}❌ Logo source non trouvé: $SOURCE_LOGO${NC}"
        exit 1
    fi
    echo -e "${GREEN}✅ Logo source trouvé: $SOURCE_LOGO${NC}"
}

# Générer l'icône Windows (.ico)
generate_windows_icon() {
    echo -e "${BLUE}🪟 Génération de l'icône Windows...${NC}"
    
    # Utiliser convert ou magick selon la version d'ImageMagick
    if command -v magick &> /dev/null; then
        MAGICK_CMD="magick"
    else
        MAGICK_CMD="convert"
    fi
    
    $MAGICK_CMD "$SOURCE_LOGO" \
        -background transparent \
        \( -clone 0 -resize 16x16 \) \
        \( -clone 0 -resize 32x32 \) \
        \( -clone 0 -resize 48x48 \) \
        \( -clone 0 -resize 64x64 \) \
        \( -clone 0 -resize 128x128 \) \
        \( -clone 0 -resize 256x256 \) \
        -delete 0 \
        "$ICONS_DIR/windows/app.ico"
    
    echo -e "${GREEN}✅ Icône Windows créée: $ICONS_DIR/windows/app.ico${NC}"
}

# Générer l'icône macOS (.icns)
generate_macos_icon() {
    echo -e "${BLUE}🍎 Génération de l'icône macOS...${NC}"
    
    # Créer les tailles pour iconset
    local iconset_dir="$TEMP_DIR/app.iconset"
    mkdir -p "$iconset_dir"
    
    # Générer toutes les tailles requises pour macOS
    declare -a sizes=("16" "32" "128" "256" "512")
    declare -a retina_sizes=("32" "64" "256" "512" "1024")
    
    if command -v magick &> /dev/null; then
        MAGICK_CMD="magick"
    else
        MAGICK_CMD="convert"
    fi
    
    # Tailles normales
    for i in "${!sizes[@]}"; do
        size=${sizes[i]}
        $MAGICK_CMD "$SOURCE_LOGO" -resize ${size}x${size} "$iconset_dir/icon_${size}x${size}.png"
    done
    
    # Tailles retina (@2x)
    for i in "${!sizes[@]}"; do
        size=${sizes[i]}
        retina_size=${retina_sizes[i]}
        $MAGICK_CMD "$SOURCE_LOGO" -resize ${retina_size}x${retina_size} "$iconset_dir/icon_${size}x${size}@2x.png"
    done
    
    # Créer l'icône ICNS si iconutil est disponible (macOS uniquement)
    if command -v iconutil &> /dev/null; then
        iconutil -c icns "$iconset_dir" -o "$ICONS_DIR/macos/app.icns"
        echo -e "${GREEN}✅ Icône macOS créée avec iconutil: $ICONS_DIR/macos/app.icns${NC}"
    else
        # Alternative avec ImageMagick
        $MAGICK_CMD "$SOURCE_LOGO" -resize 1024x1024 "$ICONS_DIR/macos/app.png"
        echo -e "${YELLOW}⚠️  iconutil non disponible. PNG haute résolution créé: $ICONS_DIR/macos/app.png${NC}"
        echo -e "${YELLOW}   Pour créer l'ICNS sur macOS: iconutil -c icns $iconset_dir${NC}"
    fi
}

# Générer les icônes Linux (.png)
generate_linux_icons() {
    echo -e "${BLUE}🐧 Génération des icônes Linux...${NC}"
    
    if command -v magick &> /dev/null; then
        MAGICK_CMD="magick"
    else
        MAGICK_CMD="convert"
    fi
    
    # Icône principale haute résolution
    $MAGICK_CMD "$SOURCE_LOGO" -resize 512x512 "$ICONS_DIR/linux/app.png"
    
    # Créer la structure hicolor pour les différentes tailles
    declare -a linux_sizes=("16" "24" "32" "48" "64" "96" "128" "192" "256" "512")
    
    for size in "${linux_sizes[@]}"; do
        size_dir="$ICONS_DIR/linux/hicolor/${size}x${size}/apps"
        mkdir -p "$size_dir"
        $MAGICK_CMD "$SOURCE_LOGO" -resize ${size}x${size} "$size_dir/distrobox.png"
    done
    
    echo -e "${GREEN}✅ Icônes Linux créées dans: $ICONS_DIR/linux/${NC}"
}

# Nettoyer les fichiers temporaires
cleanup() {
    echo -e "${BLUE}🧹 Nettoyage des fichiers temporaires...${NC}"
    rm -rf "$TEMP_DIR"
}

# Afficher un résumé
show_summary() {
    echo -e "\n${GREEN}🎉 Génération d'icônes terminée !${NC}\n"
    echo -e "${BLUE}📊 Résumé:${NC}"
    echo -e "  Windows: $ICONS_DIR/windows/app.ico"
    echo -e "  macOS:   $ICONS_DIR/macos/app.icns (ou .png)"
    echo -e "  Linux:   $ICONS_DIR/linux/app.png + structure hicolor"
    echo -e "\n${YELLOW}📝 Prochaines étapes:${NC}"
    echo -e "  1. Mettre à jour NativeAppServiceProvider.php"
    echo -e "  2. Configurer les scripts de build"
    echo -e "  3. Tester sur chaque plateforme"
}

# Fonction principale
main() {
    echo -e "${BLUE}🚀 Génération d'icônes NativePHP pour Distrobox${NC}\n"
    
    check_imagemagick
    check_source_logo
    create_directories
    
    generate_windows_icon
    generate_macos_icon
    generate_linux_icons
    
    cleanup
    show_summary
}

# Exécuter si le script est appelé directement
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi