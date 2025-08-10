#!/bin/bash

# Script de création de release pour Distrobox
# Usage: ./scripts/release.sh [version] [description]

set -e

# Configuration
GITHUB_OWNER="${GITHUB_OWNER:-votre-username}"
GITHUB_REPO="${GITHUB_REPO:-distrobox}"
GITHUB_TOKEN="${GITHUB_TOKEN:-votre_github_token}"

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction d'aide
show_help() {
    echo -e "${BLUE}🚀 Script de création de release Distrobox${NC}"
    echo ""
    echo "Usage: $0 [version] [description]"
    echo ""
    echo "Arguments:"
    echo "  version      Version à créer (ex: 1.1.0, 2.0.0-beta.1)"
    echo "  description  Description de la release (optionnel)"
    echo ""
    echo "Variables d'environnement:"
    echo "  GITHUB_OWNER  Propriétaire du repository GitHub"
    echo "  GITHUB_REPO   Nom du repository GitHub"  
    echo "  GITHUB_TOKEN  Token d'accès GitHub"
    echo ""
    echo "Exemples:"
    echo "  $0 1.1.0"
    echo "  $0 1.1.0 \"Nouvelle version avec corrections de bugs\""
    echo "  $0 2.0.0-beta.1 \"Version beta avec nouvelles fonctionnalités\""
}

# Vérification des paramètres
if [ "$1" = "-h" ] || [ "$1" = "--help" ]; then
    show_help
    exit 0
fi

if [ -z "$1" ]; then
    echo -e "${RED}❌ Erreur: Version requise${NC}"
    show_help
    exit 1
fi

VERSION="$1"
DESCRIPTION="${2:-Nouvelle version $VERSION}"

# Vérification des prérequis
if [ ! -f "composer.json" ]; then
    echo -e "${RED}❌ Erreur: Ce script doit être exécuté depuis la racine du projet${NC}"
    exit 1
fi

if ! command -v git &> /dev/null; then
    echo -e "${RED}❌ Erreur: Git n'est pas installé${NC}"
    exit 1
fi

if ! command -v gh &> /dev/null; then
    echo -e "${YELLOW}⚠️  GitHub CLI non trouvé, utilisation de curl pour les API calls${NC}"
    USE_GH_CLI=false
else
    USE_GH_CLI=true
fi

# Validation du format de version
if [[ ! $VERSION =~ ^[0-9]+\.[0-9]+\.[0-9]+(-[a-zA-Z0-9.-]+)?$ ]]; then
    echo -e "${RED}❌ Erreur: Format de version invalide (utilisez x.y.z ou x.y.z-suffix)${NC}"
    exit 1
fi

echo -e "${BLUE}🏗️  Création de la release v$VERSION${NC}"

# Vérification du repository Git
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}❌ Erreur: Pas dans un repository Git${NC}"
    exit 1
fi

# Vérification qu'on est sur la branche main/master
CURRENT_BRANCH=$(git branch --show-current)
if [[ "$CURRENT_BRANCH" != "main" && "$CURRENT_BRANCH" != "master" ]]; then
    echo -e "${YELLOW}⚠️  Attention: Vous n'êtes pas sur main/master (branche: $CURRENT_BRANCH)${NC}"
    read -p "Continuer? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Vérification des changements non commités
if ! git diff --quiet; then
    echo -e "${RED}❌ Erreur: Des changements non commités sont présents${NC}"
    echo "Veuillez commiter ou stash vos changements avant de continuer."
    exit 1
fi

# Mise à jour de la version dans les fichiers de configuration
echo -e "${BLUE}📝 Mise à jour de la version dans les fichiers...${NC}"

# Mise à jour du .env
if [ -f ".env" ]; then
    sed -i.bak "s/^NATIVEPHP_APP_VERSION=.*/NATIVEPHP_APP_VERSION=$VERSION/" .env
    echo "✅ .env mis à jour"
fi

# Mise à jour du package.json si présent
if [ -f "package.json" ]; then
    sed -i.bak "s/\"version\": \"[^\"]*\"/\"version\": \"$VERSION\"/" package.json
    echo "✅ package.json mis à jour"
fi

# Mise à jour du config/app.php si nécessaire
if grep -q "build_date" config/app.php 2>/dev/null; then
    sed -i.bak "s/'build_date' => '[^']*'/'build_date' => '$(date -u +%Y-%m-%dT%H:%M:%SZ)'/" config/app.php
