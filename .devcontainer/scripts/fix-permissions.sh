#!/bin/bash
# Fix permissions for Moodle data directories
# Run this if you encounter permission issues

echo "🔧 Fixing Moodle permissions..."

chown -R www-data:www-data /var/www/moodledata
chown -R www-data:www-data /var/www/phpunitdata
chown -R www-data:www-data /var/www/behatdata
chown -R www-data:www-data /var/www/behatfaildumps

chmod -R 0777 /var/www/moodledata
chmod -R 0777 /var/www/phpunitdata
chmod -R 0777 /var/www/behatdata
chmod -R 0777 /var/www/behatfaildumps

echo "✅ Permissions fixed!"
