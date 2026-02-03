# 🚀 Guide de démarrage rapide - Système Admin Unifié

## ⚡ Installation en 3 étapes

### 1. Configuration (.env)
Créez un fichier `.env` à la racine de votre projet :

```
APP_URL=http://localhost
APP_NAME=Educations Plurielles
DB_HOST=localhost
DB_NAME=educations_plurielles
DB_USER=root
DB_PASS=
MAIL_FROM=admin@exemple.com
MAIL_FROM_NAME=Admin
```

### 2. Installation de la base de données
Accédez à : **http://localhost/admin/install-unified.php**

Remplissez le formulaire :
- Nom complet
- Email
- Mot de passe (min 8 caractères)

✓ **Prêt !** Les tables sont créées et votre compte admin est créé.

### 3. Connexion
Allez à : **http://localhost/admin/login-unified.php**

Entrez vos identifiants pour vous connecter.

---

## 🔗 Liens utiles

| Page | URL | Description |
|------|-----|-------------|
| **Connexion** | `/admin/login-unified.php` | Page de connexion |
| **Tableau de bord** | `/admin/dashboard-unified.php` | Vue d'ensemble |
| **Gestion utilisateurs** | `/admin/users.php` | Créer/modifier/supprimer utilisateurs |
| **Réinitialisation mot de passe** | `/admin/reset-request-unified.php` | Demander réinitialisation |
| **Test du système** | `/admin/test-auth.php` | Diagnostiquer les problèmes |

---

## 🔐 Intégrer dans vos pages

Pour protéger une page avec l'authentification :

```php
<?php
require_once __DIR__ . '/admin/auth.php';

// Exiger une connexion
$auth->requireLogin();

// Récupérer l'utilisateur actuel
$user = $auth->getCurrentUser();
echo "Bonjour " . $user['name'];
?>
```

Pour un rôle spécifique :

```php
<?php
// Exiger le rôle admin
$auth->requireRole('admin');
?>
```

---

## 🎯 Rôles disponibles

- **super_admin** : Accès complet
- **admin** : Gestion complète
- **editor** : Création/modification d'articles
- **viewer** : Lecture seule

---

## ⚙️ Configuration avancée

### Session timeout
Dans [auth.php](auth.php#L10) :
```php
const SESSION_TIMEOUT = 3600; // 1 heure
```

### Tentatives de connexion
```php
const MAX_LOGIN_ATTEMPTS = 5;
const LOCKOUT_DURATION = 900; // 15 minutes
```

### Longueur min mot de passe
```php
const PASSWORD_MIN_LENGTH = 8;
```

---

## 🐛 Dépannage

### "Impossible de se connecter à la base de données"
✓ Vérifiez votre fichier `.env`
✓ Vérifiez que MySQL est actif
✓ Testez avec : http://localhost/admin/test-auth.php

### "Tables manquantes"
✓ Accédez à http://localhost/admin/install-unified.php

### "Mot de passe oublié"
✓ Cliquez sur "Mot de passe oublié" à la page de connexion
✓ Vérifiez votre email (ou le dossier spam)
✓ Cliquez sur le lien reçu

---

## 📚 Documentation complète

Consultez : [ADMIN-UNIFIED-README.md](../ADMIN-UNIFIED-README.md)

---

## ✅ Checklist pour un nouveau site

- [ ] Créer le fichier `.env`
- [ ] Accéder à `/admin/install-unified.php`
- [ ] Créer le compte super admin
- [ ] Tester la connexion
- [ ] Créer les utilisateurs supplémentaires
- [ ] Intégrer l'auth dans les pages protégées
- [ ] Configurer les emails de réinitialisation

---

**Tout est prêt ! Votre système admin est fonctionnel. 🎉**
