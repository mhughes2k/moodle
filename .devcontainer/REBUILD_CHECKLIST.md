# ✅ Rebuild Checklist

Use this checklist when rebuilding your Codespace with the new single-container configuration.

## Pre-Rebuild

- [ ] Commit any important local changes
- [ ] Review [MIGRATION.md](MIGRATION.md) to understand changes
- [ ] Note current Codespace name (for reference)

## Rebuild Process

- [ ] Open Command Palette (`Cmd/Ctrl + Shift + P`)
- [ ] Type and select: **"Codespaces: Rebuild Container"**
- [ ] Confirm rebuild action
- [ ] Wait 5-10 minutes for container to rebuild

## Post-Rebuild Verification

### Automatic (Should happen automatically)
- [ ] Container built successfully
- [ ] Post-create script ran
- [ ] MariaDB initialized
- [ ] Database and user created
- [ ] Composer dependencies installed
- [ ] NPM packages installed
- [ ] config.php copied to public/

### Manual Verification

Run the verification script:
```bash
bash .devcontainer/scripts/verify-setup.sh
```

Or check manually:

- [ ] PHP is available: `php -v`
- [ ] Node.js is available: `node -v`
- [ ] Composer is available: `composer -V`
- [ ] MariaDB is running: `pgrep mariadbd`
- [ ] Apache is running: `pgrep apache2`
- [ ] Database exists: `mysql -u moodle -pmoodle_password -e "SHOW DATABASES;"`
- [ ] Moodle files exist: `ls -la /workspace/public/`
- [ ] Config exists: `ls -la /workspace/public/config.php`
- [ ] Data directories exist: `ls -la /var/www/moodledata`

## Access Moodle

- [ ] Go to **Ports** tab in VS Code
- [ ] Find port **8080**
- [ ] Click the **globe icon** to open in browser
- [ ] Verify Moodle loads (installation wizard or site)

## If Issues Occur

### MariaDB not running
```bash
brew services start mariadb
mysqladmin ping -h localhost
```

### Apache not running
```bash
sudo apachectl start
ps aux | grep apache2
```

### Database connection fails
```bash
# Check MariaDB status
brew services list

# Test connection
mysql -u root -e "SHOW DATABASES;"

# Recreate database if needed
mysql -u root <<EOF
DROP DATABASE IF EXISTS moodle;
CREATE DATABASE moodle DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP USER IF EXISTS 'moodle'@'localhost';
CREATE USER 'moodle'@'localhost' IDENTIFIED BY 'moodle_password';
GRANT ALL PRIVILEGES ON moodle.* TO 'moodle'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### Config.php missing
```bash
cp .devcontainer/config.php public/config.php
```

### Permissions issues
```bash
sudo chown -R www-data:www-data /var/www/moodledata
sudo chmod -R 0777 /var/www/moodledata
```

### Complete failure - Re-run post-create
```bash
bash .devcontainer/scripts/post-create.sh
```

## Complete Moodle Installation

If Moodle is not yet installed:

### Option 1: Web Installer (Recommended)
- [ ] Open Moodle in browser (port 8080)
- [ ] Follow installation wizard
- [ ] Use these database settings:
  - Database type: **MariaDB**
  - Host: **localhost**
  - Database: **moodle**
  - User: **moodle**
  - Password: **moodle_password**

### Option 2: CLI Installer
```bash
php public/admin/cli/install.php \
  --lang=en \
  --wwwroot=https://$(echo $CODESPACE_NAME)-8080.app.github.dev \
  --dataroot=/var/www/moodledata \
  --dbtype=mariadb \
  --dbhost=localhost \
  --dbname=moodle \
  --dbuser=moodle \
  --dbpass=moodle_password \
  --fullname="Moodle Dev Site" \
  --shortname="dev" \
  --adminuser=admin \
  --adminpass=Admin123! \
  --adminemail=admin@example.com \
  --agree-license \
  --non-interactive
```

## Post-Installation

- [ ] Log in to Moodle admin panel
- [ ] Configure site settings as needed
- [ ] Test creating a course
- [ ] Test file uploads
- [ ] Check error logs are accessible

## Development Ready

- [ ] VS Code extensions installed
- [ ] Xdebug configured (if needed)
- [ ] Git configured
- [ ] Ready to code!

## Success Criteria

✅ All checkboxes above are checked  
✅ Moodle loads in browser  
✅ Database connects successfully  
✅ Can log in to admin panel  
✅ No critical errors in logs  

---

## If All Else Fails

1. **Delete Codespace** and create a new one
2. **Check creation logs** in `/workspaces/.codespaces/.persistedshare/creation.log`
3. **Review error messages** during build
4. **Open an issue** if configuration problems persist

---

## Next Steps After Success

- [ ] Read [QUICK_REFERENCE.md](QUICK_REFERENCE.md) for common commands
- [ ] Review [CODESPACES.md](CODESPACES.md) for detailed documentation
- [ ] Set up Xdebug for PHP debugging
- [ ] Install additional VS Code extensions as needed
- [ ] Start developing your Moodle plugin or customization!

---

**Time to rebuild?** Open Command Palette → "Codespaces: Rebuild Container"
