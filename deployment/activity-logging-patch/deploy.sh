#!/bin/bash
# =============================================================
# Deployment Script: Dashboard Activity Logging
# Date: 2026-03-02
# Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
# =============================================================
#
# This script deploys the activity logging feature to production.
# It backs up existing files before overwriting.
#
# Usage:
#   cd /path/to/site
#   bash deployment/activity-logging-patch/deploy.sh
#
# =============================================================

set -e

SITE_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
PATCH_DIR="$SITE_ROOT/deployment/activity-logging-patch"
BACKUP_DIR="$SITE_ROOT/backups/pre-activity-logging-$(date +%Y%m%d-%H%M%S)"

echo "=========================================="
echo " Activity Logging Deployment"
echo "=========================================="
echo ""
echo "Site root: $SITE_ROOT"
echo "Patch dir: $PATCH_DIR"
echo ""

# Verify we're in the right place
if [ ! -f "$SITE_ROOT/public/index.php" ]; then
    echo "ERROR: Cannot find public/index.php. Are you in the site root?"
    exit 1
fi

# Create backup
echo "[1/3] Backing up current files..."
mkdir -p "$BACKUP_DIR/Components"
mkdir -p "$BACKUP_DIR/App/Views/dashboard/logs"
mkdir -p "$BACKUP_DIR/App/Controllers/Dashboard"

cp "$SITE_ROOT/Components/Helpers.php" "$BACKUP_DIR/Components/"
cp "$SITE_ROOT/App/Views/dashboard/logs/index.php" "$BACKUP_DIR/App/Views/dashboard/logs/"

for f in "$SITE_ROOT/App/Controllers/Dashboard/"*.php; do
    cp "$f" "$BACKUP_DIR/App/Controllers/Dashboard/"
done

echo "   Backup saved to: $BACKUP_DIR"

# Deploy files
echo "[2/3] Deploying updated files..."

cp "$PATCH_DIR/Components/Helpers.php" "$SITE_ROOT/Components/Helpers.php"
cp "$PATCH_DIR/App/Views/dashboard/logs/index.php" "$SITE_ROOT/App/Views/dashboard/logs/index.php"

for f in "$PATCH_DIR/App/Controllers/Dashboard/"*.php; do
    filename=$(basename "$f")
    cp "$f" "$SITE_ROOT/App/Controllers/Dashboard/$filename"
done

echo "   22 files deployed successfully."

# Verify
echo "[3/3] Verifying deployment..."
HELPER_CHECK=$(grep -c "function logActivity" "$SITE_ROOT/Components/Helpers.php")
VIEW_CHECK=$(grep -c "activity" "$SITE_ROOT/App/Views/dashboard/logs/index.php")
CONTROLLER_CHECK=$(grep -rl "logActivity(" "$SITE_ROOT/App/Controllers/Dashboard/" | wc -l)

if [ "$HELPER_CHECK" -ge 1 ] && [ "$VIEW_CHECK" -ge 2 ] && [ "$CONTROLLER_CHECK" -ge 20 ]; then
    echo "   Verification PASSED"
else
    echo "   WARNING: Verification check counts unexpected."
    echo "   Helper: $HELPER_CHECK (expected 1+)"
    echo "   View: $VIEW_CHECK (expected 2+)"
    echo "   Controllers: $CONTROLLER_CHECK (expected 20)"
fi

echo ""
echo "=========================================="
echo " Deployment complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "  1. Log into the dashboard"
echo "  2. Create/edit/delete any record"
echo "  3. Go to Activity Logs > filter by 'Activities'"
echo "  4. Verify the entry appears with ACTIVITY badge"
echo ""
echo "To rollback, copy files from:"
echo "  $BACKUP_DIR"
echo ""
