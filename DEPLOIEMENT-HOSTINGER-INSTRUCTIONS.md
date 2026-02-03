# 🚢 DÉPLOIEMENT HOSTINGER - INSTRUCTIONS ÉTAPE PAR ÉTAPE

## ⏱️ Temps estimé : 10 minutes

---

## 📋 PRÉPARATIFS (ce dont vous avez besoin)

✅ Accès FTP à votre compte Hostinger  
✅ Accès cPanel/Hostinger Panel  
✅ Accès phpMyAdmin (pour vérifier la DB)  
✅ Les 2 fichiers PHP de ce projet  

---

## 🔑 ÉTAPE 1 : Générer votre clé API sécurisée (1 min)

### Windows (PowerShell)

Ouvrez PowerShell et tapez :

```powershell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))
```

### Linux/Mac (Terminal)

```bash
openssl rand -base64 32
```

### En ligne (si pas de terminal)

Allez sur : https://www.random.org/strings/?num=1&len=32&digits=on&upperalpha=on&loweralpha=on&unique=on&format=html&rnd=new

**📝 Copiez cette clé quelque part** (Notepad), vous en aurez besoin 3 fois !

**Exemple de clé** : `k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8`

---

## 📤 ÉTAPE 2 : Uploader les fichiers PHP (2 min)

### A. Connexion FTP

1. Ouvrez FileZilla (ou votre client FTP)
2. Connectez-vous à Hostinger :
   - **Hôte** : votre-domaine.com (ou IP fournie)
   - **User** : u123456_ftpuser (ou votre user FTP)
   - **Pass** : votre_mot_de_passe_ftp
   - **Port** : 21

### B. Créer la structure de dossiers

Dans le dossier racine (public_html) :

```
public_html/
├── admin/
│   └── api/
│       ├── sync.php       ← À créer
│       └── upload.php     ← À créer
└── uploads/
    └── images/            ← À créer
```

**Actions** :
1. Naviguez vers `public_html`
2. Créez dossier `admin` (s'il n'existe pas)
3. Dans `admin`, créez dossier `api`
4. Retour à `public_html`, créez dossier `uploads`
5. Dans `uploads`, créez dossier `images`

### C. Upload des fichiers

1. **Fichier local** : `HOSTINGER-SYNC-UPLOAD.php`  
   → **Uploadez vers** : `/public_html/admin/api/sync.php`  
   → **Renommez en** : `sync.php` (enlevez le préfixe HOSTINGER)

2. **Fichier local** : `HOSTINGER-IMAGE-UPLOAD.php`  
   → **Uploadez vers** : `/public_html/admin/api/upload.php`  
   → **Renommez en** : `upload.php` (enlevez le préfixe HOSTINGER)

---

## ⚙️ ÉTAPE 3 : Configurer sync.php (2 min)

### A. Éditer le fichier

Via FTP : clic droit sur `sync.php` → **View/Edit**  
Ou via cPanel : **File Manager** → `admin/api/sync.php` → **Edit**

### B. Modifier les lignes 9-13

