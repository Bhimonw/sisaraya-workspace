# CI/CD Configuration Guide

## Overview

SISARAYA Workspace menggunakan GitHub Actions untuk automated testing dan deployment. Ada 3 workflow utama:

1. **Tests** - Automated testing pada setiap push/PR
2. **Code Quality** - Code quality checks dan security audit
3. **Deploy** - Automated deployment ke production server

## Workflows

### 1. Tests Workflow (`.github/workflows/tests.yml`)

**Trigger:**
- Push ke branch `main` atau `develop`
- Pull request ke branch `main` atau `develop`

**Apa yang dilakukan:**
- Setup PHP 8.4 dengan ekstensi yang diperlukan
- Setup MySQL 8.0 service container
- Install Composer dependencies
- Generate application key dan VAPID keys
- Run database migrations dan seeders
- Execute PHPUnit tests

**Services:**
- MySQL 8.0 untuk testing database

### 2. Code Quality Workflow (`.github/workflows/code-quality.yml`)

**Trigger:**
- Push ke branch `main` atau `develop`
- Pull request ke branch `main` atau `develop`

**Checks:**
- PHP syntax errors
- PHPStan static analysis (jika dikonfigurasi)
- Laravel Pint code style
- Composer security audit
- Environment file existence

### 3. Deploy Workflow (`.github/workflows/deploy.yml`)

**Trigger:**
- Push ke branch `main`
- Manual dispatch via GitHub Actions UI

**Deployment Steps:**
1. SSH ke production server
2. Pull latest code dari `main` branch
3. Install/update Composer dependencies (production mode)
4. Install/update NPM dependencies dan build assets
5. Run database migrations
6. Clear dan optimize caches
7. Restart queue workers
8. Set proper file permissions

**⚠️ Requires GitHub Secrets (see setup below)**

## Setup Instructions

### Prerequisites

1. **GitHub Repository**
   - Repository sudah ada: `Bhimonw/sisaraya-workspace`
   - Push access ke repository

2. **Production Server**
   - SSH access ke server production
   - Git sudah terinstall
   - PHP 8.4+ sudah terinstall
   - Composer sudah terinstall
   - Node.js dan NPM sudah terinstall

### Step 1: Configure GitHub Secrets

Untuk deployment workflow, tambahkan secrets berikut di GitHub repository:

**Navigation:** Repository → Settings → Secrets and variables → Actions → New repository secret

| Secret Name | Description | Example Value |
|------------|-------------|---------------|
| `SSH_HOST` | Production server hostname atau IP | `srv1045082.hosting.com` atau `123.456.789.0` |
| `SSH_USERNAME` | SSH username | `root` |
| `SSH_PRIVATE_KEY` | SSH private key untuk authentication | `-----BEGIN OPENSSH PRIVATE KEY-----\n...` |
| `SSH_PORT` | SSH port (optional, default: 22) | `22` |

#### Cara Generate SSH Key Pair:

**Di local machine:**
```bash
# Generate new SSH key pair
ssh-keygen -t ed25519 -C "github-actions@sisaraya" -f ~/.ssh/sisaraya_deploy

# Copy public key ke production server
ssh-copy-id -i ~/.ssh/sisaraya_deploy.pub root@srv1045082

# Copy private key untuk GitHub Secret
cat ~/.ssh/sisaraya_deploy
```

**Copy seluruh output private key ke GitHub Secret `SSH_PRIVATE_KEY`**

### Step 2: Test SSH Connection

Verifikasi SSH key works:

```bash
ssh -i ~/.ssh/sisaraya_deploy root@srv1045082 "cd ~/projects/sisaraya-workspace && pwd"
```

Jika berhasil, akan menampilkan path project.

### Step 3: Prepare Production Server

**Di production server:**

```bash
# Pastikan project sudah clone
cd ~/projects
git clone https://github.com/Bhimonw/sisaraya-workspace.git
cd sisaraya-workspace

# Setup permissions
chmod -R 775 storage bootstrap/cache

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Setup environment
cp .env.example .env
nano .env  # Configure database credentials, etc.

# Generate keys
php artisan key:generate
php artisan webpush:vapid

# Run migrations
php artisan migrate --seed

# Setup web server (Nginx/Apache) to point to public/ directory
```

### Step 4: Enable Workflows

Workflows akan otomatis berjalan setelah file `.github/workflows/*.yml` di-push ke repository.

**Push workflows:**
```bash
git add .github/workflows/
git commit -m "Setup CI/CD workflows"
git push origin main
```

### Step 5: Verify Workflows

**Check workflow status:**
1. Go to repository on GitHub
2. Click "Actions" tab
3. Lihat workflow runs dan status

**Manual trigger deployment:**
1. Go to Actions tab
2. Click "Deploy to Production" workflow
3. Click "Run workflow" button
4. Select branch `main`
5. Click "Run workflow"

