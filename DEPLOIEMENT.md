# 🚀 Guide de Déploiement - Système Admin Unified

## État des Lieux

Vous avez reçu un système d'administration complet avec:
- ✅ **Interface SPA** dans `admin.html`
- ✅ **API REST** centralisée dans `admin/api/index.php`
- ✅ **Backend PHP** complet pour articles, pubs, admins
- ✅ **Base de données MySQL** prête à l'emploi

## 🎯 Étapes de Déploiement (10-15 minutes)

### Étape 1️⃣ - Configuration Environnement

**Fichier à créer:** `admin/.env`

```bash
# Database
DB_HOST=localhost
DB_NAME=site_educations_plurielles
DB_USER=root
DB_PASS=votre_mot_de_passe

# Email (pour reset password)
MAIL_SMTP_HOST=smtp.gmail.com
MAIL_SMTP_PORT=587
MAIL_SMTP_USER=votreemail@gmail.com
MAIL_SMTP_PASS=votre_mdp_app_google
MAIL_FROM_NAME="Site Educations"
MAIL_FROM=votreemail@gmail.com

# Application
APP_URL=https://votredomaine.com
APP_NAME=Educations Plurielles
```

**Où récupérer les infos:**
- `DB_*` : Host/Port MySQL, credentials
- `MAIL_SMTP_*` : Account Google Workspace ou service email
- `APP_URL` : Domaine production

### Étape 2️⃣ - Créer Base de Données

**Option A - Ligne de commande:**
```bash
mysql -u root -p < admin/schema.sql
```

**Option B - PHPMyAdmin:**
1. Ouvrir PHPMyAdmin
2. Créer base `site_educations_plurielles`
3. Coller contenu `admin/schema.sql`
4. Exécuter

**Résultat:** Tables `users`, `articles`, `ads`, `password_resets` créées

### Étape 3️⃣ - Créer Répertoires Uploads

```bash
mkdir -p uploads/articles
mkdir -p uploads/ads
chmod 755 uploads/
chmod 755 uploads/articles/
chmod 755 uploads/ads/
```

### Étape 4️⃣ - Accéder au Système

**URL:** `https://votredomaine.com/admin.html`

1. **Première visite** → Automatiquement redirigé vers formulaire install
2. **Créer premier administrateur:**
   - Nom: Votre nom
   - Email: votreemail@exemple.com
   - Mot de passe: ≥ 6 caractères (min)
3. **Cliquer "Créer"** → Redirigé login
4. **Se connecter** avec email + password créé
5. **Dashboard** → Statistiques (0 articles, 0 pubs, 1 admin)

### Étape 5️⃣ - Activer HTTPS (Important!)

