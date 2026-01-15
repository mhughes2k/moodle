# Moodle 5 Devcontainer Configuration - Summary

## Overview

I've created a complete devcontainer configuration for Moodle 5 development based on:
- The LdesignMedia/moodle-codespace repository
- Official Moodle 5.0+ installation requirements
- Best practices for Moodle development environments

## What's Been Created

### Core Configuration Files

1. **`.devcontainer/devcontainer.json`**
   - Main devcontainer configuration
   - VS Code extensions for PHP, Docker, database tools
   - Port forwarding (8080 for web, 3306/5432 for databases)
   - Post-create and post-start scripts

2. **`.devcontainer/docker-compose.yml`**
   - Multi-service setup: Moodle, MariaDB, Mailpit
   - PostgreSQL option (commented out, easy to switch)
   - Persistent volumes for data directories
   - Environment variable configuration

3. **`.devcontainer/Dockerfile`**
   - Based on `moodlehq/moodle-php-apache:8.3`
   - PHP 8.3 (required for Moodle 5.0+)
   - All required PHP extensions
   - Xdebug 3.3 for debugging
   - Composer 2 pre-installed
   - Development tools and utilities

### Database Configuration

4. **`.devcontainer/mariadb-custom.cnf`**
   - UTF-8mb4 character set (required for Moodle)
   - Barracuda file format
   - Performance tuning for development

### Moodle Configuration

5. **`.devcontainer/config.php`**
   - Environment-based configuration
   - Works in both devcontainer and GitHub Codespaces
   - Debug mode enabled for development
   - PHPUnit and Behat pre-configured
   - Mailpit integration for email testing

### Helper Scripts

6. **`.devcontainer/scripts/post-create.sh`**
   - Runs once after container creation
   - Installs Composer/NPM dependencies
   - Sets up config.php
   - Fixes permissions

7. **`.devcontainer/scripts/post-start.sh`**
   - Runs every time container starts
   - Waits for database to be ready
   - Ensures proper permissions

8. **`.devcontainer/scripts/install-moodle.sh`**
   - Automated CLI installation
   - Creates admin user (admin/Admin123!)
   - Non-interactive installation

9. **`.devcontainer/scripts/fix-permissions.sh`**
   - Quick fix for permission issues
   - Useful when switching between host and container

### VS Code Configuration

10. **`.vscode/launch.json`**
    - Xdebug configuration
    - Path mappings for debugging

11. **`.vscode/settings.json`**
    - PHP settings optimized for Moodle
    - Search/file exclusions
    - Editor formatting rules

12. **`.devcontainer/README.md`**
    - Comprehensive documentation
    - Quick start guide
    - Troubleshooting tips

## Key Features

### ✅ Moodle 5 Requirements Met

- **PHP 8.3** (8.1-8.3 supported, 8.3 recommended)
- **Database**: MariaDB 11.2 or PostgreSQL 16
- **Required PHP Extensions**: All installed (GD, intl, ldap, mbstring, etc.)
- **Composer**: For dependency management
- **Node.js 20**: For Grunt/npm build tasks

### 🔧 Development Tools