## Workflow Badges

Tambahkan badges ini ke `README.md`:

```markdown
![Tests](https://github.com/Bhimonw/sisaraya-workspace/workflows/Tests/badge.svg)
![Code Quality](https://github.com/Bhimonw/sisaraya-workspace/workflows/Code%20Quality/badge.svg)
![Deploy](https://github.com/Bhimonw/sisaraya-workspace/workflows/Deploy%20to%20Production/badge.svg)
```

## Troubleshooting

### Tests Failing

**Issue:** Database connection error
**Solution:** Check MySQL service health in workflow logs. Adjust health check parameters if needed.

**Issue:** Tests timeout
**Solution:** Increase timeout in `phpunit.xml`:
```xml
<phpunit backupGlobals="false"
         processIsolationTimeout="300">
```

### Deployment Failing

**Issue:** SSH connection refused
**Solution:** 
- Verify SSH_HOST secret is correct
- Check SSH_PORT (default 22)
- Verify SSH key is added to server's `~/.ssh/authorized_keys`

**Issue:** Permission denied errors
**Solution:** Run on server:
```bash
cd ~/projects/sisaraya-workspace
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Issue:** Migration errors
**Solution:** Check database credentials in production `.env` file

**Issue:** NPM build fails
**Solution:** 
- Ensure Node.js version on server is compatible (v18+)
- Clear npm cache: `npm cache clean --force`

### Code Quality Failing

**Issue:** PHP syntax errors
**Solution:** Run locally: `find . -name "*.php" -exec php -l {} \;`

**Issue:** Composer audit fails
**Solution:** Update vulnerable packages: `composer update`

## Best Practices

### Branch Protection Rules

**Protect `main` branch:**
1. Repository → Settings → Branches
2. Add rule for `main` branch
3. Enable:
   - ✅ Require status checks to pass before merging
   - ✅ Require branches to be up to date
   - ✅ Required checks: Tests, Code Quality
   - ✅ Require pull request reviews

### Deployment Strategy

**Recommended workflow:**
1. Develop di branch `develop`
2. Create PR ke `main`
3. Tests dan Code Quality harus pass
4. Review dan merge PR
5. Automatic deployment ke production

**Emergency hotfix:**
1. Create branch `hotfix/description` dari `main`
2. Fix issue
3. Create PR ke `main`
4. Fast-track review
5. Merge and deploy

### Rollback Procedure

Jika deployment bermasalah:

```bash
# SSH ke production server
ssh root@srv1045082

cd ~/projects/sisaraya-workspace

# Rollback ke commit sebelumnya
git log --oneline -5  # Lihat history
git reset --hard COMMIT_HASH

# Revert dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Clear cache
php artisan optimize:clear
php artisan optimize

# Restart services
php artisan queue:restart
```

## Monitoring

### Deployment Notifications

Untuk notifikasi deployment, tambahkan step di `deploy.yml`:

**Slack notification:**
```yaml
- name: Notify Slack
  if: always()
  uses: 8398a7/action-slack@v3
  with:
    status: ${{ job.status }}
    webhook_url: ${{ secrets.SLACK_WEBHOOK }}
```

**Discord notification:**
```yaml
- name: Notify Discord
  if: always()
  uses: sarisia/actions-status-discord@v1
  with:
    webhook: ${{ secrets.DISCORD_WEBHOOK }}
```

### Logs

**View deployment logs:**
- GitHub: Actions tab → Select workflow run
- Server: `~/projects/sisaraya-workspace/storage/logs/laravel.log`

## Performance Optimization

### Cache Strategy

Workflow sudah include caching untuk:
- Composer packages (via `composer install`)
- NPM packages (via `npm ci`)
- Application cache (via `artisan optimize`)

### Build Artifacts

Untuk menyimpan build artifacts (optional):

```yaml
- name: Upload Build Artifacts
  uses: actions/upload-artifact@v3
  with:
    name: production-build
    path: |
      public/build/
      vendor/
```

## Security Considerations

1. **Never commit secrets** - Use GitHub Secrets
2. **Rotate SSH keys** regularly (every 3-6 months)
3. **Use read-only tokens** where possible
4. **Enable 2FA** on GitHub account
5. **Review workflow runs** regularly
6. **Monitor composer audit** output
7. **Keep dependencies updated**

## Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Deployment Best Practices](https://laravel.com/docs/deployment)
- [Deploying Laravel Apps](https://laracasts.com/series/deployment)

## Support

Jika ada masalah dengan CI/CD:
1. Check workflow logs di GitHub Actions
2. Test locally: `php artisan test`
3. Check server logs: `storage/logs/laravel.log`
4. Review this documentation
5. Contact DevOps team

---

**Last Updated:** 2025-10-22  
**Maintained By:** SISARAYA DevOps Team
