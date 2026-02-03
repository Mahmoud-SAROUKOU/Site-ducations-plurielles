@echo off
chcp 65001 > nul
setlocal enabledelayedexpansion

echo.
echo ========================================
echo   🚀 ADMIN PANEL - Éducations Plurielles
echo ========================================
echo.

REM Démarrer le serveur PHP en arrière-plan
cd /d "d:\Site Educations Plurielles"

REM Vérifier si le serveur est déjà en cours d'exécution
tasklist | find "php.exe" >nul
if %errorlevel% equ 0 (
    echo ✅ Serveur PHP déjà en cours d'exécution
) else (
    echo ⏳ Démarrage du serveur PHP...
    start "" C:\php\php.exe -S 127.0.0.1:8000 > nul 2>&1
    timeout /t 2 /nobreak > nul
)

echo.
echo 📋 Identifiants de connexion :
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo 📧 Email    : saroukouy@gmail.com
echo 🔐 Mot de passe : Educations@2026
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo.
echo 💡 Les identifiants sont pré-remplis
echo    Cliquez simplement sur "Connexion rapide"
echo.
echo ⏳ Ouverture du navigateur...
timeout /t 1 /nobreak > nul

REM Ouvrir le navigateur
start "" "http://localhost:8000/admin.html"

echo ✅ Admin panel lancé !
echo.
pause
