# Deployment: Dashboard Activity Logging

**Date:** 2026-03-02
**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
**Risk Level:** Low

## What This Patch Does

Adds audit trail logging for all dashboard CRUD operations. Previously, the Activity Logs only tracked authentication events (login, logout, auto-logout). After this patch, every create/update/delete action across all 20 dashboard controllers is logged.

### Changes Summary

| File | Change |
|------|--------|
| `Components/Helpers.php` | Added `logActivity()` helper function |
| `App/Views/dashboard/logs/index.php` | Added "Activities" filter + dark badge |
| `App/Controllers/Dashboard/*.php` (20 files) | Added 70 `logActivity()` calls |

### Log Format

Each activity log entry records:
- **Who:** Username, email, user ID (from session)
- **What:** Action + resource + details (e.g., "Created tender: Road Maintenance RFQ")
- **When:** Timestamp
- **Where:** IP address + User Agent

## Prerequisites

- No database migrations required (uses existing `logs` table)
- No new dependencies
- No configuration changes

## Deployment Steps

### Option A: Automated (Recommended)

```bash
# SSH into production server
ssh user@reseller142.aserv.co.za

# Navigate to site root
cd /path/to/umdoni-website

# Upload the deployment package (from local machine)
# scp -r deployment/activity-logging-patch/ user@server:/path/to/site/deployment/

# Run the deploy script
bash deployment/activity-logging-patch/deploy.sh
```

### Option B: Manual

```bash
# 1. Backup current files
tar -czf backup-pre-activity-logging.tar.gz \
  Components/Helpers.php \
  App/Views/dashboard/logs/index.php \
  App/Controllers/Dashboard/

# 2. Copy files from patch
cp deployment/activity-logging-patch/Components/Helpers.php Components/Helpers.php
cp deployment/activity-logging-patch/App/Views/dashboard/logs/index.php App/Views/dashboard/logs/index.php
cp deployment/activity-logging-patch/App/Controllers/Dashboard/*.php App/Controllers/Dashboard/

# 3. Verify
grep -c "function logActivity" Components/Helpers.php
# Should output: 1
```

## Verification

After deploying:

1. Log into the dashboard
2. Create, edit, or delete any record (e.g., a tender)
3. Navigate to **Activity Logs**
4. Select **Activities** from the "Log Type" filter dropdown
5. Click **Apply Filters**
6. Verify the action appears with a dark **ACTIVITY** badge

## Rollback

If issues arise, restore from the backup created by the deploy script:

```bash
# The deploy script saves backups to:
# backups/pre-activity-logging-YYYYMMDD-HHMMSS/

# Restore all files
cp backups/pre-activity-logging-*/Components/Helpers.php Components/Helpers.php
cp backups/pre-activity-logging-*/App/Views/dashboard/logs/index.php App/Views/dashboard/logs/index.php
cp backups/pre-activity-logging-*/App/Controllers/Dashboard/*.php App/Controllers/Dashboard/
```

## Files Included

```
deployment/activity-logging-patch/
├── README.md                          (this file)
├── deploy.sh                          (automated deployment script)
├── Components/
│   └── Helpers.php
├── App/
│   ├── Views/
│   │   └── dashboard/logs/
│   │       └── index.php
│   └── Controllers/Dashboard/
│       ├── Agendas.php
│       ├── Backups.php
│       ├── Councillors.php
│       ├── Documents.php
│       ├── Events.php
│       ├── Meetings.php
│       ├── News.php
│       ├── Newsletters.php
│       ├── Notices.php
│       ├── Projects.php
│       ├── Publications.php
│       ├── Quotations.php
│       ├── Rfps.php
│       ├── Roles.php
│       ├── Services.php
│       ├── Settings.php
│       ├── Tenders.php
│       ├── Users.php
│       ├── Vacancies.php
│       └── Wardinfo.php
```
