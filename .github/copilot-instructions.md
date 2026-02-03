# Educations Plurielles - AI Coding Instructions

## Architecture Overview

**Site statique + Admin panel hybride** - Frontend HTML/JS + Backend PHP avec sync bidirectionnelle.

### Structure principale
- **Frontend public** : [index.html](../index.html) - SPA avec navigation JS ([script.js](../script.js), [content-loader.js](../content-loader.js))
- **Admin panel** : `admin/` - Interface de gestion stockée dans localStorage, synchronisable avec serveur
- **API de sync** : [HOSTINGER-SYNC-UPLOAD.php](../HOSTINGER-SYNC-UPLOAD.php) → déploie vers `/admin/api/sync.php` sur serveur
- **Upload d'images** : [HOSTINGER-IMAGE-UPLOAD.php](../HOSTINGER-IMAGE-UPLOAD.php) → double compression client+serveur

### Flux de données
1. **Local** : Modification dans `admin.html` → localStorage (`ep_articles`, `ep_ads`)
2. **Sync** : POST vers `/admin/api/sync.php` avec clé API → base MySQL distante
3. **Affichage** : Frontend charge depuis `/admin/api/index.php?action=articles` ou fallback `site-content.json` puis localStorage

## Configuration & Démarrage

### Local Development
```bash
# Lancer serveur PHP local (port 8000)
.\CONNEXION-RAPIDE.bat  # Windows
# ou
php -S localhost:8000
```

### Variables d'environnement
Copier [.env.example](../.env.example) → `.env` et configurer :
- **DB_*** : MySQL/SQLite (local utilise SQLite : `admin/database.sqlite`)
- **ADMIN_SYNC_KEY** : Clé API pour synchronisation (générer avec `openssl rand -base64 32`)
- **UPLOAD_BASE_URL** : URL publique du dossier `uploads/images/`

### Installation admin
1. Accéder à `http://localhost:8000/admin/setup.php`
2. Ou exécuter `.\install-admin.bat` (Windows)

**Connexion rapide** : Identifiants dans [.admin-credentials.txt](../.admin-credentials.txt)

## PHP Backend

### Architecture Auth ([admin/auth.php](../admin/auth.php))
- **Classe `Auth`** : Session + gestion admins
- **Connexion sans mot de passe** : `loginWithoutPassword($email)` pour système simplifié
- **Session timeout** : 1h (constante `SESSION_TIMEOUT`)
- **Protection pages** : `require_once 'admin/auth.php'; $auth->requireLogin();`

### Base de données ([admin/db.php](../admin/db.php))
- **Classe `Database`** : Singleton PDO, supporte MySQL + SQLite
- **Auto-init** : Tables créées automatiquement si inexistantes
- **Switch DB** : Variable `USE_SQLITE` dans [admin/config.php](../admin/config.php)

### API Endpoints (`admin/api/`)
- `sync.php` : CRUD pour articles/publicités/admins/catégories (POST + clé API header `X-Admin-Sync-Key`)
- `index.php?action=articles` : GET public pour frontend
- Upload images avec compression GD Library (JPEG 82%, WebP 80%)

## Frontend JavaScript

### Navigation ([script.js](../script.js))
- **Classe `NavigationManager`** : SPA hash-based (`#accueil`, `#articles`, etc.)
- **iOS optimizations** : Gestion touch events spécifique
- **Pages** : accueil, articles, videos, ressources, apropos

### Chargement contenu ([content-loader.js](../content-loader.js))
- **Classe `ContentManager`** : Cascade de fallbacks
  1. API serveur (`/admin/api/index.php?action=articles`)
  2. Fichier statique (`site-content.json`)
  3. localStorage (`ep_articles`, `ep_ads`)
- **Event** : `window.addEventListener('contentLoaded', ...)`

### Admin Panel (`admin.html`)
- **localStorage** : Stockage local avec tracking `remote_id`
- **Compression client** : Canvas API, max 1600px, quality 85%
- **Sync auto** : Bouton "Synchroniser" envoie POST batch vers API