- **Xdebug 3.3**: Fully configured for VS Code
- **Mailpit**: Email testing server (http://localhost:8025)
- **Git**: Pre-installed with GitHub CLI
- **VS Code Extensions**: PHP IntelliSense, Docker, database tools

### 🚀 Developer Experience

- **Automatic setup**: Dependencies install on container creation
- **Database ready**: MariaDB/PostgreSQL start automatically
- **Permission handling**: Scripts ensure proper file permissions
- **Hot reload**: Workspace mounted for instant code changes
- **Debugging ready**: Breakpoints work out of the box

## How to Use

### 1. Rebuild Container
```bash
# In VS Code Command Palette (Ctrl/Cmd + Shift + P)
Dev Containers: Rebuild Container
```

### 2. Wait for Setup
The container will:
- Build the Docker image
- Start services (Moodle, MariaDB, Mailpit)
- Run post-create scripts
- Install dependencies

### 3. Install Moodle

**Option A - Web Installer:**
- Open http://localhost:8080
- Follow the installation wizard
- Use database credentials from docker-compose.yml

**Option B - CLI Installer (Recommended):**
```bash
bash .devcontainer/scripts/install-moodle.sh
```

### 4. Start Developing!
- URL: http://localhost:8080
- Admin: admin / Admin123! (CLI install)
- Email testing: http://localhost:8025

## Comparison with LdesignMedia Setup

### Improvements Made

1. **Moodle 5 Support**
   - PHP 8.3 (vs 8.1 in original)
   - Updated dependencies
   - Modern Xdebug 3.3

2. **Better Database Support**
   - MariaDB 11.2 (vs unspecified)
   - PostgreSQL 16 option
   - Proper UTF-8mb4 configuration

3. **Enhanced Developer Experience**
   - Automated installation scripts
   - Mailpit for email testing
   - Pre-configured VS Code extensions
   - Better documentation

4. **Improved Structure**
   - Organized scripts directory
   - Comprehensive README
   - VS Code debugging configuration
   - GitHub Codespaces compatible

5. **Production-Ready Config**
   - Environment-based configuration
   - Proper security settings
   - Volume persistence
   - Health checks

## Database Options

### MariaDB (Default)
```yaml
MOODLE_DBTYPE: "mariadb"
MOODLE_DBHOST: "mariadb"
MOODLE_DBPORT: "3306"
```

### PostgreSQL (Alternative)
Edit `.devcontainer/docker-compose.yml`:
1. Comment out `mariadb` service
2. Uncomment `postgres` service
3. Update environment variables
4. Rebuild container

## Volumes and Data Persistence

All important data is stored in Docker volumes:
- `moodledata`: Uploaded files and generated content
- `phpunitdata`: PHPUnit test data
- `behatdata`: Behat test data
- `mariadb-data`: Database files
- `composer-cache`: Composer packages cache

These volumes persist across container rebuilds.

## Common Commands

```bash
# Purge caches
php public/admin/cli/purge_caches.php

# Run cron
php public/admin/cli/cron.php

# Database upgrade
php public/admin/cli/upgrade.php

# Initialize PHPUnit
php public/admin/tool/phpunit/cli/init.php

# Initialize Behat
php public/admin/tool/behat/cli/init.php

# Fix permissions
bash .devcontainer/scripts/fix-permissions.sh

# Frontend build
npx grunt
```

## Environment Variables

Key variables (set in docker-compose.yml):
- `MOODLE_DBTYPE`: Database type (mariadb/pgsql)
- `MOODLE_DBHOST`: Database hostname
- `MOODLE_DBNAME`: Database name
- `MOODLE_DBUSER`: Database username
- `MOODLE_DBPASSWORD`: Database password
- `MOODLE_WWWROOT`: Site URL
- `MOODLE_DEBUG`: Enable debug mode (true/false)

## Troubleshooting

### Permission Errors
```bash
bash .devcontainer/scripts/fix-permissions.sh
```

### Database Connection Failed
```bash
# Check if database is running
docker compose -f .devcontainer/docker-compose.yml ps

# View logs
docker compose -f .devcontainer/docker-compose.yml logs mariadb
```

### Container Won't Start
```bash
# Clean rebuild
docker compose -f .devcontainer/docker-compose.yml down -v
# Then rebuild in VS Code
```

### Can't Access Localhost:8080
- Check port forwarding in VS Code
- Ensure Apache is running in container
- Check firewall settings

## Security Notes

⚠️ **Development Only Configuration**

This setup is optimized for development and includes:
- Debug mode enabled
- Error display on
- Simple passwords
- Permissive file permissions

**DO NOT use this configuration in production!**

## Next Steps

After installation:
1. Complete Moodle setup wizard (web) or run CLI installer
2. Enable additional plugins as needed
3. Configure PHPUnit: `php public/admin/tool/phpunit/cli/init.php`
4. Configure Behat: `php public/admin/tool/behat/cli/init.php`
5. Install frontend dependencies: `npm install`
6. Build assets: `npx grunt`

## Resources

- **Moodle Docs**: https://docs.moodle.org/501/en/Installing_Moodle
- **Moodle Dev Docs**: https://moodledev.io/
- **PHP Requirements**: https://docs.moodle.org/501/en/PHP
- **Dev Containers**: https://code.visualstudio.com/docs/devcontainers/containers

## Support

For issues with:
- **Moodle**: Check https://moodle.org/course/
- **Devcontainer**: Check logs and rebuild
- **Database**: Verify credentials and connection
- **Permissions**: Run fix-permissions.sh script
