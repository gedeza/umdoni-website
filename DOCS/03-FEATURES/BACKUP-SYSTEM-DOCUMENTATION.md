# Database Backup System Documentation

**Project:** uMdoni Municipality Website
**Feature:** Automated Database Backups
**Author:** Nhlanhla Mnyandu
**Date:** 2025-12-04
**Version:** 1.0

---

## Overview

Automated database backup system with intelligent retention policy, dashboard management interface, and scheduled execution.

### Key Features

- ✅ **Automated Daily Backups** - Runs at 2:00 AM via cron job
- ✅ **Manual Backup Trigger** - Create backups on-demand from dashboard
- ✅ **Intelligent Retention Policy** - Daily (7 days), Weekly (4 weeks), Monthly (3 months)
- ✅ **Compressed Storage** - GZIP compression (typically 85-90% reduction)
- ✅ **Dashboard Management** - View, download, and delete backups
- ✅ **Activity Logging** - All backup operations logged to database
- ✅ **Organized Storage** - Year/Month directory structure

---

## Architecture

### Components

1. **Backup Script** (`scripts/database-backup.php`)
   - Creates mysqldump of entire database
   - Compresses using gzip (level 9)
   - Applies retention policy automatically
   - Logs all operations to database

2. **Dashboard Controller** (`App/Controllers/Dashboard/Backups.php`)
   - Lists all backups with statistics
   - Trigger manual backups
   - Download backup files
   - Delete old backups

3. **Dashboard View** (`App/Views/dashboard/backups/index.php`)
   - Statistics cards (count, size, dates)
   - Backup history table
   - Action buttons (create, download, delete)

### Storage Structure

```
backups/database/
├── 2025/
│   ├── 12/
│   │   ├── umdoni_backup_2025-12-04_02-00-00.sql.gz
│   │   ├── umdoni_backup_2025-12-05_02-00-00.sql.gz
│   │   └── umdoni_backup_2025-12-06_02-00-00.sql.gz
│   └── 11/
│       └── umdoni_backup_2025-11-01_02-00-00.sql.gz (monthly)
└── 2024/
    └── 12/
        └── umdoni_backup_2024-12-01_02-00-00.sql.gz (monthly)
```

---

## Retention Policy

### Automatic Cleanup Rules

The system automatically keeps:

| Type | Retention | Example |
|------|-----------|---------|
| **Daily** | Last 7 days | All backups from today back 7 days |
| **Weekly** | Last 4 weeks | Sunday backups for 4 weeks (4 backups) |
| **Monthly** | Last 3 months | 1st of month backups for 3 months (3 backups) |

**Example Timeline:**
- Today (Dec 4): Keep daily backup
- Dec 3-Nov 27 (7 days): Keep all daily backups
- Nov 26, 19, 12, 5 (Sundays): Keep weekly backups
- Nov 1, Oct 1, Sep 1: Keep monthly backups
- **All others**: Automatically deleted

**Total Storage:** ~14 backups on average (7 daily + 4 weekly + 3 monthly)

---

## Installation & Deployment

### Prerequisites

- PHP 8.0+ with exec() function enabled
- `mysqldump` command available on server
- Write permissions on `backups/database/` directory
- Access to cPanel cron job configuration

### Deployment Steps

#### 1. Upload Files

Upload these 3 files to production:

```
scripts/database-backup.php          → /public_html/scripts/
App/Controllers/Dashboard/Backups.php → /public_html/App/Controllers/Dashboard/
App/Views/dashboard/backups/index.php → /public_html/App/Views/dashboard/backups/
```

#### 2. Set Permissions

```bash
chmod +x /public_html/scripts/database-backup.php
chmod 755 /public_html/backups/database
```

#### 3. Test Manual Backup

1. Login to dashboard: https://umdoni.gov.za/dashboard
2. Navigate to: **Dashboard → Backups**
3. Click: **"Create Backup"** button
4. Verify: Backup appears in list and file exists

#### 4. Configure Cron Job (via cPanel)

**cPanel → Advanced → Cron Jobs**

**Schedule:** Daily at 2:00 AM

```bash
0 2 * * * cd /home/umdonigov/public_html && php scripts/database-backup.php >> logs/backup.log 2>&1
```

**Breakdown:**
- `0 2 * * *` - Run at 2:00 AM every day
- `cd /home/umdonigov/public_html` - Change to website directory
- `php scripts/database-backup.php` - Execute backup script
- `>> logs/backup.log 2>&1` - Log output to file

---

## Usage

### Dashboard Access

**URL:** https://umdoni.gov.za/dashboard/backups

### Creating Manual Backups

1. Navigate to **Dashboard → Backups**
2. Click **"Create Backup"** button
3. Confirm the action
4. Wait for completion message
5. New backup appears in list

### Downloading Backups

1. Navigate to **Dashboard → Backups**
2. Find the backup in the list
3. Click the **download icon** (blue button)
4. File downloads to your computer

### Deleting Backups

1. Navigate to **Dashboard → Backups**
2. Find the backup to delete
3. Click the **trash icon** (red button)
4. Confirm deletion
5. Backup is permanently removed

---

## Restoration Process

### Option 1: Via phpMyAdmin (Recommended)

