@echo off
chcp 65001 >nul
echo.
echo ═══════════════════════════════════════════════════════════════
echo    INSTALLATION DE GIT POUR WINDOWS
echo ═══════════════════════════════════════════════════════════════
echo.

:: Vérifier les droits administrateur
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ❌ Ce script nécessite les droits administrateur!
    echo.
    echo 💡 Solution:
    echo    1. Faites un clic droit sur ce fichier
    echo    2. Sélectionnez "Exécuter en tant qu'administrateur"
    echo.
    pause
    exit /b 1
)

echo ✅ Droits administrateur confirmés
echo.
echo 🚀 Lancement de l'installation...
echo.

:: Lancer le script PowerShell
powershell.exe -ExecutionPolicy Bypass -File "%~dp0INSTALLER-GIT.ps1"

echo.
echo Installation terminée!
pause