Si pas encore en HTTPS:
1. Acquérir certificat SSL (Let's Encrypt gratuit)
2. Configurer serveur pour HTTPS
3. Redirection HTTP → HTTPS dans `.htaccess`

**Ajout .htaccess:**
```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Étape 6️⃣ - Sécurité Post-Installation

**IMPORTANT - À faire immédiatement:**

1. **Supprimer install.php**
   ```bash
   rm admin/install.php
   ```
   (Sinon quelqu'un peut créer nouvel admin!)

2. **Vérifier permissions fichiers**
   ```bash
   chmod 644 admin/*.php
   chmod 755 admin/
   chmod 755 admin/api/
   ```

3. **Tester le reset password**
   - Aller `admin.html`
   - Logout
   - Cliquer "Mot de passe oublié?"
   - Rentrer votre email
   - Vérifier email reçu avec lien reset

4. **Configurer rate-limiting (recommandé)**
   - Limiter tentatives login
   - Bloquer accès après X essais

## 📋 Checklist Avant Production

- [ ] `.env` créé et configuré
- [ ] Base de données créée (schema.sql exécuté)
- [ ] Répertoires `/uploads/` créés avec permissions
- [ ] Premier administrateur créé
- [ ] HTTPS activé
- [ ] `admin/install.php` supprimé
- [ ] Permissions fichiers correctes
- [ ] Email reset password testé
- [ ] Backup base de données configurée
- [ ] Logs d'erreurs activés (pour debug)

## 🧪 Validation du Système

### Test 1️⃣ - API Test Page
Visiter: `https://votredomaine.com/admin-test.html`
- Vérifie endpoints API
- Affiche statut base de données
- Test compteurs articles/pubs/admins

### Test 2️⃣ - Test Articles
1. Login admin
2. Dashboard → Créer article (bouton "Créer")
3. Remplir formulaire:
   - Titre: "Test Article"
   - Catégorie: "parentalite"
   - Contenu: "Contenu test"
   - Status: "published"
4. Cliquer "Enregistrer"
5. Article doit apparaître dans tableau
6. Dashboard → compteur articles = 1
7. Site public → article visible

### Test 3️⃣ - Test Publicités
1. Aller section Pubs
2. Créer pub:
   - Nom: "Test Pub"
   - Message: "Message test"
   - Status: "active"
3. Enregistrer
4. Pub doit apparaître en haut du site public

### Test 4️⃣ - Test Administrateurs
1. Section Admins
2. Créer nouvel admin:
   - Nom: "Admin Test"
   - Email: "test@example.com"
   - Mot de passe: "SecurePass123"
3. Enregistrer
4. Nouveau admin peut se connecter avec ces identifiants

## 🐛 Troubleshooting

### Problème: "Database connection error"

**Solution:**
1. Vérifier `.env` : credentials corrects?
2. Vérifier MySQL: `mysql -u root -p -e "SELECT 1"`
3. Vérifier base existe: `mysql -u root -p -e "SHOW DATABASES"`
4. Vérifier tables: `mysql -u root -p site_educations_plurielles -e "SHOW TABLES"`

### Problème: Admin.html reste sur login

**Solution:**
1. Ouvrir console navigateur (F12)
2. Voir erreurs réseau/JavaScript
3. Visiter `admin/api/index.php?action=check`
4. Doit retourner JSON (même si `{"authenticated": false}`)
5. Si erreur PHP: check error logs serveur

### Problème: Articles n'apparaissent pas

**Solution:**
1. Admin: Vérifier article dans tableau
2. Vérifier status = "published"
3. Si image: vérifier `/uploads/articles/` existe
4. Vérifier contenu en base: `SELECT * FROM articles;`

### Problème: Email reset ne marche pas

**Solution:**
1. Vérifier `.env` : MAIL_SMTP_USER/PASS corrects
2. Si Gmail: activer ["App Passwords"](https://support.google.com/accounts/answer/185833)
3. Test simple: `php admin/functions.php` (ne retourne rien si OK)
4. Check error logs PHP pour détails

### Problème: Upload fichier échoue

**Solution:**
1. Vérifier `/uploads/articles/` existe et est writable
2. Vérifier permission: `ls -la uploads/`
3. Doit afficher `drwxr-xr-x`
4. Vérifier `php.ini`: `upload_max_filesize = 20M`

## 📊 Accès API Directement

Tester endpoints directement (pour debug):

```bash
# Vérifier authentification
curl https://votredomaine.com/admin/api/index.php?action=check

# Compter articles
curl https://votredomaine.com/admin/api/index.php?action=articles_count

# Lister articles publiés
curl https://votredomaine.com/admin/api/index.php?action=articles

# Lister pubs actives
curl https://votredomaine.com/admin/api/index.php?action=ads
```

## 🔄 Maintenance Régulière

### Hebdomadaire
- [ ] Vérifier erreurs dans logs
- [ ] Vérifier espace disque
- [ ] Tester reset password

### Mensuel
- [ ] Backup base de données
- [ ] Review administrateurs actifs
- [ ] Purger articles/pubs périmées

### Trimestriellement
- [ ] Update PHP/MySQL
- [ ] Security audit
- [ ] Vérifier certificat SSL (expiration)

## 🆘 Support et Questions

### Ressources Incluses
- `ADMIN-SETUP.md` - Documentation complète
- `ADMIN-README.md` - Guide détaillé
- `SYSTEME-ADMIN-RESUME.md` - Vue d'ensemble technique
- `admin-test.html` - Outil test endpoints

### Fichiers Clés
- `admin.html` - Interface admin (SPA)
- `admin/api/index.php` - API REST
- `admin/config.php` - Configuration + database
- `admin/functions.php` - Fonctions utilitaires
- `admin/.env` - Variables d'environnement

### PHP Version
- Minimum: **PHP 7.4**
- Recommandé: **PHP 8.0+**

### MySQL Version
- Minimum: **MySQL 5.7**
- Recommandé: **MySQL 8.0+**

---

**🎉 Votre système admin est prêt! Commencez par l'étape 1.**

**Questions fréquentes? Consultez les fichiers markdown inclus.**

**Date:** 2024  
**Version:** 1.0 Final
