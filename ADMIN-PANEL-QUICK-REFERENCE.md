# ⚡ Admin Panel - Référence rapide

**Cheat sheet** pour accès rapide aux infos essentielles  
**Gardez cette page à portée de main !**

---

## 🚀 Démarrage 30 secondes

```
1. Ouvrir: http://localhost:8000/admin.html
2. Aller à: ⚙️ Paramètres (bottom left)
3. Remplir: URL sync + clé API
4. Cocher: "Synchroniser en ligne"
5. Sauvegarder
6. Tester: "Tester la connexion"
```

---

## 📰 Créer article en 1 minute

```
1. Cliquez: "Créer un nouvel article"
2. Remplissez:
   • Titre: "Mon article"
   • Contenu: "Texte..."
   • Catégorie: dropdown
   • Image: drag & drop
3. Cliquez: "Enregistrer"
4. ✅ Article créé et visible dans grille
```

---

## 🔗 URLs essentielles

| Besoin | URL | Note |
|--------|-----|------|
| **Admin local** | `http://localhost:8000/admin.html` | Développement |
| **Admin prod** | `https://domaine.com/admin.html` | Production |
| **Sync endpoint** | `/admin/api/sync.php` | Configuration |
| **Upload endpoint** | `/admin/api/upload.php` | Configuration |
| **Config test** | `test-configuration.html` | Diagnostic |
| **Public site** | `index.html` | Affichage articles |

---

## 🔑 Clé API

**Générer** :
```powershell
# Windows PowerShell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))
```

**Utiliser** :
1. Mettre dans sync.php ligne 13
2. Mettre dans upload.php ligne 9
3. Mettre dans admin.html → Paramètres
4. **TOUTES LES 3 DOIVENT ÊTRE IDENTIQUES**

---

## 💾 localStorage (4 clés)

```javascript
// Console (F12)

// 1. Articles
localStorage.getItem('ep_articles')
// Résultat: [{"id":1,"title":"..."}]

// 2. Publicités
localStorage.getItem('ep_ads')
// Résultat: [{"id":1,"name":"..."}]

// 3. Configuration sync
localStorage.getItem('syncConfig')
// Résultat: {"enabled":true,"endpoint":"...","apiKey":"..."}

// 4. Vider complètement
localStorage.clear()
location.reload()
```

---

## 🖼️ Images - Spécifications

| Paramètre | Valeur |
|-----------|--------|
| **Formats** | JPEG, PNG, WebP, GIF |
| **Taille max** | 5 MB |
| **Compression client** | 85% |
| **Compression serveur** | 82% (JPEG), 80% (WebP) |
| **Dimensions max** | 1600x1600 px |
| **Recommended** | 1200x800 px |

---

## 🔄 Synchronisation

### Configuration admin.html

```
Paramètres → Synchronisation Hostinger

URL sync:     https://domaine.com/admin/api/sync.php
URL upload:   https://domaine.com/admin/api/upload.php
URL refresh:  https://domaine.com/?refresh=1
Clé API:      votre_clé_ici
Sync en ligne: ☑️ Coché
```

### Tester connexion

```
Cliquez "Tester la connexion"

✅ Réussi = endpoint OK
❌ 401 = clé invalide
❌ 404 = fichier pas trouvé
⚠️ 500 = erreur DB
```

### Synchroniser

```
Cliquez bouton "Synchroniser" (top right)
Attendez la fin
Articles doivent avoir remote_id
```

---

## 💾 Export / Import

### Exporter

```
Paramètres → Sauvegarde
Cliquez "Exporter les données"
Fichier JSON téléchargé: ep-backup-DATE.json
```

### Importer

```
Paramètres → Sauvegarde
Cliquez "Importer les données"
Choisir fichier JSON
Données remplacées
```

---

## 🎨 Catégories

```javascript
// Disponibles
• parentalite
• education
• droits
• temoignages

// Ajouter nouvelle catégorie
Paramètres → Catégories → Créer
```

---

## 🔍 Recherche & Filtrage

