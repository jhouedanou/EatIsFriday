# Configuration du déploiement FTP via GitHub Actions

## 🚀 Déploiement automatique du thème WordPress

Ce projet utilise GitHub Actions pour déployer automatiquement les modifications du thème WordPress vers le serveur FTP.

## 📋 Configuration requise

### 1. Ajouter les secrets GitHub

Allez dans **Settings → Secrets and variables → Actions** de votre repository GitHub et ajoutez les secrets suivants :

| Secret | Description | Exemple |
|--------|-------------|---------|
| `FTP_HOST` | Adresse du serveur FTP | `ftp.bigfive.dev` |
| `FTP_USERNAME` | Nom d'utilisateur FTP | `votre_username` |
| `FTP_PASSWORD` | Mot de passe FTP | `votre_mot_de_passe` |
| `FTP_THEME_PATH` | Chemin vers le thème sur le serveur | `/public_html/eatisfamily/wp-content/themes/eatisfamily/` |

### 2. Comment ajouter les secrets

1. Allez sur https://github.com/jhouedanou/EatIsFriday/settings/secrets/actions
2. Cliquez sur **"New repository secret"**
3. Ajoutez chaque secret un par un

### 3. Structure du chemin FTP

```
FTP_THEME_PATH doit pointer vers le dossier du thème :
/public_html/eatisfamily/wp-content/themes/eatisfamily/
```

## 🔄 Fonctionnement

### Déploiement automatique
- Le workflow se déclenche automatiquement à chaque push sur `main` qui modifie des fichiers dans `wordpress-theme/`
- Seuls les fichiers modifiés sont uploadés (déploiement incrémentiel)

### Déploiement manuel
1. Allez sur https://github.com/jhouedanou/EatIsFriday/actions
2. Sélectionnez le workflow **"Deploy WordPress Theme to FTP"**
3. Cliquez sur **"Run workflow"**
4. Cochez "Deploy all theme files" si vous voulez tout re-déployer

## 📁 Fichiers exclus du déploiement

- `.git*` - Fichiers Git
- `node_modules/` - Dépendances Node
- `.DS_Store` - Fichiers système Mac
- `Thumbs.db` - Fichiers système Windows
- `*.md` - Fichiers Markdown (documentation)
- `*.log` - Fichiers de log

## 🔧 Résolution des problèmes

### Erreur 403 Forbidden dans WordPress Admin

L'erreur 403 est causée par mod_security. Solutions :

1. **Fichier .htaccess** (déjà créé dans le thème)
   ```apache
   <IfModule mod_security2.c>
       SecRuleEngine Off
   </IfModule>
   ```

2. **Contacter l'hébergeur** pour désactiver mod_security sur `/wp-admin/`

3. **Utiliser l'encodage AJAX base64** (implémenté dans admin-pages.php)

### Vérifier le déploiement

1. Allez sur GitHub → Actions
2. Vérifiez le statut du workflow
3. Consultez les logs pour les erreurs

## 📌 Commandes utiles

```bash
# Commit et push des modifications du thème
git add wordpress-theme/
git commit -m "Update WordPress theme"
git push origin main
```

## ⚠️ Notes importantes

- Ne commitez JAMAIS les secrets dans le code
- Testez localement avant de déployer
- Gardez une sauvegarde du thème sur le serveur avant le premier déploiement
