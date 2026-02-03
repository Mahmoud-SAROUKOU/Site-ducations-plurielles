# 👥 SYSTÈME DE GESTION DES ADMINISTRATEURS

## 🎯 Vue d'ensemble

Vous avez maintenant un **système complet de gestion des administrateurs** dans votre espace admin.

### Architecture :
- **Vous** = Super Admin (accès direct, sans mot de passe)
- **Autres admins** = Email + Mot de passe auto-généré + Email de notification
- **Rôles disponibles** : Admin complet, Éditeur, Modérateur

---

## 📋 Comment ça marche

### 1️⃣ **Vous connecter (Super Admin)**

Vous êtes le **super-admin principal**. Vous pouvez :
- Accéder directement à `admin.html` sans mot de passe
- Ajouter/modifier/supprimer d'autres administrateurs
- Modifier votre mot de passe si vous le souhaitez

### 2️⃣ **Ajouter un nouvel administrateur**

**Option A** : Via le bouton rapide du dashboard
1. Cliquez sur **"Nouvel admin"** dans les Actions rapides
2. Ou allez à **Administrateurs** → **Ajouter un administrateur**

**Option B** : Via le formulaire complet
1. Allez à **Administrateurs** dans le menu
2. Cliquez sur **"Ajouter un administrateur"**

### 3️⃣ **Remplir le formulaire**

```
Nom complet : Jean Dupont
Email : jean@exemple.com
Mot de passe : [AUTO-GÉNÉRÉ] ← Cliquez "Régénérer" pour changer
Rôle : Administrateur complet
```

**⚠️ Important** : Le mot de passe est **auto-généré** pour sécurité. Vous pouvez le régénérer avec le bouton 🔄.

### 4️⃣ **Email automatique envoyé**

Quand vous ajoutez un admin, il reçoit un email avec :
- ✅ Son email de connexion
- ✅ Son mot de passe
- ✅ Le lien de connexion
- ✅ Instructions de première connexion

### 5️⃣ **Modifier un administrateur**

1. Allez à **Administrateurs**
2. Cliquez sur **"Modifier"** sur la fiche admin
3. Changez le nom/email/rôle
4. Cliquez **"Enregistrer"**

**Note** : Pour changer le mot de passe, supprimez et recréez l'admin.

### 6️⃣ **Supprimer un administrateur**

1. Allez à **Administrateurs**
2. Cliquez sur **"Supprimer"**
3. Confirmez la suppression

---

## 🔐 Sécurité

### Mot de passe auto-généré

