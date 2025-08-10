# Guide des Mises à jour Automatiques - Distrobox

## 📋 Vue d'ensemble

Distrobox intègre un système de mise à jour automatique basé sur NativePHP qui permet :
- ✅ Vérification automatique des nouvelles versions
- 🔄 Téléchargement et installation automatiques
- 📱 Notifications à l'utilisateur  
- 🎛️ Interface de gestion des mises à jour
- 📊 Historique des mises à jour

## 🔧 Configuration

### Variables d'environnement (.env)

```env
# Configuration NativePHP
NATIVEPHP_APP_VERSION=1.0.0
NATIVEPHP_UPDATER_ENABLED=true
NATIVEPHP_UPDATER_PROVIDER=github
NATIVEPHP_UPDATE_CHECK_INTERVAL=3600

# Configuration GitHub
GITHUB_OWNER=votre-username
GITHUB_REPO=distrobox
GITHUB_TOKEN=votre_github_token
GITHUB_V_PREFIXED_TAG_NAME=true
GITHUB_PRIVATE=false
GITHUB_CHANNEL=latest
GITHUB_RELEASE_TYPE=release
```

### Providers supportés

1. **GitHub** (recommandé)
   - Utilise les releases GitHub
   - Support des versions beta/alpha via les tags
   - Authentification via token (optionnelle pour repos publics)

2. **S3/Spaces**
   - Stockage sur AWS S3 ou DigitalOcean Spaces
   - Plus de contrôle sur la distribution
   - Idéal pour entreprises

## 🏗️ Architecture

### Composants principaux

1. **UpdateManager** (`app/Services/UpdateManager.php`)
   - Gestion logique des mises à jour
   - Vérification des versions
   - Interface avec les providers

2. **UpdateController** (`app/Http/Controllers/UpdateController.php`)
   - API REST pour l'interface web
   - Gestion des paramètres utilisateur

3. **NativeAppServiceProvider** 
   - Intégration avec les menus natifs
   - Vérification au démarrage
   - Notifications système

### Flux de mise à jour

```
1. Vérification périodique ou manuelle
2. Comparaison versions (locale vs distante)
3. Notification à l'utilisateur
4. Téléchargement (automatique ou sur demande)
5. Installation et redémarrage
6. Historique mis à jour
```

## 🖥️ Interface utilisateur

### Page de gestion (`/page/updates`)

- **Informations de version** : Version actuelle, dernière vérification
- **Contrôles** : Vérification manuelle, toggle auto-update
- **Historique** : Journal des mises à jour précédentes
- **Paramètres avancés** : Canal, fréquence, notifications

### Menu natif

- **"À propos"** : Informations sur la version
- **"Vérifier les mises à jour"** : Lancement manuel
- **Raccourcis clavier** : Cmd/Ctrl+, pour préférences

## 📦 Création de releases

### Script automatisé

```bash
# Création d'une release simple
./scripts/release.sh 1.1.0

# Avec description personnalisée
./scripts/release.sh 1.1.0 "Corrections de bugs critiques"

# Version beta
./scripts/release.sh 2.0.0-beta.1 "Nouvelles fonctionnalités en test"
```

### Processus automatique

1. **Validation** : Format version, état Git
2. **Mise à jour** : Fichiers de configuration
3. **Build** : Applications toutes plateformes  
4. **Commit** : Changements + tag Git
5. **Push** : Vers repository GitHub
6. **Release** : Création release GitHub + assets

### Types de versions

- **Stable** : `1.0.0`, `1.1.0`, `2.0.0`
- **Beta** : `1.1.0-beta.1`, `2.0.0-beta.2`  
- **Alpha** : `2.0.0-alpha.1`
- **RC** : `1.1.0-rc.1`

## 🔒 Sécurité

### Bonnes pratiques

1. **Tokens** : Utilisez des tokens avec permissions minimales
2. **Signature** : Signez vos releases en production
3. **HTTPS** : Toujours utiliser HTTPS pour les téléchargements
4. **Validation** : Vérifiez les checksums
5. **Rollback** : Plan de retour en arrière

### Token GitHub

