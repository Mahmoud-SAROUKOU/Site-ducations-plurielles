# ⚡ DÉMARRAGE RAPIDE - 3 ÉTAPES

## 🎯 Ce qu'on a maintenant

✅ **Compression double** : client (85%) + serveur (82% JPEG / 80% WebP)  
✅ **Sync automatique** : create/update/delete articles, pubs, admins, catégories  
✅ **Nettoyage auto** : suppression anciennes images  
✅ **Configuration unifiée** : local ↔ Hostinger  

---

## 🚀 ÉTAPE 1 : Configuration Hostinger (5 min)

### A. Uploadez 2 fichiers PHP

| Fichier local | Destination Hostinger | Action |
|--------------|----------------------|---------|
| `HOSTINGER-SYNC-UPLOAD.php` | `/admin/api/sync.php` | Upload via FTP/cPanel |
| `HOSTINGER-IMAGE-UPLOAD.php` | `/admin/api/upload.php` | Upload via FTP/cPanel |

### B. Éditez les 2 fichiers uploadés

**Dans `sync.php` :**
```php
define('ADMIN_SYNC_KEY', 'MA_CLE_SECRETE_UNIQUE_123');
define('DB_NAME', 'u123456_educations'); // Votre DB
define('DB_USER', 'u123456_admin');       // Votre user
define('DB_PASS', 'votre_mot_de_passe');  // Votre pass
```

**Dans `upload.php` :**
```php
define('ADMIN_SYNC_KEY', 'MA_CLE_SECRETE_UNIQUE_123'); // MÊME clé
define('UPLOAD_BASE_URL', 'https://votre-domaine.com/uploads/images');
```

### C. Créez le dossier images

```bash
# Via FTP ou cPanel File Manager
mkdir /uploads/images
chmod 755 /uploads/images
```

---

## 💻 ÉTAPE 2 : Configuration admin.html (2 min)

### Méthode A : Interface graphique

1. Ouvrez `admin.html` dans Chrome/Firefox
2. Créez votre compte admin (première fois)
3. Cliquez sur **Paramètres** ⚙️
4. Remplissez :
   - **URL sync** : `https://votre-domaine.com/admin/api/sync.php`
   - **URL upload** : `https://votre-domaine.com/admin/api/upload.php`
   - **Clé API** : `MA_CLE_SECRETE_UNIQUE_123` (même que PHP)
   - ☑️ Cochez **Synchroniser en ligne**
5. Cliquez **💾 Enregistrer**

### Méthode B : Script rapide (Console)

1. Ouvrez `config-rapide.js`
2. Modifiez les lignes 12-14 :
   ```js
   domain: 'votre-domaine.com',
   apiKey: 'MA_CLE_SECRETE_UNIQUE_123',
   enableSync: true  // false = tester en local d'abord
   ```
3. Copiez tout le code
4. Dans `admin.html`, appuyez F12 > Console > Collez > Entrée
5. Rechargez la page (F5)

---

## ✅ ÉTAPE 3 : Tests (3 min)

### Option A : Test manuel

1. Dans `admin.html`, créez un article avec image (📤 Upload fichier)
2. Vérifiez la console (F12) : `📦 Compression client: XXkb → YYkb`
3. Connectez-vous à votre DB Hostinger → Table `articles` → L'article doit apparaître
4. Vérifiez `/uploads/images/` sur FTP → L'image doit être présente

### Option B : Test automatique

1. Ouvrez `test-configuration.html` dans votre navigateur
2. Remplissez les 3 champs (URLs + clé)
3. Cliquez **🚀 Lancer les tests**
4. Résultat : `🎉 Tous les tests sont passés !` = OK

---

## 📊 Flux complet

```
┌────────────┐
│admin.html  │ 1. Upload image 500KB
│  (Local)   │
└─────┬──────┘
      │ 📦 Compression client (Canvas)
      │ → 500KB devient 180KB
      ▼
┌────────────┐
│upload.php  │ 2. Re-compression serveur
│(Hostinger) │ → 180KB devient 120KB
└─────┬──────┘
      │ 💾 Sauvegarde /uploads/images/
      │ 🔗 Retourne URL
      ▼
┌────────────┐
│sync.php    │ 3. INSERT dans DB
│(Hostinger) │
└────────────┘
```

---

## 🆘 Problèmes courants

| Erreur | Solution |
|--------|----------|
| "Upload indisponible" | Vérifiez que "Synchroniser en ligne" est coché |
| "Clé invalide" | Comparez ADMIN_SYNC_KEY dans sync.php, upload.php et admin.html |
| Images non compressées | Vérifiez console (F12), GD doit être installé sur serveur |
| Sync ne fonctionne pas | Ouvrez `test-configuration.html` pour diagnostiquer |

---

## 📁 Fichiers importants

| Fichier | Usage |
|---------|-------|
| `admin.html` | Interface d'administration (local) |
| `HOSTINGER-SYNC-UPLOAD.php` | À uploader → `/admin/api/sync.php` |
| `HOSTINGER-IMAGE-UPLOAD.php` | À uploader → `/admin/api/upload.php` |
| `CONFIGURATION-COMPLETE.md` | Guide détaillé (+ sécurité) |
| `test-configuration.html` | Test automatique de config |
| `config-rapide.js` | Script console pour config rapide |
| `.env.example` | Template variables environnement |

---

## 🎉 C'est tout !

**Temps total** : ~10 minutes  
**Résultat** : Système complet avec sync + upload + compression + nettoyage auto

Pour aller plus loin, consultez `CONFIGURATION-COMPLETE.md`
