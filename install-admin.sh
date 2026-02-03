#!/bin/bash
# Script d'installation du système admin unifié pour Linux/Mac

echo "=========================================="
echo "Installation du système Admin Unifié"
echo "=========================================="
echo ""

# Vérifier PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé"
    exit 1
fi

echo "✓ PHP détecté: $(php -v | head -n 1)"
echo ""

# Vérifier MySQL
if ! command -v mysql &> /dev/null; then
    echo "⚠️  MySQL CLI non trouvé (non critique si vous utilisez PHPMyAdmin)"
fi

# Créer le fichier .env s'il n'existe pas
if [ ! -f ".env" ]; then
    echo "📝 Création du fichier .env..."
    cat > .env << 'EOF'
APP_URL=http://localhost
APP_NAME=Educations Plurielles
DB_HOST=localhost
DB_NAME=educations_plurielles
DB_USER=root
DB_PASS=
MAIL_FROM=admin@exemple.com
MAIL_FROM_NAME=Admin
MAIL_SMTP_HOST=
MAIL_SMTP_PORT=587
MAIL_SMTP_USER=
MAIL_SMTP_PASS=
MAIL_SMTP_SECURE=tls
EOF
    echo "✓ Fichier .env créé"
    echo "⚠️  IMPORTANT: Modifiez les paramètres de base de données dans .env"
else
    echo "✓ Fichier .env existe déjà"
fi

echo ""
echo "=========================================="
echo "✅ Prêt à l'installation"
echo "=========================================="
echo ""
echo "Prochaines étapes:"
echo "1. Modifiez .env avec vos paramètres de BD"
echo "2. Lancez un serveur PHP: php -S localhost:8000"
echo "3. Accédez à: http://localhost:8000/admin/install-unified.php"
echo ""
