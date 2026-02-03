# 🚀 GUIDE RAPIDE - GIT & GITHUB

## ✅ Étape 1 : Installation de Git (EN COURS)

Le script **INSTALLER-GIT.ps1** est en train de :
- ✅ Télécharger Git pour Windows (v2.43.0)
- ⏳ Installer Git automatiquement
- ⏳ Configurer votre nom et email

**Patientez 2-3 minutes...**

---

## 📋 Étape 2 : Initialisation du projet

Une fois l'installation terminée :

1. **Fermez le terminal actuel**
2. **Ouvrez un NOUVEAU terminal PowerShell**
3. **Vérifiez Git** :
   ```powershell
   git --version
   ```
   Vous devez voir : `git version 2.43.0.windows.1`

4. **Lancez l'initialisation** :
   ```powershell
   cd "d:\Site Educations Plurielles"
   .\GIT-INIT-PROJET.bat
   ```

Cela va :
- ✅ Créer le dépôt Git local
- ✅ Ajouter tous les fichiers (sauf ceux dans .gitignore)
- ✅ Créer le premier commit
- ✅ Préparer la connexion GitHub

---

## 🌐 Étape 3 : Créer le dépôt GitHub

1. **Allez sur** : https://github.com/new

2. **Remplissez** :
   - **Nom** : `educations-plurielles`
   - **Description** : `Site Éducations Plurielles avec admin v1.1`
   - **Visibilité** : ⚠️ **Private** (recommandé - contient système admin)
   - ❌ Ne PAS cocher "Initialize with README"

3. **Cliquez** : "Create repository"

4. **Copiez l'URL** affichée (format : `https://github.com/USERNAME/educations-plurielles.git`)

---

## 🔗 Étape 4 : Lier local → GitHub

Dans votre terminal, exécutez (remplacez USERNAME par votre nom GitHub) :

```powershell
git remote add origin https://github.com/USERNAME/educations-plurielles.git
git branch -M main
git push -u origin main
```

**Entrez vos identifiants GitHub** si demandé.

✅ **Tous vos fichiers sont maintenant sur GitHub !**

---

## 📊 Étape 5 : Vérifier la synchronisation Hostinger

1. **Ouvrez** : `admin.html` dans votre navigateur

2. **Allez dans** : Paramètres ⚙️

3. **Section** : "Synchronisation Hostinger"

4. **Remplissez** :
   - **URL de synchronisation** : `https://votre-domaine.com/admin/api/sync.php`
   - **URL d'upload** : `https://votre-domaine.com/admin/api/upload.php`
   - **URL de rafraîchissement** : `https://votre-domaine.com/?refresh=1`
   - **Clé API** : Votre clé sécurisée (définie dans les fichiers PHP)

5. **Cochez** : ☑️ Synchroniser en ligne (Hostinger)

6. **Cliquez** : 💾 Enregistrer la synchro

7. **Testez** : Créez un article test et vérifiez qu'il apparaît sur Hostinger

---

## 🎯 Utilisation quotidienne de Git

### Sauvegarder vos modifications :
```powershell
git add .
git commit -m "Description de vos modifications"
git push
```

### Récupérer les modifications :
```powershell
git pull
```

### Voir le statut :
```powershell
git status
```

### Voir l'historique :
```powershell
git log --oneline
```

---

## 🔐 Fichiers protégés (.gitignore)

Ces fichiers NE SERONT JAMAIS sur GitHub (c'est normal) :
- ✅ `.env` (variables d'environnement)
- ✅ `.admin-credentials.txt` (identifiants)
- ✅ `admin/database.sqlite` (base de données)
- ✅ `uploads/images/*` (fichiers uploadés)
- ✅ `vendor/` (dépendances)
- ✅ `*.log` (logs)

---

## 📞 En cas de problème

### "git n'est pas reconnu"
→ Redémarrez Windows complètement

### "Permission denied"
→ Configurez SSH : https://docs.github.com/fr/authentication/connecting-to-github-with-ssh

### "Failed to push"
→ Vérifiez votre connexion internet et vos identifiants GitHub

### Hostinger sync ne fonctionne pas
→ Vérifiez test-configuration.html

---

## 📝 Notes importantes

✅ **Local** : Vos modifications sont sur votre PC  
✅ **GitHub** : Sauvegarde en ligne de votre code  
✅ **Hostinger** : Site web public accessible à tous

**Workflow** :
```
Modification locale → Commit Git → Push GitHub → Sync Hostinger
```

---

## 🎉 Félicitations !

Votre projet est maintenant :
- ✅ Versionné avec Git
- ✅ Sauvegardé sur GitHub
- ✅ Synchronisé avec Hostinger
- ✅ Prêt pour le travail collaboratif !

**Prochaine étape recommandée** :  
Testez la synchronisation Hostinger dans admin.html

---

**Date de création** : 3 février 2026  
**Version Git installée** : 2.43.0  
**Projet** : Éducations Plurielles v1.1
