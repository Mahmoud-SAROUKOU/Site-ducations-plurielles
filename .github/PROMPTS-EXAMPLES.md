# Exemples de Prompts - Agent IA

## 📝 Création de contenu

### Créer un article
```
Prompt: "Créer un nouvel article sur la parentalité positive avec upload d'image"

L'IA génère:
- Structure HTML formulaire admin.html
- Compression image client (Canvas API)
- Appel syncToServer() avec bon format
- Gestion erreurs + feedback utilisateur
```

### Ajouter une publicité
```
Prompt: "Ajouter une bannière publicitaire dans le slider principal"

L'IA sait:
- localStorage key: 'ep_ads'
- Structure: {name, message, icon, position, target, status}
- Render dans news-ticker via ContentManager
```

## 🔧 Développement Backend

### Nouvel endpoint API
```
Prompt: "Créer endpoint API pour gérer les commentaires d'articles"

L'IA génère (dans HOSTINGER-SYNC-UPLOAD.php):
- Vérification table 'comments' existe
- CRUD complet (create/update/delete)
- Validation données + sanitization
- Retour JSON standardisé
```

### Protection de page
```
Prompt: "Protéger la page admin/settings.php avec authentification"

L'IA ajoute:
<?php
require_once __DIR__ . '/auth.php';
$auth = new Auth();
$auth->require();
$admin = $auth->getAdmin();
?>
```

## 🎨 Frontend & UI

### Nouvelle page SPA
```
Prompt: "Ajouter une page 'contact' dans la navigation principale"

L'IA modifie:
1. index.html: nouvelle section <section id="contact">
2. script.js: NavigationManager.pages += 'contact'
3. style.css: styles spécifiques page contact
4. Navigation: nouveau lien avec data-page="contact"
```

### Animation mobile
```
Prompt: "Améliorer le swipe menu mobile pour iOS"

L'IA référence:
- mobile-enhancements.js (fonction initTouchHandlers)
- Pattern touch events passifs
- Gestion -webkit-transform pour GPU
```

## 🐛 Debugging

### Images ne s'uploadent pas
```
Prompt: "Debug: les images ne s'uploadent pas, erreur 500"

L'IA propose checklist:
1. Vérifier GD Library: php -m | grep gd
2. Permissions uploads/images/: chmod 755
3. Taille < 5MB (MAX_UPLOAD_BYTES)
4. Console navigateur: logs compression
5. Logs serveur PHP: error.log
```

### Synchronisation échoue
```
Prompt: "La synchronisation avec Hostinger ne fonctionne pas"

L'IA génère script debug:
- Test endpoint: fetch avec X-Admin-Sync-Key
- Comparer clés (sync.php vs admin.html)
- Vérifier CORS headers
- Test connexion DB (PDO)
```

### Page blanche après navigation
```
Prompt: "Page blanche quand je clique sur Articles"

L'IA vérifie:
- NavigationManager.navigateTo() appelé
- ID section = 'articles' (pas 'article')
- Classes CSS active/display
- Console errors JavaScript
- ContentManager articles chargés
```

## 🚀 Optimisation

### Performance images
```
Prompt: "Optimiser le chargement des images pour mobile"

L'IA suggère:
- Lazy loading natif (loading="lazy")
- Responsive srcset avec tailles multiples
- WebP avec fallback JPEG
- Compression ajustée (quality 75 pour mobile)
```

### Cache & PWA
```
Prompt: "Implémenter service worker pour mode offline"

L'IA référence:
- manifest.json existant
- Strategy: Cache-First pour assets statiques
- Network-First pour API calls
- Fallback localStorage pour contenu
```

## 📊 Analyse & Reporting

### Statistiques admin
```
Prompt: "Ajouter dashboard avec stats: articles publiés, vues, top catégories"

L'IA crée:
- Endpoint API: /admin/api/stats.php
- Requêtes SQL groupées efficaces
- Graphiques Chart.js ou similaire
- Mise à jour temps réel (polling/SSE)
```

### Export données
```
Prompt: "Fonction export tous les articles en JSON pour backup"

L'IA génère:
function exportArticles() {
    const articles = localStorage.getItem('ep_articles');
    const blob = new Blob([articles], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `articles-${Date.now()}.json`;
    a.click();
}
```

## 🔒 Sécurité