La plateforme génère des **mots de passe sécurisés de 14 caractères** :
- Lettres majuscules et minuscules
- Chiffres
- Caractères spéciaux (!@#$%^&*)

**Exemple** : `K7#mP2$vN8@qL4s`

### Stockage

- Les mots de passe sont **hashés** (btoa actuellement, à remplacer par bcrypt en production)
- Stockés dans `localStorage` (admin.html local)
- À terme, à migrer vers MySQL sécurisé

### Super Admin

- **Pas de mot de passe** pour accès direct
- Accès illimité au tableau de bord
- Peut éditer tous les contenus

---

## 📊 Tableau de bord

Le dashboard affiche :
- **Nombre d'administrateurs** actuellement enregistrés
- **Statistiques** : Articles, Vidéos, Ressources, Publicités

### Exemple :
```
📊 Administrateurs: 3
   - 1 Super Admin (vous)
   - 2 Admins complets
   - 0 Éditeurs
```

---

## 🔄 Intégration avec Hostinger (Optionnel)

### Synchronisation des admins

À terme, vous pouvez synchroniser les administrateurs vers votre serveur Hostinger :

1. Endpoint API : `admin/api/sync.php`
2. Type : `'admin'` dans la requête POST
3. Opérations : `create`, `update`, `delete`

### Exemple de sync :

```javascript
// Créer un admin sur le serveur
syncToServer('admin', {
    name: 'Jean Dupont',
    email: 'jean@exemple.com',
    role: 'admin',
    password: 'K7#mP2$vN8@qL4s'
}, 'create');
```

---

## ✉️ Configuration email (Optionnel)

Le système envoie les emails via `admin/api/send-admin-email.php`.

### Pour activer les emails :

**Option 1** : Utiliser `mail()` (PHP natif)
- Déjà configuré ✅
- Fonctionne si votre serveur a un SMTP

**Option 2** : SMTP personnalisé (plus tard)
- À configurer dans le fichier PHP
- Utiliser une librairie comme PHPMailer

### Email envoyé :

```
De : admin@educationsplurielles.fr
Objet : "Accès administrateur Éducations Plurielles"
Contenu : HTML formaté avec identifiants + lien connexion
```

---

## 🛠️ Rôles et permissions (À implémenter)

### Rôles actuels :

| Rôle | Articles | Vidéos | Ressources | Publicités | Admins |
|------|----------|--------|-----------|-----------|--------|
| Super Admin | ✅ | ✅ | ✅ | ✅ | ✅ |
| Admin | ✅ | ✅ | ✅ | ✅ | ❌ |
| Éditeur | ✅ | ✅ | ✅ | ❌ | ❌ |
| Modérateur | ✅ | ✅ | ❌ | ❌ | ❌ |

**À venir** : Implémentation des restrictions par rôle

---

## 📱 Données stockées

### Local (localStorage)

**Clé** : `ep_admins`

**Exemple** :
```json
[
  {
    "id": 1,
    "name": "Administrateur Principal",
    "email": "admin@educationsplurielles.local",
    "role": "super-admin",
    "status": "active",
    "createdAt": "2026-02-02T10:30:00Z",
    "passwordHash": null
  },
  {
    "id": 1738503400000,
    "name": "Jean Dupont",
    "email": "jean@exemple.com",
    "role": "admin",
    "status": "active",
    "createdAt": "2026-02-02T15:45:00Z",
    "passwordHash": "Szc3tP9jW1sL5qM4vN8"
  }
]
```

---

## 🚀 Prochaines étapes

### Phase 2 (En cours)
- ✅ Créer section Administrateurs
- ✅ Ajouter/modifier/supprimer admins
- ✅ Générer mots de passe
- ⏳ Envoyer emails (await configuration SMTP)

### Phase 3 (À faire)
- 🔄 Intégrer système de login
- 🔄 Protéger pages par authentification
- 🔄 Implémenter les rôles/permissions
- 🔄 Synchroniser vers MySQL

### Phase 4 (Sécurité)
- 🔒 Remplacer btoa par bcrypt
- 🔒 Mettre en HTTPS
- 🔒 Ajouter rate limiting
- 🔒 Ajouter 2FA (optionnel)

---

## ❓ Questions fréquentes

### **Q : Où sont stockés les mots de passe ?**
A : Dans `localStorage` localement. À terme, dans MySQL avec hashage sécurisé.

### **Q : Je peux changer mon mot de passe (super-admin) ?**
A : Pas encore, c'est pour venir. Actuellement, vous avez accès sans password.

### **Q : Que faire si un admin oublie son mot de passe ?**
A : Supprimez son compte et recréez-le (il recevra un nouvel email).

### **Q : Les emails sont-ils envoyés pour de vrai ?**
A : Oui si SMTP est configuré. Sinon, un message "Email non envoyé" s'affiche.

### **Q : Je veux supprimer le super-admin ?**
A : C'est protégé (bouton Supprimer désactivé). Vous devez faire ça manuellement.

---

## 📞 Support

- **Documentation complète** : Voir `ADMIN-PANEL-GUIDE.md`
- **Téchnique** : Voir `ADMIN-PANEL-TECHNIQUE.md`
- **Dépannage** : Voir `ADMIN-PANEL-TROUBLESHOOT.md`

---

**🎉 Vous êtes prêt à gérer vos administrateurs !**

Cliquez sur **Administrateurs** dans le menu pour commencer.