else
    # Ajouter la date de build si elle n'existe pas
    echo "    'build_date' => '$(date -u +%Y-%m-%dT%H:%M:%SZ)'," >> config/app.php
fi

# Build des applications pour toutes les plateformes
echo -e "${BLUE}🔨 Build des applications...${NC}"
if [ -f "./build.sh" ]; then
    ./build.sh all
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Erreur lors du build${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}⚠️  Script de build non trouvé, continuons sans build${NC}"
fi

# Commit des changements de version
echo -e "${BLUE}💾 Commit des changements...${NC}"
git add .
git commit -m "chore: bump version to $VERSION

🚀 Release $VERSION

$DESCRIPTION

🤖 Generated with release script" || true

# Création du tag
echo -e "${BLUE}🏷️  Création du tag v$VERSION...${NC}"
git tag -a "v$VERSION" -m "Release $VERSION

$DESCRIPTION

Changelog:
- $(git log --oneline --since="$(git describe --tags --abbrev=0 HEAD^ 2>/dev/null || echo '1 month ago')" --pretty=format:"- %s" | head -10)

🤖 Auto-generated release"

# Push des changements et du tag
echo -e "${BLUE}📤 Push vers GitHub...${NC}"
git push origin "$CURRENT_BRANCH"
git push origin "v$VERSION"

# Création de la release GitHub
echo -e "${BLUE}🎉 Création de la release GitHub...${NC}"

if [ "$USE_GH_CLI" = true ]; then
    # Utilisation de GitHub CLI
    gh release create "v$VERSION" \
        --title "Distrobox v$VERSION" \
        --notes "$DESCRIPTION

## 🆕 Nouveautés

$(git log --oneline --since="$(git describe --tags --abbrev=0 HEAD^ 2>/dev/null || echo '1 month ago')" --pretty=format:"- %s" | head -5)

## 📦 Installation

Téléchargez la version correspondant à votre système d'exploitation ci-dessous.

## 🔄 Mise à jour automatique

Si vous avez déjà Distrobox installé, la mise à jour se fera automatiquement au prochain démarrage (si activée dans les paramètres).

---

🤖 Release générée automatiquement" \
        $(find builds/ -name "*.exe" -o -name "*.app" -o -name "distrobox" -o -name "*.deb" -o -name "*.rpm" -o -name "*.AppImage" 2>/dev/null | head -10) 2>/dev/null || true

else
    # Utilisation de curl pour l'API GitHub
    if [ -n "$GITHUB_TOKEN" ]; then
        curl -X POST \
            -H "Authorization: token $GITHUB_TOKEN" \
            -H "Accept: application/vnd.github.v3+json" \
            "https://api.github.com/repos/$GITHUB_OWNER/$GITHUB_REPO/releases" \
            -d "{
                \"tag_name\": \"v$VERSION\",
                \"name\": \"Distrobox v$VERSION\",
                \"body\": \"$DESCRIPTION\\n\\n## Installation\\n\\nTéléchargez la version correspondant à votre système d'exploitation.\\n\\n🤖 Release générée automatiquement\",
                \"draft\": false,
                \"prerelease\": $(if [[ $VERSION =~ -[a-zA-Z] ]]; then echo true; else echo false; fi)
            }"
    fi
fi

# Nettoyage des fichiers de sauvegarde
rm -f .env.bak package.json.bak config/app.php.bak 2>/dev/null || true

echo -e "${GREEN}✅ Release v$VERSION créée avec succès !${NC}"
echo ""
echo -e "${BLUE}📋 Résumé :${NC}"
echo "  • Version: $VERSION"
echo "  • Tag: v$VERSION"  
echo "  • Branche: $CURRENT_BRANCH"
echo "  • Description: $DESCRIPTION"
echo ""
echo -e "${BLUE}🔗 Liens utiles :${NC}"
echo "  • Release: https://github.com/$GITHUB_OWNER/$GITHUB_REPO/releases/tag/v$VERSION"
echo "  • Repository: https://github.com/$GITHUB_OWNER/$GITHUB_REPO"
echo ""
echo -e "${GREEN}🎉 Votre application peut maintenant se mettre à jour automatiquement vers cette version !${NC}"