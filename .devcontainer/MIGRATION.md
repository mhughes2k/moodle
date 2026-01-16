# Migration Summary: Docker Compose → Single Container Setup

## Files Modified

### 1. `.devcontainer/devcontainer.json`
**Changes:**
- ❌ Removed: `dockerComposeFile` and `service` properties
- ✅ Added: `build` configuration pointing to Dockerfile
- ✅ Added: MariaDB feature (`ghcr.io/devcontainers-contrib/features/mariadb-homebrew:1`)
- ✅ Added: `containerEnv` with all Moodle environment variables
- ✅ Added: `mounts` for persistent data volumes
- 🔧 Changed: `shutdownAction` from `stopCompose` to `stopContainer`
- 🔧 Changed: `remoteUser` from `root` to `www-data` (Apache user)

### 2. `.devcontainer/scripts/post-create.sh`
**Changes:**
- ✅ Added: MariaDB initialization and startup logic
- ✅ Added: Database creation and user setup
- ✅ Added: Wait logic for MariaDB to be ready
- 🔧 Updated: Uses localhost instead of container hostname

### 3. `.devcontainer/scripts/post-start.sh`
**Changes:**
- ✅ Added: MariaDB service check and restart if needed
- ✅ Simplified: Removed PostgreSQL logic (can be added back if needed)
- 🔧 Updated: Uses localhost for database connection

### 4. `.devcontainer/config.php`
**Changes:**
- 🔧 Changed: Default `$CFG->dbhost` from `'mariadb'` to `'localhost'`
- ❌ Removed: Mailpit SMTP configuration (service not available)
- ✅ Added: Comment about email configuration options

### 5. `.devcontainer/docker-compose.yml`
**Status:**
- 📦 Backed up to: `docker-compose.yml.backup`
- ℹ️ No longer used by devcontainer
- ℹ️ Can be used for local Docker development if needed

## New Files Created

### 1. `.devcontainer/CODESPACES.md`
- Complete documentation for Codespaces setup
- Troubleshooting guide
- Database access information
- Rebuild instructions

### 2. `.devcontainer/docker-compose.yml.backup`
- Backup of original multi-container setup
- Reference for future migrations
- Can be restored for local development

## Environment Changes

### Database Connection
| Before | After |
|--------|-------|
| Separate MariaDB container | MariaDB in same container (via Homebrew) |
| Host: `mariadb` | Host: `localhost` |
| Network: `moodle-network` | Network: not needed |

### Email Testing
| Before | After |
|--------|-------|
| Mailpit container available | Not available |
| SMTP: `mailpit:1025` | Disabled (configure external SMTP if needed) |

### Port Mapping
| Before | After |
|--------|-------|
| Docker Compose port mapping | Codespaces port forwarding |
| Fixed port 8080 | Dynamic Codespaces URL |

## How to Apply Changes

### Option A: Rebuild Codespace (Recommended)
```
1. Open Command Palette (Cmd/Ctrl + Shift + P)
2. Type: "Codespaces: Rebuild Container"
3. Select and confirm
4. Wait 5-10 minutes for rebuild
```

### Option B: Create New Codespace
```
1. Stop current Codespace
2. Create new Codespace from repository
3. Fresh build with new configuration
```

## What to Expect After Rebuild

1. **Container Build**: 5-10 minutes
   - Pull base image (moodlehq/moodle-php-apache:8.3)
   - Install system packages
   - Configure PHP extensions
   - Install features (Git, Node, MariaDB)

2. **Post-Create Hook**: 2-3 minutes
   - Initialize MariaDB
   - Create database and user
   - Install Composer dependencies
   - Install NPM dependencies
   - Set up config.php

3. **Ready to Use**:
   - Apache running on port 8080
   - MariaDB running on localhost:3306
   - Moodle accessible via Codespaces URL

## Testing the Setup

After rebuild, run these commands to verify:

```bash
# Check PHP version
php -v

# Check MariaDB is running
mysqladmin ping -h localhost

# Check database exists
mysql -u moodle -pmoodle_password -e "SHOW DATABASES;"

# Check Apache is running
ps aux | grep apache2

# Check Moodle directory
ls -la /workspace/public/
```

## Rollback Plan

If you need to revert to docker-compose (local dev only):

```bash
# Restore docker-compose.yml
mv .devcontainer/docker-compose.yml.backup .devcontainer/docker-compose.yml

# Edit devcontainer.json - change first few lines to:
{
  "name": "Moodle 5 Development",
  "dockerComposeFile": "docker-compose.yml",
  "service": "moodle",
  "workspaceFolder": "/workspace",
  ...
}

# Rebuild container
```

**Note**: Docker Compose setup will NOT work in GitHub Codespaces, only locally.

## Benefits of Single Container Setup

✅ **Codespaces Compatible**: Works seamlessly with GitHub Codespaces
✅ **Faster Startup**: Single container is quicker to build and start
✅ **Simpler Configuration**: Less complexity in networking and volumes
✅ **Better Resource Usage**: Shared resources, no separate containers
✅ **Easier Debugging**: All logs in one place

## Trade-offs

⚠️ **Less Isolation**: Database and web server share resources
⚠️ **No Email Testing**: Mailpit not available (use external service)
⚠️ **Different from Production**: Production typically uses separate DB server

## Next Steps

1. ✅ Rebuild container using Command Palette
2. ✅ Wait for post-create script to complete
3. ✅ Open port 8080 in browser
4. ✅ Complete Moodle installation
5. ✅ Start developing!

---

**Questions or Issues?**
- Check `.devcontainer/CODESPACES.md` for troubleshooting
- Review container logs during build
- Verify environment variables are set correctly