**AVANT** :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'educations_plurielles');
define('DB_USER', 'root');
define('DB_PASS', '');
define('ADMIN_SYNC_KEY', 'change_me');
```

**APRÈS** (avec VOS valeurs) :
```php
define('DB_HOST', 'localhost');                  // Généralement localhost
define('DB_NAME', 'u123456_educations');         // Votre nom de base de données
define('DB_USER', 'u123456_admin');              // Votre user MySQL
define('DB_PASS', 'VotreMot2PasseMySQL');        // Votre pass MySQL
define('ADMIN_SYNC_KEY', 'k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8'); // Votre clé de l'étape 1
```

**💾 Sauvegardez** le fichier

### C. Où trouver vos infos DB ?

1. Hostinger Panel → **Databases** → **MySQL Databases**
2. Notez :
   - Nom de la base (ex: `u123456_educations`)
   - User MySQL (ex: `u123456_admin`)
   - Password (si oublié, cliquez **Change Password**)

---

## ⚙️ ÉTAPE 4 : Configurer upload.php (2 min)

### A. Éditer le fichier

Via FTP : clic droit sur `upload.php` → **View/Edit**  
Ou via cPanel : **File Manager** → `admin/api/upload.php` → **Edit**

### B. Modifier les lignes 9-13

**AVANT** :
```php
define('ADMIN_SYNC_KEY', 'change_me');
define('UPLOAD_DIR', __DIR__ . '/uploads/images');
define('UPLOAD_BASE_URL', 'https://votre-domaine.com/uploads/images');
```

**APRÈS** (avec VOS valeurs) :
```php
define('ADMIN_SYNC_KEY', 'k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8'); // MÊME clé que sync.php
define('UPLOAD_DIR', __DIR__ . '/../../uploads/images');
define('UPLOAD_BASE_URL', 'https://votre-domaine.com/uploads/images'); // Votre domaine réel
```

**⚠️ IMPORTANT** : La clé API doit être **EXACTEMENT la même** que dans `sync.php` !

**💾 Sauvegardez** le fichier

---

## 🔒 ÉTAPE 5 : Permissions des dossiers (1 min)

### Via FTP (FileZilla)

1. Clic droit sur `/uploads` → **File permissions**
2. Entrez : `755`
3. Cochez **Recurse into subdirectories**
4. Cliquez **OK**

### Via cPanel File Manager

1. Sélectionnez `/uploads`
2. Cliquez **Permissions**
3. Cochez : `Owner: Read/Write/Execute`, `Group: Read/Execute`, `Public: Read/Execute`
4. Appliquez à tous les sous-dossiers

**Résultat** : `/uploads/images/` doit être writable (755)

---

## 🧪 ÉTAPE 6 : Vérifications serveur (1 min)

### A. Tester l'URL de sync

Ouvrez votre navigateur :
```
https://votre-domaine.com/admin/api/sync.php
```

**Attendu** : Message JSON comme `{"success":false,"error":"..."}`  
**❌ Si 404** : Fichier mal placé ou mal nommé  
**❌ Si 500** : Erreur PHP (vérifiez logs)

### B. Tester l'URL d'upload

```
https://votre-domaine.com/admin/api/upload.php
```

**Attendu** : Message JSON comme `{"success":false,"error":"Méthode non autorisée"}`  
**✅ Normal** : GET n'est pas autorisé, mais le fichier existe

### C. Vérifier GD Library

Via cPanel → **MultiPHP INI Editor** → Recherchez `gd`  
Ou créez un fichier `test.php` :

```php
<?php
phpinfo();
```

Uploadez-le, visitez `https://votre-domaine.com/test.php`, cherchez "GD Support"  
**✅ Doit dire** : `enabled`  
**❌ Si absent** : Contactez support Hostinger pour activer GD

---

## 💻 ÉTAPE 7 : Configuration admin.html (2 min)

### Méthode A : Via l'interface

1. Ouvrez `admin.html` dans Chrome/Firefox
2. Créez votre compte admin (si première fois)
3. Cliquez sur **Paramètres** ⚙️
4. Section **Synchronisation Hostinger**, remplissez :

| Champ | Valeur |
|-------|--------|
| **URL du point de synchronisation** | `https://votre-domaine.com/admin/api/sync.php` |
| **URL d'upload d'images** | `https://votre-domaine.com/admin/api/upload.php` |
| **URL de rafraîchissement public** | `https://votre-domaine.com/?refresh=1` |
| **Clé de synchronisation** | `k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8` (votre clé) |

5. **☑️ Cochez** : Synchroniser en ligne
6. Cliquez **💾 Enregistrer la synchro**

### Méthode B : Via script console

1. Ouvrez `config-rapide.js`
2. Modifiez les lignes :

```javascript
const CONFIG = {
    domain: 'votre-domaine.com',  // Votre domaine réel
    apiKey: 'k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8',  // Votre clé
    enableSync: true  // true pour activer immédiatement
};
```

3. Copiez **tout le code** du fichier
4. Dans `admin.html`, appuyez **F12** → **Console**
5. Collez le code → **Entrée**
6. Rechargez la page (**F5**)

---

## ✅ ÉTAPE 8 : Tests finaux (2 min)

### Test automatique (recommandé)

1. Ouvrez `test-configuration.html`
2. Remplissez les 3 champs :
   - URL sync : `https://votre-domaine.com/admin/api/sync.php`
   - URL upload : `https://votre-domaine.com/admin/api/upload.php`
   - Clé API : `k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8`
3. Cliquez **🚀 Lancer les tests**
4. **Résultat attendu** : `🎉 Tous les tests sont passés !`

### Test manuel

1. Dans `admin.html`, cliquez **Articles** → **Ajouter un article**
2. Remplissez titre + contenu
3. Cliquez **📤 Upload fichier** → Choisissez une image
4. **Vérifiez console (F12)** : Message `📦 Compression client: XXkb → YYkb`
5. Cliquez **💾 Enregistrer**
6. Connectez-vous à **phpMyAdmin** :
   - Hostinger Panel → **Databases** → **phpMyAdmin**
   - Sélectionnez votre base `u123456_educations`
   - Table `articles` → **Browse**
   - **✅ Votre article doit apparaître**
7. Via FTP, vérifiez `/uploads/images/` → **✅ Image présente**

---

## 🎯 CHECKLIST FINALE

Avant de considérer le déploiement terminé :