### CSRF Protection
```
Prompt: "Ajouter protection CSRF aux formulaires admin"

L'IA implémente:
- Génération token: bin2hex(random_bytes(32))
- Stockage session + hidden input
- Validation côté serveur avant traitement
- Régénération après usage
```

### Rate Limiting
```
Prompt: "Limiter tentatives connexion à 5/15min par IP"

L'IA crée:
- Table attempts (ip, timestamp, count)
- Cleanup automatique vieux records
- Vérif avant login: MAX_ATTEMPTS const
- Lockout temporaire: LOCKOUT_TIME
```

## 📱 Mobile & Responsive

### Fix viewport iOS
```
Prompt: "Corriger hauteur viewport 100vh sur iPhone"

L'IA utilise pattern existant:
- CSS custom property: --vh
- JavaScript: window.innerHeight * 0.01
- Écoute resize/orientationchange
- Référence: mobile-enhancements.js
```

### Touch gestures
```
Prompt: "Ajouter swipe left/right sur galerie d'images"

L'IA implémente:
- touchstart: enregistrer position
- touchmove: calculer delta
- touchend: déclencher action si > threshold
- Prévention scroll vertical simultané
```

## 🎨 Charte graphique

### Nouveau composant stylé
```
Prompt: "Créer card testimonial respectant charte graphique"

L'IA utilise (CHARTE_GRAPHIQUE.md):
- Couleurs: primary #1e3a8a, accent #fbbf24
- Border radius: 12px standard
- Transition: 0.3s cubic-bezier
- Hover: translateY(-5px) + shadow augmentée
- Font-weight: 600 pour titres
```

## 💾 Base de données

### Migration SQLite → MySQL
```
Prompt: "Migrer de SQLite local vers MySQL Hostinger"

L'IA crée script:
1. Export SQLite: .dump to SQL file
2. Adapter syntaxe MySQL (AUTO_INCREMENT, etc.)
3. Modifier config.php: USE_SQLITE = false
4. Import via phpMyAdmin
5. Test connexion: admin/test-auth.php
```

### Nouvelle table
```
Prompt: "Ajouter table 'newsletters' pour abonnés"

L'IA génère (dans db.php init()):
CREATE TABLE IF NOT EXISTS newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) UNIQUE NOT NULL,
    name VARCHAR(120),
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    INDEX(email),
    INDEX(status)
)
```

## 🔍 Recherche & Filtres

### Recherche fulltext
```
Prompt: "Ajouter recherche dans articles par titre/contenu"

L'IA implémente:
- Frontend: input + debounce pour éviter spam
- API: LIKE %query% ou MATCH AGAINST (MySQL)
- Highlight résultats: mark tag HTML
- Pagination résultats
```

## 📧 Notifications

### Email transactionnel
```
Prompt: "Envoyer email quand nouvel article publié"

L'IA utilise (admin/mailer.php):
- Classe Mailer existante
- Template HTML responsive
- Variables dynamiques: {nom}, {titre}, {lien}
- Logs: admin/emails.log
- Fallback si SMTP échoue
```

## ⚡ Performance Tips

L'IA connait ces optimisations du projet :
- **Lazy loading** : images + scripts non critiques
- **Code splitting** : JS chargé par page
- **Compression** : double (client + serveur)
- **CDN** : Tailwind, FontAwesome, Google Fonts
- **Cache** : localStorage + fallback cascade
- **Preconnect** : DNS prefetch pour CDN
- **GPU** : translateZ(0) pour animations

## 🎯 Patterns Avancés

### Event-driven architecture
```
Prompt: "Créer système d'events pour articles modifiés"

L'IA implémente:
window.dispatchEvent(new CustomEvent('articleUpdated', {
    detail: {id, title, changes}
}));

// Listeners
window.addEventListener('articleUpdated', (e) => {
    refreshArticlesList();
    showNotification('Article mis à jour');
});
```

### Middleware pattern
```
Prompt: "Ajouter middleware validation avant sync API"

L'IA crée:
const middlewares = [
    validateArticleData,
    checkDuplicates,
    sanitizeHtml,
    compressImage
];

async function syncWithMiddleware(article) {
    for (const mw of middlewares) {
        article = await mw(article);
    }
    return syncToServer('article', article, 'create');
}
```

---

**💡 Pro Tip** : Plus le prompt est précis avec contexte projet, meilleure sera la réponse de l'IA. Référencer fichiers/patterns existants aide énormément.