Permissions requises :
- `repo` (pour repos privés)
- `public_repo` (pour repos publics)
- `write:packages` (si utilisation packages)

## 📱 Notifications

### Types de notifications

1. **Nouvelle version disponible** : Avec option d'installation
2. **Téléchargement en cours** : Progression
3. **Installation réussie** : Confirmation + redémarrage
4. **Erreur** : Message d'erreur détaillé

### Paramètres utilisateur

- **Notifications activées/désactivées**
- **Canal de mise à jour** (stable, beta, alpha)
- **Fréquence de vérification**
- **Installation automatique**

## 🧪 Tests et validation

### Tests manuels

```bash
# Simuler une nouvelle version
# 1. Modifiez NATIVEPHP_APP_VERSION dans .env
# 2. Créez un tag Git supérieur
# 3. Testez la détection

# Test de vérification
curl -X POST http://localhost:8100/page/updates/check

# Test toggle auto-update
curl -X POST http://localhost:8100/page/updates/auto-toggle \
  -d "enabled=true" \
  -H "Content-Type: application/x-www-form-urlencoded"
```

### Validation production

1. **Repository test** : Testez avec un repo séparé
2. **Versions beta** : Utilisez des versions de test
3. **Rollback** : Préparez une procédure de retour
4. **Monitoring** : Surveillez les erreurs

## 🛠️ Dépannage

### Problèmes courants

**Erreur "No releases found"**
- Vérifiez les credentials GitHub
- Confirmez l'existence du repository
- Contrôlez les permissions du token

**"Update check failed"**
- Problème réseau ou proxy
- Token expiré ou invalide
- Repository privé sans permissions

**"Download failed"**
- Espace disque insuffisant
- Permissions d'écriture manquantes
- Interruption réseau

### Debug

Activez les logs détaillés :
```env
LOG_LEVEL=debug
```

Consultez les logs :
```bash
tail -f storage/logs/laravel.log | grep -i update
```

## 📈 Monitoring

### Métriques à surveiller

1. **Taux d'adoption** : % d'utilisateurs à jour
2. **Échecs d'installation** : Erreurs de mise à jour
3. **Temps de propagation** : Vitesse de déploiement
4. **Rollback** : Fréquence des retours en arrière

### Analytics

Intégrez avec vos outils d'analytics pour suivre :
- Versions utilisées
- Succès des mises à jour
- Problèmes rencontrés

## 🚀 Déploiement

### Environnement de production

1. **Repository GitHub** : Configuré avec releases
2. **Tokens** : Secrets sécurisés
3. **CI/CD** : Pipeline automatisé
4. **Monitoring** : Surveillance active
5. **Support** : Procédures d'assistance

### Checklist de release

- [ ] Tests fonctionnels complets
- [ ] Build toutes plateformes
- [ ] Signature des exécutables
- [ ] Documentation mise à jour
- [ ] Changelog rédigé
- [ ] Plan de rollback prêt
- [ ] Équipe support informée

## 🔄 Workflow recommandé

### Développement
```
develop → feature/xxx → develop
```

### Release
```
develop → release/vX.X.X → main → tag vX.X.X
```

### Hotfix
```
main → hotfix/vX.X.X → main → tag vX.X.X
```

## 📞 Support

En cas de problème avec les mises à jour :

1. **Logs** : Consultez les logs applicatifs
2. **Network** : Vérifiez la connectivité
3. **Permissions** : Contrôlez les droits d'écriture
4. **Version** : Confirmez la version actuelle
5. **Manual** : Possibilité de mise à jour manuelle

---

## 🎯 Résumé

Le système de mise à jour automatique de Distrobox offre :

✅ **Simplicité** : Configuration en quelques variables  
✅ **Flexibilité** : Plusieurs providers supportés  
✅ **Sécurité** : Vérifications et validations  
✅ **UX** : Interface utilisateur intuitive  
✅ **Automation** : Scripts de release automatisés

Votre application peut maintenant se maintenir à jour automatiquement, garantissant que vos utilisateurs bénéficient toujours des dernières améliorations et corrections de sécurité !