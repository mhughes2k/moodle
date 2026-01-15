#!/bin/bash
# Post-start script for Moodle devcontainer
# Runs every time the container starts

set -e

echo "🔄 Running post-start tasks..."

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
timeout=60
counter=0

if [ "$MOODLE_DBTYPE" = "mariadb" ] || [ "$MOODLE_DBTYPE" = "mysqli" ]; then
    while ! mysqladmin ping -h"$MOODLE_DBHOST" -u"$MOODLE_DBUSER" -p"$MOODLE_DBPASSWORD" --silent 2>/dev/null; do
        sleep 1
        counter=$((counter + 1))
        if [ $counter -ge $timeout ]; then
            echo "❌ Database connection timeout"
            exit 1
        fi
    done
    echo "✅ MariaDB is ready!"
elif [ "$MOODLE_DBTYPE" = "pgsql" ]; then
    while ! pg_isready -h "$MOODLE_DBHOST" -p "$MOODLE_DBPORT" -U "$MOODLE_DBUSER" 2>/dev/null; do
        sleep 1
        counter=$((counter + 1))
        if [ $counter -ge $timeout ]; then
            echo "❌ Database connection timeout"
            exit 1
        fi
    done
    echo "✅ PostgreSQL is ready!"
fi

# Set permissions on data directories
echo "🔐 Checking permissions..."
chown -R www-data:www-data /var/www/moodledata /var/www/phpunitdata /var/www/behatdata /var/www/behatfaildumps 2>/dev/null || true

echo "✅ Post-start tasks complete!"
