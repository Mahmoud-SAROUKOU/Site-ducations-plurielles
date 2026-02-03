# 📚 Documentation - Système Admin Unifié

## Vue d'ensemble

Ce système admin unifié fournit une base d'authentification complète et sécurisée pour gérer les administrateurs de votre site.

## 🎯 Fonctionnalités principales

### 1. **Authentification robuste**
- Connexion avec email/mot de passe
- Hashage sécurisé avec bcrypt
- Sessions avec validation
- Protection contre les attaques par force brute
- Cookies "Se souvenir de moi" (30 jours)

### 2. **Gestion des utilisateurs**
- Création de comptes administrateurs
- 4 niveaux de rôles : Super Admin, Admin, Éditeur, Lecteur
- Statut : Actif, Inactif, Suspendu
- Suppression douce (soft delete)

### 3. **Sécurité**
- Token CSRF sur tous les formulaires
- Tokens de réinitialisation de mot de passe
- Audit logs pour toutes les actions
- Validation d'IP et user agent
- Lockout après 5 tentatives échouées (15 min)

### 4. **Récupération de compte**
- Demande de réinitialisation par email
- Tokens avec expiration (1 heure)
- Confirmation de mot de passe

## 📁 Structure des fichiers

```
admin/
├── config.php                 # Configuration de base de données
├── auth.php                  # Classe d'authentification
├── db-init.php               # Initialisation de la BD
├── install-unified.php       # Script d'installation
├── login-unified.php         # Page de connexion
├── reset-request-unified.php # Demande réinitialisation
├── reset-unified.php         # Réinitialisation mot de passe
├── users.php                 # Gestion des utilisateurs
└── logout.php                # Déconnexion
```

## 🚀 Installation

### Étape 1 : Configuration

Créez ou modifiez le fichier `.env` à la racine du projet :

```
APP_URL=http://localhost
APP_NAME=Educations Plurielles
DB_HOST=localhost
DB_NAME=educations_plurielles
DB_USER=root
DB_PASS=
MAIL_FROM=admin@exemple.com
MAIL_FROM_NAME=Administrateur
```

### Étape 2 : Installation de la BD

Accédez à : `http://localhost/admin/install-unified.php`

Remplissez le formulaire avec les informations du premier administrateur :
- Nom complet
- Email
- Mot de passe (min 8 caractères)

Le script va :
1. Créer la base de données
2. Créer les tables
3. Créer le compte super administrateur

### Étape 3 : Première connexion

Allez à : `http://localhost/admin/login-unified.php`

Connectez-vous avec l'email et mot de passe créés lors de l'installation.

## 🔐 Rôles et permissions

### Super Admin
- Accès à tout
- Gestion des utilisateurs
- Lecture de l'audit log

### Admin
- Gestion des articles
- Gestion des annonces
- Gestion des utilisateurs (limité)

### Éditeur
- Création/modification d'articles
- Lecture des annonces

### Lecteur
- Lecture seule

## 💻 Utilisation

### Connexion

```php
<?php
require_once __DIR__ . '/admin/auth.php';

// L'authentification est automatiquement initialisée
if (!$auth->isLoggedIn()) {
    header('Location: login-unified.php');
    exit;
}

$user = $auth->getCurrentUser();
echo "Bonjour " . $user['name'];
?>
```

### Exiger une connexion

```php
<?php
$auth->requireLogin();
// Code protégé
?>
```

### Exiger un rôle spécifique

```php
<?php
$auth->requireRole('admin'); // ou 'super_admin', 'editor', 'viewer'
// Code protégé
?>
```

### Déconnexion

```php
<?php
$auth->logout();
header('Location: login-unified.php');
?>
```

### Changer de mot de passe

```php
<?php
$auth->requireLogin();
$result = $auth->changePassword(
    $_SESSION['user_id'],
    $_POST['old_password'],
    $_POST['new_password']
);
if ($result['success']) {
    // Succès
}
?>
```

## 🗄️ Schéma de base de données

### Table `users`
```
id              INT UNSIGNED PRIMARY KEY
name            VARCHAR(120)
email           VARCHAR(190) UNIQUE
password_hash   VARCHAR(255)
role            ENUM('super_admin','admin','editor','viewer')
status          ENUM('active','inactive','suspended')
last_login      DATETIME
last_ip         VARCHAR(45)
created_at      DATETIME
updated_at      DATETIME
deleted_at      DATETIME (soft delete)
```

### Table `password_resets`
```
id              INT UNSIGNED PRIMARY KEY
user_id         INT UNSIGNED (FK users)
token_hash      VARCHAR(255) UNIQUE
expires_at      DATETIME
used_at         DATETIME
ip_address      VARCHAR(45)
created_at      DATETIME
```

### Table `audit_logs`
```
id              INT UNSIGNED PRIMARY KEY
user_id         INT UNSIGNED (FK users)
action          VARCHAR(100)
entity_type     VARCHAR(50)
entity_id       INT UNSIGNED
old_values      JSON
new_values      JSON
ip_address      VARCHAR(45)
user_agent      VARCHAR(500)
created_at      DATETIME
```

### Table `admin_sessions`
```
id              INT UNSIGNED PRIMARY KEY
user_id         INT UNSIGNED (FK users)
session_token   VARCHAR(255) UNIQUE
ip_address      VARCHAR(45)
user_agent      VARCHAR(500)
expires_at      DATETIME
last_activity   DATETIME
created_at      DATETIME
```

## 🛡️ Mesures de sécurité

1. **Mot de passe**
   - Minimum 8 caractères
   - Hashé avec bcrypt (PASSWORD_BCRYPT)
   - Jamais stocké en clair

2. **Sessions**
   - Token CSRF sur les formulaires
   - Validation de user agent
   - Validation d'IP
   - Timeout de 1 heure

3. **Authentification**
   - Lockout après 5 tentatives (15 minutes)
   - Validation du compte (statut)
   - Email de confirmation de réinitialisation

4. **Audit**
   - Logging de toutes les actions
   - Enregistrement d'IP et user agent
   - Avant/après pour les modifications

## 🔧 Maintenance

### Nettoyer les sessions expirées

```php
<?php
$stmt = db()->prepare(
    'DELETE FROM admin_sessions WHERE expires_at < NOW()'
);
$stmt->execute();
?>
```

### Nettoyer les tokens expirés

```php
<?php
$stmt = db()->prepare(
    'DELETE FROM password_resets WHERE expires_at < NOW()'
);
$stmt->execute();
?>
```

### Récupérer l'audit log

```php
<?php
$stmt = db()->prepare(
    'SELECT * FROM audit_logs 
     WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
     ORDER BY created_at DESC'
);
$stmt->execute();
$logs = $stmt->fetchAll();
?>
```

## 📞 Support

Pour toute question ou problème, consultez les fichiers de code commentés ou les logs du serveur.
