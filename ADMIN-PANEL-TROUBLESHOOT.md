# 🔧 Dépannage Admin Panel - Guide rapide

## 📋 Diagnostic d'urgence

### Le panel admin ne charge pas

**Symptôme** : Page blanche ou erreurs 404

**Diagnostic** :

1. **Vérifier le chemin**
   ```
   ✓ Correct  : http://localhost:8000/admin.html
   ✗ Incorrect: http://localhost:8000/admin/admin.html
   ```

2. **Vérifier le serveur est lancé**
   ```powershell
   # Windows
   .\CONNEXION-RAPIDE.bat
   
   # Si port 8000 est occupé
   php -S localhost:9000
   ```

3. **Vérifier la console navigateur** (F12 → Console)
   - Erreurs JavaScript ?
   - Fichiers manquants (404) ?

**Solution** :
```
1. Assurez-vous que admin.html existe à la racine
2. Redémarrez le serveur
3. Videz le cache (Ctrl+Shift+Del)
4. Rechargez la page
```

---

## 📊 Articles ne s'affichent pas

### Aucun article visible dans la grille

**Causes possibles** :

| Symptôme | Cause | Solution |
|----------|-------|----------|
| Grille vide | localStorage vide | Créer un article test |
| "undefined" | JSON corrompu | Vider le cache → réimporter |
| Pas de pagination | Données mal formées | Exporter/importer JSON |

**Vérifier localStorage** (F12 → Application) :

```javascript
// Console
localStorage.getItem('ep_articles')
// Devrait afficher: 
// [{"id":1,"title":"...",etc}]
```

### Créer un article test

**Procédure** :
```
1. Cliquez "Créer un nouvel article"
2. Remplissez le titre : "Test"
3. Remplissez le contenu : "Test"
4. Catégorie : "Parentalité"
5. Cliquez "Enregistrer"
6. L'article doit apparaître dans la grille ✓
```

**Si impossible** :
```javascript
// Console - créer manuellement
const articles = [{
  id: 1,
  title: 'Test',
  content: 'Contenu test',
  category: 'parentalite',
  tags: [],
  image: '',
  author: 'Admin',
  createdAt: new Date().toISOString()
}];
localStorage.setItem('ep_articles', JSON.stringify(articles));
location.reload();
```

---

## 🖼️ Images ne s'affichent pas

### Image upload échoue

**Symptôme** : Bouton "Upload" non réactif ou "Erreur lors du chargement du fichier"

**Vérifications** :

1. **Format du fichier**
   ```
   ✓ JPEG, PNG, WebP, GIF
   ✗ BMP, TIFF, SVG
   ```

2. **Taille du fichier**
   ```
   ✓ Moins de 5 MB
   ✗ Plus de 5 MB
   ```

3. **Résolution image**
   ```
   ✓ 800x600 ou plus
   ✗ Très petite (< 200px)
   ```

**Solutions** :

```bash
# Compresser l'image (ImageMagick)
convert image.jpg -resize 1600x1200 -quality 85 image-compressed.jpg

# Ou utiliser un outil en ligne
# https://compressor.io
# https://tinypng.com
```

### Preview n'apparaît pas

**Cause** : FileReader API non supportée

**Solution** :
```javascript
// Console - vérifier support
if (typeof FileReader !== 'undefined') {
  console.log('✓ FileReader supporté');
} else {
  console.log('✗ FileReader non supporté - utiliser navigateur récent');
}

// Mettre à jour le navigateur
```

---

## 🔄 Synchronisation ne fonctionne pas

### Bouton "Synchroniser" inactif

**Diagnostic** :

1. **Vérifier la configuration**
   ```
   Allez à ⚙️ Paramètres → Synchronisation
   
   ✓ URL sync remplie ?
   ✓ URL upload remplie ?
   ✓ Clé API remplie ?
   ✓ Case "Synchroniser en ligne" cochée ?
   ```

2. **Tester la connexion**
   ```
   Cliquez "Tester la connexion"
   Doit afficher: ✅ Connexion réussie
   ```

3. **Vérifier les logs** (F12 → Console)
   ```javascript
   // Résultats attendus
   "Endpoint: https://..."
   "API Key: k7Hx9..."
   "Status: enabled"
   ```

### Erreur 401 - Clé API invalide

**Cause** : La clé dans admin.html ne correspond pas à celle du serveur

**Solution** :

1. **Vérifier dans sync.php**
   ```php
   // HOSTINGER-SYNC-UPLOAD.php (ligne 13)
   define('ADMIN_SYNC_KEY', 'k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8');
   // ^ Copiez cette clé exactement
   ```

2. **Entrer dans admin.html**
   ```
   Allez à ⚙️ Paramètres
   Collez la clé dans "Clé de synchronisation"
   Cliquez "Enregistrer la synchro"
   ```

3. **Tester**
   ```
   Cliquez "Tester la connexion"
   Doit afficher: ✅ Connexion réussie
   ```

