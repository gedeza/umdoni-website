# Database Backup System - Deployment Instructions

**Date:** 2025-12-04
**Task:** #3 - Automated Database Backups
**Status:** Ready for Production Deployment

---

## 📦 What's Included

This deployment package contains **3 files + documentation**:

### Core Files
1. `scripts/database-backup.php` (NEW) - Automated backup script
2. `App/Controllers/Dashboard/Backups.php` (NEW) - Dashboard controller
3. `App/Views/dashboard/backups/index.php` (NEW) - Dashboard view
4. `BACKUP-SYSTEM-DOCUMENTATION.md` - Complete system documentation

---

## 🎯 What This Does

### Automated Database Backup System
**Features:**
- **Automated Daily Backups** at 2:00 AM (via cron)
- **Manual Backup Trigger** from dashboard
- **Intelligent Retention Policy:**
  - Daily: Keep last 7 days
  - Weekly: Keep last 4 weeks (Sundays)
  - Monthly: Keep last 3 months (1st of month)
- **Compressed Storage** - GZIP compression (85-90% reduction)
- **Dashboard Management** - View, download, delete backups
- **Activity Logging** - All operations logged to database

**Dashboard URL:** https://umdoni.gov.za/dashboard/backups

---

## 📋 Deployment Steps

### Step 1: Upload Files to Production

**Via cPanel File Manager:**

1. **Upload backup script:**
   - Navigate to: `public_html/scripts/`
   - Upload: `database-backup.php`
   - Set permissions: `755` (click Permissions, check: Owner Read/Write/Execute)

2. **Upload dashboard controller:**
   - Navigate to: `public_html/App/Controllers/Dashboard/`
   - Upload: `Backups.php`

