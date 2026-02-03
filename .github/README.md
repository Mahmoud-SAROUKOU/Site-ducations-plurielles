# Configuration Agent IA - Educations Plurielles

## 🤖 Utilisation avec GitHub Copilot

Ce projet est optimisé pour GitHub Copilot et autres agents IA grâce au fichier [copilot-instructions.md](copilot-instructions.md).

### Activation

Les instructions sont automatiquement chargées par :
- **GitHub Copilot** (VS Code, Visual Studio, JetBrains)
- **GitHub Copilot Chat** (`@workspace` commands)
- **Claude** (via .cursorrules)
- **Cursor AI**
- **Windsurf**

### Commandes utiles

Dans GitHub Copilot Chat :
```
@workspace Comment créer un nouvel article ?
@workspace Expliquer le système de synchronisation
@workspace Débugger erreur upload image
@workspace Ajouter une nouvelle catégorie
```

## 📋 Quick Start pour IA

L'agent IA peut immédiatement :

1. **Créer articles/publicités** - Génère code admin.html conforme
2. **Ajouter endpoints API** - Template dans HOSTINGER-SYNC-UPLOAD.php
3. **Protéger pages** - Pattern Auth avec session management
4. **Débugger sync** - Scripts console + vérification endpoints
5. **Optimiser images** - Compression double client/serveur

## 🔧 Configuration avancée

### Pour personnaliser les instructions

Éditer [copilot-instructions.md](copilot-instructions.md) :
- **Ajouter patterns** : Section "Patterns de Code Récurrents"
- **Nouveaux workflows** : Section "Workflows Courants"
- **Debug spécifique** : Section "Résolution Problèmes Fréquents"

### Pour d'autres IDE

- **VS Code** : Instructions chargées automatiquement
- **Cursor** : Créer `.cursorrules` → copier contenu
- **Windsurf** : Créer `.windsurfrules` → copier contenu
- **Cline** : Créer `.clinerules` → copier contenu

## 📚 Documentation complète

- [copilot-instructions.md](copilot-instructions.md) - Instructions complètes pour IA
- [../START-HERE.md](../START-HERE.md) - Quick start humain
- [../INDEX.md](../INDEX.md) - Index documentation projet

## 🎯 Exemples de prompts efficaces

```
"Créer un nouvel endpoint API pour les catégories d'articles"
→ L'IA connaît la structure HOSTINGER-SYNC-UPLOAD.php

"Ajouter validation email dans formulaire admin"
→ L'IA connaît les patterns PHP Auth

"Débugger pourquoi les images ne s'uploadent pas"
→ L'IA connaît les étapes de vérification

"Optimiser la navigation mobile iOS"
→ L'IA connaît mobile-enhancements.js et les patterns tactiles
```

## ⚡ Performance

Les instructions sont conçues pour :
- ✅ Réponses rapides (< 140 lignes, pas de verbosité)
- ✅ Exemples concrets du projet (pas de générique)
- ✅ Liens directs vers fichiers pertinents
- ✅ Patterns réutilisables (copy-paste ready)

## 🔄 Mise à jour

Après modifications importantes :
```powershell
# Régénérer les instructions
git add .github/copilot-instructions.md
git commit -m "chore: update AI instructions"
```

L'agent IA s'adaptera automatiquement aux nouveaux patterns.
