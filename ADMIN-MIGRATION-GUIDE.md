# 🔄 Migration guide - De l'ancien système au nouveau

## 📋 Différences principales

### Ancien système
- Sessions PHP basiques
- Authentification minimale
- Peu de sécurité CSRF
- Pas de logging d'audit
- Pas de gestion de rôles fine

### Nouveau système (Unifié)
- ✅ Authentification robuste avec bcrypt
- ✅ Sessions validées avec token + IP + user agent
- ✅ Protection CSRF globale
- ✅ Audit logging complet
- ✅ 4 niveaux de rôles (super_admin, admin, editor, viewer)
- ✅ Gestion des tentatives de connexion
- ✅ Récupération de compte sécurisée
- ✅ Soft delete des utilisateurs

---

## 🚀 Migration en 5 étapes

### Étape 1 : Sauvegarde
```bash
# Sauvegarder votre base de données existante
mysqldump -u root -p educations_plurielles > backup.sql
```

### Étape 2 : Installation du nouveau système
Accédez à : **http://localhost/admin/install-unified.php**

Cela va :
- Créer les nouvelles tables si nécessaire
- Améliorer les tables existantes
- Créer votre compte super admin

### Étape 3 : Migrer les utilisateurs existants (optionnel)

Si vous avez des utilisateurs existants, vous pouvez les importer :

```php
<?php
require_once __DIR__ . '/admin/auth.php';
$auth->requireRole('super_admin');

// Exemple : importer depuis une liste CSV
$file = fopen('users.csv', 'r');
while ($row = fgetcsv($file)) {
    $name = $row[0];
    $email = $row[1];
    $password = $row[2]; // Doit être au moins 8 caractères
    
    $result = $auth->register($name, $email, $password, 'admin');
    if ($result['success']) {
        echo "✓ Utilisateur créé: $email\n";
    } else {
        echo "✗ Erreur: {$result['error']}\n";
    }
}
fclose($file);
?>
```

### Étape 4 : Remplacer les anciens fichiers de login

**Ancien :**
- `/admin/login.php`
- `/admin/reset-request.php`
- `/admin/reset.php`

**Nouveau :**
- `/admin/login-unified.php`
- `/admin/reset-request-unified.php`
- `/admin/reset-unified.php`

Vous pouvez :
1. **Garder les deux** (coexistance)
2. **Remplacer les anciens** par redirection

```php
<?php
// Ancien login.php (à remplacer)
header('Location: login-unified.php');
exit;
?>
```

### Étape 5 : Mettre à jour vos pages protégées

**Ancien code :**
```php
<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: admin/login.php');
    exit;
}
// Votre page
?>
```

**Nouveau code :**
```php
<?php
require_once __DIR__ . '/admin/auth.php';
$auth->requireLogin();
// Votre page - plus de sécurité automatique !
?>
```

---

## 🔄 Coexistance (recommandée pendant la transition)

Vous pouvez faire coexister l'ancien et le nouveau système :

```
/admin/
├── login.php (ancien - redirige vers login-unified.php)
├── login-unified.php (nouveau)
├── reset-request.php (ancien)
├── reset-request-unified.php (nouveau)
└── auth.php (nouveau)
```

**Anciens fichiers pour redirection :**

```php
<?php
// /admin/login.php
header('Location: login-unified.php');
exit;
?>

<?php
// /admin/reset-request.php
header('Location: reset-request-unified.php');
exit;
?>

<?php
// /admin/reset.php
$token = $_GET['token'] ?? '';
header("Location: reset-unified.php?token=" . urlencode($token));
exit;
?>
```

---

## 📊 Comparaison des fonctionnalités

| Fonction | Ancien | Nouveau |
|----------|--------|---------|
| Connexion | ✓ | ✓✓ |
| Mot de passe hashé | ✓ | ✓✓ (bcrypt) |
| Réinitialisation | ✓ | ✓✓ |
| Gestion utilisateurs | ✓ | ✓✓ |
| Protection CSRF | ○ | ✓✓ |
| Tentatives lockout | ○ | ✓ |
| Rôles | ✗ | ✓✓ |
| Audit log | ✗ | ✓ |
| Sessions sécurisées | ○ | ✓ |
| Soft delete | ✗ | ✓ |
| 2FA preparé | ✗ | ✓ |

---

## 🧪 Testing

### Vérifier que tout marche

1. **Test du système:**
   ```
   http://localhost/admin/test-auth.php
   ```

2. **Connexion:**
   ```
   http://localhost/admin/login-unified.php
   ```

3. **Tableau de bord:**
   ```
   http://localhost/admin/dashboard-unified.php
   ```

4. **Gestion utilisateurs:**
   ```
   http://localhost/admin/users.php
   ```

### Tester la sécurité

- [ ] Essayez de vous connecter avec un mauvais mot de passe 5 fois → doit bloquer
- [ ] Attendez 15 minutes, doit déverrouiller
- [ ] Modifiez le formulaire de connexion → token CSRF doit refuser
- [ ] Tentez d'accéder directement à `/admin/users.php` sans connexion → redirection
- [ ] Vérifiez que les logs d'audit enregistrent vos actions

---

## 🗄️ Migration de la base de données

Si vous aviez des données existantes :

```sql
-- Vérifier les utilisateurs existants
SELECT * FROM users;

-- Vérifier les articles
SELECT * FROM articles;

-- Vérifier les annonces
SELECT * FROM ads;
```

Les nouvelles tables coexistent avec les anciennes. Vos données ne sont pas supprimées.

---

## 🔐 Changements de sécurité

1. **Mots de passe**
   - Ancien: Pouvait être faible
   - Nouveau: Minimum 8 caractères, bcrypt

2. **Sessions**
   - Ancien: Basique
   - Nouveau: Token + IP + user agent validé

3. **CSRF**
   - Ancien: Pas toujours
   - Nouveau: Systématique

4. **Audit**
   - Ancien: Rien
   - Nouveau: Tout enregistré

---

## 💡 Conseils

1. **Pendant la transition**, utilisez les deux systèmes en parallèle
2. **Testez** avant de faire des changements en production
3. **Formez** votre équipe à l'utilisation du nouveau système
4. **Sauvegardez** régulièrement votre base de données
5. **Changez** les mots de passe par défaut

---

## 📞 En cas de problème

1. **Vérifier les logs** : 
   ```
   http://localhost/admin/test-auth.php
   ```

2. **Vérifier `.env`** :
   - DB_HOST correct
   - DB_NAME correct
   - DB_USER correct
   - DB_PASS correct

3. **Vérifier les droits MySQL** :
   ```sql
   GRANT ALL ON educations_plurielles.* TO 'root'@'localhost';
   FLUSH PRIVILEGES;
   ```

4. **Consulter les logs serveur** :
   - `error.log` de PHP
   - Logs d'application

---

## ✅ Checklist de migration

- [ ] Sauvegarde de la BD effectuée
- [ ] `.env` configuré correctement
- [ ] `/admin/install-unified.php` exécuté
- [ ] Compte super admin créé
- [ ] Test du système réussi
- [ ] Anciennes pages redirigées
- [ ] Nouvelles pages testées
- [ ] Équipe formée
- [ ] Utilisateurs migrés (si nécessaire)
- [ ] Mots de passe changés

---

**Votre système est maintenant plus sécurisé et puissant ! 🚀**