```javascript
// Rechercher articles par titre
Tapez dans "Rechercher articles..."

// Filtrer par catégorie
Dropdown "Catégorie"

// Combinaison
Recherche + Filtrage = résultats affichés immédiatement
```

---

## ⚠️ Erreurs courants et fixes rapides

| Erreur | Fix |
|--------|-----|
| **Admin page blanche** | F5 refresh, vider cache (Ctrl+Shift+Del) |
| **Articles vides** | Créer article test, ou importer backup |
| **Image ne s'upload** | Vérifier < 5MB, format JPEG/PNG, dimensions |
| **Sync échoue 401** | Vérifier clé API dans les 3 endroits |
| **Sync échoue 404** | Vérifier sync.php uploadé à /admin/api/sync.php |
| **Données perdues** | Importer ancien backup JSON |
| **localStorage plein** | Exporter + supprimer anciens articles |
| **Mobile pas responsive** | Ouvrir admin.html sur mobile, doit s'adapter |

---

## 🧪 Tests rapides (console F12)

```javascript
// Vérifier si articles chargés
console.log(localStorage.getItem('ep_articles') ? '✓ OK' : '✗ VIDE')

// Vérifier config sync
console.log(JSON.parse(localStorage.getItem('syncConfig')))

// Tester API directement
fetch('https://domaine.com/admin/api/sync.php', {
  method: 'POST',
  headers: { 'X-Admin-Sync-Key': 'votre_cle' },
  body: JSON.stringify({ type: 'test' })
}).then(r => r.json()).then(console.log)

// Vérifier taille localStorage
let total = 0;
for (let key in localStorage) {
  total += localStorage[key].length;
}
console.log((total / 1024).toFixed(2), 'KB utilisés')

// Forcer export
const articles = JSON.parse(localStorage.getItem('ep_articles')) || [];
console.log('Articles:', articles.length, '→ Exporter si besoin')
```

---

## 📞 Support rapide

**Avant de demander aide** :

1. Vérifier [ADMIN-PANEL-TROUBLESHOOT.md](ADMIN-PANEL-TROUBLESHOOT.md)
2. Tester avec [test-configuration.html](test-configuration.html)
3. Vérifier la console (F12) pour erreurs JavaScript
4. Consulter les logs Hostinger (Panel → Error Logs)

**Message type pour technicien** :
```
Problème: [Décrire symptôme]
URL: [Votre domaine ou localhost]
Erreur console (F12): [Copier message exactement]
Étapes pour reproduire: [1. ... 2. ... 3. ...]
```

---

## 📱 Responsive

```javascript
// Desktop (1920+): Sidebar visible + grille multi-colonnes
// Tablet (768-1024): Sidebar rétractable + grille 2-3 colonnes  
// Mobile (< 768): Menu burger + 1 colonne

// Tester: F12 → Toggle device toolbar
```

---

## 🔐 Sécurité

```javascript
// Ne JAMAIS partager:
• Clé API (localStorage.syncConfig.apiKey)
• Identifiants DB (dans sync.php)

// Bonnes pratiques:
✓ Utiliser HTTPS en production
✓ Changer clé API tous les 3 mois
✓ Exporter données régulièrement
✓ Garder backups hors ligne
✓ Utiliser password manager pour clés
```

---

## 📊 Données

### Article (structure)

```javascript
{
  id: 1,                    // Auto-incrémenté
  title: "Titre article",   // Obligatoire
  content: "Corps texte",   // Obligatoire
  category: "parentalite",  // Enum
  image: "url_image",       // Optional
  tags: ["tag1", "tag2"],   // Array
  author: "Nom",            // String
  createdAt: "ISO date",    // Auto
  updatedAt: "ISO date",    // Auto
  status: "published",      // draft|published|archived
  remote_id: 123            // Après sync (optional)
}
```

### Publicité (structure)

```javascript
{
  id: 1,
  name: "Nom pub",
  message: "Texte affiché",
  icon: "emoji",
  target_url: "lien",
  position: "ticker|sidebar|footer",
  status: "active|inactive",
  order: 1
}
```

