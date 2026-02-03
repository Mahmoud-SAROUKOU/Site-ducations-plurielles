# 🎯 BASE ADMIN NOUVELLE & PROPRE

> **Système d'administration simplifié et facile d'utilisation**  
> **Version 2.0 - Janvier 2026**

---

## 📦 STRUCTURE NOUVELLE (Minimaliste)

```
admin/
├── config.php           ← Configuration DB
├── db.php               ← Classe Database (initialisation + gestion)
├── auth.php             ← Classe Auth (connexion, session, admins)
├── index.php            ← Redirection auto (login/dashboard)
├── install.php          ← Installation initialelle
├── login.php            ← Page de connexion
├── logout.php           ← Déconnexion
├── dashboard.php        ← Page d'accueil admin
├── admins.php           ← Gestion des administrateurs
└── api/
    ├── stats.php        ← Stats (JSON)
    └── admins-list.php  ← Liste admins (JSON)
```

**C'est tout !** Pas de fichiers inutiles, pas de doublons. ✨

---

## 🚀 DÉMARRAGE RAPIDE

### 1️⃣ Installation (première fois)
```
http://localhost/admin/install.php
```
- Initialise la base de données
- Crée le premier super admin
- C'est prêt ! 

### 2️⃣ Connexion
```
http://localhost/admin/login.php
```
- Email + mot de passe
- Redirection automatique au dashboard

### 3️⃣ Dashboard
```
http://localhost/admin/dashboard.php
```
- Vue d'ensemble
- Gestion des administrateurs
- Statistiques

### 4️⃣ Gérer les Administrateurs
```
http://localhost/admin/admins.php
```
- Ajouter des admins
- Voir la liste complète
- Supprimer des admins (super_admin seulement)

---

## 🔐 SYSTÈME D'AUTHENTIFICATION

### Classe `Auth` (admin/auth.php)

**Méthodes :**
```php
$auth = new Auth();

// Connexion
$auth->login($email, $password);

// Créer un admin
$auth->createAdmin($nom, $email, $password, $role);

// Vérifier la connexion
if ($auth->isConnected()) { ... }

// Obtenir l'admin connecté
$admin = $auth->getAdmin();

// Forcer connexion ou redirection
$auth->require();

// Déconnexion
$auth->logout();
```

### Sécurité
- ✅ **Bcrypt** pour les mots de passe
- ✅ **Sessions sécurisées** avec token
- ✅ **Logs d'audit** des actions
- ✅ **Protection IP** et user agent
- ✅ **Validation email**

### Rôles
- **super_admin** : Accès total (créer/supprimer admins)
- **admin** : Accès standard (gestion contenu)

---

## 🗄️ BASE DE DONNÉES

### Tables créées automatiquement

#### `admins`
```sql
id              INT PRIMARY KEY
nom             VARCHAR(120)
email           VARCHAR(190) UNIQUE
password_hash   VARCHAR(255)
role            ENUM('super_admin', 'admin')
actif           TINYINT (0=inactif, 1=actif)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### `sessions`
```sql
id              INT PRIMARY KEY
admin_id        INT FOREIGN KEY
token           VARCHAR(255) UNIQUE
ip_address      VARCHAR(45)
user_agent      VARCHAR(255)
expires_at      DATETIME
created_at      TIMESTAMP
```

#### `logs`
```sql
id              INT PRIMARY KEY
admin_id        INT FOREIGN KEY
action          VARCHAR(50)
details         TEXT
created_at      TIMESTAMP
```

---

## 📚 FICHIERS & LEUR RÔLE

### Core System

**[config.php](config.php)**
- Charge les variables d'environnement (.env)
- Définit les constantes (DB, APP, MAIL)
- Singleton PDO pour la connexion

**[db.php](db.php)**
- Classe `Database`
- Méthode `connect()` : Connexion DB singleton
- Méthode `init()` : Création des tables

**[auth.php](auth.php)**
- Classe `Auth` - Gestion complète de l'authentification
- Sessions sécurisées avec token
- Création/gestion des admins
- Logs d'audit

### Pages Web

**[install.php](install.php)**
- Initialisation complète du système
- Création du premier super admin
- UI simple et intuitive
- Accessible au démarrage

**[login.php](login.php)**
- Page de connexion admin
- Email + mot de passe
- Validation sécurisée
- Redirection automatique

**[dashboard.php](dashboard.php)**
- Page d'accueil (require connexion)
- Statistiques rapides
- Liste des admins
- Actions principales

**[admins.php](admins.php)**
- Gestion complète des administrateurs
- Formulaire création nouvel admin
- Tableau de tous les admins
- Actions (delete pour super_admin)

**[logout.php](logout.php)**
- Déconnexion + redirection login

**[index.php](index.php)**
- Redirection intelligente
- Si connecté → dashboard
- Si non connecté → login

### API JSON

**[api/stats.php](api/stats.php)**
- Retourne les stats en JSON
- Nombre d'admins, articles, ads, logs
- Utilisé par le dashboard

**[api/admins-list.php](api/admins-list.php)**
- Retourne la liste des admins en JSON
- Utilisé pour remplir le tableau du dashboard

---

## 🎨 DESIGN

### Couleurs
- **Gradient principal** : `#667eea` → `#764ba2` (violet moderne)
- **Texte** : `#333` (noir très foncé)
- **Arrière-plan** : `#f5f7fa` (gris très clair)

