# 🎯 DÉMARRER IMMÉDIATEMENT

## 🤖 Nouveau : Agent IA Configuré !

Ce projet est **optimisé pour GitHub Copilot et agents IA**. Voir [.github/AGENT-SETUP-COMPLETE.md](.github/AGENT-SETUP-COMPLETE.md) pour :
- Instructions complètes pour IA
- Exemples de prompts efficaces
- Intégration IDE (VS Code, Cursor, Windsurf, etc.)

---

## 3 étapes pour avoir votre système admin en place

### 1️⃣ Créer `.env`
À la racine de votre projet, créez un fichier nommé `.env` :

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

Adaptez `DB_USER` et `DB_PASS` à votre configuration.

### 2️⃣ Installer
Ouvrez votre navigateur et allez à :

```
http://localhost/admin/install-unified.php
```

Remplissez le formulaire et validez.

**Voilà !** Votre base de données est créée et votre compte admin aussi.

### 3️⃣ Se connecter
Allez à :

```
http://localhost/admin/login-unified.php
```

Connectez-vous avec l'email et le mot de passe que vous avez créés.

---

## 🔗 Liens principaux

| Besoin | Lien |
|--------|------|
| Accueil | `http://localhost/admin-index.php` |
| Se connecter | `http://localhost/admin/login-unified.php` |
| Créer utilisateur | `http://localhost/admin/users.php` |
| Mot de passe oublié | `http://localhost/admin/reset-request-unified.php` |
| Aide | `README-ADMIN-SYSTEM.md` |

---

## ✅ Vérifier que c'est OK

Allez à : `http://localhost/admin/test-auth.php`

Vous devriez voir du vert partout ✓

---

## 💻 Utiliser dans une page

Pour protéger une page avec la connexion :

```php
<?php
require_once __DIR__ . '/admin/auth.php';
$auth->requireLogin();
?>
```

C'est tout ! La page est maintenant protégée.

---

## 📚 Besoin de plus ?

- **Installation détaillée** → `ADMIN-QUICK-START.md`
- **Documentation complète** → `ADMIN-UNIFIED-README.md`
- **Exemples de code** → `ADMIN-INTEGRATION-EXAMPLES.php`
- **Guide de migration** → `ADMIN-MIGRATION-GUIDE.md`

---

**Vous avez besoin d'aide ? Consultez la documentation.** 📖