1. Download the backup file from dashboard
2. Decompress: `gunzip umdoni_backup_YYYY-MM-DD_HH-MM-SS.sql.gz`
3. Login to cPanel → phpMyAdmin
4. Select database: `umdonigov_umdoni`
5. Click **Import** tab
6. Choose SQL file
7. Click **Go**

### Option 2: Via SSH Command Line

```bash
# Decompress backup
gunzip umdoni_backup_2025-12-04_02-00-00.sql.gz

# Restore to database
mysql -h reseller142.aserv.co.za \
      -u umdonigov_admin \
      -p \
      umdonigov_umdoni < umdoni_backup_2025-12-04_02-00-00.sql
```

### Option 3: Via PHP Script (Advanced)

```php
<?php
// Example restoration script (use with caution)
$backupFile = 'umdoni_backup_2025-12-04_02-00-00.sql.gz';

// Decompress
exec("gunzip {$backupFile}");

// Import
$sqlFile = str_replace('.gz', '', $backupFile);
exec("mysql -h reseller142.aserv.co.za -u umdonigov_admin -p'PASSWORD' umdonigov_umdoni < {$sqlFile}");
?>
```

---

## Monitoring & Logs

### Activity Logs

All backup operations are logged to the `logs` table:

**Location:** Dashboard → Activity Logs

**Filter:** Select "Info" to see backup successes, "Error" to see backup failures

**Log Format:**
- **Success:** "Database backup completed successfully. File: [filename], Size: [size]"
- **Error:** "Database backup failed: [error message]"

### Backup Log File

**Location:** `logs/backup.log`

Contains detailed output from cron job executions.

### Email Notifications (Future Enhancement)

Currently not implemented. Can be added by modifying the backup script to send email on success/failure.

---

## Troubleshooting

### Issue: "Backup script not found"

**Cause:** Script file missing or incorrect path
**Solution:** Verify `scripts/database-backup.php` exists with correct permissions

### Issue: "mysqldump: command not found"

**Cause:** mysqldump not available on server
**Solution:** Contact hosting provider or use alternative backup method

### Issue: "Permission denied"

**Cause:** Insufficient write permissions
**Solution:**
```bash
chmod 755 backups/database
chmod +x scripts/database-backup.php
```

### Issue: Cron job not running

**Causes:**
1. Incorrect cron syntax
2. Wrong path to PHP binary
3. Insufficient permissions

**Solution:**
1. Check cron job configuration in cPanel
2. Verify path: `which php` (should be `/usr/bin/php` or similar)
3. Check logs: `logs/backup.log`

### Issue: Backups not being deleted

**Cause:** Retention policy not applying
**Solution:**
1. Run manual backup to trigger cleanup
2. Check file permissions on old backups
3. Verify script has delete permissions

---

## Security Considerations

### File Permissions

- Backup directory: `755` (rwxr-xr-x)
- Backup files: `644` (rw-r--r--)
- Script file: `755` (rwxr-xr-x)

### Access Control

- Dashboard access requires authentication
- Only admin users can trigger backups
- Download requires authentication
- Delete requires POST request (CSRF protection)

### Database Credentials

- Stored in `App/Config.php`
- **IMPORTANT:** Ensure `.gitignore` excludes Config.php
- Never commit database credentials to version control

### Backup File Security

- Files stored outside web root (if possible)
- GZIP compression provides minimal security (not encryption)
- Consider encrypting backups for sensitive data

---

## Performance & Storage

### Typical Backup Sizes

| Database Size | Compressed Backup | Compression Ratio |
|---------------|-------------------|-------------------|
| 50 MB | ~7 MB | 86% |
| 100 MB | ~15 MB | 85% |
| 500 MB | ~75 MB | 85% |
| 1 GB | ~150 MB | 85% |

### Execution Time

- Small DB (< 100 MB): 10-30 seconds
- Medium DB (100-500 MB): 30-120 seconds
- Large DB (> 500 MB): 2-5 minutes

### Storage Requirements

With retention policy (7 daily + 4 weekly + 3 monthly = 14 backups):

| Database Size | Total Storage Needed |
|---------------|----------------------|
| 50 MB | ~100 MB |
| 100 MB | ~210 MB |
| 500 MB | ~1 GB |
| 1 GB | ~2.1 GB |

---

## Maintenance

### Regular Tasks

**Weekly:**
- Check dashboard for backup success
- Verify recent backups are being created
- Review Activity Logs for errors

**Monthly:**
- Test backup restoration process
- Verify cron job is running
- Check storage usage

**Quarterly:**
- Download important monthly backups for off-site storage
- Review and update retention policy if needed
- Test full disaster recovery procedure

---

## Future Enhancements

### Planned Features

- [ ] Email notifications on backup success/failure
- [ ] Off-site backup storage (AWS S3, Dropbox, etc.)
- [ ] Backup encryption
- [ ] Incremental backups
- [ ] Restore from dashboard interface
- [ ] Backup verification (test restore)
- [ ] Multi-database support
- [ ] Backup compression level selection

---

## Support & Contact

**Developer:** Nhlanhla Mnyandu
**Date Created:** 2025-12-04
**Last Updated:** 2025-12-04

For issues or questions, contact the development team or refer to the main project documentation.

---

## Changelog

### Version 1.0 (2025-12-04)
- Initial release
- Automated daily backups
- Dashboard management interface
- Intelligent retention policy
- Compressed storage
- Activity logging
