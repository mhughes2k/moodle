#!/bin/bash
# Post-create script for Moodle devcontainer
# Runs once after the container is created

set -e

echo "🚀 Running post-create setup for Moodle 5..."

# Change to workspace directory
cd /workspace

# Configure Git to trust this directory (fixes ownership warnings in dev containers)
echo "🔧 Configuring Git safe directory..."
git config --global --add safe.directory /workspace || true

# Moodle is in the public/ directory
MOODLE_DIR="/workspace/public"
echo "📁 Using Moodle directory: $MOODLE_DIR"

# Install Composer dependencies if composer.json exists
if [ -f "/workspace/composer.json" ]; then
    echo "📦 Installing Composer dependencies..."
    cd /workspace
    composer install --no-interaction --prefer-dist || true
fi

# Install NPM dependencies if package.json exists
if [ -f "/workspace/package.json" ]; then
    echo "📦 Installing NPM dependencies..."
    cd /workspace
    npm install || true
fi

# Create config.php if it doesn't exist in public/
if [ ! -f "$MOODLE_DIR/../config.php" ]; then
    echo "⚙️  Creating Moodle config.php..."
    cp /workspace/.devcontainer/config.php "$MOODLE_DIR/../config.php"
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/moodledata /var/www/phpunitdata /var/www/behatdata /var/www/behatfaildumps || true
chmod -R 0777 /var/www/moodledata /var/www/phpunitdata /var/www/behatdata /var/www/behatfaildumps || true

echo "✅ Post-create setup complete!"
echo ""
echo "📝 Next steps:"
echo "   1. Wait for the database to be ready"
echo "   2. Open http://localhost:8080 in your browser"
echo "   3. Complete the Moodle installation wizard"
echo ""
echo "💡 Useful commands:"
echo "   - Install Moodle CLI: php $MOODLE_DIR/admin/cli/install.php --help"
echo "   - Run PHPUnit: php $MOODLE_DIR/admin/tool/phpunit/cli/init.php"
echo "   - Run Behat: php $MOODLE_DIR/admin/tool/behat/cli/init.php"
echo "   - Purge caches: php $MOODLE_DIR/admin/cli/purge_caches.php"
echo ""