## Conventions de Développement

### Naming
- **PHP classes** : PascalCase (`Auth`, `Database`, `Mailer`)
- **JS classes** : PascalCase (`NavigationManager`, `ContentManager`)
- **Functions** : camelCase PHP/JS, snake_case SQL
- **Fichiers** : kebab-case généralement, UPPER pour docs

### Sécurité
- **API Auth** : Header `X-Admin-Sync-Key` vérifié avant toute opération
- **CORS** : Configuré dans tous les endpoints API
- **Validation** : Input sanitization dans [HOSTINGER-SYNC-UPLOAD.php](../HOSTINGER-SYNC-UPLOAD.php) (lignes 68-90)

### Images
- **Upload path** : `uploads/images/`
- **Compression double** : Client (avant upload) + Serveur (GD Library)
- **Formats** : JPEG (quality 82%), WebP (80%), PNG (compression 6)
- **Nettoyage auto** : Suppression ancienne image lors update/delete

## Déploiement Hostinger

**Guide complet** : [DEPLOIEMENT-HOSTINGER-INSTRUCTIONS.md](../DEPLOIEMENT-HOSTINGER-INSTRUCTIONS.md)

### Checklist
1. Générer clé API sécurisée (PowerShell/openssl)
2. Upload `HOSTINGER-SYNC-UPLOAD.php` → `/admin/api/sync.php`
3. Upload `HOSTINGER-IMAGE-UPLOAD.php` → `/admin/api/upload.php`
4. Configurer DB + clé API (même clé dans sync.php, upload.php, admin.html)
5. Créer dossiers : `uploads/images/`, `admin/api/`
6. Tester avec [test-configuration.html](../test-configuration.html)

## Charte Graphique

**Référence** : [CHARTE_GRAPHIQUE.md](../CHARTE_GRAPHIQUE.md)

- **Primary** : Bleu `#1e3a8a` (autorité) → `#3b82f6` (modernité)
- **Accents** : Or `#fbbf24`, Rose `#f472b6`, Bleu ciel `#7dd3fc`
- **Transitions** : 0.3s cubic-bezier, hover translateY(-5px)
- **Classes Tailwind** : Utilisées massivement, config inline dans [index.html](../index.html)

## Points d'attention

### Compatibilité local/distant
- **Sync bidirectionnelle** : localStorage ↔ MySQL avec tracking `remote_id`
- **Fallback cascade** : API → JSON statique → localStorage garantit affichage même hors ligne
- **Slug matching** : Si pas de `remote_id`, match par slug pour éviter doublons

### Mobile & PWA
- **Manifest** : [manifest.json](../manifest.json) pour installation mobile
- **Optimizations CSS** : [mobile-optimizations.css](../mobile-optimizations.css)
- **Enhancements JS** : [mobile-enhancements.js](../mobile-enhancements.js)
- **Meta tags** : iOS/Android specific dans [index.html](../index.html) lignes 9-21

### Documentation abondante
**30+ fichiers MD/TXT** - Privilégier :
- [START-HERE.md](../START-HERE.md) - Quick start 3 étapes
- [INDEX.md](../INDEX.md) - Index complet de la documentation
- [RECAPITULATIF-FINAL.md](../RECAPITULATIF-FINAL.md) - Vue d'ensemble technique

## Tests & Debug

- **Test config** : Ouvrir [test-configuration.html](../test-configuration.html) pour vérifier endpoints
- **Test auth** : `http://localhost:8000/admin/test-auth.php`
- **Logs emails** : `admin/emails.log`
- **Scripts console** : [config-rapide.js](../config-rapide.js) pour configuration en 1 clic

## Workflows Courants

### Créer un nouvel article (local → serveur)
```javascript
// Dans admin.html, après création article
const article = {
    title: "Mon article",
    content: "Contenu...",
    category: "parentalite",
    author: "Admin",
    image_url: await uploadImageFile(imageFile) // Upload + compression auto
};
await syncToServer('article', article, 'create'); // Sync MySQL distant
```

