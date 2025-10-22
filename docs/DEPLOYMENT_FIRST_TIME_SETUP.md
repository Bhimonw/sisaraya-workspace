# 🚀 First Time Deployment Setup

## Prerequisites Check

Before automatic deployment can work, you need to manually setup the server **once**.

## Step 1: SSH to Production Server

```bash
ssh root@[YOUR_SERVER_IP]
# or
ssh root@srv1045082
```

## Step 2: Check if Project Directory Exists

```bash
ls -la ~/projects/
```

**If `sisaraya-workspace` doesn't exist, continue to Step 3.**

## Step 3: Clone Repository to Server

```bash
# Create projects directory
mkdir -p ~/projects
cd ~/projects

# Clone repository (you'll need GitHub credentials or SSH key)
git clone https://github.com/Bhimonw/sisaraya-workspace.git
cd sisaraya-workspace

# Verify
pwd
# Should show: /root/projects/sisaraya-workspace
```

## Step 4: Install Dependencies

```bash
cd ~/projects/sisaraya-workspace

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm ci
npm run build
```

## Step 5: Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Edit with production settings
nano .env
```

**Important `.env` settings:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

## Step 6: Generate Keys & Run Migrations

```bash
# Generate application key
php artisan key:generate

# Generate VAPID keys for web push
php artisan webpush:vapid

# Run migrations
php artisan migrate --seed

# Optimize
php artisan optimize
```

## Step 7: Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Step 8: Configure Web Server

**For Nginx:**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /root/projects/sisaraya-workspace/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**For Apache (.htaccess already included):**

Ensure `DocumentRoot` points to `/root/projects/sisaraya-workspace/public`

## Step 9: Test Application

```bash
# Test if application runs
php artisan serve --host=0.0.0.0 --port=8000

# In another terminal or browser:
curl http://your-server-ip:8000
```

## Step 10: Verify GitHub Actions Can Access

```bash
# Test git pull works
cd ~/projects/sisaraya-workspace
git fetch origin
git status
```

## ✅ After First Time Setup

Once steps 1-10 are complete, **automatic deployment will work**:

1. Every push to `main` branch will trigger deployment
2. GitHub Actions will:
   - SSH to your server
   - Pull latest code
   - Install dependencies
   - Build assets
   - Run migrations
   - Optimize caches
   - Restart services

## 🧪 Test Automatic Deployment

After setup, push a small change:

```bash
# Make a small change
echo "# Test deployment" >> README.md
git add .
git commit -m "Test automatic deployment"
git push origin main
```

Then check GitHub Actions:
- Go to: https://github.com/Bhimonw/sisaraya-workspace/actions
- Watch the "Deploy to Production" workflow

## 🐛 Troubleshooting

### Deployment Still Not Running?

**Check 1: Verify Secrets Are Configured**
```
GitHub → Settings → Secrets and variables → Actions
```
Should have:
- ✅ SERVER_IP
- ✅ SERVER_USER
- ✅ SSH_PRIVATE_KEY

**Check 2: Test SSH Manually**
```bash
ssh -i ~/.ssh/your_key root@your-server-ip
```

**Check 3: Check Workflow Logs**
```
GitHub → Actions tab → Click on latest workflow run
```

**Check 4: Verify Git Clone Used HTTPS or SSH**
```bash
# On server
cd ~/projects/sisaraya-workspace
git remote -v
```

If using SSH, ensure server has GitHub deploy key.
If using HTTPS, ensure credentials are cached or use token.

### Common Errors

**Error: `cd: ~/projects/sisaraya-workspace: No such file or directory`**
- **Solution:** Complete Step 3 (clone repository)

**Error: `Permission denied (publickey)`**
- **Solution:** Verify SSH_PRIVATE_KEY secret matches key on server

**Error: `composer: command not found`**
- **Solution:** Install Composer on server: https://getcomposer.org/download/

**Error: `npm: command not found`**
- **Solution:** Install Node.js: `curl -fsSL https://deb.nodesource.com/setup_20.x | bash -`

## 📞 Need Help?

If deployment still not working after these steps, check:
1. GitHub Actions logs (detailed error messages)
2. Server logs: `/var/log/nginx/error.log` or `/var/log/apache2/error.log`
3. Laravel logs: `storage/logs/laravel.log`

---

**Last Updated:** 2025-10-22
**Status:** Ready for First Time Setup
