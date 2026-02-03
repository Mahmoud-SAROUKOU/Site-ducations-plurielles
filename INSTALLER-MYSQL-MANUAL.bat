@echo off
chcp 65001 > nul
echo ========================================
echo   INSTALLATION MySQL POUR WINDOWS
echo ========================================
echo.
echo OPTION 1: XAMPP (Recommandé - Inclut MySQL + phpMyAdmin)
echo   - Téléchargez: https://www.apachefriends.org/download.html
echo   - Version: XAMPP 8.2.12 pour Windows
echo   - Installation: Suivez l'assistant, installez dans C:\xampp
echo   - Après installation: Lancez XAMPP Control Panel
echo   - Démarrez les services: Apache et MySQL
echo.
echo OPTION 2: MySQL Community Server (MySQL seul)
echo   - Téléchargez: https://dev.mysql.com/downloads/installer/
echo   - Choisissez: mysql-installer-community-8.0.40.0.msi
echo   - Installation: "Developer Default" ou "Server only"
echo   - Configuration: Root password (ex: root123)
echo.
echo ========================================
echo APRÈS INSTALLATION:
echo ========================================
echo.
echo 1. Si XAMPP:
echo    - Ouvrez XAMPP Control Panel
echo    - Cliquez "Start" pour MySQL
echo    - Cliquez "Admin" pour phpMyAdmin
echo    - Par défaut: user=root, password=(vide)
echo.
echo 2. Si MySQL seul:
echo    - Ouvrez MySQL Workbench
echo    - Connectez-vous avec root
echo.
echo 3. Créer la base de données:
echo    - Ouvrez phpMyAdmin ou MySQL Workbench
echo    - Créez une base: educations_plurielles
echo    - Charset: utf8mb4_general_ci
echo.
echo 4. Configurer votre projet:
echo    - Modifiez admin/config.php ou créez .env
echo    - Définissez USE_SQLITE=false
echo    - Renseignez DB_HOST, DB_NAME, DB_USER, DB_PASS
echo.
echo ========================================
echo Appuyez sur une touche pour fermer...
pause > nul
