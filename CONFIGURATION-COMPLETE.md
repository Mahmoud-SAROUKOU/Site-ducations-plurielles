# 🚀 Configuration Complète - Éducations Plurielles

## 📋 Vue d'ensemble

Ce guide unifie la configuration entre votre environnement **local** et votre hébergement **Hostinger**.

---

## 🔧 1. Configuration sur Hostinger

### A. Fichiers à uploader

#### 1️⃣ Endpoint de synchronisation
- **Fichier local**: `HOSTINGER-SYNC-UPLOAD.php`
- **Destination**: `/admin/api/sync.php` (sur Hostinger)
- **Configuration requise**:
```php
define('ADMIN_SYNC_KEY', 'votre_cle_secrete_unique');
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_base_de_donnees');
define('DB_USER', 'votre_utilisateur_db');
define('DB_PASS', 'votre_mot_de_passe_db');
```

#### 2️⃣ Endpoint d'upload d'images
- **Fichier local**: `HOSTINGER-IMAGE-UPLOAD.php`
- **Destination**: `/admin/api/upload.php` (sur Hostinger)
- **Configuration requise**:
```php
define('ADMIN_SYNC_KEY', 'votre_cle_secrete_unique'); // MÊME CLÉ QUE sync.php
define('UPLOAD_DIR', __DIR__ . '/../../uploads/images');
define('UPLOAD_BASE_URL', 'https://votre-domaine.com/uploads/images');
```

### B. Dossiers à créer

```bash
# Via FTP ou cPanel File Manager
mkdir -p /uploads/images
chmod 755 /uploads
chmod 755 /uploads/images
```

### C. Vérifications serveur

#### ✅ Vérifier GD Library (pour compression images)
```bash
php -m | grep -i gd
# Doit retourner: gd
```

Si absent, contactez Hostinger ou ajoutez dans `.htaccess`:
```apache
php_flag gd.jpeg_ignore_warning on
```

#### ✅ Limites d'upload PHP
Vérifiez dans `php.ini` ou `.user.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 60
memory_limit = 128M
```

### D. Base de données

Assurez-vous que ces tables existent (voir `admin/schema.sql`):
- `articles`
- `ads`
- `admins`
- `users`
- `categories`

---

## 💻 2. Configuration dans admin.html (Local)

### A. Accéder aux paramètres

1. Ouvrez `admin.html` dans votre navigateur
2. Connectez-vous ou créez votre compte admin
3. Cliquez sur **Paramètres** (⚙️)
4. Section **Synchronisation Hostinger**

### B. Remplir les champs

| Champ | Valeur à saisir |
|-------|----------------|
| **URL du point de synchronisation** | `https://votre-domaine.com/admin/api/sync.php` |
| **URL d'upload d'images** | `https://votre-domaine.com/admin/api/upload.php` |
| **URL de rafraîchissement public** | `https://votre-domaine.com/?refresh=1` (optionnel) |
| **Clé de synchronisation** | `votre_cle_secrete_unique` (MÊME que dans PHP) |
| **☑️ Synchroniser en ligne** | Cocher la case |

### C. Enregistrer

Cliquez sur **💾 Enregistrer la synchro**

---

## 🧪 3. Tests de validation

### Test 1: Synchronisation
1. Dans admin.html, créez un article avec titre + contenu
2. Vérifiez dans la base de données Hostinger que l'article apparaît

### Test 2: Upload d'image
1. Créez un article et utilisez **📤 Upload fichier**
2. Sélectionnez une image (JPG, PNG, WebP)
3. Vérifiez:
   - Console navigateur: message `📦 Compression client: XXXkb → YYYkb`
   - Serveur: fichier dans `/uploads/images/`
   - URL retournée commence par `https://votre-domaine.com/uploads/images/`

### Test 3: Modification
1. Modifiez un article existant et changez son image
2. Vérifiez que l'ancienne image est supprimée du serveur

### Test 4: Suppression
1. Supprimez un article avec image
2. Vérifiez que l'image est supprimée du serveur

### Test 5: Refresh public (optionnel)
Cliquez sur **🔁 Tester le refresh** dans Paramètres

---

## 🔐 4. Sécurité

### Générer une clé sécurisée

```bash
# Sous Linux/Mac
openssl rand -base64 32

# Sous Windows PowerShell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))
```

Utilisez cette clé dans:
- `sync.php` → `ADMIN_SYNC_KEY`
- `upload.php` → `ADMIN_SYNC_KEY`
- `admin.html` → Paramètres → Clé de synchronisation

