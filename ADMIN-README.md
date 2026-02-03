# Educations Plurielles - Système Admin & Base de Données

## 🚀 Installation rapide

### 1. Créer la base de données

```bash
mysql -u root -p < admin/schema.sql
```

Ou importer manuellement via phpMyAdmin :
- Fichier: `admin/schema.sql`
- Charset: UTF-8
- Collation: utf8mb4_unicode_ci

### 2. Configurer `.env`

Éditer le fichier `.env` à la racine :

```ini
APP_URL=http://localhost/Site%20Educations%20Plurielles
DB_HOST=localhost
DB_NAME=educations_plurielles
DB_USER=root
DB_PASS=CHANGE_MOI

# Email (optionnel mais recommandé)
MAIL_FROM=admin@monsite.fr
MAIL_FROM_NAME=Educations Plurielles
MAIL_SMTP_HOST=smtp.gmail.com
MAIL_SMTP_PORT=587
MAIL_SMTP_USER=votreemail@gmail.com
MAIL_SMTP_PASS=motdepasse_app_google
MAIL_SMTP_SECURE=tls
```

### 3. Installer les dépendances (optionnel - pour SMTP)

```bash
composer install
```

Sinon, le reset de mot de passe utilisera `mail()` PHP (fallback).

### 4. Créer le premier admin

Ouvrir : **http://localhost/Site%20Educations%20Plurielles/admin/install.php**

Créer un compte admin, puis **supprimer `install.php`** pour des raisons de sécurité.

### 5. Se connecter

Aller à : **http://localhost/Site%20Educations%20Plurielles/admin/login.php**

Ou via le point d'entrée unique : **http://localhost/Site%20Educations%20Plurielles/admin.html**

---

## 📊 Structure admin

| Page | URL | Fonction |
|------|-----|----------|
| **Connexion** | `/admin/login.php` | S'identifier |
| **Tableau de bord** | `/admin/dashboard.php` | Aperçu & stats |
| **Articles** | `/admin/articles.php` | CRUD articles |
| **Publicités** | `/admin/ads.php` | CRUD pubs + ticker |
| **Reset mot de passe** | `/admin/reset-request.php` | Demande reset |
| **Reset (lien token)** | `/admin/reset.php?token=...` | Nouveau mot de passe |

---

## 🎯 Fonctionnalités principales

### Articles
- ✅ CRUD (créer/lire/éditer/supprimer)
- ✅ Statut (brouillon/publié)
- ✅ Catégories (parentalité, droits, protection, éducation, santé, développement, témoignages)
- ✅ Images (upload ou URL)
- ✅ Tags (séparés par virgules)
- ✅ Slug automatique
- ✅ Temps de lecture
- ✅ Date de publication programmée

### Publicités
- ✅ CRUD pubs
- ✅ Image + lien cible
- ✅ Ticker (bandeau défilant avec messages)
- ✅ Dates d'activation (début/fin)
- ✅ Ordre d'affichage
- ✅ Statut (active/pause)

### Sécurité
- ✅ Authentification (mot de passe bcrypt)
- ✅ Sessions sécurisées
- ✅ CSRF protection
- ✅ SQL injection prevention (requêtes préparées)
- ✅ Reset mot de passe avec token expirant (1h)

---

## 🔌 API publique

L'interface récupère le contenu via des API REST :

### `/admin/api/content.php`
Tous les articles + pubs publiés.
```bash
GET /Site%20Educations%20Plurielles/admin/api/content.php
```

**Réponse** :
```json
{
  "articles": [
    {
      "id": 1,
      "title": "...",
      "slug": "...",
      "category": "parentalite",
      "content": "...",
      "image": "...",
      "tags": ["tag1", "tag2"],
      "date": "29 janvier 2026",
      "author": "..."
    }
  ],
  "ads": [
    {
      "id": 1,
      "name": "...",
      "message": "...",
      "icon": "📢",
      "image": "..."
    }
  ]
}
```

### `/admin/api/article.php?slug=mon-article`
Un article spécifique.
```bash
GET /admin/api/article.php?slug=ma-publication
```

---

## 🎨 Pages dynamiques frontend

### Articles publics
Lire un article via `/article.html?slug=...`

### Pages statiques (optionnel)
Gérer via `/admin/pages.php` (configurable).

---

## 📁 Structure des fichiers

```
Site Educations Plurielles/
├── admin/
│   ├── api/
│   │   ├── content.php    (API articles + pubs)
│   │   └── article.php    (Article simple)
│   ├── config.php         (Configuration DB)
│   ├── functions.php      (Fonctions utilitaires)
│   ├── style.css          (Styles admin)
│   ├── install.php        (Setup first admin)
│   ├── login.php          (Connexion)
│   ├── dashboard.php      (Tableau de bord)
│   ├── articles.php       (CRUD articles)
│   ├── ads.php            (CRUD pubs)
│   ├── reset-request.php  (Demande reset)
│   ├── reset.php          (Nouveau MDP)
│   ├── logout.php         (Déconnexion)
│   ├── schema.sql         (Schéma BD)
│   └── migrate.sql        (Migration si BD existe)
├── uploads/
│   ├── articles/          (Images articles)
│   └── ads/               (Images pubs)
├── admin.html             (Point d'entrée unique)
├── article.html           (Vue article détail)
├── index.html             (Front)
├── .env                   (Secrets & config)
└── composer.json          (Dépendances PHP)
```

---

## ⚙️ Configuration avancée

### Ajouter une catégorie d'article
Éditer les options dans `admin/articles.php` (environ ligne 65).

### Modifier le SMTP
Éditer `.env` ou acheter un plan d'hébergement avec SMTP.

### Uploads personnalisés
- Fichiers uploaded: `/uploads/articles/` et `/uploads/ads/`
- Max size: configurable dans `php.ini` (upload_max_filesize)

---

## 🔐 Sécurité

- **Ne jamais committer `.env`** → Ajouter à `.gitignore`
- **Supprimer `install.php`** après création
- **HTTPS en production** (configurer dans `.env` APP_URL)
- **Bloquer l'accès `/admin/` côté Apache** si souhaité (.htaccess)

---

## 🆘 Troubleshooting

**Erreur: "base de données non trouvée"**
→ Vérifier les identifiants `.env` et s'assurer que MySQL tourne.

**Erreur: "Les uploads ne fonctionnent pas"**
→ S'assurer que `/uploads/articles/` et `/uploads/ads/` sont writable (777).

**Reset MDP ne marche pas**
→ Vérifier MAIL_SMTP_HOST dans `.env` ou utiliser `mail()` natif.

**Article ne s'affiche pas publiquement**
→ Vérifier que le statut est "Publié" et la date est dans le passé.

---

## 📞 Support

Pour toute question ou problème, voir l'interface admin (dashboard).

Version: **1.0** | Date: **29 janvier 2026**