### Protéger une nouvelle page PHP
```php
<?php
require_once __DIR__ . '/admin/auth.php';
$auth = new Auth();
$auth->require(); // Redirige si non connecté
$admin = $auth->getAdmin(); // Récupère infos admin connecté
?>
```

### Ajouter une nouvelle route API
```php
// Dans HOSTINGER-SYNC-UPLOAD.php, après les autres types
if ($type === 'mon_type') {
    if (!tableExists('ma_table')) {
        throw new Exception('Table introuvable');
    }
    // Logique CRUD ici
    $operation = $input['operation']; // 'create', 'update', 'delete'
    // ...
}
```

### Debugging synchronisation
```javascript
// Dans console navigateur (F12)
localStorage.getItem('syncConfig'); // Voir config actuelle
localStorage.getItem('ep_articles'); // Voir articles locaux
// Tester endpoint manuellement
fetch('/admin/api/sync.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Admin-Sync-Key': 'votre_cle'
    },
    body: JSON.stringify({type: 'article', operation: 'create', data: {...}})
});
```

## Commandes Utiles

```powershell
# Démarrer serveur développement
.\CONNEXION-RAPIDE.bat

# Arrêter serveur
.\ARRETER-SERVEUR.bat

# Réinitialiser base de données (perte de données!)
Remove-Item admin\database.sqlite
# Puis naviguer vers: http://localhost:8000/admin/setup.php

# Générer clé API sécurisée
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))

# Tester connexion DB
php -r "new PDO('sqlite:admin/database.sqlite');"
```

## Patterns de Code Récurrents

### Classe PHP avec PDO
```php
class MaClasse {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::connect(); // Singleton
    }
    
    public function getData(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM table WHERE id = ?");
        $stmt->execute([1]);
        return $stmt->fetchAll();
    }
}
```

### Classe JavaScript SPA
```javascript
class MonManager {
    constructor() {
        this.data = null;
        this.init();
    }
    
    init() {
        // Setup event listeners
        document.addEventListener('DOMContentLoaded', () => {
            this.loadData();
        });
    }
    
    async loadData() {
        try {
            const response = await fetch('/api/endpoint');
            this.data = await response.json();
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
}
```

### Gestion localStorage avec sync
```javascript
function saveArticle(article) {
    // 1. Sauvegarder localement
    let articles = JSON.parse(localStorage.getItem('ep_articles') || '[]');
    articles.push(article);
    localStorage.setItem('ep_articles', JSON.stringify(articles));
    
    // 2. Sync serveur si activé
    if (syncConfig.enabled) {
        syncToServer('article', article, 'create');
    }
}
```

## Résolution Problèmes Fréquents

### Images ne s'uploadent pas
1. Vérifier `uploads/images/` existe avec permissions 755
2. Vérifier GD Library installée : `php -m | grep gd`
3. Vérifier taille < 5MB et format JPEG/PNG/WebP
4. Console navigateur : chercher erreurs compression client

### Synchronisation échoue
1. [test-configuration.html](../test-configuration.html) → Vérifier endpoints
2. Comparer clé API dans sync.php, upload.php, admin.html (doivent être identiques)
3. Console navigateur : vérifier header `X-Admin-Sync-Key` envoyé
4. Logs serveur PHP pour erreurs backend

### Pages ne s'affichent pas (SPA)
1. Console navigateur : vérifier erreurs JavaScript
2. Vérifier `NavigationManager` initialisé : `window.navigationManager`
3. Tester navigation manuelle : `window.navigationManager.navigateTo('articles')`
4. Vérifier IDs pages correspondent : `accueil`, `articles`, `videos`, `ressources`, `apropos`

### Session expirée trop vite
- Modifier `SESSION_TIMEOUT` dans [admin/auth.php](../admin/auth.php) ligne 13 (défaut: 3600s = 1h)
