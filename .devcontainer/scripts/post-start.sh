#!/bin/bash
# # Post-start script for Moodle devcontainer
# # Runs every time the container starts

set -e

echo "🔄 Running post-start tasks..."

# # Set permissions on data directories
echo "🔐 Checking permissions..."
chown -R www-data:www-data /var/www/moodledata /var/www/phpunitdata /var/www/behatdata /var/www/behatfaildumps 2>/dev/null || true

echo "✅ Post-start tasks complete!"
