# 🚀 GUIDE RAPIDE - Mise à jour du site

## Workflow simplifié

### 1️⃣ Connexion à l'admin
- Ouvrez `admin.html` dans votre navigateur
- Connectez-vous avec vos identifiants

### 2️⃣ Créer/Modifier le contenu
- **Articles** : Créez ou modifiez vos articles
- **Publicités** : Gérez les messages défilants

### 3️⃣ Exporter les données
- Allez dans le **Tableau de bord**
- Cliquez sur **"Générer site-content.json"**
- Le fichier se télécharge automatiquement

### 4️⃣ Mettre à jour le site
- Remplacez le fichier : `data/site-content.json`
- Si FTP : uploadez le nouveau fichier
- Si Git :
  ```bash
  git add data/site-content.json
  git commit -m "Mise à jour du contenu"
  git push
  ```

### 5️⃣ Vérification
- Actualisez le site public (F5)
- Vérifiez que les changements apparaissent

---

## ⚡ Commandes Git rapides

```bash
# Mise à jour du contenu
git add data/site-content.json
git commit -m "Ajout de nouveaux articles"
git push

# Mise à jour complète
git add .
git commit -m "Mise à jour du site et du contenu"
git push
```

---

## 📝 Checklist avant publication

- [ ] Les articles sont bien marqués "Publié"
- [ ] Les publicités sont "Actives"
- [ ] Le fichier JSON a été généré
- [ ] Le fichier a été uploadé/commité
- [ ] Le site a été actualisé et vérifié

---

## 🔐 Sécurité - Points clés

✅ L'URL admin.html ne doit PAS être partagée publiquement
✅ Changez les mots de passe par défaut
✅ Déconnectez-vous après chaque session
✅ Exportez régulièrement le JSON (sauvegarde)
✅ Pour production : protégez admin.html avec .htaccess

---

## 📞 Aide rapide

**Problème** : Les modifications n'apparaissent pas
**Solution** : 
1. Avez-vous généré le JSON ?
2. Avez-vous remplacé le fichier sur le serveur ?
3. Avez-vous actualisé le site (Ctrl+Shift+R) ?

**Problème** : Erreur "Failed to fetch"
**Solution** : 
1. Vérifiez que `data/site-content.json` existe
2. En local, utilisez un serveur web (Live Server, etc.)
3. Vérifiez les permissions du fichier

---

*Pour plus de détails, consultez DOCUMENTATION-ADMIN.md*
