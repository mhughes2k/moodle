#!/bin/bash
# Quick verification script for Moodle devcontainer setup
# Run this after container rebuild to verify everything is working

echo "🔍 Moodle Devcontainer Verification"
echo "===================================="
echo ""

# Check PHP
echo "📌 PHP Version:"
php -v | head -n 1
echo ""

# Check MariaDB
echo "📌 MariaDB Status:"
if pgrep -x "mariadbd" > /dev/null || pgrep -x "mysqld" > /dev/null; then
    echo "✅ MariaDB is running"
    if mysqladmin ping -h localhost --silent 2>/dev/null; then
        echo "✅ MariaDB is responding to connections"
        
        # Check database
        if mysql -u moodle -pmoodle_password -e "USE moodle;" 2>/dev/null; then
            echo "✅ Moodle database exists and is accessible"
        else
            echo "❌ Moodle database not accessible"
        fi
    else
        echo "⚠️ MariaDB is running but not responding"
    fi
else
    echo "❌ MariaDB is not running"
    echo "   Run: brew services start mariadb"
fi
echo ""

# Check Apache
echo "📌 Apache Status:"
if pgrep -x "apache2" > /dev/null; then
    echo "✅ Apache is running"
else
    echo "❌ Apache is not running"
    echo "   Run: sudo apachectl start"
fi
echo ""

# Check Moodle directory
echo "📌 Moodle Files:"
if [ -d "/workspace/public" ]; then
    echo "✅ Moodle directory exists: /workspace/public"
    if [ -f "/workspace/public/config.php" ]; then
        echo "✅ config.php exists"
    else
        echo "⚠️ config.php not found - run post-create script or copy from .devcontainer/"
    fi
    if [ -f "/workspace/public/index.php" ]; then
        echo "✅ Moodle core files present"
    else
        echo "❌ Moodle core files missing"
    fi
else
    echo "❌ Moodle directory not found"
fi
echo ""

# Check data directories
echo "📌 Data Directories:"
for dir in moodledata phpunitdata behatdata behatfaildumps; do
    if [ -d "/var/www/$dir" ]; then
        echo "✅ /var/www/$dir exists"
    else
        echo "⚠️ /var/www/$dir missing"
    fi
done
echo ""

# Check Node.js
echo "📌 Node.js:"
if command -v node > /dev/null; then
    echo "✅ Node.js $(node -v)"
else
    echo "❌ Node.js not installed"
fi
echo ""

# Check Composer
echo "📌 Composer:"
if command -v composer > /dev/null; then
    echo "✅ Composer $(composer -V | cut -d' ' -f3)"
else
    echo "❌ Composer not installed"
fi
echo ""

# Summary
echo "===================================="
echo "📊 Summary"
echo "===================================="

ALL_GOOD=true

if ! (pgrep -x "mariadbd" > /dev/null || pgrep -x "mysqld" > /dev/null); then
    ALL_GOOD=false
    echo "⚠️ Start MariaDB: brew services start mariadb"
fi

if ! pgrep -x "apache2" > /dev/null; then
    ALL_GOOD=false
    echo "⚠️ Start Apache: sudo apachectl start"
fi

if [ ! -f "/workspace/public/config.php" ]; then
    ALL_GOOD=false
    echo "⚠️ Copy config: cp .devcontainer/config.php public/config.php"
fi

if $ALL_GOOD; then
    echo ""
    echo "✅ All systems operational!"
    echo ""
    echo "🚀 Next steps:"
    echo "   1. Open Ports tab and click globe icon next to port 8080"
    echo "   2. Complete Moodle installation wizard"
    echo "   3. Start developing!"
else
    echo ""
    echo "⚠️ Some issues need attention (see above)"
    echo ""
    echo "🔧 Quick fix:"
    echo "   bash .devcontainer/scripts/post-create.sh"
fi
echo ""
