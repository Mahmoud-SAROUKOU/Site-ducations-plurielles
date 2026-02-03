# 📊 Guide d'administration - Admin Panel

## 🎯 Vue d'ensemble

**admin.html** est une interface de gestion complète pour le site Educations Plurielles, offrant :
- ✅ Gestion centralisée des articles, vidéos, ressources et publicités
- ✅ Synchronisation bidirectionnelle avec serveur Hostinger
- ✅ Interface intuitive et responsive
- ✅ Stockage local en localStorage
- ✅ Export/Import de données en JSON

---

## 🚀 Accès et démarrage

### Ouvrir l'interface admin

**Local** :
```
http://localhost:8000/admin.html
```

**En production** :
```
https://votre-domaine.com/admin.html
```

### Premier lancement

1. **Accédez** à admin.html
2. **Allez** à ⚙️ Paramètres (en bas du menu)
3. **Configurez** la synchronisation Hostinger (voir section "Synchronisation")
4. **Testez** la connexion avec le bouton "Tester la connexion"

---

## 📋 Interface et Navigation

### Structure générale

```
┌─────────────────────────────────────────────────────┐
│ Admin Panel                                         │
├──────────────┬──────────────────────────────────────┤
│ SIDEBAR      │ HEADER (Titre + Sync + Profil)       │
│              ├──────────────────────────────────────┤
│ • Tableau    │                                      │
│ • Articles   │ CONTENU PRINCIPAL                    │
│ • Vidéos     │ (Articles, Vidéos, Ressources...)   │
│ • Ressources │                                      │
│ • Publicités │                                      │
│ • Catégories │                                      │
│ • Paramètres │                                      │
└──────────────┴──────────────────────────────────────┘
```

### Éléments clés

| Élément | Description |
|---------|------------|
| **Sidebar** | Navigation principale (260px fixe) |
| **Header** | Titre de la section + bouton Sync + profil utilisateur |
| **Contenu** | Affichage dynamique selon la section sélectionnée |
| **Modales** | Formulaires pour créer/modifier articles |
| **Alerts** | Messages temporaires (succès, erreur, info) |

---

## 📰 Gestion des Articles

### Créer un nouvel article

