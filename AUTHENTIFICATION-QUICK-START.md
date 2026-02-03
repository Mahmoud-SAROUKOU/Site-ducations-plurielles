# 🚀 DÉMARRAGE RAPIDE - Authentification Dual Mode

## En 3 minutes, votre système est opérationnel !

---

## 📝 MODE LOCAL (Développement)

### Étape 1 : Ouvrir admin.html

```bash
# Ouvrez simplement le fichier dans votre navigateur
d:\Site Educations Plurielles\admin.html
```

### Étape 2 : Se connecter

**Email** : `admin@educationsplurielles.local`  
**Mot de passe** : *(laisser vide)*

✅ **C'est tout !** Le système utilise localStorage automatiquement.

**Badge visible** : 🏠 Mode local (Hors ligne)

---

## ☁️ MODE DISTANT (Hostinger)

### Prérequis (5 min)

#### 1. Uploader le backend

Connectez-vous à votre FTP Hostinger et uploadez :

```
admin/api/auth.php       → /public_html/admin/api/auth.php
```

#### 2. Configurer config.php

Éditez `/public_html/admin/config.php` :

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_base_mysql');
define('DB_USER', 'votre_user_mysql');
define('DB_PASS', 'votre_password_mysql');
define('ADMIN_SYNC_KEY', 'choisissez_une_cle_secrete_longue');
```

**Générer une clé sécurisée** (PowerShell) :
```powershell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))
```

Exemple de clé : `k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8VbNm2Lp9Qx4`

### Configuration client (2 min)

#### Option A : Via console (F12)

Ouvrez admin.html, appuyez sur **F12**, collez dans la console :

```javascript
localStorage.setItem('syncConfig', JSON.stringify({
    enabled: true,
    endpoint: 'https://votre-domaine.com/admin/api/sync.php',
    apiKey: 'MÊME_CLE_QUE_CONFIG_PHP'
}));

// Recharger la page
location.reload();
```

#### Option B : Via l'interface

1. Ouvrez admin.html (mode local d'abord)
2. Allez dans **Paramètres** ⚙️
3. Section "Synchronisation Hostinger"
4. Cochez **☑️ Synchroniser en ligne**
5. Remplissez :
   - **URL sync** : `https://votre-domaine.com/admin/api/sync.php`
   - **Clé API** : Votre clé sécurisée (même que config.php)
6. Cliquez **💾 Enregistrer**
7. Rechargez la page

### Se connecter en mode distant

**Email** : `admin@educationsplurielles.local`  
**Mot de passe** : *(laisser vide pour super-admin)*

✅ **Badge visible** : ☁️ Mode en ligne (Hostinger)

---

## 🧪 Vérifier que tout fonctionne

### Test automatique

Ouvrez : `test-auth-dual-mode.html`

Cliquez sur :
1. **🔍 Tester détection environnement** → Doit afficher "DISTANT" si configuré
2. **☁️ Tester connexion distante** → Doit réussir
3. **📋 Vérifier session actuelle** → Doit afficher vos infos

### Test manuel

**Mode LOCAL** :
```bash
1. Supprimer syncConfig : localStorage.removeItem('syncConfig')
2. Recharger → Badge "Mode local" visible
3. Se connecter → Doit fonctionner sans internet
```

**Mode DISTANT** :
```bash
1. Configurer syncConfig (voir ci-dessus)
2. Recharger → Badge "Mode en ligne" visible
3. Ouvrir Console (F12) → Onglet Network
4. Se connecter → Doit voir "POST /admin/api/auth.php" avec status 200
5. Se déconnecter → Idem
```

---

## ⚠️ Problèmes courants

### "Clé de synchronisation invalide"

**Cause** : Clé API différente entre client et serveur

**Solution** :
```javascript
// Vérifier la clé côté client
const config = JSON.parse(localStorage.getItem('syncConfig'));
console.log('Clé client:', config.apiKey);

// Comparer avec config.php sur le serveur
// Elles doivent être EXACTEMENT identiques
```

### Badge reste "Mode local" alors que syncConfig est configuré

**Cause** : `enabled: false` ou `endpoint` mal formé

**Solution** :
```javascript
// Vérifier la config complète
const config = JSON.parse(localStorage.getItem('syncConfig'));
console.log(config);

// Doit avoir :
// { enabled: true, endpoint: "https://...", apiKey: "..." }
```

### Erreur 500 lors de la connexion distante

**Causes** :
- auth.php pas uploadé au bon endroit
- config.php avec mauvais identifiants DB
- Base de données inexistante

**Vérifications** :
```bash
# 1. Tester l'URL directement
curl https://votre-domaine.com/admin/api/auth.php
# Doit retourner du JSON (même si erreur)

# 2. Vérifier dans phpMyAdmin
# Tables : users, sessions
# Doit avoir 1 ligne dans users (super-admin)
```

---

## 📚 Documentation complète

Pour en savoir plus :
- **README complet** : `AUTHENTIFICATION-DUAL-MODE-README.md`
- **Outil de test** : `test-auth-dual-mode.html`
- **Code source** : `admin.html` (lignes 2150-2520) + `admin/api/auth.php`

---

## 🎯 Checklist finale

### Configuration serveur
- [ ] auth.php uploadé dans `/admin/api/`
- [ ] config.php configuré avec DB + clé API
- [ ] Base de données MySQL créée
- [ ] URL testée : `https://domaine.com/admin/api/auth.php` répond

### Configuration client
- [ ] syncConfig configuré dans localStorage
- [ ] Badge "Mode en ligne" visible au login
- [ ] Connexion fonctionne
- [ ] Session persiste au rechargement
- [ ] Déconnexion fonctionne

### Tests validés
- [ ] test-auth-dual-mode.html → Tous les tests verts
- [ ] Console (F12) → Aucune erreur rouge
- [ ] phpMyAdmin → Table sessions se remplit à chaque connexion

---

## ✅ Résultat

Vous avez maintenant :
- ✅ Un système qui fonctionne **offline** (développement local)
- ✅ Un système qui fonctionne **online** (production Hostinger)
- ✅ Détection automatique de l'environnement
- ✅ Sécurité : bcrypt, tokens, clé API
- ✅ 0 modification de code nécessaire pour changer de mode

**Temps total : 5-10 minutes** ⏱️

**Prêt pour production !** 🚀

---

## 🆘 Besoin d'aide ?

1. Lisez `AUTHENTIFICATION-DUAL-MODE-README.md` (guide complet)
2. Testez avec `test-auth-dual-mode.html`
3. Vérifiez Console navigateur (F12) pour erreurs
4. Vérifiez logs serveur PHP si mode distant ne fonctionne pas