### Erreur 404 - Endpoint non trouvé

**Cause** : Fichier sync.php mal uploadé sur Hostinger

**Diagnostic** :

1. **Tester l'URL directement**
   ```
   Allez à https://votre-domaine.com/admin/api/sync.php
   
   ✓ Affiche JSON : endpoint OK
   ✗ Erreur 404 : fichier manquant
   ✗ Erreur 500 : erreur PHP
   ```

2. **Vérifier le placement**
   ```
   Correct:  /public_html/admin/api/sync.php
   Incorrect: /public_html/admin/admin/api/sync.php
   Incorrect: /public_html/api/sync.php
   ```

3. **Vérifier les permissions**
   ```
   Doit être: 644 (rw-r--r--)
   Via FTP : Clic droit → Propriétés
   ```

**Solution** :
```
1. Via FTP, allez à /public_html/admin/api/
2. Vérifiez que sync.php existe
3. Si absent, uploadez HOSTINGER-SYNC-UPLOAD.php
4. Renommez-le en sync.php
5. Testez de nouveau
```

### Erreur 500 - Erreur serveur

**Cause** : Configuration DB ou extension manquante

**Diagnostic** :

1. **Vérifier les logs Hostinger**
   ```
   Hostinger Panel → Error Logs
   Cherchez les erreurs PHP récentes
   ```

2. **Vérifier la DB**
   ```
   sync.php (lignes 7-12):
   
   define('DB_HOST', 'localhost');      ← Correct ?
   define('DB_NAME', 'votre_base');    ← Existe ?
   define('DB_USER', 'user');          ← Correct ?
   define('DB_PASS', 'pass');          ← Correct ?
   ```

3. **Vérifier les extensions PHP**
   ```
   Cible: PDO MySQL doit être installé
   
   Via phpMyAdmin:
   Affiche une page → ✓ MySQL OK
   Erreur → ✗ MySQL non accessible
   ```

**Solution** :
```
1. Mettez à jour les credentials (DB_USER, DB_PASS)
2. Contactez Hostinger si problème de DB
3. Vérifiez PDO est activé dans PHP
```

---

## 💾 Sauvegarde et restauration

### Export échoue

**Symptôme** : Clic sur "Exporter" ne télécharge rien

**Cause** : Articles vides ou localStorage corrompu

**Solution** :
```javascript
// Console - forcer export
const articles = JSON.parse(localStorage.getItem('ep_articles')) || [];
const ads = JSON.parse(localStorage.getItem('ep_ads')) || [];
const data = { articles, ads, exportedAt: new Date().toISOString() };
const json = JSON.stringify(data, null, 2);
const blob = new Blob([json], {type: 'application/json'});
const url = URL.createObjectURL(blob);
const a = document.createElement('a');
a.href = url;
a.download = `backup-${new Date().getTime()}.json`;
a.click();
```

### Import échoue

**Symptôme** : Message "Erreur lors du chargement du fichier"

**Causes** :

| Erreur | Cause | Solution |
|--------|-------|----------|
| "Not valid JSON" | Fichier corrompu | Ouvrir dans editeur → vérifier syntaxe |
| "No articles found" | Format incorrect | Réexporter depuis admin.html |
| "Upload failed" | Permissions | Vérifier que localStorage est disponible |

**Vérifier le JSON** :
```bash
# Dans PowerShell
Get-Content backup.json | ConvertFrom-Json | ConvertTo-Json
# Si erreur → JSON mal formé
```

**Restaurer un backup corrompu** :
```javascript
// Console
const json = prompt('Collez le JSON du backup:');
try {
  const data = JSON.parse(json);
  if (data.articles && Array.isArray(data.articles)) {
    localStorage.setItem('ep_articles', JSON.stringify(data.articles));
    alert('✓ Restauré !');
    location.reload();
  }
} catch (e) {
  alert('✗ JSON invalide: ' + e.message);
}
```

---

## ⚠️ Perte de données

### Articles ont disparu

**Cause possible** : localStorage supprimé accidentellement

**Diagnostic** :
```javascript
// Console - vérifier
console.log(localStorage.getItem('ep_articles'));
// Doit afficher un array JSON
// Si null → données perdues
```

**Récupération** :

1. **Chercher un backup** dans Téléchargements
2. **Importer le backup**
   ```
   Paramètres → Sauvegarde → Importer
   ```

3. **Si pas de backup** :
   ```javascript
   // Vous pouvez utiliser DevTools Time-Travel
   // Mais c'est rarement possible
   // → Malheureusement, données perdues définitivement
   ```

### Configuration oubliée

**Cause** : Nettoyage cache du navigateur

**Solution** :
```javascript
// Recréer la config (console)
const config = {
  enabled: true,
  endpoint: 'https://votre-domaine.com/admin/api/sync.php',
  uploadUrl: 'https://votre-domaine.com/admin/api/upload.php',
  apiKey: 'votre_cle_api'
};
localStorage.setItem('syncConfig', JSON.stringify(config));
alert('✓ Configuration restaurée !');
location.reload();
```

