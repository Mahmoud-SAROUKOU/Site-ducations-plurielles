# 🔧 Guide technique - Architecture Admin Panel

## 📐 Architecture générale

### Composants principaux

```
admin.html (600+ lignes)
├── HTML Structure
│   ├── Sidebar Navigation (8 sections)
│   ├── Header (Title + Sync + Profile)
│   └── Content Areas (Dashboard, Articles, etc.)
│
├── CSS Styling (Custom, ~200 lignes)
│   ├── Layout (CSS Grid/Flexbox)
│   ├── Components (Cards, Forms, Modals)
│   ├── Responsive (Breakpoints: 768px)
│   └── Dark Mode Support
│
└── JavaScript Logic (~400 lignes)
    ├── Data Management (localStorage)
    ├── CRUD Operations (Create/Read/Update/Delete)
    ├── Sync System (API calls)
    ├── UI Controllers (Modal, Navigation, Alerts)
    └── File Handling (Image upload, Compression)
```

---

## 💾 Modèle de données

### Articles (localStorage clé: `ep_articles`)

```javascript
{
  articles: [
    {
      // Métadonnées
      id: 1,                                    // Auto-incrémenté
      remote_id: 456,                           // ID serveur (après sync)
      slug: "parentalite-positive-au-quotidien", // Généré depuis titre
      
      // Contenu
      title: "La parentalité positive",
      content: "Découvrez les principes...",
      excerpt: "Texto court pour aperçu",
      
      // Données organisationnelles
      category: "parentalite",                  // Enum: parentalite|education|droits|temoignages
      tags: ["Bienveillance", "Famille"],
      author: "Marie Dupont",
      
      // Média
      image: "https://domain.com/uploads/images/image.jpg",
      image_url: "https://...",                 // Alternative
      
      // Timing
      createdAt: "2026-02-02T10:30:00Z",
      updatedAt: "2026-02-02T11:45:00Z",
      publishedAt: "2026-02-02T12:00:00Z",
      
      // Status
      status: "published",                      // published | draft | archived
      published: true,
      
      // Stats
      readTime: "8 min",
      views: 0,
      likes: 0
    }
  ]
}
```

### Publicités (localStorage clé: `ep_ads`)

```javascript
{
  ads: [
    {
      id: 1,
      name: "Atelier en ligne",
      message: "Atelier samedi : Éducation bienveillante en milieu africain",
      icon: "🎉",
      target_url: "https://example.com",
      position: "ticker",                       // ticker | sidebar | footer
      order: 1,
      status: "active",                         // active | inactive | archived
      createdAt: "2026-02-02T10:30:00Z"
    }
  ]
}
```

### Configuration Sync (localStorage clé: `syncConfig`)

```javascript
{
  enabled: true,
  endpoint: "https://domain.com/admin/api/sync.php",
  uploadUrl: "https://domain.com/admin/api/upload.php",
  refreshUrl: "https://domain.com/?refresh=1",
  apiKey: "k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8",
  lastSync: "2026-02-02T12:00:00Z"
}
```

---

## 🔄 Flux de données

### 1. Lecture (Load) ✅

```javascript
┌─────────────────────────────────────────────┐
│ Page DOMContentLoaded                        │
└────────────────┬────────────────────────────┘
                 │
                 ▼
        ┌─────────────────┐
        │ loadArticles()  │  Fetch ep_articles depuis localStorage
        └────────┬────────┘
                 │
                 ▼
        ┌──────────────────────────────────────┐
        │ renderArticles(articles)             │  Affiche grille d'articles
        └────────┬─────────────────────────────┘
                 │
         ┌───────┴────────────┐
         ▼                    ▼
    [Thumbnail]         [Article Card]
    avec preview        avec actions
```

**Code** :
```javascript
function loadArticles() {
    const stored = localStorage.getItem('ep_articles');
    articles = stored ? JSON.parse(stored) : [];
    renderArticles();
}

function renderArticles() {
    const html = articles.map((article, index) => `
        <div class="article-card">
            <img src="${article.image}" alt="${article.title}">
            <h3>${article.title}</h3>
            <div class="actions">
                <button onclick="editArticle(${index})">Modifier</button>
                <button onclick="deleteArticle(${index})">Supprimer</button>
            </div>
        </div>
    `).join('');
    
    articlesContainer.innerHTML = html;
}
```

### 2. Création (Create) ✅

