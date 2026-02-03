@echo off
chcp 65001 >nul
echo.
echo ═══════════════════════════════════════════════════════════════
echo    INITIALISATION DU PROJET GIT
echo    Éducations Plurielles - Version 1.1
echo ═══════════════════════════════════════════════════════════════
echo.

:: Vérifier si Git est installé
git --version >nul 2>&1
if %errorLevel% neq 0 (
    echo ❌ Git n'est pas installé!
    echo.
    echo 💡 Lancez d'abord: INSTALLER-GIT.bat
    echo.
    pause
    exit /b 1
)

echo ✅ Git détecté: 
git --version
echo.

:: Vérifier si le dépôt existe déjà
if exist ".git" (
    echo ⚠️ Un dépôt Git existe déjà!
    echo.
    echo Voulez-vous réinitialiser? (O/N)
    set /p response=
    if /i not "%response%"=="O" (
        echo.
        echo Opération annulée.
        pause
        exit /b 0
    )
    echo.
    echo 🗑️ Suppression du dépôt existant...
    rmdir /s /q .git
)

echo 📦 Initialisation du dépôt Git...
git init
echo.

:: Vérifier le .gitignore
if not exist ".gitignore" (
    echo ⚠️ Fichier .gitignore manquant!
    echo.
    pause
    exit /b 1
)

echo ✅ .gitignore trouvé
echo.

echo 📋 Configuration Git...
echo.
echo Entrez votre nom (pour les commits):
set /p username=
echo.
echo Entrez votre email:
set /p useremail=
echo.

git config user.name "%username%"
git config user.email "%useremail%"
git config init.defaultBranch main

echo.
echo ✅ Configuration enregistrée
echo.

echo 📁 Ajout des fichiers au dépôt...
git add .
echo.

echo 💬 Création du premier commit...
git commit -m "Initial commit: Admin v1.1 avec interface welcome page et sync Hostinger"
echo.

echo ═══════════════════════════════════════════════════════════════
echo ✅ DÉPÔT GIT INITIALISÉ AVEC SUCCÈS!
echo ═══════════════════════════════════════════════════════════════
echo.
echo 📊 Statut du dépôt:
git status
echo.
echo 📝 Historique des commits:
git log --oneline
echo.
echo ═══════════════════════════════════════════════════════════════
echo 📌 PROCHAINES ÉTAPES:
echo ═══════════════════════════════════════════════════════════════
echo.
echo 1️⃣ Créer un dépôt sur GitHub:
echo    - Allez sur: https://github.com/new
echo    - Nom: educations-plurielles
echo    - Visibilité: Private (recommandé)
echo    - Ne PAS initialiser avec README
echo.
echo 2️⃣ Lier votre dépôt local à GitHub:
echo    git remote add origin https://github.com/VOTRE_USERNAME/educations-plurielles.git
echo    git branch -M main
echo    git push -u origin main
echo.
echo 3️⃣ Vérifier la synchronisation Hostinger:
echo    - Ouvrir admin.html dans le navigateur
echo    - Aller dans Paramètres ⚙️
echo    - Section "Synchronisation Hostinger"
echo    - Remplir les champs endpoint, uploadUrl, apiKey
echo.
echo ═══════════════════════════════════════════════════════════════
echo.
pause