### Composants UI
- 🔘 **Boutons** : Gradient avec animation hover
- 📋 **Cartes** : Shadow, hover lift effect
- 📊 **Tableaux** : Striped, hover highlight
- 📝 **Formulaires** : Focus state avec couleur gradient

### Responsive
- Mobile-first design
- Grid layout auto-fit
- Padding adapté aux écrans petits

---

## 📝 EXEMPLES D'UTILISATION

### Utiliser Auth dans vos pages
```php
<?php
require_once __DIR__ . '/auth.php';

$auth = new Auth();
$auth->require(); // Force connexion

$admin = $auth->getAdmin();
echo "Bienvenue " . htmlspecialchars($admin['nom']);
?>
```

### Créer un nouvel admin par code
```php
$auth = new Auth();
$result = $auth->createAdmin(
    'Sophie Martin',
    'sophie@example.com',
    'SecurePassword123',
    'admin'
);

if ($result['success']) {
    echo "Admin créé : ID " . $result['id'];
} else {
    echo "Erreur : " . $result['msg'];
}
```

### Accéder à la base de données
```php
$pdo = Database::connect();

$stmt = $pdo->query("SELECT * FROM admins");
$admins = $stmt->fetchAll();
```

---

## 🔧 CONFIGURATION (.env)

```ini
APP_URL=http://localhost
APP_NAME=Admin

DB_HOST=localhost
DB_NAME=educations_plurielles
DB_USER=root
DB_PASS=

MAIL_FROM=admin@example.com
MAIL_FROM_NAME=Admin
```

---

## ✅ POINTS FORTS

### Simplicité
- ✅ Code minimaliste et lisible
- ✅ Pas de frameworks lourd
- ✅ Facile à maintenir et modifier

### Sécurité
- ✅ Authentification robuste
- ✅ Sessions sécurisées
- ✅ Validation inputs
- ✅ Logs d'audit

### Praticité
- ✅ Installation en 3 clics
- ✅ UI moderne et responsive
- ✅ Gestion admins intégrée
- ✅ API JSON disponible

### Performance
- ✅ Pas de requêtes inutiles
- ✅ CSS inline optimisé
- ✅ Sessions allégées

---

## 📞 SUPPORT & TROUBLESHOOTING

### Erreur "Erreur de connexion DB"
- Vérifier que MySQL est lancé
- Vérifier les identifiants DB dans `.env`
- Vérifier que le user DB a les droits

### "Session invalide" / Déconnexion fréquente
- Vérifier l'heure du serveur
- Vérifier que les tables `sessions` existent
- Vérifier les logs en DB table `logs`

### Oublier mot de passe
- Actuellement pas de système reset email
- Solution temporaire : Supprimer l'admin et en recréer un
- Futur : Ajouter reset par email

---

## 🚀 PROCHAINES ÉTAPES

Vous pouvez ajouter :
1. **Reset mot de passe** par email avec token
2. **Gestion articles** et **publicités**
3. **Two-factor authentication** (2FA)
4. **Export logs** en CSV/PDF
5. **Dark mode** UI
6. **Intégration mail** (PHPMailer, etc.)

---

## 📋 CHECKLIST D'UTILISATION

- [ ] Accéder à `admin/install.php`
- [ ] Créer le premier super admin
- [ ] Se connecter avec ces identifiants
- [ ] Voir le dashboard
- [ ] Créer un 2e admin
- [ ] Vérifier la liste des admins
- [ ] Tester la déconnexion
- [ ] Tester la reconnexion

✨ **Votre système admin est prêt !**

---

*Créé janvier 2026 - Éducations Plurielles*
