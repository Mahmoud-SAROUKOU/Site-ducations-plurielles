@echo off
chcp 65001 > nul
setlocal enabledelayedexpansion

echo.
echo ========================================
echo   🤖 VÉRIFICATION AGENT IA
echo   Educations Plurielles
echo ========================================
echo.

REM Vérifier les fichiers principaux
echo 📋 Vérification des fichiers...
echo.

set "ERRORS=0"

if exist ".github\copilot-instructions.md" (
    echo ✅ .github\copilot-instructions.md
) else (
    echo ❌ .github\copilot-instructions.md MANQUANT
    set /a ERRORS+=1
)

if exist ".github\README.md" (
    echo ✅ .github\README.md
) else (
    echo ❌ .github\README.md MANQUANT
    set /a ERRORS+=1
)

if exist ".github\IDE-INTEGRATION.md" (
    echo ✅ .github\IDE-INTEGRATION.md
) else (
    echo ❌ .github\IDE-INTEGRATION.md MANQUANT
    set /a ERRORS+=1
)

if exist ".github\PROMPTS-EXAMPLES.md" (
    echo ✅ .github\PROMPTS-EXAMPLES.md
) else (
    echo ❌ .github\PROMPTS-EXAMPLES.md MANQUANT
    set /a ERRORS+=1
)

if exist ".github\AGENT-SETUP-COMPLETE.md" (
    echo ✅ .github\AGENT-SETUP-COMPLETE.md
) else (
    echo ❌ .github\AGENT-SETUP-COMPLETE.md MANQUANT
    set /a ERRORS+=1
)

if exist ".cursorrules" (
    echo ✅ .cursorrules ^(pour Cursor IDE^)
) else (
    echo ⚠️  .cursorrules absent ^(optionnel pour Cursor^)
)

echo.
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo.

REM Compter lignes du fichier principal
if exist ".github\copilot-instructions.md" (
    for /f %%a in ('find /c /v "" ^< ".github\copilot-instructions.md"') do set LINES=%%a
    echo 📊 Instructions IA : !LINES! lignes
)

echo.

REM Vérifier VS Code
where code >nul 2>nul
if %errorlevel% equ 0 (
    echo 💻 VS Code détecté - GitHub Copilot compatible
) else (
    echo ℹ️  VS Code non détecté dans PATH
)

echo.
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo.

if %ERRORS% equ 0 (
    echo ✅ CONFIGURATION AGENT IA OK
    echo.
    echo 📖 Prochaines étapes:
    echo    1. Ouvrir projet dans VS Code / Cursor
    echo    2. Installer GitHub Copilot si pas fait
    echo    3. Tester: @workspace Explique le système de sync
    echo.
    echo 📚 Documentation complète:
    echo    .github\AGENT-SETUP-COMPLETE.md
) else (
    echo ❌ %ERRORS% fichier^(s^) manquant^(s^)
    echo.
    echo 🔧 Pour recréer les fichiers:
    echo    Exécuter la commande de génération des instructions IA
)

echo.
echo ========================================
echo.

pause
