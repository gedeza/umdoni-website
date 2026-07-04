#!/bin/bash
# =============================================================
# Deployment Script: ISU Provider Console + Service Kill-Switch
# Date: 2026-07-04
# Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
# =============================================================
#
# Deploys the ISU console to production. Backs up existing files
# before overwriting, then installs the new controllers, models
# and views, and ensures the storage/ directory is writable.
#
# Run from the SITE ROOT (~/public_html) in cPanel Terminal:
#   cd ~/public_html
#   bash deployment/isu-console-patch/deploy.sh
#
# IMPORTANT: after running this, you MUST also run migration.sql
# against the database (see step "Database" below / README.md).
# =============================================================

set -e

SITE_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
PATCH_DIR="$SITE_ROOT/deployment/isu-console-patch"
BACKUP_DIR="$SITE_ROOT/backups/pre-isu-console-$(date +%Y%m%d-%H%M%S)"

echo "=========================================="
echo " ISU Console Deployment"
echo "=========================================="
echo ""
echo "Site root: $SITE_ROOT"
echo "Patch dir: $PATCH_DIR"
echo ""

# Verify we're in the right place
if [ ! -f "$SITE_ROOT/public/index.php" ]; then
    echo "ERROR: Cannot find public/index.php. Run from the site root (~/public_html)."
    exit 1
fi

# --- [1/4] Backup -------------------------------------------------------
echo "[1/4] Backing up files that will be overwritten..."
mkdir -p "$BACKUP_DIR/public"
cp "$SITE_ROOT/public/index.php" "$BACKUP_DIR/public/index.php"
echo "   Backup saved to: $BACKUP_DIR"

# --- [2/4] Deploy new + modified files ---------------------------------
echo "[2/4] Deploying files..."
mkdir -p "$SITE_ROOT/App/Models"
mkdir -p "$SITE_ROOT/App/Controllers/Isu"
mkdir -p "$SITE_ROOT/App/Views/isu/console"
mkdir -p "$SITE_ROOT/App/Views/errors"
mkdir -p "$SITE_ROOT/public/layouts"

cp "$PATCH_DIR/App/Models/SiteControl.php"                 "$SITE_ROOT/App/Models/SiteControl.php"
cp "$PATCH_DIR/App/Controllers/Isu/Guarded.php"            "$SITE_ROOT/App/Controllers/Isu/Guarded.php"
cp "$PATCH_DIR/App/Controllers/Isu/Console.php"            "$SITE_ROOT/App/Controllers/Isu/Console.php"
cp "$PATCH_DIR/App/Views/isu/console/index.php"            "$SITE_ROOT/App/Views/isu/console/index.php"
cp "$PATCH_DIR/App/Views/errors/service-unavailable.php"   "$SITE_ROOT/App/Views/errors/service-unavailable.php"
cp "$PATCH_DIR/public/layouts/isuLayout.php"               "$SITE_ROOT/public/layouts/isuLayout.php"
cp "$PATCH_DIR/public/index.php"                           "$SITE_ROOT/public/index.php"

# Standard file permissions (match site convention: 644)
chmod 644 \
    "$SITE_ROOT/App/Models/SiteControl.php" \
    "$SITE_ROOT/App/Controllers/Isu/Guarded.php" \
    "$SITE_ROOT/App/Controllers/Isu/Console.php" \
    "$SITE_ROOT/App/Views/isu/console/index.php" \
    "$SITE_ROOT/App/Views/errors/service-unavailable.php" \
    "$SITE_ROOT/public/layouts/isuLayout.php" \
    "$SITE_ROOT/public/index.php"

echo "   7 files deployed."

# --- [3/4] Writable storage/ for the kill-switch flag ------------------
echo "[3/4] Ensuring storage/ is writable..."
mkdir -p "$SITE_ROOT/storage"
chmod 755 "$SITE_ROOT/storage"
# Prove PHP-writable by round-tripping a probe file
PROBE="$SITE_ROOT/storage/.write-test"
if touch "$PROBE" 2>/dev/null; then
    rm -f "$PROBE"
    echo "   storage/ is writable."
else
    echo "   WARNING: storage/ is NOT writable — the kill-switch cannot create its flag."
    echo "            Fix ownership/permissions so the web user can write to it."
fi

# --- [4/4] Verify ------------------------------------------------------
echo "[4/4] Verifying deployment..."
ROUTE_CHECK=$(grep -c "namespace' => 'Isu'" "$SITE_ROOT/public/index.php" || true)
GATE_CHECK=$(grep -c "isIsuAdmin" "$SITE_ROOT/App/Controllers/Isu/Guarded.php" || true)
MODEL_CHECK=$([ -f "$SITE_ROOT/App/Models/SiteControl.php" ] && echo 1 || echo 0)

if [ "$ROUTE_CHECK" -ge 1 ] && [ "$GATE_CHECK" -ge 1 ] && [ "$MODEL_CHECK" -eq 1 ]; then
    echo "   Verification PASSED (route + gate + model present)."
else
    echo "   WARNING: verification counts unexpected."
    echo "   isu route: $ROUTE_CHECK (expected 1+)"
    echo "   gate:      $GATE_CHECK (expected 1+)"
    echo "   model:     $MODEL_CHECK (expected 1)"
fi

echo ""
echo "=========================================="
echo " Files deployed. ONE STEP REMAINS."
echo "=========================================="
echo ""
echo "Database (required — do this now):"
echo "  Run deployment/isu-console-patch/migration.sql via"
echo "  cPanel > phpMyAdmin (adds is_isu column, flags your"
echo "  email, creates the site_control table)."
echo ""
echo "  Or from the terminal:"
echo "    mysql -u umdonigov_admin -p umdonigov_umdoni < \\"
echo "      deployment/isu-console-patch/migration.sql"
echo ""
echo "Then verify:"
echo "  1. Log in as your ISU account (nhlanhla@isutech.co.za)"
echo "  2. Visit /isu/console  ->  should show 'Service is LIVE'"
echo "  3. A normal municipal admin visiting /isu/console is redirected away"
echo ""
echo "To rollback the code, restore public/index.php from:"
echo "  $BACKUP_DIR"
echo ""
