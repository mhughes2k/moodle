#!/bin/bash
# Script to install Moodle via CLI
# Usage: bash .devcontainer/scripts/install-moodle.sh

set -e

echo "🎓 Installing Moodle via CLI..."

# Determine Moodle directory
if [ -d "/workspace/public" ]; then
    MOODLE_DIR="/workspace/public"
else
    MOODLE_DIR="/workspace"
fi

# Check if Moodle is already installed
if [ -f "$MOODLE_DIR/config.php" ]; then
    echo "⚠️  Moodle config.php already exists. Checking if installed..."
    
    # Try to access database - if it works, Moodle is likely installed
    if php "$MOODLE_DIR/admin/cli/check_database_schema.php" 2>/dev/null; then
        echo "✅ Moodle appears to be already installed!"
        exit 0
    fi
fi

# Install Moodle
php "$MOODLE_DIR/admin/cli/install.php" \
    --lang=en \
    --wwwroot="${MOODLE_WWWROOT:-http://localhost:8080}" \
    --dataroot="${MOODLE_DATAROOT:-/var/www/moodledata}" \
    --dbtype="${MOODLE_DBTYPE:-mariadb}" \
    --dbhost="${MOODLE_DBHOST:-mariadb}" \
    --dbname="${MOODLE_DBNAME:-moodle}" \
    --dbuser="${MOODLE_DBUSER:-moodle}" \
    --dbpass="${MOODLE_DBPASSWORD:-moodle_password}" \
    --dbport="${MOODLE_DBPORT:-3306}" \
    --prefix=mdl_ \
    --fullname="Moodle Development" \
    --shortname="mdev" \
    --adminuser=admin \
    --adminpass=Admin123! \
    --adminemail=admin@example.com \
    --non-interactive \
    --agree-license

echo ""
echo "✅ Moodle installed successfully!"
echo ""
echo "🔑 Login credentials:"
echo "   Username: admin"
echo "   Password: Admin123!"
echo ""
echo "🌐 Access Moodle at: ${MOODLE_WWWROOT:-http://localhost:8080}"
echo ""
