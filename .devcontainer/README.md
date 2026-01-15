# Moodle 5 Devcontainer

This devcontainer provides a complete development environment for Moodle 5.x with all necessary dependencies.

## Features

- **PHP 8.3** with Apache
- **MariaDB 11.2** (or PostgreSQL 16 - configurable)
- **Node.js 20** for frontend builds
- **Composer** for PHP dependencies
- **Xdebug 3.3** for debugging
- **Mailpit** for email testing
- Pre-configured VS Code extensions for PHP, Docker, and Moodle development

## Quick Start

1. **Open in Dev Container**
   - VS Code will prompt to reopen in container
   - Or use Command Palette: "Dev Containers: Reopen in Container"

2. **Wait for Setup**
   - Container builds and dependencies install automatically
   - Database initializes in the background

3. **Install Moodle**
   
   Option A - Web Installer:
   ```bash
   # Open http://localhost:8080 in your browser
   # Follow the installation wizard
   ```
   
   Option B - CLI Installer:
   ```bash
   bash .devcontainer/scripts/install-moodle.sh
   ```

4. **Access Moodle**
   - URL: http://localhost:8080
   - Username: admin
   - Password: Admin123! (if using CLI installer)

## Database Options

### MariaDB (Default)
Already configured and ready to use.

### PostgreSQL
To use PostgreSQL instead:
1. Edit `.devcontainer/docker-compose.yml`:
   - Comment out the `mariadb` service
   - Uncomment the `postgres` service
   - Update environment variables in the `moodle` service
2. Rebuild the container

## Useful Commands

### Moodle CLI
```bash
# Purge caches
php public/admin/cli/purge_caches.php

# Run cron
php public/admin/cli/cron.php

# Upgrade database
php public/admin/cli/upgrade.php

# Install Moodle
php public/admin/cli/install.php --help
```

### Testing

```bash
# Initialize PHPUnit
php public/admin/tool/phpunit/cli/init.php

# Run PHPUnit tests
vendor/bin/phpunit --testdox

# Initialize Behat
php public/admin/tool/behat/cli/init.php

# Run Behat tests
vendor/bin/behat
```

### Frontend Development

```bash
# Install dependencies
npm install

# Run Grunt tasks
npx grunt

# Watch for changes
npx grunt watch
```

### Database Access

```bash
# MariaDB
mysql -h mariadb -u moodle -pmoodle_password moodle

# PostgreSQL (if configured)
psql -h postgres -U moodle moodle
```

## Debugging with Xdebug

Xdebug is pre-configured and ready to use:

1. Set breakpoints in VS Code
2. Start debugging (F5 or Debug panel)
3. Load a page in your browser
4. VS Code will pause at your breakpoints

Configuration in `.vscode/launch.json`:
```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
        "/workspace": "${workspaceFolder}"
      }
    }
  ]
}
```

## Email Testing with Mailpit

Access the Mailpit web interface:
- URL: http://localhost:8025
- All emails sent by Moodle appear here
- No configuration needed in Moodle

## Troubleshooting

### Permission Issues
```bash
bash .devcontainer/scripts/fix-permissions.sh
```

### Database Connection Issues
```bash
# Check if database is running
docker compose -f .devcontainer/docker-compose.yml ps

# Check logs
docker compose -f .devcontainer/docker-compose.yml logs mariadb
```

### Clear Everything and Start Fresh
```bash
# From outside the container
docker compose -f .devcontainer/docker-compose.yml down -v
# Rebuild container in VS Code
```

## Directory Structure

```
.devcontainer/
├── devcontainer.json       # Main devcontainer configuration
├── docker-compose.yml      # Docker services definition
├── Dockerfile             # PHP/Apache container image
├── mariadb-custom.cnf     # MariaDB configuration
├── config.php             # Moodle config template
├── scripts/
│   ├── post-create.sh     # Runs after container creation
│   ├── post-start.sh      # Runs on container start
│   ├── install-moodle.sh  # CLI installer script
│   └── fix-permissions.sh # Permission fix utility
└── README.md              # This file
```

## Customization

### PHP Configuration
Edit `Dockerfile` and rebuild:
- Memory limit
- Max execution time
- Upload limits

### Database Configuration
Edit `docker-compose.yml`:
- Database credentials
- Character set
- Performance settings

### VS Code Settings
Edit `devcontainer.json`:
- Add/remove extensions
- Modify editor settings
- Configure debugging

## Requirements

- Docker Desktop or Docker Engine
- VS Code with "Dev Containers" extension
- 4GB RAM minimum (8GB recommended)
- 10GB disk space

## Support

For Moodle-specific issues:
- Documentation: https://docs.moodle.org/
- Forums: https://moodle.org/course/

For devcontainer issues:
- Check Docker logs
- Rebuild container
- Check system requirements
