@echo off
chcp 65001 >nul
echo.
echo ═══════════════════════════════════════════════════════════════
echo    CONNEXION GITHUB - EDUCATIONS PLURIELLES
echo ═══════════════════════════════════════════════════════════════
echo.

powershell.exe -ExecutionPolicy Bypass -File "%~dp0CONNECTER-GITHUB.ps1"

pause
