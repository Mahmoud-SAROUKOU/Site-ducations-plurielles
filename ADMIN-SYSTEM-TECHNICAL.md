# 🔧 SYSTÈME ADMINISTRATEURS - RÉFÉRENCE TECHNIQUE

## Architecture

### Composants

```
admin.html
  ├─ Section HTML (lignes 1005-1025)
  │  └─ <section data-section="administrateurs">
  │
  ├─ JavaScript (lignes 1620-1965)
  │  ├─ ADMIN_CONFIG
  │  ├─ loadAdmins()
  │  ├─ renderAdmins()
  │  ├─ saveAdmin()
  │  ├─ openCreateAdminModal()
  │  ├─ editAdmin()
  │  ├─ deleteAdmin()
  │  └─ generatePassword()
  │
  └─ API Backend
     └─ admin/api/send-admin-email.php (envoi email)
```

---

## Configuration

### ADMIN_CONFIG (admin.html)

```javascript
const ADMIN_CONFIG = {
    storageKey: 'ep_admins',                    // localStorage key
    mainAdminEmail: 'admin@educationsplurielles.local',
    mainAdminPassword: ''                       // Vide = pas de password
};
```

---

## Modèle de données

### Structure Admin

```javascript
{
    id: 1,                                      // Unique ID
    name: 'Jean Dupont',                        // Nom complet
    email: 'jean@exemple.com',                  // Email unique
    role: 'admin' | 'editor' | 'moderator',    // Rôle
    status: 'active' | 'inactive',              // Statut
    createdAt: '2026-02-02T10:30:00Z',         // ISO date
    passwordHash: 'btoa(password)'              // Hash (à améliorer)
}
```

### Super Admin

```javascript
{
    id: 1,
    name: 'Administrateur Principal',
    email: 'admin@educationsplurielles.local',
    role: 'super-admin',                       // Rôle spécial
    status: 'active',
    createdAt: '2026-02-02T...',
    passwordHash: null                          // Pas de password
}
```

---

## Fonctions principales

### loadAdmins()

```javascript
function loadAdmins() {
    // Charge depuis localStorage
    admins = JSON.parse(localStorage.getItem(ADMIN_CONFIG.storageKey) || '[]');
    
    // Crée super-admin s'il n'existe pas
    const superAdminExists = admins.some(a => a.role === 'super-admin');
    if (!superAdminExists && admins.length === 0) {
        admins.push({
            id: 1,
            name: 'Administrateur Principal',
            email: ADMIN_CONFIG.mainAdminEmail,
            role: 'super-admin',
            status: 'active',
            createdAt: new Date().toISOString(),
            passwordHash: null
        });
        saveAdmins();
    }
    
    renderAdmins();
}
```

### renderAdmins()

```javascript
function renderAdmins() {
    // Affiche grille d'admins (grid 300px min, auto-fill)
    // Pour chaque admin :
    //   - Carte avec nom, email, rôle, statut, date
    //   - Boutons Modifier/Supprimer (sauf super-admin)
    // Appelle HTML template avec map()
}
```

### generatePassword(length = 12)

```javascript
function generatePassword(length = 12) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    // Retour : chaîne aléatoire de `length` caractères
    // Défaut : 14 caractères pour la création
    // Contient : majuscules, minuscules, chiffres, spéciaux
}
```

### saveAdmin(event)

```javascript
function saveAdmin(event) {
    event.preventDefault();
    
    // 1. Récupère données du formulaire
    const name = document.getElementById('adminName').value.trim();
    const email = document.getElementById('adminEmail').value.trim();
    const password = document.getElementById('adminPassword').value;
    const role = document.getElementById('adminRole').value;
    
    // 2. Valide les données
    if (!name || !email || !password) throw error;
    
    // 3. Crée ou modifie l'objet
    const admin = {
        id: currentAdminIndex !== null ? admins[currentAdminIndex].id : Date.now(),
        name, email, role,
        status: 'active',
        createdAt: new Date().toISOString(),
        passwordHash: btoa(password)  // ⚠️ À remplacer par bcrypt
    };
    
    // 4. Si nouveau : appelle sendAdminEmail()
    if (currentAdminIndex === null) {
        admins.push(admin);
        sendAdminEmail(email, name, password);
    } else {
        admins[currentAdminIndex] = admin;
    }
    
    // 5. Sauvegarde et réaffiche
    saveAdmins();
    loadAdmins();
}
```