**Étapes** :
1. Cliquez sur **Articles** dans le sidebar
2. Cliquez sur **"Créer un nouvel article"** (ou utilisez bouton d'action rapide)
3. Remplissez le formulaire :

| Champ | Description | Obligatoire |
|-------|-------------|------------|
| **Titre** | Titre de l'article | ✅ |
| **Contenu** | Corps du texte (support Markdown possible) | ✅ |
| **Catégorie** | parentalite / education / droits / temoignages | ✅ |
| **Image** | Couverture de l'article | ❌ |
| **Auteur** | Nom de l'auteur (défaut: Admin) | ❌ |
| **Tags** | Mots-clés (séparés par virgules) | ❌ |

### Upload d'image

**Trois méthodes** :
1. **Cliquer** sur la zone de drop
2. **Glisser-déposer** une image dans la zone
3. **Utiliser** le bouton "Upload fichier"

**Spécifications** :
- Formats supportés : JPEG, PNG, WebP, GIF
- Taille maximale : 5 MB
- Dimensions recommandées : 1600x1200 px
- **Compression automatique** : Client (85%) + Serveur (82%)

### Modifier un article

1. Allez dans **Articles**
2. Cliquez sur le bouton **"Modifier"** de l'article
3. Modifiez les champs
4. Cliquez sur **"Enregistrer"**

### Supprimer un article

1. Allez dans **Articles**
2. Cliquez sur **"Supprimer"** (bouton rouge)
3. Confirmez la suppression

### Rechercher et filtrer

- **Recherche** : Tapez dans le champ de recherche (titre)
- **Filtrer** : Sélectionnez une catégorie dans le dropdown

---

## 🎥 Gestion des Vidéos

**Status** : Module en développement

**Prochainement** :
- ✅ Support YouTube/Vimeo (embed URLs)
- ✅ Gestion des playlists
- ✅ Organisation par catégories

**Configuration actuelle** : Utilisez la section Paramètres pour les URLs

---

## 📚 Gestion des Ressources

**Status** : Module en développement

**Prochainement** :
- ✅ Upload fichiers PDF
- ✅ Documents Word/PowerPoint
- ✅ Classement par type (fiche, guide, outil)
- ✅ Descriptifs et métadonnées

**Utilisation actuelle** : Disponible via l'interface principale du site

---

## 📢 Gestion des Publicités

### Créer une publicité

1. Allez dans **Publicités**
2. Cliquez sur **"Créer une publicité"**
3. Remplissez les champs

| Champ | Description |
|-------|-------------|
| **Titre** | Nom de la publicité |
| **Message** | Texte du banner déroulant |
| **Icône** | Emoji ou icône (📢, 🎉, etc.) |
| **Lien** | URL cible (optionnel) |
| **Statut** | active / inactive |

### Apparence

Les publicités s'affichent dans le **News Ticker** en haut du site :

```
📢 Nouvel article : Comment écouter les émotions de votre enfant
📢 Atelier en ligne ce samedi : Éducation bienveillante...
```

---

## 🏷️ Gestion des Catégories

### Catégories disponibles

```
• Parentalité
• Éducation
• Droits de l'enfant
• Témoignages
```

### Ajouter une catégorie

1. Allez dans **Catégories**
2. Cliquez sur **"Créer une catégorie"**
3. Entrez le nom et la description

---

## ⚙️ Paramètres & Configuration

### 🔄 Synchronisation Hostinger

Permet de synchroniser les articles avec une base de données MySQL distante.

#### Configuration

**Accédez** à Paramètres → Synchronisation Hostinger

**Remplissez les champs** :

| Champ | Exemple |
|-------|---------|
| **URL du point de sync** | `https://votre-domaine.com/admin/api/sync.php` |
| **URL d'upload** | `https://votre-domaine.com/admin/api/upload.php` |
| **URL de refresh** | `https://votre-domaine.com/?refresh=1` |
| **Clé API** | `k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8` |

#### Tester la connexion

```
Cliquez : "Tester la connexion"
```

**Résultats possibles** :
- ✅ **Connexion réussie** : Endpoint accessible
- ❌ **Clé API invalide** : Vérifier la clé dans sync.php
- ⚠️ **Erreur 404** : Fichier sync.php mal placé

#### Activer la sync

```
☑️ Cochez "Synchroniser en ligne"
Cliquez "Enregistrer la synchro"
```

**Après activation** :
- Tous les nouveaux articles seront automatiquement envoyés au serveur
- Un tracking `remote_id` sera ajouté à chaque article

### 💾 Sauvegarde & Export

#### Exporter les données

1. Allez dans **Paramètres** → Sauvegarde
2. Cliquez sur **"Exporter les données"**
3. Un fichier JSON est téléchargé : `ep-backup-2026-02-02.json`

**Contenu du backup** :
```json
{
  "articles": [...],
  "ads": [...],
  "exportedAt": "2026-02-02T..."
}
```

#### Importer les données

1. Allez dans **Paramètres** → Sauvegarde
2. Cliquez sur **"Importer les données"**
3. Sélectionnez un fichier JSON
4. Les données remplacent les données locales

⚠️ **Attention** : Sauvegardez d'abord vos données actuelles !

### ℹ️ Informations Système

Affiche :
- Version du panel
- Espace utilisé (localStorage)
- Date de la dernière synchronisation
- Bouton pour vider le cache

### Vider le cache

```
Cliquez : "Vider le cache"
```

**Effet** : Supprime TOUS les données locales (articles, config, cache)

⚠️ **Irréversible** ! Exportez d'abord vos données.

---

## 📊 Tableau de bord

### Statistiques affichées

| Stat | Description |
|------|-------------|
| **Articles publiés** | Nombre total d'articles |
| **Vidéos uploadées** | Nombre de vidéos |
| **Ressources** | Documents disponibles |
| **Publicités actives** | Annonces actuelles |

### État de synchronisation

Affiche :
- Status de la connexion
- Dernier sync
- Anomalies détectées

### Actions rapides

Boutons d'accès direct :
- 📝 Nouvel article
- 🎥 Nouvelle vidéo
- 📢 Nouvelle pub
- ⚙️ Paramètres

---

## 🔒 Stockage & Sécurité

### localStorage

Les données sont stockées localement dans le navigateur.

**Clés utilisées** :
```javascript
ep_articles       // Articles en JSON
ep_ads           // Publicités en JSON
syncConfig       // Configuration sync + clé API
```

### Clé API

**Sécurité** :
- ✅ Stockée localement uniquement
- ✅ Jamais envoyée en URL
- ✅ Header personnalisé (`X-Admin-Sync-Key`)
- ⚠️ **Ne partagez pas** votre clé API

**Rotation recommandée** : Tous les 3 mois sur Hostinger

### Sauvegarde des données

**Recommandations** :
1. Exportez vos données chaque semaine
2. Stockez les backups en lieu sûr
3. Testez les imports régulièrement
4. Gardez une copie hors ligne

---

## 🐛 Dépannage

### Les articles ne s'affichent pas

**Cause** : localStorage vide

**Solution** :
```
1. Allez à Paramètres
2. Vérifiez "Informations système"
3. Essayez d'importer une sauvegarde
```

### La synchronisation échoue

**Causes possibles** :

| Erreur | Solution |
|--------|----------|
| "Impossible de se connecter" | Vérifier l'URL endpoint |
| "Clé API invalide" | Vérifier la clé dans sync.php |
| "URL non accessible" | Vérifier le domaine/HTTPS |

**Diagnostic** :
```
1. Cliquez "Tester la connexion"
2. Vérifiez l'URL endpoint
3. Testez avec test-configuration.html
4. Vérifiez les logs serveur Hostinger
```

### Les images ne s'uploadent pas

**Causes** :

| Problème | Solution |
|---------|----------|
| Fichier > 5 MB | Compresser l'image |
| Format non supporté | Utiliser JPEG/PNG/WebP |
| Erreur serveur | Vérifier GD Library est activée |

### Perte de données

**Récupération** :
1. Chercher un backup dans les téléchargements
2. Aller à Paramètres → Importer
3. Sélectionner le fichier JSON

### Performance lente

**Optimisations** :
```
1. Vider le cache : Paramètres → "Vider le cache"
2. Redémarrer le navigateur
3. Exporter/Importer les données
4. Supprimer les articles non utilisés
```

---

## 📖 Workflows Courants

### Créer un article complet

```
1. Allez à Articles
2. Cliquez "Créer un nouvel article"
3. Remplissez :
   - Titre
   - Contenu (markdown supporté)
   - Catégorie (ex: Parentalité)
   - Tags (ex: bienveillance, éducation)
   - Image (drag & drop ou clic)
4. Cliquez "Enregistrer"
5. Article apparaît immédiatement
6. (Optionnel) Cliquez "Synchroniser" pour envoyer à Hostinger
```

### Publier une série d'articles

```
1. Créez tous les articles localement
2. Allez à Paramètres → Synchronisation
3. Remplissez la config Hostinger
4. Cochez "Synchroniser en ligne"
5. Enregistrez
6. Revenez aux Articles
7. Cliquez le grand bouton "Synchroniser" (top right)
8. Attendez la fin
```

### Sauvegarder et transférer

```
1. Allez à Paramètres
2. Cliquez "Exporter les données"
3. Sauvegardez le JSON téléchargé
4. Sur un autre ordi :
   - Ouvrez admin.html
   - Allez à Paramètres
   - Cliquez "Importer les données"
   - Sélectionnez le JSON
5. Toutes les données sont restaurées ✅
```

### Migrer de SQLite à MySQL

```
1. Exportez données locales (JSON)
2. Configurez la sync Hostinger
3. Activez "Synchroniser en ligne"
4. Cliquez "Synchroniser"
5. Les articles sont maintenant dans MySQL ✅
```

---

## 📱 Responsive Design

L'admin panel fonctionne sur tous les appareils :

**Desktop** (1920x1080+) :
- ✅ Sidebar toujours visible
- ✅ Grille multi-colonnes
- ✅ Interface complète

**Tablet** (768-1024px) :
- ✅ Sidebar rétractable
- ✅ Grille adaptative
- ✅ Contrôles tactiles

**Mobile** (< 768px) :
- ✅ Sidebar en drawer (menu burger)
- ✅ Affichage une colonne
- ✅ Touch-optimisé

---

## 🎨 Personnalisation

### Modifier les couleurs

Éditez les variables CSS au début de admin.html :

```css
:root {
    --primary: #1e3a8a;           /* Bleu principal */
    --primary-light: #3b82f6;     /* Bleu clair */
    --accent: #fbbf24;            /* Accent (or) */
    --danger: #ef4444;            /* Danger (rouge) */
    --success: #10b981;           /* Succès (vert) */
}
```

### Ajouter un logo personnalisé

```html
<img src="VOTRE_LOGO.png" alt="Logo" style="height: 40px;">
```

### Modifier les catégories par défaut

Cherchez dans le HTML :

```html
<option value="parentalite">Parentalité</option>
<option value="education">Éducation</option>
<!-- Ajoutez ici -->
```

---

## 💡 Conseils d'utilisation

### ✅ Bonnes pratiques

1. **Titres clairs et descriptifs** pour meilleur SEO
2. **Images optimisées** (dimensions appropriées)
3. **Tags pertinents** pour classement
4. **Sauvegardez régulièrement** vos données
5. **Testez la sync** avant publication
6. **Utilisez le même auteur** pour cohérence

### ❌ À éviter

1. Ne pas partager la clé API
2. Ne pas supprimer articles sans sauvegarde
3. Ne pas vider le cache sans export
4. Ne pas importer/exporter souvent (perf)
5. Ne pas utiliser caractères spéciaux dans slugs

### 🔄 Maintenance régulière

- **Hebdomadaire** : Exporter les données
- **Mensuel** : Nettoyer les articles obsolètes
- **Trimestriel** : Rotation clé API (Hostinger)
- **Annuel** : Audit complet des contenus

---

## 📞 Support & Documentation

### Fichiers liés

- [START-HERE.md](START-HERE.md) - Quick start
- [DEPLOIEMENT-HOSTINGER-INSTRUCTIONS.md](DEPLOIEMENT-HOSTINGER-INSTRUCTIONS.md) - Déploiement
- [CHARTE_GRAPHIQUE.md](CHARTE_GRAPHIQUE.md) - Design
- [CONFIGURATION-COMPLETE.md](CONFIGURATION-COMPLETE.md) - Config avancée

### Commandes utiles

```powershell
# Démarrer le serveur local
.\CONNEXION-RAPIDE.bat

# Tester la configuration
.\VERIFIER-AGENT-IA.bat

# Arrêter le serveur
.\ARRETER-SERVEUR.bat
```

---

## 📋 Checklist d'initialisation

- [ ] Accès à admin.html réussi
- [ ] Création d'un article test
- [ ] Upload image réussi
- [ ] Configuration sync complétée
- [ ] Test connexion réussi
- [ ] Sync activée
- [ ] Synchronisation exécutée
- [ ] Export de données effectué
- [ ] Documentation lue

---

**Version** : 1.0  
**Créé** : 2 février 2026  
**Projet** : Educations Plurielles

**Questions ?** Consultez la documentation complète ou testez avec l'agent IA ! 🤖

