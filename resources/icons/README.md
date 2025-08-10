# Guide des Icônes NativePHP

## Structure des Icônes par Plateforme

### Windows (.ico)
- **Format** : ICO (recommandé : 256x256, 128x128, 64x64, 48x48, 32x32, 16x16)
- **Emplacement** : `resources/icons/windows/app.ico`

### macOS (.icns)
- **Format** : ICNS (recommandé : 1024x1024, 512x512, 256x256, 128x128, 64x64, 32x32, 16x16)
- **Emplacement** : `resources/icons/macos/app.icns`

### Linux (.png)
- **Format** : PNG (recommandé : 512x512, 256x256, 128x128, 64x64, 48x48, 32x32, 16x16)
- **Emplacement** : `resources/icons/linux/app.png` (utiliser la plus haute résolution)
- **Icônes additionnelles** : Placer toutes les tailles dans `resources/icons/linux/hicolor/{size}x{size}/apps/`

## Outils Recommandés pour la Conversion

### ImageMagick (Linux/macOS/Windows)
```bash
# Convertir PNG vers ICO (Windows)
magick convert logo.png -define icon:auto-resize=256,128,64,48,32,16 app.ico

# Convertir PNG vers ICNS (macOS)
magick convert logo.png -define icon:auto-resize=1024,512,256,128,64,32,16 app.icns
```

### Gimp
1. Ouvrir votre logo dans Gimp
2. Redimensionner à la taille souhaitée
3. Exporter au format approprié

### En ligne
- **ICO** : https://icoconvert.com/
- **ICNS** : https://iconverticons.com/online/
- **PNG** : Redimensionnement avec n'importe quel éditeur d'image

## Utilisation dans NativePHP

Une fois les icônes créées, mettez à jour votre configuration dans :
1. `app/Providers/NativeAppServiceProvider.php`
2. Les scripts de build dans `scripts/build/`

## Tailles Recommandées

- **16x16** : Barre des tâches Windows, dock macOS
- **32x32** : Icône standard Windows
- **48x48** : Icône moyenne Windows
- **64x64** : Icône standard Linux
- **128x128** : Icône grande Linux/macOS
- **256x256** : Icône très grande Windows/Linux
- **512x512** : Retina macOS
- **1024x1024** : Haute résolution macOS