### sendAdminEmail(email, name, password)

```javascript
function sendAdminEmail(email, name, password) {
    // Appelle : POST /admin/api/send-admin-email.php
    // Headers :
    //   - Content-Type: application/json
    //   - X-Admin-Key: ADMIN_CONFIG.mainAdminEmail
    // Body :
    //   {
    //     email,
    //     name,
    //     password,
    //     loginUrl: window.location.origin + '/admin/login-unified.php'
    //   }
    
    // Réponse attendue : { success: true, message: '...' }
    // En cas d'erreur : affiche warning (ne bloque pas création)
}
```

### openCreateAdminModal()

```javascript
function openCreateAdminModal() {
    currentAdminIndex = null;
    const generatedPassword = generatePassword(14);
    
    // Crée modal HTML avec :
    // - Input: name, email, password (read-only, généré), role
    // - Bouton: Régénérer mot de passe
    // - Bouton: Ajouter l'administrateur / Annuler
    
    // Injecte dans body avec insertAdjacentHTML()
}
```

---

## Endpoint d'email

### POST /admin/api/send-admin-email.php

**Entrée** :
```json
{
    "email": "jean@exemple.com",
    "name": "Jean Dupont",
    "password": "K7#mP2$vN8@qL4s",
    "loginUrl": "https://site.com/admin/login-unified.php"
}
```

**Traitement** :
1. Valide paramètres (email, name, password)
2. Génère HTML email formaté
3. Appelle `mail()` (PHP natif)
4. Enregistre dans `admin/emails.log`
5. Retourne réponse JSON

**Réponse** :
```json
{
    "success": true,
    "message": "Email envoyé avec succès",
    "email": "jean@exemple.com",
    "timestamp": "2026-02-02T15:45:00+00:00"
}
```

**Erreurs** :
```json
{
    "success": false,
    "message": "Paramètres manquants / Email invalide / Erreur SMTP"
}
```

---

## Sécurité actuelle

### ✅ Implémenté

- Validation email (filter_var)
- Validation champs (trim, non-vides)
- Unicité email (check avant création)
- Génération aléatoire mot de passe
- localStorage key isolée
- Super-admin non modifiable

### ⚠️ À améliorer

| Aspect | Actuel | À faire |
|--------|--------|---------|
| Hash password | btoa() | bcrypt (PHP) |
| Transport | localStorage | MySQL sécurisé |
| Email | mail() | SMTP + TLS |
| Auth | Aucune | Session + JWT |
| Permissions | Non implémentées | Rôles + ACL |
| HTTPS | Non | Recommandé |

---

## Flux de création d'admin

### Diagramme

```
User clique "Ajouter admin"
    ↓
openCreateAdminModal() crée formulaire
    ↓
User remplit : name, email, role
    ↓
generatePassword(14) crée mot de passe
    ↓
User peut cliquer "Régénérer" → nouveau password
    ↓
User clique "Ajouter l'administrateur"
    ↓
saveAdmin(event) valide données
    ↓
Crée objet admin avec passwordHash = btoa(password)
    ↓
admins.push(admin)
    ↓
sendAdminEmail(email, name, password)
    ├─ POST /admin/api/send-admin-email.php
    ├─ Envoie mail avec identifiants
    └─ Log dans admin/emails.log
    ↓
saveAdmins() → localStorage.setItem('ep_admins', JSON.stringify(admins))
    ↓
loadAdmins() → renderAdmins()
    ↓
Dashboard affiche nouvel admin
    ↓
✅ Fait !
```

---

## Rôles et permissions

### Rôles implémentés

```javascript
const ROLES = {
    'super-admin': { label: 'Super Admin', canManageAdmins: true },
    'admin': { label: 'Administrateur', canManageAdmins: false },
    'editor': { label: 'Éditeur', canManageAdmins: false },
    'moderator': { label: 'Modérateur', canManageAdmins: false }
};
```

### À implémenter

```javascript
// Exemple de middleware (future)
function checkPermission(admin, action) {
    const permissions = {
        'super-admin': ['create_article', 'edit_article', 'delete_article', 'manage_admins'],
        'admin': ['create_article', 'edit_article', 'delete_article'],
        'editor': ['create_article', 'edit_article'],
        'moderator': ['view_article']
    };
    
    return permissions[admin.role]?.includes(action) || false;
}
```

