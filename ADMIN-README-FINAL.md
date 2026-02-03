# Admin Dashboard - Point d'Entrée Unique

## Configuration Actuelle

**Point d'accès:** `admin.php`

### Flux de navigation
```
admin.php (Point d'entrée)
    ↓
Vérification authentification
    ↓
Si non connecté: → admin/login.php (connexion)
Si connecté: → Tableau de bord dynamique
```

### URLs disponibles
- `/admin.php` - Point d'entrée principal
- `/admin/login.php` - Formulaire de connexion
- `/admin/articles.php` - Gestion des articles
- `/admin/ads.php` - Gestion des publicités
- `/admin/reset-request.php` - Réinitialisation mot de passe

### Base de données
- **Fichier:** `admin/schema.sql`
- **À importer une seule fois dans MySQL**
- **Admin auto-créé** à la première visite si BD vide

### Identifiants par défaut
- **Email:** admin@educationsplurielles.fr
- **Mot de passe:** Auto-généré et affiché à la première connexion
- **Stockage:** .admin-credentials.txt (à supprimer après)

## Installation rapide

1. **Importer la base de données**
   ```bash
   mysql -u root < admin/schema.sql
   ```

2. **Accéder à l'admin**
   ```
   http://localhost/admin.php
   ```

3. **Connexion automatique**
   - L'admin est créé automatiquement
   - Les identifiants sont affichés

## Fichiers archivés
- `admin/admin.php.backup` - Ancien tableau de bord (backup)
- Tous les fichiers de documentation ont été supprimés

## Configuration

Fichiers de configuration à connaître:
- `admin/config.php` - Configuration BD et mail
- `admin/functions.php` - Fonctions utilitaires
- `.env` - Variables d'environnement

---

**Version:** 1.0 | **Status:** Production Ready