### Serveur Hostinger
- [ ] `sync.php` uploadé dans `/admin/api/`
- [ ] `upload.php` uploadé dans `/admin/api/`
- [ ] DB_HOST, DB_NAME, DB_USER, DB_PASS configurés dans `sync.php`
- [ ] ADMIN_SYNC_KEY configurée dans `sync.php`
- [ ] ADMIN_SYNC_KEY configurée dans `upload.php` (MÊME valeur)
- [ ] UPLOAD_BASE_URL configurée dans `upload.php`
- [ ] Dossier `/uploads/images/` créé
- [ ] Permissions 755 sur `/uploads/images/`
- [ ] GD Library activée (phpinfo)
- [ ] Les 2 URLs répondent (même si erreur JSON)

### Client admin.html
- [ ] URL sync renseignée
- [ ] URL upload renseignée
- [ ] URL refresh renseignée (optionnel)
- [ ] Clé API renseignée (MÊME que serveur)
- [ ] Case "Synchroniser en ligne" cochée
- [ ] Configuration sauvegardée

### Tests
- [ ] `test-configuration.html` → Tous tests verts
- [ ] Création article → Visible dans phpMyAdmin
- [ ] Upload image → Fichier dans `/uploads/images/`
- [ ] Console navigateur → Message compression visible
- [ ] Modification article → UPDATE en DB
- [ ] Suppression article → DELETE en DB

---

## 🆘 PROBLÈMES COURANTS

### ❌ "404 Not Found" sur sync.php

**Causes** :
- Fichier mal placé
- Nom incorrect (doit être `sync.php`, pas `HOSTINGER-SYNC-UPLOAD.php`)
- .htaccess bloque l'accès

**Solutions** :
1. Vérifiez le chemin exact : `/public_html/admin/api/sync.php`
2. Vérifiez les permissions du fichier (644)
3. Vérifiez `.htaccess` dans `/admin/` (s'il existe)

### ❌ "500 Internal Server Error"

**Causes** :
- Erreur PHP dans le code
- DB credentials incorrects
- Extension manquante

**Solutions** :
1. Hostinger Panel → **Error Logs** → Consultez derniers logs
2. Vérifiez DB_HOST, DB_NAME, DB_USER, DB_PASS
3. Testez connexion DB via phpMyAdmin

### ❌ "Clé de synchronisation invalide"

**Causes** :
- Clé différente entre admin.html et PHP
- Espace en début/fin de clé
- Quotes mal fermées

**Solutions** :
1. Comparez clé dans `sync.php` ligne 13
2. Comparez clé dans `upload.php` ligne 9
3. Comparez clé dans admin.html → Paramètres
4. **Toutes doivent être EXACTEMENT identiques**

### ❌ Images non uploadées

**Causes** :
- Permissions dossier `/uploads/images/` incorrectes
- UPLOAD_BASE_URL incorrecte
- Taille fichier > 5MB
- GD Library non installée

**Solutions** :
1. Permissions : `chmod 755 /uploads/images/`
2. UPLOAD_BASE_URL doit finir par `/uploads/images` (sans /)
3. Vérifiez taille image (max 5MB)
4. Vérifiez GD : `php -m | grep gd`

### ❌ Compression ne fonctionne pas

**Causes** :
- GD Library manquante côté serveur
- Erreur JavaScript côté client

**Solutions** :
1. **Serveur** : Installez GD (contactez Hostinger)
2. **Client** : Ouvrez console (F12), cherchez erreurs JavaScript
3. **Fallback** : Si compression échoue, fichier original est utilisé (normal)

---

## 📞 SUPPORT HOSTINGER

Si problème persistant :

1. **Live Chat** : Hostinger Panel → Chat icon (bottom right)
2. **Ticket** : https://www.hostinger.com/cpanel-login → Submit Ticket
3. **Infos à fournir** :
   - Votre domaine
   - Fichier concerné (`sync.php` ou `upload.php`)
   - Message d'erreur exact
   - Logs d'erreur (Error Logs dans cPanel)

---

## 🎉 DÉPLOIEMENT TERMINÉ !

**Félicitations !** Votre système est maintenant opérationnel sur Hostinger.

### Prochaines étapes

1. **Testez toutes les fonctionnalités** (create/update/delete)
2. **Créez vos catégories** d'articles
3. **Uploadez vos contenus**
4. **Partagez l'accès** avec d'autres admins (si besoin)

### Maintenance

- **Backups** : Exportez votre DB régulièrement (phpMyAdmin → Export)
- **Sécurité** : Changez votre clé API tous les 3 mois
- **Nettoyage** : Vérifiez `/uploads/images/` pour orphelins

---

**🚀 Le système est prêt à l'emploi !**

Consultez `RECAPITULATIF-FINAL.md` pour toutes les fonctionnalités disponibles.