---

## Intégration localStorage

### Clé

```javascript
KEY = 'ep_admins'
```

### Lecture

```javascript
const admins = JSON.parse(localStorage.getItem('ep_admins') || '[]');
```

### Écriture

```javascript
localStorage.setItem('ep_admins', JSON.stringify(admins));
```

### Effacement (si besoin reset)

```javascript
localStorage.removeItem('ep_admins');
```

---

## Tests unitaires (recommandés)

### À tester

```javascript
// 1. Génération password
assert(generatePassword(14).length === 14);
assert(/[A-Z]/.test(generatePassword()));  // Contient majuscules
assert(/[0-9]/.test(generatePassword()));  // Contient chiffres
assert(/[!@#$%^&*]/.test(generatePassword())); // Contient spéciaux

// 2. Création admin
const admin = { name: 'Test', email: 'test@test.com', role: 'admin' };
assert(validateAdmin(admin) === true);

// 3. Email unique
assert(checkEmailUnique('new@email.com') === true);
assert(checkEmailUnique('existing@email.com') === false);

// 4. localStorage
localStorage.setItem('ep_admins', JSON.stringify([admin]));
const loaded = JSON.parse(localStorage.getItem('ep_admins'));
assert(loaded.length === 1);
assert(loaded[0].email === 'test@test.com');
```

---

## Dépannage

### Problème : Super admin n'apparaît pas

**Solution** :
1. Ouvrir console (F12)
2. Exécuter `localStorage.removeItem('ep_admins')`
3. Recharger page (F5)
4. Super admin auto-créé

### Problème : Email non envoyé

**Solution** :
1. Vérifier `admin/api/send-admin-email.php` existe
2. Vérifier logs : `admin/emails.log`
3. Vérifier SMTP configuré (mail() PHP)
4. En local : vérifier `php.ini` (sendmail_path)

### Problème : Mot de passe mal généré

**Solution** :
1. Cliquer bouton "Régénérer" 🔄
2. Vérifier console : pas d'erreur JS
3. Vérifier longueur = 14 caractères

---

## Améliorations futures

### Phase 2

```javascript
// Authentification
function loginAdmin(email, password) {
    const admin = admins.find(a => a.email === email);
    if (admin && admin.passwordHash === btoa(password)) {
        // Créer session
        sessionStorage.setItem('currentAdmin', JSON.stringify(admin));
        return true;
    }
    return false;
}

// Protection page
function requireAuth() {
    const admin = JSON.parse(sessionStorage.getItem('currentAdmin') || 'null');
    if (!admin) {
        window.location.href = 'login.html';
    }
    return admin;
}
```

### Phase 3

```javascript
// Permissions
const currentAdmin = requireAuth();

// Vérifier accès
if (!hasPermission(currentAdmin.role, 'manage_admins')) {
    showAlert('Accès refusé', 'error');
    return;
}
```

### Phase 4

```javascript
// Sync MySQL
function syncToServer(type, data, operation) {
    if (type === 'admin') {
        fetch('/admin/api/sync.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Admin-Sync-Key': SYNC_CONFIG.apiKey
            },
            body: JSON.stringify({
                type: 'admin',
                operation: operation,  // create/update/delete
                data: data
            })
        });
    }
}
```

---

## Références fichiers

| Fichier | Rôle | Lignes |
|---------|------|--------|
| admin.html | Main | 1-1965 |
| admin.html (nav) | Section admin nav | 817 |
| admin.html (HTML) | Section admin content | 1005-1025 |
| admin.html (JS) | Toutes fonctions | 1620-1965 |
| admin/api/send-admin-email.php | Email backend | 1-150 |

---

## Variables globales

```javascript
let admins = [];                    // Array des admins
let currentAdminIndex = null;       // Index en édition
const ADMIN_CONFIG = {...};         // Config
```

---

## Documentation recommandée

- **User Guide** : `ADMIN-SYSTEM-GUIDE.md`
- **Quick Start** : `ADMIN-DEMARRAGE-RAPIDE.md`
- **Admin Guide** : `ADMIN-PANEL-GUIDE.md`
- **Troubleshoot** : `ADMIN-PANEL-TROUBLESHOOT.md`

---

**Dernière mise à jour** : 2 février 2026 ✨