```javascript
┌──────────────────────────────────────┐
│ Utilisateur clique "Créer article"   │
└─────────────┬────────────────────────┘
              │
              ▼
      ┌───────────────┐
      │ openModal()   │  Affiche formulaire vide
      └───────┬───────┘
              │
              ▼
      ┌──────────────────────┐
      │ Utilisateur rempli   │  Titre, contenu, image, etc.
      │ le formulaire        │
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ handleImageUpload()  │  Upload + génère preview
      │ (si image)           │
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ saveArticle(event)   │  
      │ - Crée objet article │
      │ - Ajoute à tableau   │
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ saveArticles()       │  Enregistre dans localStorage
      │ localStorage.setItem │
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ renderArticles()     │  Rafraîchit l'affichage
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ showAlert('Créé')    │  Notification
      └──────────────────────┘
```

**Code** :
```javascript
function saveArticle(event) {
    event.preventDefault();
    
    const article = {
        id: articles.length + 1,
        title: document.getElementById('articleTitle').value,
        content: document.getElementById('articleContent').value,
        category: document.getElementById('articleCategory').value,
        tags: document.getElementById('articleTags').value.split(','),
        image: document.getElementById('imagePreview').querySelector('img')?.src || '',
        author: document.getElementById('articleAuthor').value,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        status: 'published'
    };
    
    if (currentArticle !== null) {
        articles[currentArticle] = article;
    } else {
        articles.push(article);
    }
    
    saveArticles();
    renderArticles();
    closeModal();
    showAlert('Article enregistré !', 'success');
}

function saveArticles() {
    localStorage.setItem('ep_articles', JSON.stringify(articles));
}
```

### 3. Modification (Update) ✅

```javascript
┌──────────────────────────────────────┐
│ Utilisateur clique "Modifier"        │
└─────────────┬────────────────────────┘
              │
              ▼
      ┌──────────────────┐
      │ editArticle(idx) │  Récupère article[idx]
      └────────┬─────────┘
               │
               ▼
      ┌──────────────────────┐
      │ Pré-remplit formulaire│  Titre, contenu, image
      └────────┬─────────────┘
               │
               ▼
      ┌──────────────────────┐
      │ Utilisateur modifie  │
      └────────┬─────────────┘
               │
               ▼
      ┌──────────────────────┐
      │ saveArticle()        │  currentArticle !== null
      │ - Update au lieu de  │  → articles[idx] = new
      │   Push               │
      └────────┬─────────────┘
               │
               ▼
      ┌──────────────────────┐
      │ saveArticles()       │  Enregistre
      └────────┬─────────────┘
               │
               ▼
      ┌──────────────────────┐
      │ Alert 'Modifié'      │
      └──────────────────────┘
```

### 4. Suppression (Delete) ✅

```javascript
┌──────────────────────────────────────┐
│ Utilisateur clique "Supprimer"       │
└─────────────┬────────────────────────┘
              │
              ▼
      ┌──────────────────┐
      │ deleteArticle()  │  Confirme action
      └────────┬─────────┘
               │
               ▼
      ┌──────────────────────────────┐
      │ articles.splice(index, 1)    │  Retire du tableau
      └────────┬─────────────────────┘
               │
               ▼
      ┌──────────────────┐
      │ saveArticles()   │  Enregistre
      └────────┬─────────┘
               │
               ▼
      ┌──────────────────┐
      │ renderArticles() │  Rafraîchit
      └──────────────────┘
```

### 5. Synchronisation (Sync) ✅

```javascript
┌─────────────────────────────────────┐
│ Utilisateur clique "Synchroniser"   │
└────────────────┬────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ syncArticles()       │  Récupère config
      └────────┬─────────────┘
               │
       ┌───────┴────────────┐
       │                    │
       ▼                    ▼
   Sync activée ?      Endpoint OK ?
       │                    │
    OUI                    OUI
       │                    │
       ▼                    ▼
    ┌────────────────────────────────────┐
    │ Pour chaque article:               │
    │ POST /admin/api/sync.php           │
    │ ├─ type: 'article'                 │
    │ ├─ operation: 'create'/'update'    │
    │ └─ data: { article... }            │
    └────────┬─────────────────────────┘
             │
             ▼
    ┌────────────────────────┐
    │ Serveur (sync.php)     │
    │ ├─ Valide clé API      │
    │ ├─ INSERT/UPDATE DB    │
    │ └─ Retourne remote_id  │
    └────────┬───────────────┘
             │
             ▼
    ┌────────────────────────┐
    │ Mettre à jour remote_id│
    │ articles[i].remote_id= │
    │ response.id            │
    └────────┬───────────────┘
             │
             ▼
    ┌────────────────────────┐
    │ Sauvegarder articles   │
    │ saveArticles()         │
    └────────┬───────────────┘
             │
             ▼
    ┌────────────────────────┐
    │ showAlert('Synchronisé')
    └────────────────────────┘
```