---

## 🔒 Sécurité et accès

### Clé API compromiséee

**Symptôme** : Quelqu'un d'autre a accès aux données

**Actions immédiates** :
```
1. Allez sur Hostinger Panel
2. Allez à HOSTINGER-SYNC-UPLOAD.php
3. Changez ADMIN_SYNC_KEY (nouvelle clé)
4. Sauvegardez le fichier
5. Mise à jour dans admin.html
6. Testez la connexion
```

**Générer une nouvelle clé** :
```powershell
# PowerShell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))
# Ou en ligne: https://random.org
```

---

## 🌐 Problèmes navigateur spécifiques

### Chrome / Edge

**localStorage indisponible** :
```
Cause: Page en HTTP, pas HTTPS (sur domaine distant)
Solution: Utiliser HTTPS pour tout
```

**Session expirée** :
```
Cause: Cache agressif
Solution: Ctrl+Shift+Del → Tout effacer → Recharger
```

### Firefox

**Popup bloquée** (export) :
```
Fix: Préférences → Confidentialité → Autorise firefox.com (popup)
```

### Safari / iOS

**localStorage limité** :
```
Limite: 5 MB max
Solution: Exporter souvent, importer au besoin
```

**Conseils** :
```
- Activer "Demander avant d'effacer"
- Ne pas effacer les données du site
- Garder une copie iCloud des backups
```

---

## 🧪 Tests automatiques

### Vérifier la santé du système

```javascript
// Console - diagnostique complet
console.log('=== DIAGNOSTIQUE ADMIN PANEL ===');

// 1. Vérifier localStorage
const articles = localStorage.getItem('ep_articles');
const config = localStorage.getItem('syncConfig');
console.log('✓ Articles:', articles ? 'OK' : 'VIDE');
console.log('✓ Config:', config ? 'OK' : 'VIDE');

// 2. Vérifier taille
let total = 0;
for (let key in localStorage) {
  total += localStorage[key].length;
}
console.log('✓ Taille utilisée:', (total / 1024).toFixed(2), 'KB / 5000 KB');

// 3. Vérifier API
if (config) {
  const cfg = JSON.parse(config);
  console.log('✓ Sync:', cfg.enabled ? 'ACTIVÉE' : 'DÉSACTIVÉE');
  console.log('✓ Endpoint:', cfg.endpoint);
}

// 4. Vérifier DOM
console.log('✓ Interface chargée:', document.getElementById('sidebar') ? 'OUI' : 'NON');

console.log('=== FIN DIAGNOSTIQUE ===');
```

### Résoudre étape par étape

```bash
# 1. Ouvrir admin.html dans le navigateur
http://localhost:8000/admin.html

# 2. Ouvrir DevTools (F12)

# 3. Aller à l'onglet "Console"

# 4. Collez le code de diagnostique ci-dessus

# 5. Lisez les ✓ et ✗ pour identifier le problème
```

---

## 📞 Escalade support

### Avant de contacter Hostinger

**Checklist** :

- [ ] Vérifié le chemin de sync.php (/admin/api/sync.php)
- [ ] Vérifié que le fichier existe en FTP
- [ ] Testé l'URL directement dans navigateur
- [ ] Vérifié DB_HOST, DB_NAME, DB_USER, DB_PASS
- [ ] Vérifiée que PDO MySQL est activé
- [ ] Regardé les Error Logs Hostinger
- [ ] Généré une clé API sécurisée
- [ ] Testé avec test-configuration.html

### Message type pour support Hostinger

```
Sujet: Erreur PHP lors de synchronisation base de données

Description:
- Fichier: /public_html/admin/api/sync.php
- Erreur: [Copier depuis Error Logs]
- Credentials BD: [Confirmer DB_HOST='localhost', etc]
- Extensions requises: PDO, GD Library
- Lien de test: https://mon-domaine.com/admin/api/sync.php

Pouvez-vous vérifier:
1. PDO MySQL est activé ?
2. Base de données 'educations' accessible ?
3. Permissions dossiers /uploads/images/ ?
```

---

## 📚 Ressources supplémentaires

| Problème | Ressource |
|----------|-----------|
| JSON invalide | [jsonlint.com](https://jsonlint.com) |
| Test API | [Postman](https://www.postman.com) / test-configuration.html |
| Logs serveur | Hostinger Panel → Error Logs |
| Permissions FTP | Clic droit → Propriétés (644 ou 755) |
| Image compression | [TinyPNG](https://tinypng.com) |
| Clé API | [OpenSSL](https://openssl.org) ou [random.org](https://random.org) |

---

**Dernière mise à jour** : 2 février 2026  
**Version** : 1.0 - Guide de Dépannage Complet

