# Intégration Agent IA - Guide IDE

## Visual Studio Code (GitHub Copilot)

### Installation
1. Installer l'extension **GitHub Copilot**
2. Se connecter avec compte GitHub
3. Les instructions sont chargées automatiquement depuis `.github/copilot-instructions.md`

### Utilisation
- **Complétion inline** : Commencer à taper, Copilot suggère
- **Chat** : `Ctrl+I` puis `@workspace` pour questions projet
- **Commandes** :
  - `@workspace /explain` - Expliquer code sélectionné
  - `@workspace /fix` - Corriger erreur
  - `@workspace /tests` - Générer tests

## Cursor IDE

### Configuration
1. Créer fichier `.cursorrules` à la racine :
```bash
cp .github/copilot-instructions.md .cursorrules
```

2. Ou pointer vers les instructions :
```
# .cursorrules
@import .github/copilot-instructions.md
```

### Utilisation
- **Cmd+K** : Chat inline
- **Cmd+L** : Panel chat complet
- **Référence automatique** : Cursor charge les règles au démarrage

## Windsurf IDE

### Configuration
```bash
mkdir -p .windsurf/rules
cp .github/copilot-instructions.md .windsurf/rules/instructions.md
```

### Utilisation
- Les règles sont chargées automatiquement
- Prefix `@rules` pour forcer référence explicite

## Cline (VS Code Extension)

### Configuration
```bash
cp .github/copilot-instructions.md .clinerules
```

### Utilisation
- Ouvrir panel Cline (sidebar)
- Instructions chargées automatiquement
- Prefix `@project` pour contexte complet

## Claude (Desktop/Web)

### Configuration
Pour projets partagés via Claude Desktop :

1. **Projects** → **New Project** → **Add Instructions**
2. Copier contenu de `.github/copilot-instructions.md`
3. Sauvegarder

### Utilisation
```
Dans le projet "Educations Plurielles" :
- Comment créer un article ?
- Débugger sync images
- Ajouter endpoint API
```

## Comparaison IDE

| IDE/Extension | Auto-load | Chat | Inline | Context |
|--------------|-----------|------|--------|---------|
| GitHub Copilot | ✅ | ✅ | ✅ | Workspace |
| Cursor | ⚠️ (.cursorrules) | ✅ | ✅ | Full project |
| Windsurf | ✅ | ✅ | ✅ | Full project |
| Cline | ⚠️ (.clinerules) | ✅ | ❌ | Limited |
| Claude Desktop | ⚠️ (manual) | ✅ | ❌ | Project-based |

## Test de l'intégration

### Pour tous les IDE

1. **Test simple** :
```
Prompt: "Explique-moi le système de synchronisation"
Attendu: Référence à HOSTINGER-SYNC-UPLOAD.php, localStorage, API
```

2. **Test code** :
```
Prompt: "Créer une fonction pour ajouter une catégorie"
Attendu: Pattern PHP/PDO avec Database::connect()
```

3. **Test debug** :
```
Prompt: "Pourquoi mes images ne s'uploadent pas ?"
Attendu: Checklist (permissions, GD Library, taille, console)
```

## Troubleshooting

### Instructions non chargées (Copilot)
```bash
# Vérifier emplacement fichier
ls .github/copilot-instructions.md

# Recharger VS Code
Ctrl+Shift+P → "Reload Window"
```

### Instructions non appliquées (Cursor)
```bash
# Vérifier .cursorrules existe
ls .cursorrules

# Redémarrer Cursor complètement
```

### Contexte incomplet
- **Augmenter tokens** : Settings → Context → Max tokens
- **Vérifier gitignore** : Fichiers importants non ignorés
- **Index workspace** : Laisser IDE indexer (première ouverture)

## Optimisation Performance

### Pour projets volumineux

Exclure dossiers inutiles dans `.gitignore` :
```
node_modules/
.git/
uploads/images/*
*.log
```

### Cache Copilot
```bash
# Windows
Remove-Item -Recurse ~\AppData\Roaming\GitHub Copilot\

# Redémarrer VS Code
```

## Mises à jour

Après modification des instructions :
```bash
# Commit
git add .github/copilot-instructions.md
git commit -m "chore: update AI instructions"

# Pour IDE nécessitant copie manuelle
cp .github/copilot-instructions.md .cursorrules
```

## Support

- **GitHub Copilot** : [docs.github.com/copilot](https://docs.github.com/copilot)
- **Cursor** : [cursor.sh/docs](https://cursor.sh/docs)
- **Instructions format** : [aka.ms/vscode-instructions-docs](https://aka.ms/vscode-instructions-docs)
