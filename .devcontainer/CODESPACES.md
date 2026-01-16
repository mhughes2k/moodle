# GitHub Codespaces Setup for Moodle

This devcontainer has been configured for GitHub Codespaces compatibility using a single-container setup.

## Changes Made

### Configuration Updates
- **Removed**: `docker-compose.yml` dependency
- **Added**: Single-container setup with MariaDB as a feature
- **Updated**: Database connection to use `localhost` instead of separate container

### What's Different from Multi-Container Setup

1. **Database**: MariaDB runs in the same container as Moodle (via Homebrew feature)
2. **Email**: Mailpit is not available (emails won't be sent in development)
3. **Volumes**: Using named volumes for persistence
4. **Networking**: All services on localhost

## How to Rebuild

If you're in a recovery container or need to rebuild:

1. **Open Command Palette** (Cmd/Ctrl + Shift + P)
2. **Run**: "Codespaces: Rebuild Container"
3. Wait for the container to rebuild (5-10 minutes)

## Database Access

- **Host**: `localhost`
- **Port**: `3306`
- **Database**: `moodle`
- **Username**: `moodle`
- **Password**: `moodle_password`
- **Root Password**: (none - passwordless root for development)

## Testing Database Connection

```bash
# Test MySQL connection
mysqladmin ping -h localhost

# Connect to MySQL
mysql -u moodle -pmoodle_password moodle

# Connect as root
mysql -u root
```

## Accessing Moodle

Once the container is built and services are running:

1. The devcontainer will forward port 8080
2. Click on the "Ports" tab in VS Code
3. Click the globe icon next to port 8080 to open Moodle in your browser
4. Complete the Moodle installation wizard

The URL will be something like:
```
https://[codespace-name]-8080.app.github.dev
```

## Starting/Stopping MariaDB

MariaDB is managed by Homebrew services:

```bash
# Check status
brew services list

# Start MariaDB
brew services start mariadb

# Stop MariaDB
brew services stop mariadb

# Restart MariaDB
brew services restart mariadb
```

## Troubleshooting

### Container Started in Alpine Recovery Mode

If you see Alpine Linux and a basic shell:
1. The original build failed
2. Run "Codespaces: Rebuild Container" from Command Palette
3. Check the creation logs for errors

### Database Connection Failed

```bash
# Check if MariaDB is running
pgrep -x mariadbd || pgrep -x mysqld

# If not running, start it
brew services start mariadb

# Wait a moment, then test
mysqladmin ping -h localhost
```

### Permissions Issues

```bash
# Fix data directory permissions
sudo chown -R www-data:www-data /var/www/moodledata
sudo chmod -R 0777 /var/www/moodledata
```

### Port 8080 Not Working

1. Check if Apache is running: `ps aux | grep apache2`
2. Restart Apache: `sudo apachectl restart`
3. Check Apache logs: `sudo tail -f /var/log/apache2/error.log`

## Features vs Docker Compose

### Why Features?

GitHub Codespaces has better support for single-container setups using features:

- ✅ Faster startup times
- ✅ Better resource management
- ✅ Simpler networking
- ✅ Better compatibility with Codespaces infrastructure

### Trade-offs

- ❌ No separate database container (less isolated)
- ❌ No Mailpit for email testing
- ❌ All services share container resources

## For Local Development

If you want to use this configuration locally with VS Code Dev Containers:

1. Install Docker Desktop
2. Install "Dev Containers" extension in VS Code
3. Open folder in container
4. The setup works the same way

## Reverting to Docker Compose (Local Only)

If you want to use docker-compose locally (NOT compatible with Codespaces):

1. Rename `docker-compose.yml.backup` to `docker-compose.yml` (if you kept a backup)
2. Update `devcontainer.json`:
   ```json
   {
     "name": "Moodle 5 Development",
     "dockerComposeFile": "docker-compose.yml",
     "service": "moodle",
     "workspaceFolder": "/workspace",
     // ... rest of config
   }
   ```
3. Remove the MariaDB feature from the features section
4. Rebuild container

## Additional Resources

- [Dev Containers Documentation](https://containers.dev/)
- [GitHub Codespaces Docs](https://docs.github.com/en/codespaces)
- [Moodle Developer Documentation](https://moodledev.io/)
