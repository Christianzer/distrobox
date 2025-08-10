# 🎨 Guide des Icônes NativePHP - Distrobox

## 📁 Structure des Icônes

### Icônes Générées
Les icônes ont été automatiquement générées à partir de votre logo `public/front/logoEmaster.png` dans les formats suivants :

```
resources/icons/
├── windows/
│   └── app.ico        # Icône Windows (multi-tailles)
├── macos/
│   └── app.png        # Icône macOS haute résolution
└── linux/
    ├── app.png        # Icône principale Linux
    └── hicolor/       # Icônes système Linux
        ├── 16x16/apps/distrobox.png
        ├── 32x32/apps/distrobox.png
        ├── 48x48/apps/distrobox.png
        ├── 64x64/apps/distrobox.png
        ├── 128x128/apps/distrobox.png
        ├── 256x256/apps/distrobox.png
        └── 512x512/apps/distrobox.png
```

## 🔧 Configuration Automatique

### 1. NativeAppServiceProvider
La méthode `getAppIcon()` détecte automatiquement la plateforme et utilise l'icône appropriée :
- **Windows** : `resources/icons/windows/app.ico`
- **macOS** : `resources/icons/macos/app.icns` (ou `.png` si ICNS non disponible)
- **Linux** : `resources/icons/linux/app.png`
- **Fallback** : `public/front/logoEmaster.png`

### 2. Scripts de Build
Les scripts de build dans `scripts/build/` ont été mis à jour pour :
- Vérifier la présence des icônes
- Les copier dans le bon emplacement pour NativePHP
- Générer automatiquement les icônes si manquantes

## 🚀 Utilisation

### Génération des Icônes
```bash
# Générer toutes les icônes automatiquement
bash scripts/generate-icons.sh
```

### Build avec Icônes
```bash
# Windows
bash scripts/build/build-windows.sh

# macOS
bash scripts/build/build-macos.sh

# Linux
bash scripts/build/build-linux.sh

# Tous
bash scripts/build/build-all.sh
```

## 🎯 Formats et Tailles par Plateforme

### Windows (.ico)
- **Format** : ICO multi-tailles
- **Tailles incluses** : 16x16, 32x32, 48x48, 64x64, 128x128, 256x256
- **Usage** : Barre des tâches, explorateur de fichiers, propriétés

### macOS (.icns/.png)
- **Format préféré** : ICNS (nécessite `iconutil` sur macOS)
- **Format alternatif** : PNG haute résolution (1024x1024)
- **Tailles ICNS** : 16x16, 32x32, 128x128, 256x256, 512x512, 1024x1024 + versions @2x
- **Usage** : Dock, Finder, Launchpad, App Store

### Linux (.png)
- **Format** : PNG
- **Icône principale** : 512x512
- **Structure hicolor** : Toutes les tailles standard pour intégration système
- **Usage** : Gestionnaires de fichiers, menus d'applications, barres de tâches

## 🔄 Régénération des Icônes

Si vous modifiez le logo source (`public/front/logoEmaster.png`) :

1. **Régénération automatique** :
   ```bash
   bash scripts/generate-icons.sh
   ```

2. **Ou lors du build** : Les scripts de build vérifient et régénèrent automatiquement si nécessaire

## 📝 Personnalisation Avancée

### Modifier l'Icône Source
1. Remplacez `public/front/logoEmaster.png` par votre nouveau logo
2. Assurez-vous qu'il soit au format PNG avec transparence
3. Taille recommandée : 1024x1024 ou plus
4. Régénérez les icônes avec le script

### Configuration Manuel
Pour des besoins spécifiques, modifiez `app/Providers/NativeAppServiceProvider.php` :

```php
protected function getAppIcon(): string
{
    // Votre logique personnalisée ici
    return 'chemin/vers/votre/icone.png';
}
```

## 🛠️ Dépannage

### Icônes Non Affichées
1. Vérifiez que les fichiers d'icônes existent dans `resources/icons/`
2. Régénérez avec `bash scripts/generate-icons.sh`
3. Vérifiez les logs dans `storage/logs/` pour les erreurs de chemin

### Qualité d'Icône
- Utilisez un logo vectoriel (SVG) ou haute résolution (1024x1024+) comme source
- Évitez les détails trop fins qui ne seront pas visibles en petite taille
- Testez sur différentes tailles et arrière-plans

### Build Errors
- Assurez-vous qu'ImageMagick est installé : `magick -version`
- Vérifiez les permissions sur le dossier `resources/icons/`
- Consultez les logs de build pour plus de détails

## 📚 Ressources Utiles

- [ImageMagick Documentation](https://imagemagick.org/script/command-line-processing.php)
- [Apple Icon Guidelines](https://developer.apple.com/design/human-interface-guidelines/app-icons)
- [Windows Icon Guidelines](https://docs.microsoft.com/en-us/windows/apps/design/style/iconography/app-icons-and-logos)
- [Linux Icon Theme Specification](https://specifications.freedesktop.org/icon-theme-spec/icon-theme-spec-latest.html)