3. **Create backups view directory (if doesn't exist):**
   - Navigate to: `public_html/App/Views/dashboard/`
   - Create folder: `backups`

4. **Upload dashboard view:**
   - Navigate to: `public_html/App/Views/dashboard/backups/`
   - Upload: `index.php`

5. **Create backup storage directory:**
   - Navigate to: `public_html/backups/`
   - Create folder: `database` (if doesn't exist)
   - Set permissions: `755`

**Alternative - Upload ZIP:**
1. Upload `database-backup-20251204.zip` to `public_html/`
2. Extract it
3. Move files to correct locations as above

---

## 🧪 Testing Instructions

### Test 1: Access Dashboard Page
1. **Login to dashboard:** https://umdoni.gov.za/dashboard
2. **Navigate to:** Dashboard → Backups (or directly: https://umdoni.gov.za/dashboard/backups)
3. **Expected:**
   - Page loads successfully
   - Statistics cards show: 0 Total Backups, 0 B Total Size
   - Warning message: "No backups found. Click 'Create Backup'..."
   - "Create Backup" button visible at top right

### Test 2: Create Manual Backup
1. **On backups dashboard page**
2. **Click:** "Create Backup" button (green button, top right)
3. **Confirm:** The popup dialog
4. **Wait:** 10-30 seconds (page will refresh)
5. **Expected:**
   - Green success message: "Database backup created successfully!"
   - Statistics updated (1 Total Backup, size shown)
   - Backup appears in list with:
     - Date & time
     - Filename: `umdoni_backup_YYYY-MM-DD_HH-MM-SS.sql.gz`
     - Size (e.g., "7.2 MB")
     - Age ("0.0 hours ago")
     - Download and Delete buttons

### Test 3: Download Backup
1. **Find the backup in the list**
2. **Click:** Blue download button (download icon)
3. **Expected:**
   - File downloads to your computer
   - Filename: `umdoni_backup_YYYY-MM-DD_HH-MM-SS.sql.gz`

### Test 4: Activity Logs
1. **Navigate to:** Dashboard → Activity Logs
2. **Filter by:** "Info" type
3. **Expected:**
   - Log entry shows: "Database backup completed successfully. File: [filename], Size: [size]"
   - Username: "Database Backup"
   - Location: "Automated Backup Script"

### Test 5: Verify Backup File Exists
1. **Via cPanel File Manager:**
2. **Navigate to:** `public_html/backups/database/2025/12/`
3. **Expected:**
   - Backup file exists: `umdoni_backup_YYYY-MM-DD_HH-MM-SS.sql.gz`
   - File size shows (e.g., 7-15 MB for typical database)

---

## ⏰ Step 2: Configure Cron Job (Automated Backups)

**IMPORTANT:** Only configure after successful manual backup test!

### Via cPanel → Advanced → Cron Jobs

**Add New Cron Job:**

**Common Settings:**
- Minute: `0`
- Hour: `2`
- Day: `*`
- Month: `*`
- Weekday: `*`

**Command:**
```bash
cd /home/umdonigov/public_html && php scripts/database-backup.php >> logs/backup.log 2>&1
```

**Breakdown:**
- Runs **daily at 2:00 AM**
- Changes to website directory
- Executes backup script
- Logs output to `logs/backup.log`

**Email Notifications:**
- Leave email field empty to disable notifications
- OR enter email to receive cron output

**Click:** "Add New Cron Job"

---

## ✅ Success Criteria

**After Manual Test:**
- [ ] Dashboard backups page loads without errors
- [ ] "Create Backup" button creates backup successfully
- [ ] Backup appears in list with correct details
- [ ] Download button downloads backup file
- [ ] Activity logs show backup success message
- [ ] Backup file exists in `backups/database/YYYY/MM/` directory

**After Cron Configuration:**
- [ ] Cron job added successfully in cPanel
- [ ] Wait 24 hours and check for automatic backup
- [ ] New backup appears at ~2:00 AM
- [ ] Activity logs show automated backup success

---

## 🚨 Troubleshooting

### Issue: "Backup script not found"
**Solution:** Verify `scripts/database-backup.php` exists and has execute permissions (755)

### Issue: "Permission denied"
**Solution:**
1. Check `backups/database` directory exists
2. Set permissions to 755: Right-click → Permissions → 755

### Issue: Dashboard page shows error
**Solution:**
1. Verify `App/Controllers/Dashboard/Backups.php` exists
2. Verify `App/Views/dashboard/backups/index.php` exists
3. Check PHP error logs in cPanel

### Issue: Backup creation hangs/times out
**Solution:**
1. Database might be very large (>500MB)
2. Contact hosting provider about increasing PHP execution time
3. Try again during off-peak hours

### Issue: Cron job not running
**Solution:**
1. Verify cron command path is correct: `/home/umdonigov/public_html`
2. Check `logs/backup.log` for error messages
3. Test command manually via SSH (if available)

---

## 🔄 Rollback Plan

If issues occur:

**Remove Dashboard Access:**
1. Delete: `/App/Controllers/Dashboard/Backups.php`
2. Delete: `/App/Views/dashboard/backups/` directory
3. Navigate away from backups page

**Remove Cron Job:**
1. Go to: cPanel → Advanced → Cron Jobs
2. Find the backup cron job
3. Click: Delete

**Remove Script:**
1. Delete: `/scripts/database-backup.php`

**Note:** Any existing backups will remain in `backups/database/` and can be deleted manually if needed.

---

## 📊 What Happens After Deployment

### Immediate (Manual Backups):
- Admins can create backups on-demand from dashboard
- Download backups for off-site storage
- View backup history and statistics

### After Cron Configuration:
- Automatic daily backups at 2:00 AM
- Old backups automatically cleaned up per retention policy
- ~14 backups maintained at any time (7 daily + 4 weekly + 3 monthly)
- Activity logs track all backup operations

### Storage Requirements:
For typical uMdoni database (~50-100MB):
- Each compressed backup: ~7-15 MB
- Total storage (14 backups): ~100-210 MB
- Automatic cleanup prevents unlimited growth

---

## 📖 Additional Resources

**Full Documentation:** `BACKUP-SYSTEM-DOCUMENTATION.md`

**Topics Covered:**
- Architecture details
- Restoration procedures (3 methods)
- Monitoring and logs
- Security considerations
- Performance metrics
- Maintenance tasks
- Future enhancements

---

**Deployed By:** Nhlanhla Mnyandu
**Deployment Date:** 2025-12-04
**Task:** #3 - Automated Database Backups
**Git Commit:** (to be added after commit)
