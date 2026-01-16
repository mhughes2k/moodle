# 🚀 Quick Reference - Moodle Devcontainer

## Rebuild Container

**Command Palette** → `Codespaces: Rebuild Container`

---

## Database Access

```bash
# Connect to MySQL
mysql -u moodle -pmoodle_password moodle

# Connect as root
mysql -u root

# Test connection
mysqladmin ping -h localhost
```

**Connection Details:**
- Host: `localhost`
- Port: `3306`
- Database: `moodle`
- User: `moodle`
- Password: `moodle_password`

---

## Service Management

```bash
# MariaDB
brew services start mariadb
brew services stop mariadb
brew services restart mariadb
brew services list

# Apache
sudo apachectl start
sudo apachectl stop
sudo apachectl restart
sudo apachectl -t  # Test config
```

---

## Verification

```bash
# Run verification script
bash .devcontainer/scripts/verify-setup.sh

# Check services manually
pgrep -x mariadbd    # Check MariaDB
pgrep -x apache2     # Check Apache
ps aux | grep mysql  # See MySQL processes
ps aux | grep apache # See Apache processes
```

---

## Common Tasks

```bash
# Purge Moodle caches
php public/admin/cli/purge_caches.php

# Run Moodle CLI installer
php public/admin/cli/install.php --help

# Initialize PHPUnit
php public/admin/tool/phpunit/cli/init.php

# Initialize Behat
php public/admin/tool/behat/cli/init.php

# Fix permissions
sudo chown -R www-data:www-data /var/www/moodledata
sudo chmod -R 0777 /var/www/moodledata
```

---

## Logs

```bash
# Apache logs
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/access.log

# Xdebug log
tail -f /tmp/xdebug.log

# Moodle logs (if configured)
tail -f /var/www/moodledata/moodle.log
```

---

## Port Forwarding

| Port | Service | Access |
|------|---------|--------|
| 8080 | Moodle Web | Ports tab → Globe icon |
| 3306 | MariaDB | localhost:3306 |

---

## Environment Variables

Set in `devcontainer.json` → `containerEnv`:

```json
MOODLE_DBTYPE=mariadb
MOODLE_DBHOST=localhost
MOODLE_DBNAME=moodle
MOODLE_DBUSER=moodle
MOODLE_DBPASSWORD=moodle_password
MOODLE_WWWROOT=http://localhost:8080
```

---

## File Locations

```
/workspace/              # Your repository
/workspace/public/       # Moodle installation
/workspace/.devcontainer/ # Container config
/var/www/moodledata/     # Moodle data files
/var/www/phpunitdata/    # PHPUnit data
/var/www/behatdata/      # Behat data
```

---

## Troubleshooting

### Database won't start
```bash
brew services restart mariadb
mysqladmin ping -h localhost
```

### Apache won't start
```bash
sudo apachectl -t          # Test config
sudo apachectl restart     # Restart
sudo tail /var/log/apache2/error.log  # Check logs
```

### Can't access Moodle
1. Check Apache is running: `pgrep apache2`
2. Check port 8080 is forwarded in Ports tab
3. Click globe icon to open in browser

### Permission denied
```bash
sudo chown -R www-data:www-data /var/www/moodledata
sudo chmod -R 0777 /var/www/moodledata
```

---

## Useful VS Code Commands

- `Ctrl/Cmd + Shift + P` - Command Palette
- `Ctrl/Cmd + ` ` - Toggle Terminal
- `Ctrl/Cmd + Shift + E` - Explorer
- `Ctrl/Cmd + Shift + F` - Search
- `Ctrl/Cmd + Shift + D` - Debug

---

## Documentation

- 📘 [CODESPACES.md](.devcontainer/CODESPACES.md) - Full Codespaces guide
- 📘 [MIGRATION.md](.devcontainer/MIGRATION.md) - Migration details
- 📘 [Moodle Dev Docs](https://moodledev.io/)

---

## Need Help?

1. Check logs (Apache, MariaDB, Xdebug)
2. Run verification script
3. Review CODESPACES.md
4. Rebuild container if needed
