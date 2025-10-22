#!/bin/bash
# ============================================================
# SISARAYA Quick Deployment Script for Production Server
# Run this script on production server after git pull
# ============================================================

set -e  # Exit on any error

echo "=========================================="
echo "SISARAYA Production Deployment"
echo "=========================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Are you in the project root?"
    exit 1
fi

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}📦 Step 1: Pulling latest code...${NC}"
git fetch origin
git reset --hard origin/main
echo -e "${GREEN}✅ Code updated${NC}"
echo ""

echo -e "${YELLOW}📦 Step 2: Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✅ Composer dependencies installed${NC}"
echo ""

echo -e "${YELLOW}📦 Step 3: Installing NPM dependencies...${NC}"
npm ci --production
echo -e "${GREEN}✅ NPM dependencies installed${NC}"
echo ""

echo -e "${YELLOW}🔨 Step 4: Building frontend assets...${NC}"
npm run build
echo -e "${GREEN}✅ Assets built${NC}"
echo ""

echo -e "${YELLOW}🗄️  Step 5: Running database migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations completed${NC}"
echo ""

echo -e "${YELLOW}🧹 Step 6: Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

echo -e "${YELLOW}⚡ Step 7: Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo -e "${GREEN}✅ Application optimized${NC}"
echo ""

echo -e "${YELLOW}🔄 Step 8: Restarting queue workers...${NC}"
php artisan queue:restart || echo "Queue restart skipped (not running)"
echo -e "${GREEN}✅ Queue workers restarted${NC}"
echo ""

echo -e "${YELLOW}🔐 Step 9: Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

echo "=========================================="
echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "=========================================="
echo ""
echo "📝 Next steps:"
echo "1. Test the application: curl http://localhost"
echo "2. Check logs: tail -f storage/logs/laravel.log"
echo "3. Monitor queue: php artisan queue:monitor"
echo ""