**Code** :
```javascript
async function syncArticles() {
    const config = loadSyncConfig();
    
    if (!config.enabled || !config.endpoint) {
        showAlert('Sync désactivée', 'warning');
        return;
    }
    
    showAlert('Synchronisation en cours...', 'info');
    
    for (let i = 0; i < articles.length; i++) {
        const article = articles[i];
        const operation = article.remote_id ? 'update' : 'create';
        
        try {
            const response = await fetch(config.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Admin-Sync-Key': config.apiKey
                },
                body: JSON.stringify({
                    type: 'article',
                    operation: operation,
                    data: article
                })
            });
            
            const result = await response.json();
            
            if (result.success && result.id && !article.remote_id) {
                articles[i].remote_id = result.id;
            }
        } catch (error) {
            showAlert(`Erreur sync article ${i}: ${error.message}`, 'error');
        }
    }
    
    saveArticles();
    showAlert('Synchronisation terminée !', 'success');
}
```

---

## 🖼️ Upload d'images

### Processus complet

```javascript
┌────────────────────────────────────────┐
│ Utilisateur sélectionne image          │
└─────────────┬────────────────────────┘
              │
              ▼
      ┌──────────────────────┐
      │ handleImageUpload()  │
      │ - event.target.files │
      └────────┬─────────────┘
               │
               ▼
      ┌──────────────────────┐
      │ Validation:          │
      │ ✓ MIME type          │
      │ ✓ Taille (5MB max)   │
      │ ✓ Dimensions         │
      └────────┬─────────────┘
               │
          Valide ? 
         ╱       ╲
       OUI       NON → showAlert('Erreur')
        │          
        ▼
    ┌────────────────────────┐
    │ FileReader API         │
    │ - readAsDataURL()      │
    │ - Génère preview       │
    └────────┬───────────────┘
             │
             ▼
    ┌────────────────────────┐
    │ Affiche preview image  │
    │ document.getElementById│
    │ ('imagePreview')       │
    └────────┬───────────────┘
             │
             ▼
    ┌────────────────────────┐
    │ Enregistre file dans   │
    │ currentImageFile       │
    │ (pour upload serveur)  │
    └────────────────────────┘
```

**Code** :
```javascript
function handleImageUpload(file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    
    if (file.size > maxSize) {
        showAlert('Fichier trop volumineux (max 5MB)', 'error');
        return;
    }
    
    if (!allowedMimes.includes(file.type)) {
        showAlert('Format non supporté', 'error');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        const img = document.createElement('img');
        img.src = e.target.result;
        
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = `
            <img src="${e.target.result}" alt="Preview">
            <button type="button" onclick="removeImage()">Supprimer</button>
            <p>${(file.size / 1024).toFixed(2)} KB</p>
        `;
        
        currentImageFile = file;
    };
    
    reader.readAsDataURL(file);
}
```

---

## 🎨 Composants UI réutilisables

### Modal System

```javascript
// Ouvrir une modale
function openModal(type = 'article') {
    document.getElementById('modalTitle').textContent = 'Créer un nouvel article';
    document.getElementById('modal').classList.add('active');
    currentArticle = null; // Nouveau
}

// Fermer une modale
function closeModal() {
    document.getElementById('modal').classList.remove('active');
    document.getElementById('articleForm').reset();
    removeImage();
}

// Modifier (ouvrir avec données)
function editArticle(index) {
    currentArticle = index;
    const article = articles[index];
    
    document.getElementById('articleTitle').value = article.title;
    document.getElementById('articleContent').value = article.content;
    // ... remplir autres champs
    
    openModal();
}
```

### Alert System

```javascript
function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    document.body.appendChild(alert);
    
    // Animation d'apparition
    setTimeout(() => alert.classList.add('show'), 10);
    
    // Disparition auto après 3s
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}
```

### Navigation

```javascript
function switchSection(section) {
    // Cacher toutes les sections
    document.querySelectorAll('.section-content').forEach(el => {
        el.style.display = 'none';
    });
    
    // Afficher la sélectionnée
    document.getElementById(`${section}-section`).style.display = 'block';
    
    // Mettre à jour sidebar active
    document.querySelectorAll('.sidebar li').forEach(li => {
        li.classList.remove('active');
    });
    event.target.closest('li').classList.add('active');
}
```

---

