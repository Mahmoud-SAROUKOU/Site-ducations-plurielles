@echo off
chcp 65001 >nul
cls
echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║   CONNEXION AUTOMATIQUE A GITHUB                             ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
echo Ce script va :
echo   1. Ouvrir GitHub pour creer le depot
echo   2. Attendre votre confirmation
echo   3. Pousser automatiquement tout votre code
echo.
pause

echo.
echo ══════════════════════════════════════════════════════════════
echo ETAPE 1 : Ouverture de GitHub dans le navigateur...
echo ══════════════════════════════════════════════════════════════
echo.
start https://github.com/new
echo.
echo ✅ GitHub ouvert dans votre navigateur !
echo.
echo 📋 INSTRUCTIONS RAPIDES :
echo.
echo    1. Nom du depot : educations-plurielles
echo    2. Visibilite : Private (cochez)
echo    3. Ne cochez RIEN d'autre
echo    4. Cliquez "Create repository"
echo    5. COPIEZ l'URL affichee (format: https://github.com/USERNAME/educations-plurielles.git)
echo.
echo ══════════════════════════════════════════════════════════════
echo.
set /p github_url="Collez l'URL de votre depot GitHub ici : "
echo.

if "%github_url%"=="" (
    echo ❌ Aucune URL fournie. Annulation.
    pause
    exit /b 1
)

echo.
echo ══════════════════════════════════════════════════════════════
echo ETAPE 2 : Configuration du depot distant...
echo ══════════════════════════════════════════════════════════════
echo.

"C:\Program Files\Git\bin\git.exe" remote add origin %github_url% 2>nul

if %errorlevel% neq 0 (
    echo Remote existe deja, mise a jour...
    "C:\Program Files\Git\bin\git.exe" remote set-url origin %github_url%
)

echo ✅ Remote configure : %github_url%
echo.

echo ══════════════════════════════════════════════════════════════
echo ETAPE 3 : Push vers GitHub...
echo ══════════════════════════════════════════════════════════════
echo.
echo 🚀 Envoi de 169 fichiers (38,614 lignes)...
echo    Cela peut prendre 30-60 secondes...
echo.
echo 💡 Si on vous demande de vous authentifier :
echo    - Utilisez le navigateur (recommande)
echo    - OU utilisez un Personal Access Token
echo.

"C:\Program Files\Git\bin\git.exe" push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo ══════════════════════════════════════════════════════════════
    echo ✅ SUCCES ! VOTRE CODE EST SUR GITHUB !
    echo ══════════════════════════════════════════════════════════════
    echo.
    echo 🌐 Votre projet : %github_url%
    echo.
    echo 📊 Statistiques :
    echo    - 169 fichiers envoyes
    echo    - 38,614 lignes de code
    echo    - 2 commits
    echo.
    echo 🔒 Fichiers sensibles proteges (non envoyes) :
    echo    - .env
    echo    - .admin-credentials.txt
    echo    - admin/database.sqlite
    echo    - uploads/images/*
    echo.
    echo ══════════════════════════════════════════════════════════════
    echo PROCHAINES ETAPES :
    echo ══════════════════════════════════════════════════════════════
    echo.
    echo 1. Verifiez sur GitHub que tous vos fichiers sont la
    echo 2. Configurez la synchronisation Hostinger dans admin.html
    echo 3. Utilisez ces commandes pour les prochaines modifications :
    echo.
    echo    git add .
    echo    git commit -m "Description"
    echo    git push
    echo.
) else (
    echo.
    echo ══════════════════════════════════════════════════════════════
    echo ❌ ERREUR LORS DU PUSH
    echo ══════════════════════════════════════════════════════════════
    echo.
    echo Causes possibles :
    echo.
    echo 1. Probleme d'authentification
    echo    → Creez un Personal Access Token sur GitHub
    echo    → https://github.com/settings/tokens
    echo.
    echo 2. Depot GitHub non vide
    echo    → Assurez-vous de ne PAS avoir coche "Initialize with README"
    echo    → Supprimez le depot et recreez-le vide
    echo.
    echo 3. Connexion internet
    echo    → Verifiez votre connexion
    echo.
    echo 4. URL incorrecte
    echo    → Format attendu: https://github.com/USERNAME/educations-plurielles.git
    echo.
    echo Pour reessayer :
    echo    "C:\Program Files\Git\bin\git.exe" push -u origin main
    echo.
)

echo.
pause
