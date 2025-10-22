#!/bin/bash
# ============================================================
# SISARAYA Rollback Script for Production Server
# Use this to rollback to a previous commit
# Usage: ./scripts/rollback.sh [commit-hash]
# ============================================================

set -e

echo "=========================================="
echo "SISARAYA Production Rollback"
echo "=========================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Are you in the project root?"
    exit 1
fi

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Get commit hash from argument or show recent commits
if [ -z "$1" ]; then
    echo -e "${YELLOW}Recent commits:${NC}"
    git log --oneline -10
    echo ""
    read -p "Enter commit hash to rollback to: " COMMIT_HASH
else
    COMMIT_HASH=$1
fi

# Confirm rollback
echo ""
echo -e "${RED}⚠️  WARNING: This will rollback to commit: ${COMMIT_HASH}${NC}"
echo ""
git show --oneline --no-patch $COMMIT_HASH
echo ""
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "❌ Rollback cancelled"
    exit 1
fi

echo ""
echo -e "${YELLOW}📦 Creating backup of current state...${NC}"
BACKUP_BRANCH="backup-$(date +%Y%m%d-%H%M%S)"
git branch $BACKUP_BRANCH
echo -e "${GREEN}✅ Backup created: $BACKUP_BRANCH${NC}"
echo ""

echo -e "${YELLOW}⏪ Rolling back to commit: ${COMMIT_HASH}${NC}"
git reset --hard $COMMIT_HASH
echo -e "${GREEN}✅ Code rolled back${NC}"
echo ""

echo -e "${YELLOW}📦 Reinstalling Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✅ Composer dependencies installed${NC}"
echo ""

echo -e "${YELLOW}📦 Reinstalling NPM dependencies...${NC}"
npm ci --production
echo -e "${GREEN}✅ NPM dependencies installed${NC}"
echo ""

echo -e "${YELLOW}🔨 Rebuilding frontend assets...${NC}"
npm run build
echo -e "${GREEN}✅ Assets rebuilt${NC}"
echo ""

echo -e "${YELLOW}🗄️  Running database migrations (if needed)...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations checked${NC}"
echo ""

echo -e "${YELLOW}🧹 Clearing all caches...${NC}"
php artisan optimize:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

echo -e "${YELLOW}⚡ Optimizing application...${NC}"
php artisan optimize
echo -e "${GREEN}✅ Application optimized${NC}"
echo ""

echo -e "${YELLOW}🔄 Restarting queue workers...${NC}"
php artisan queue:restart || echo "Queue restart skipped"
echo -e "${GREEN}✅ Queue workers restarted${NC}"
echo ""

echo "=========================================="
echo -e "${GREEN}✅ Rollback completed successfully!${NC}"
echo "=========================================="
echo ""
echo "📝 Rollback information:"
echo "  - Rolled back to: $COMMIT_HASH"
echo "  - Backup branch: $BACKUP_BRANCH"
echo ""
echo "To restore from backup:"
echo "  git reset --hard $BACKUP_BRANCH"
echo ""
