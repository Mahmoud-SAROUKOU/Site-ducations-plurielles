# Système Admin Unifié - État de Déploiement

## ✅ Tâches Complètées

### Backend API
- **admin/api/index.php** - Routeur API centralisé avec tous les endpoints:
  - ✅ `?action=check` - Vérifier authentification
  - ✅ `?action=articles_count/list/detail` - Gestion articles
  - ✅ `?action=ads_count/list/detail` - Gestion pubs
  - ✅ `?action=admins_count/list/create/delete` - Gestion administrateurs
  - ✅ `?action=articles` - API publique articles
  - ✅ `?action=ads` - API publique pubs
  - ✅ `?action=article?slug=...` - Article détail public

### Frontend SPA
- **admin.html** - Interface unifiée avec:
  - ✅ Page login avec formulaire
  - ✅ Page install (création premier admin)
  - ✅ Page reset request (oubli mot de passe)
  - ✅ Dashboard avec statistiques
  - ✅ Gestion articles (CRUD complet)
  - ✅ Gestion pubs/publicités (CRUD complet)
  - ✅ Gestion administrateurs (liste + création + suppression)
  - ✅ Sidebar navigation
  - ✅ Styling responsive avec CSS variables

### JavaScript Client
- **admin/api/admin-client.js** - 470+ lignes:
  - ✅ Vérification authentification
  - ✅ Navigation SPA
  - ✅ Formulaires login/install/reset
  - ✅ Chargement données (articles, pubs, admins)
  - ✅ CRUD articles
  - ✅ CRUD pubs
  - ✅ CRUD administrateurs
  - ✅ Messages flash

## 🚀 Points d'Accès Unifiés

| URL | Purpose |
|-----|---------|
| `admin.html` | **Point d'entrée principal** (SPA complète) |
| `admin/login.php` | Authentification (appelée par SPA) |
| `admin/install.php` | Premier admin (appelée par SPA) |
| `admin/api/index.php` | API REST centralisée |

## 🔧 Configuration Requise

### 1. Créer .env
```bash
cp admin/.env.example admin/.env
# Éditer avec vos credentials:
DB_HOST=localhost
DB_NAME=votre_base
DB_USER=votre_user
DB_PASS=votre_mdp
MAIL_SMTP_HOST=smtp.gmail.com
MAIL_SMTP_USER=votre@email.com
MAIL_SMTP_PASS=votre_mdp_app
MAIL_FROM=votre@email.com
APP_URL=https://votresite.com
```

### 2. Importer la base de données
```bash
mysql -u votre_user -p votre_base < admin/schema.sql
```

### 3. Accéder à l'admin
- URL: `https://votresite.com/admin.html`
- Première visite: redirection automatique vers install
- Créer premier administrateur
- Connexion et accès au dashboard

## 📋 Flux Utilisateur

### Premier Accès
1. `admin.html` → check authentification → redirect install
2. Formulaire création premier admin
3. Redirect login
4. Connexion avec créditentials
5. Dashboard avec statistiques

### Gestion Articles
1. Cliquer "Articles" dans sidebar
2. Voir liste articles avec actions (Modifier/Supprimer)
3. Formulaire nouveau/modifier article
4. Soumettre → Base de données
5. Liste se met à jour

### Gestion Pubs
1. Cliquer "Pubs" dans sidebar
2. Voir liste pubs avec position et status
3. Formulaire créer/modifier pub
4. Soumettre → Base de données
5. Affichage sur site public

### Gestion Administrateurs
1. Cliquer "Admins" dans sidebar
2. Voir liste administrateurs
3. Créer nouvel admin (email + mdp)
4. Supprimer admin (sauf soi-même)
5. Nouvelle admin peut se connecter

## 🔐 Sécurité

- ✅ Mots de passe hashés en bcrypt
- ✅ Protection CSRF sur formulaires
- ✅ Sessions sécurisées (PHP native)
- ✅ Requêtes préparées (PDO prepared statements)
- ✅ Authentification requise (require_login)
- ✅ Validation fichiers uploadés
- ✅ .htaccess pour bloquer accès direct .php

## 📊 Endpoints API

### Check Auth
```
GET admin/api/index.php?action=check
Response: {
  authenticated: boolean,
  user: {id, name, email},
  needs_install: boolean
}
```

### Articles List (Admin)
```
GET admin/api/index.php?action=articles_list
Response: {
  articles: [{id, title, category, status, author_name}, ...]
}
```

### Articles Detail (Admin)
```
GET admin/api/index.php?action=articles_detail&id=1
Response: {
  article: {id, title, slug, category, excerpt, content, image_url, tags, read_time, status}
}
```

### Admin Create
```
POST admin/api/index.php?action=admin_create
Content-Type: application/json
{
  name: "Nom Admin",
  email: "email@example.com",
  password: "motdepasse"
}
Response: {success: true} ou {success: false, error: "..."}
```

## ⚠️ À Faire Avant Production

- [ ] Configurer .env avec credentials réels
- [ ] Tester l'email reset (PHPMailer)
- [ ] Vérifier permissions fichiers /uploads/
- [ ] HTTPS activé (https-only)
- [ ] Supprimer install.php après premier admin
- [ ] Configurer robots.txt pour /admin/
- [ ] Tests: login, CRUD articles, CRUD pubs, CRUD admins
- [ ] Tests: reset password email flow
- [ ] Tests: logout + session timeout

## 🐛 Dépannage

### "Database connection error"
- Vérifier credentials dans .env
- Vérifier MySQL est en cours d'exécution
- Vérifier base de données existe

### "Admin.html reste sur login"
- Vérifier cookies activés (sessions PHP)
- Vérifier admin/api/index.php?action=check retourne JSON
- Vérifier erreurs dans console navigateur

### Articles n'apparaissent pas
- Vérifier articles insérés en base (`SELECT * FROM articles`)
- Vérifier status='published'
- Vérifier published_at <= NOW()

### Pubs n'apparaissent pas sur site public
- Vérifier status='active'
- Vérifier admin/api/index.php?action=ads retourne JSON

## 📞 Support

Fichiers de support:
- ADMIN-README.md - Documentation détaillée
- admin/.env.example - Modèle configuration
- admin/schema.sql - Schéma base de données
- admin/config.php - Fonctions configuration
- admin/functions.php - Utilitaires

---

**Version**: 1.0 - Admin System Unified  
**Last Updated**: 2024  
**Status**: ✅ Complete