## 🔌 Intégration API

### Appels API (Hostinger)

```javascript
// Configuration
const syncConfig = {
    endpoint: 'https://domain.com/admin/api/sync.php',
    uploadUrl: 'https://domain.com/admin/api/upload.php',
    apiKey: 'votre_cle'
};

// Sync (CRUD)
async function syncWithServer(type, operation, data) {
    const response = await fetch(syncConfig.endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Admin-Sync-Key': syncConfig.apiKey
        },
        body: JSON.stringify({
            type: type,        // 'article', 'ad', 'category'
            operation: operation, // 'create', 'update', 'delete'
            data: data
        })
    });
    
    return await response.json();
}

// Upload image
async function uploadImage(file) {
    const formData = new FormData();
    formData.append('file', file);
    
    const response = await fetch(syncConfig.uploadUrl, {
        method: 'POST',
        headers: {
            'X-Admin-Sync-Key': syncConfig.apiKey
        },
        body: formData
    });
    
    return await response.json();
}
```

---

## 📊 Structure localStorage

### Tailles typiques

```
ep_articles (100 articles) : ~200 KB
ep_ads (10 publicités)     : ~5 KB  
syncConfig                 : ~1 KB
────────────────────────────────
Total                      : ~206 KB / 5-10 MB disponible
```

### Gestion du cache

```javascript
function saveSyncConfig() {
    const config = {
        enabled: document.getElementById('syncEnabled').checked,
        endpoint: document.getElementById('syncEndpoint').value,
        uploadUrl: document.getElementById('uploadUrl').value,
        refreshUrl: document.getElementById('refreshUrl').value,
        apiKey: document.getElementById('syncApiKey').value,
        lastSync: new Date().toISOString()
    };
    
    localStorage.setItem('syncConfig', JSON.stringify(config));
    showAlert('Configuration sauvegardée !', 'success');
}

function clearCache() {
    if (confirm('Êtes-vous sûr ? Les données locales seront perdues !')) {
        localStorage.clear();
        location.reload();
    }
}
```

---

## 🐛 Débogage et logs

### Logs utiles

```javascript
// Vérifier l'état de localStorage
console.log('Articles:', JSON.parse(localStorage.getItem('ep_articles')));
console.log('Config:', JSON.parse(localStorage.getItem('syncConfig')));

// Tester sync
console.log('Testing sync endpoint...');
fetch('https://domain.com/admin/api/sync.php', {
    method: 'POST',
    headers: { 'X-Admin-Sync-Key': 'votre_cle' },
    body: JSON.stringify({ type: 'test' })
}).then(r => r.json()).then(console.log);

// Vérifier taille localStorage
let total = 0;
for (let key in localStorage) {
    total += localStorage[key].length;
}
console.log('Total size:', (total / 1024).toFixed(2), 'KB');
```

---

## 🔍 Points d'extension courants

### Ajouter un nouveau module (ex: Événements)

```javascript
// 1. Ajouter clé localStorage
const events = [];

// 2. Ajouter à sidebar
<li onclick="switchSection('events')">📅 Événements</li>

// 3. Ajouter section HTML
<div id="events-section" class="section-content" style="display: none;">
    <!-- Contenu -->
</div>

// 4. Implémenter CRUD
function saveEvent(event) {
    events.push({ /* données */ });
    localStorage.setItem('ep_events', JSON.stringify(events));
}

// 5. Créer formulaire modal
```

### Ajouter un champ à articles

```javascript
// 1. Ajouter dans formulaire HTML
<input type="date" id="articlePublishDate" placeholder="Date de publication">

// 2. Ajouter à l'objet article
const article = {
    ...autres,
    publishedAt: document.getElementById('articlePublishDate').value
};

// 3. Afficher dans la grille
<span class="date">${article.publishedAt}</span>
```

---

## 📈 Performance et optimisations

### Limitations actuelles

- localStorage : 5-10 MB max
- JSON.stringify/parse : lent avec gros volumes
- Pas de pagination : charge tout en mémoire

### Optimisations possibles

```javascript
// 1. Pagination (charger 10 articles à la fois)
function loadArticles(page = 0) {
    const pageSize = 10;
    const start = page * pageSize;
    return articles.slice(start, start + pageSize);
}

// 2. Compression
function compressArticles() {
    // Enlever champs inutiles avant export
}

// 3. Indexing
function createIndex() {
    const index = {};
    articles.forEach(a => {
        index[a.id] = a;
    });
}
```

---

**Guide technique complet de admin.html**  
Mise à jour : 2 février 2026