---

## 🎯 Workflows rapides

### Créer article avec image et sync

```
1. Créer article (texte + image)
2. Configurer sync (Paramètres)
3. Cliquer "Synchroniser"
4. Vérifier remote_id ajouté
5. Article dans BD Hostinger ✓
```

### Migrer données PC1 → PC2

```
PC1:  Paramètres → Exporter → Sauvegarder JSON
PC2:  Paramètres → Importer → Choisir JSON
      Toutes les données restaurées ✓
```

### Vider + Réinitialiser

```
Paramètres → Vider le cache
⚠️ Attention: tout supprimé!
Puis: Importer ancien backup si besoin
```

---

## 🌐 URLs de lien (modifier index.html)

```javascript
// Dans index.html
<a href="admin.html">Admin</a>     // Lien vers admin local

// En production (si hosted ailleurs)
<a href="https://admin.domaine.com/admin.html">Admin</a>
<a href="admin.html">Admin</a>     // Même domaine
```

---

## 🔧 Maintenance

### Hebdomadaire
```
☐ Exporter données
☐ Sauvegarder fichier JSON
☐ Tester création article
```

### Mensuel
```
☐ Nettoyer articles obsolètes
☐ Vérifier espace localStorage utilisé
☐ Tester sync (si activée)
```

### Trimestriel
```
☐ Rotation clé API
☐ Audit contenu
☐ Vérifier logs serveur
```

### Annuel
```
☐ Audit complet système
☐ Mise à jour guide documentation
☐ Review utilisation et optimisations
```

---

## 📚 Documents à consulter

| Besoin | Document |
|--------|----------|
| **Apprendre** | [ADMIN-PANEL-GUIDE.md](ADMIN-PANEL-GUIDE.md) |
| **Développer** | [ADMIN-PANEL-TECHNIQUE.md](ADMIN-PANEL-TECHNIQUE.md) |
| **Dépanner** | [ADMIN-PANEL-TROUBLESHOOT.md](ADMIN-PANEL-TROUBLESHOOT.md) |
| **Navigation** | [ADMIN-PANEL-INDEX.md](ADMIN-PANEL-INDEX.md) (CE FICHIER) |
| **Déployer** | [DEPLOIEMENT-HOSTINGER-INSTRUCTIONS.md](DEPLOIEMENT-HOSTINGER-INSTRUCTIONS.md) |

---

## ⌨️ Raccourcis clavier

```
F12             → DevTools (diagnostic)
Ctrl+Shift+Del  → Vider cache navigateur
Ctrl+K          → Recherche (si implémentée)
Escape          → Fermer modal
F5              → Recharger page
Ctrl+S          → Sauvegarder (note: géré auto)
```

---

## 🎓 Niveaux d'expertise

```
👶 DÉBUTANT    (30 min)  → Lire GUIDE. Créer article test
👨 UTILISATEUR (2-3h)    → Lire GUIDE complet. Utiliser quotidien
👩‍💼 ADMIN       (3-4h)    → Configurer sync. Gérer backups
👨‍💻 DEV         (4-6h)    → Lire TECHNIQUE. Modifier code
🏗️ DEVOPS       (2-3h)    → Déployer. Maintenir. Monitorer
```

---

## 🎯 Checklist avant go-live

- [ ] Admin.html se charge
- [ ] Créer article fonctionne
- [ ] Upload image fonctionne
- [ ] Recherche fonctionne
- [ ] Filtres fonctionnent
- [ ] Export génère JSON valide
- [ ] Import restaure données
- [ ] sync.php uploadé (check URL)
- [ ] upload.php uploadé (check URL)
- [ ] Clé API identique partout
- [ ] Test sync réussit
- [ ] Articles synchronisés en BD
- [ ] Backup exporté et sauvegardé
- [ ] Documentation lue (min: GUIDE)

---

**Gardez cette page** 📌 comme référence rapide quotidienne !

*Consultez les guides complets pour détails*

