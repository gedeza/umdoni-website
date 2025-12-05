# Database Backup System - Timezone Fix & Cron Setup

**Deployment Package:** `database-backup-patch`
**Date:** December 5, 2025
**Author:** Nhlanhla Mnyandu

---

## What's in This Patch

This patch fixes the timezone issue and sets up automated backups for the Umdoni Municipality website.

### Changes Made:

1. **Timezone Configuration** (CRITICAL)
   - Set application timezone to `Africa/Johannesburg` (SAST - UTC+2)
   - Fixes timestamp issues in Activity Logs
   - Ensures cron job runs at correct local time

2. **Automated Backup Cron Job**
   - Runs daily at 2:00 AM SAST
   - Automatic retention policy
   - Logging to database and file

---

## Files Modified

```
public/index.php                  - Added timezone setting (line 58)
scripts/database-backup.php       - Added timezone setting (line 22)
```

## Files in This Deployment Package

```
deployment/database-backup-patch/
├── README.md                     - This file
├── setup-cron.sh                 - Automated cron installation script
└── Backups.php                   - Reference copy (already deployed)
```

---

## Deployment Steps

### Step 1: Upload Modified Files to Production

Upload these files to your production server:

```bash
# From your local machine:
scp public/index.php user@server:/path/to/umdoni-website/public/
scp scripts/database-backup.php user@server:/path/to/umdoni-website/scripts/
scp deployment/database-backup-patch/setup-cron.sh user@server:/path/to/umdoni-website/
```

**⚠️ IMPORTANT:** Backup the original files first!

```bash
# On the server:
cp public/index.php public/index.php.backup
cp scripts/database-backup.php scripts/database-backup.php.backup
```

### Step 2: Setup Automated Backups

**Option A: Automated Setup (Recommended)**

```bash
# SSH into your production server
ssh user@server

# Navigate to project root
cd /path/to/umdoni-website

# Make the setup script executable
chmod +x setup-cron.sh

# Run the setup script
./setup-cron.sh
```

The script will:
- Test the backup script
- Create logs directory
- Install the cron job
- Show you the configuration

**Option B: Manual Setup**

```bash
# SSH into your server
ssh user@server

# Edit crontab
crontab -e

# Add this line (replace /path/to with actual path):
0 2 * * * cd /path/to/umdoni-website && php scripts/database-backup.php >> logs/backup.log 2>&1
```

### Step 3: Verify Installation

1. **Check Timezone:**
   ```bash
   php -r "echo date_default_timezone_get();"
   # Should show: UTC (will be overridden by app)

   php scripts/test-timezone.php
   # Should show Africa/Johannesburg in TEST 2
   ```

2. **Test Backup Manually:**
   ```bash
   cd /path/to/umdoni-website
   php scripts/database-backup.php
   ```

   Should create backup in: `backups/database/YYYY/MM/`

3. **Check Cron Job:**
   ```bash
   crontab -l
   # Should show the backup cron job
   ```

4. **View Logs:**
   ```bash
   tail -f logs/backup.log
   ```

### Step 4: Monitor First Automated Run

- Next run: **Tomorrow at 2:00 AM SAST**
- Check logs the next morning:
  ```bash
  cat logs/backup.log
  ```
- Check Activity Log in dashboard
- Verify backup file was created

---

## Schedule & Retention

### Backup Schedule
- **Frequency:** Daily at 2:00 AM SAST (Africa/Johannesburg time)
- **Timezone:** UTC+2 (no daylight saving in South Africa)
- **Duration:** ~10-30 seconds per backup

### Retention Policy
- **Daily backups:** Keep last 7 days
- **Weekly backups:** Keep last 4 weeks (Sunday backups)
- **Monthly backups:** Keep last 3 months (1st of month backups)

Old backups are automatically deleted based on this policy.

---

## Monitoring

### Check Backup Status

**From Dashboard:**
1. Login to admin dashboard
2. Navigate to **Backups** (in sidebar)
3. View backup history and statistics

**From Command Line:**
```bash
# View recent backups
ls -lh backups/database/*/*/

# Check backup log
tail -n 50 logs/backup.log

# Check next cron run time
crontab -l
```

### Activity Logs

All backup operations are logged to the database Activity Log:
- **Success:** Status = "info"
- **Failure:** Status = "error"
- **User:** "Database Backup"
- **Location:** "Automated Backup Script"

---

## Troubleshooting

### Cron Job Not Running

1. **Check crontab:**
   ```bash
   crontab -l
   ```

2. **Check system logs:**
   ```bash
   grep CRON /var/log/syslog
   ```

3. **Verify path is correct:**
   ```bash
   crontab -l | grep backup
   # Ensure path matches your actual installation
   ```

### Backups Failing

1. **Run manually to see error:**
   ```bash
   php scripts/database-backup.php
   ```

2. **Check database credentials:**
   - Verify in `App/Config.php`

3. **Check permissions:**
   ```bash
   ls -la backups/
   ls -la logs/
   # Both should be writable
   ```

4. **Check disk space:**
   ```bash
   df -h
   ```

### Wrong Timezone

1. **Test timezone:**
   ```bash
   php scripts/test-timezone.php
   ```

2. **Should show:**
   - TEST 2: `Africa/Johannesburg`
   - Offset: `+02:00`
   - SAST time should match your current time

3. **If wrong, verify:**
   - `public/index.php` line 58 has timezone setting
   - `scripts/database-backup.php` line 22 has timezone setting

---

## Rollback Instructions

If you need to rollback this deployment:

```bash
# Restore original files
cp public/index.php.backup public/index.php
cp scripts/database-backup.php.backup scripts/database-backup.php

# Remove cron job
crontab -e
# Delete the line with database-backup.php
```

---

## Support

- **Documentation:** See main README.md
- **Backup System:** Task #3 in TASKS.md
- **Contact:** Nhlanhla Mnyandu

---

## Verification Checklist

Before considering deployment complete:

- [ ] Original files backed up
- [ ] Modified files uploaded to production
- [ ] Timezone test passes (`php scripts/test-timezone.php`)
- [ ] Manual backup test successful
- [ ] Cron job installed and listed in `crontab -l`
- [ ] Logs directory exists and is writable
- [ ] Activity Log shows backup entry
- [ ] Dashboard shows backup in list
- [ ] Scheduled to monitor tomorrow's 2:00 AM run

---

**Deployment Complete! 🎉**

The automated backup system is now configured with the correct timezone and will run daily at 2:00 AM SAST.
