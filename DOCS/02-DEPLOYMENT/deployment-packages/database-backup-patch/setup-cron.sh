#!/bin/bash
###############################################################################
# Database Backup Cron Job Setup Script
#
# This script installs the automated backup cron job on the production server
#
# Usage:
#   1. Upload this script to the server
#   2. Make it executable: chmod +x setup-cron.sh
#   3. Run it: ./setup-cron.sh
#
# What it does:
#   - Creates a cron job to run daily at 2:00 AM SAST
#   - Ensures logs directory exists
#   - Tests the backup script
#
# @author Nhlanhla Mnyandu
# @date 2025-12-05
###############################################################################

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "==========================================="
echo "Database Backup Cron Setup"
echo "==========================================="
echo ""

# Detect the project root directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"

echo "Project root: $PROJECT_ROOT"
echo ""

# Step 1: Verify backup script exists
BACKUP_SCRIPT="$PROJECT_ROOT/scripts/database-backup.php"
if [ ! -f "$BACKUP_SCRIPT" ]; then
    echo -e "${RED}ERROR: Backup script not found at $BACKUP_SCRIPT${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Backup script found${NC}"

# Step 2: Create logs directory if it doesn't exist
LOGS_DIR="$PROJECT_ROOT/logs"
if [ ! -d "$LOGS_DIR" ]; then
    mkdir -p "$LOGS_DIR"
    chmod 755 "$LOGS_DIR"
    echo -e "${GREEN}✓ Created logs directory${NC}"
else
    echo -e "${GREEN}✓ Logs directory exists${NC}"
fi

# Step 3: Test the backup script
echo ""
echo "Testing backup script..."
php "$BACKUP_SCRIPT"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Backup script test successful${NC}"
else
    echo -e "${RED}ERROR: Backup script test failed${NC}"
    exit 1
fi

# Step 4: Install cron job
echo ""
echo "Installing cron job..."

# Create cron job line (runs daily at 2:00 AM SAST)
CRON_JOB="0 2 * * * cd $PROJECT_ROOT && php scripts/database-backup.php >> logs/backup.log 2>&1"

# Check if cron job already exists
crontab -l 2>/dev/null | grep -F "database-backup.php" > /dev/null
if [ $? -eq 0 ]; then
    echo -e "${YELLOW}! Cron job already exists${NC}"
    echo "Existing cron jobs related to backup:"
    crontab -l | grep "database-backup.php"
    echo ""
    read -p "Do you want to replace it? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        # Remove old cron job
        (crontab -l 2>/dev/null | grep -v "database-backup.php") | crontab -
        echo -e "${GREEN}✓ Removed old cron job${NC}"
        # Add new cron job
        (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
        echo -e "${GREEN}✓ Installed new cron job${NC}"
    else
        echo "Keeping existing cron job"
    fi
else
    # Add cron job
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo -e "${GREEN}✓ Cron job installed${NC}"
fi

# Step 5: Display summary
echo ""
echo "==========================================="
echo "Setup Complete!"
echo "==========================================="
echo ""
echo "Cron job schedule:"
echo "  → Runs daily at 2:00 AM SAST (Africa/Johannesburg)"
echo "  → Timezone: UTC+2 (no daylight saving)"
echo ""
echo "Backup details:"
echo "  → Script: $BACKUP_SCRIPT"
echo "  → Backups: $PROJECT_ROOT/backups/database/"
echo "  → Logs: $PROJECT_ROOT/logs/backup.log"
echo ""
echo "Retention policy:"
echo "  → Daily: Last 7 days"
echo "  → Weekly: Last 4 weeks (Sundays)"
echo "  → Monthly: Last 3 months (1st of month)"
echo ""
echo "Current cron jobs:"
crontab -l | grep "database-backup.php"
echo ""
echo "To view backup logs:"
echo "  tail -f $PROJECT_ROOT/logs/backup.log"
echo ""
echo "To test manually:"
echo "  php $BACKUP_SCRIPT"
echo ""
echo "==========================================="