### Protéger l'API

Ajoutez dans `.htaccess` (à la racine):
```apache
<Files "sync.php">
    # Autoriser uniquement votre IP locale (optionnel)
    # Order Deny,Allow
    # Deny from all
    # Allow from VOTRE_IP
</Files>
```

---

## ⚙️ 5. Caractéristiques techniques

### Compression côté client (avant upload)
- **Dimensions max**: 1600x1600px
- **Qualité JPEG**: 85%
- **Qualité PNG**: préservée
- **Avantage**: upload plus rapide, moins de bande passante

### Compression côté serveur (après upload)
- **Taille max fichier**: 5MB
- **Dimensions max**: 1600x1600px
- **Qualité JPEG**: 82%
- **Qualité WebP**: 80%
- **Qualité PNG**: niveau 6

### Nettoyage automatique
- Suppression ancienne image lors de la modification
- Suppression image lors de la suppression de l'article/publicité

---

## 📊 6. Flux de données

```
┌─────────────┐
│ admin.html  │
│   (Local)   │
└──────┬──────┘
       │
       │ 1. Compression client (Canvas)
       │    1600px max, quality 85%
       │
       ▼
┌─────────────┐
│   Upload    │
│ HTTPS POST  │
└──────┬──────┘
       │
       │ 2. Vérification clé API
       │
       ▼
┌─────────────┐
│upload.php   │
│ (Hostinger) │
└──────┬──────┘
       │
       │ 3. Re-compression serveur
       │    JPEG 82%, WebP 80%
       │
       ▼
┌─────────────┐
│  /uploads/  │
│   images/   │
└─────────────┘
       │
       │ 4. URL retournée
       │
       ▼
┌─────────────┐
│  sync.php   │
│  (INSERT)   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Database   │
│   MySQL     │
└─────────────┘
```

---

## 🛠️ 7. Dépannage

### ❌ "Upload indisponible"
- Vérifiez que **Synchroniser en ligne** est coché
- Vérifiez l'URL d'upload (doit finir par `.php`)
- Vérifiez que la clé API est identique côté client et serveur

### ❌ "Clé de synchronisation invalide"
- Comparez `ADMIN_SYNC_KEY` dans sync.php et upload.php
- Comparez avec la clé dans admin.html > Paramètres
- Attention aux espaces en début/fin de clé

### ❌ Images non compressées
- Vérifiez console navigateur (F12)
- Si erreur compression client, le fichier original est envoyé
- Vérifiez que GD est installé côté serveur

### ❌ Anciennes images non supprimées
- Vérifiez que `deleteRemoteImage()` est appelée
- Vérifiez les logs serveur dans upload.php
- Permissions du dossier `/uploads/images` (755)

---

## 📝 8. Checklist finale

### Sur Hostinger
- [ ] `sync.php` uploadé dans `/admin/api/`
- [ ] `upload.php` uploadé dans `/admin/api/`
- [ ] ADMIN_SYNC_KEY identique dans les 2 fichiers
- [ ] DB_* configuré dans sync.php
- [ ] UPLOAD_BASE_URL configuré dans upload.php
- [ ] Dossier `/uploads/images/` créé et writable
- [ ] GD library installée
- [ ] Limites PHP upload ≥ 5MB

### Dans admin.html
- [ ] URL synchronisation renseignée
- [ ] URL upload renseignée
- [ ] URL refresh renseignée (optionnel)
- [ ] Clé API identique au serveur
- [ ] Case "Synchroniser en ligne" cochée
- [ ] Configuration enregistrée

### Tests
- [ ] Création article → synchro DB OK
- [ ] Upload image → compression client visible (console)
- [ ] Upload image → fichier présent sur serveur
- [ ] Modification image → ancienne supprimée
- [ ] Suppression article → image supprimée

---

## 🎉 Configuration terminée !

Votre système est maintenant opérationnel avec:
- ✅ **Double compression** (client + serveur)
- ✅ **Synchronisation automatique** (create/update/delete)
- ✅ **Nettoyage automatique** des images obsolètes
- ✅ **Sécurité** par clé API
- ✅ **Configuration unifiée** local ↔ Hostinger

Pour toute question, consultez les fichiers:
- `HOSTINGER-SYNC-UPLOAD.php` (documentation intégrée)
- `HOSTINGER-IMAGE-UPLOAD.php` (documentation intégrée)
