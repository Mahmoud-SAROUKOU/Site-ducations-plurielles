@echo off
REM Script d'installation du système admin unifié pour Windows

echo ==========================================
echo Installation du système Admin Unifié
echo ==========================================
echo.

REM Vérifier PHP
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ PHP n'est pas installé
    exit /b 1
)

echo ✓ PHP détecté
echo.

REM Créer le fichier .env s'il n'existe pas
if not exist ".env" (
    echo 📝 Création du fichier .env...
    (
        echo APP_URL=http://localhost
        echo APP_NAME=Educations Plurielles
        echo DB_HOST=localhost
        echo DB_NAME=educations_plurielles
        echo DB_USER=root
        echo DB_PASS=
        echo MAIL_FROM=admin@exemple.com
        echo MAIL_FROM_NAME=Admin
        echo MAIL_SMTP_HOST=
        echo MAIL_SMTP_PORT=587
        echo MAIL_SMTP_USER=
        echo MAIL_SMTP_PASS=
        echo MAIL_SMTP_SECURE=tls
    ) > .env
    echo ✓ Fichier .env créé
    echo ⚠️  IMPORTANT: Modifiez les paramètres de base de données dans .env
) else (
    echo ✓ Fichier .env existe déjà
)

echo.
echo ==========================================
echo ✅ Prêt à l'installation
echo ==========================================
echo.
echo Prochaines étapes:
echo 1. Modifiez .env avec vos paramètres de BD
echo 2. Lancez un serveur PHP: php -S localhost:8000
echo 3. Accédez à: http://localhost:8000/admin/install-unified.php
echo.
pause
