# 🚀 CI/CD Quick Reference Card

## 📁 Files Created

```
.github/
├── workflows/
│   ├── tests.yml              # Automated testing
│   ├── code-quality.yml       # Code quality checks
│   └── deploy.yml             # Production deployment
└── SECRETS_SETUP.md           # GitHub Secrets configuration guide

scripts/
├── deploy.sh                  # Quick deployment script
├── rollback.sh                # Rollback to previous version
└── fix-production-migration.sh # Fix migration issues

docs/
├── CI_CD_SETUP.md             # Complete CI/CD guide
├── CI_CD_SETUP_SUMMARY.md     # Quick summary (this document's parent)
└── PRODUCTION_MIGRATION_FIX.md # Migration troubleshooting
```

## ⚡ Quick Commands

### Local Development
```bash
composer run dev      # Start dev server with hot reload
php artisan test      # Run tests locally
php artisan migrate   # Run migrations
```

### Deployment (Production Server)
```bash
# Automated (recommended)
git push origin main  # Triggers automatic deployment via GitHub Actions

# Manual on server
ssh root@srv1045082
cd ~/projects/sisaraya-workspace
bash scripts/deploy.sh
```

### Rollback (if needed)
```bash
ssh root@srv1045082
cd ~/projects/sisaraya-workspace
bash scripts/rollback.sh [commit-hash]
```

## 🔑 GitHub Secrets (Required)

| Secret Name | Example Value |
|------------|---------------|
| `SSH_HOST` | `srv1045082.hosting.com` |
| `SSH_USERNAME` | `root` |
| `SSH_PRIVATE_KEY` | `-----BEGIN OPENSSH PRIVATE KEY-----\n...` |
| `SSH_PORT` | `22` (optional) |

**Setup:** Repository → Settings → Secrets and variables → Actions

## 📊 Workflow Triggers

| Workflow | Trigger | Duration |
|----------|---------|----------|
| Tests | Push/PR to `main`/`develop` | ~2-3 min |
| Code Quality | Push/PR to `main`/`develop` | ~1-2 min |
| Deploy | Push to `main` or manual | ~3-5 min |

## 🔗 Quick Links

- **Workflows:** https://github.com/Bhimonw/sisaraya-workspace/actions
- **Secrets:** https://github.com/Bhimonw/sisaraya-workspace/settings/secrets/actions
- **Deployment Logs:** `storage/logs/laravel.log` (on server)

## ✅ Pre-Deployment Checklist

- [ ] GitHub Secrets configured
- [ ] SSH keys generated and added to server
- [ ] Production `.env` configured
- [ ] Database created and accessible
- [ ] Web server (Nginx/Apache) configured
- [ ] First manual deployment tested

## 🆘 Emergency Contacts

**Issue:** Tests failing  
**Check:** GitHub Actions logs → Tests workflow

**Issue:** Deployment failing  
**Check:** GitHub Actions logs → Deploy workflow → SSH connection

**Issue:** Production down  
**Action:** `bash scripts/rollback.sh` to previous version

## 📱 Monitoring

**Build Status Badges (README.md):**
- ![Tests](https://img.shields.io/badge/Tests-passing-brightgreen)
- ![Code Quality](https://img.shields.io/badge/Code%20Quality-passing-brightgreen)
- ![Deploy](https://img.shields.io/badge/Deploy-success-brightgreen)

**Check Status:**
- Visit: https://github.com/Bhimonw/sisaraya-workspace
- Badges show real-time status of last workflow run

## 💡 Tips

- Always test locally before pushing
- Use meaningful commit messages
- Review PR before merging to main
- Monitor deployment logs
- Keep documentation updated
- Rotate SSH keys every 3-6 months

---

**Need Help?** See [`docs/CI_CD_SETUP.md`](CI_CD_SETUP.md) for complete guide
