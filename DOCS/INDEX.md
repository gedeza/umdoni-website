# uMdoni Municipality Website - Documentation Index

**Quick navigation guide to all project documentation.**

**Last Updated:** December 9, 2025

---

## 📚 Table of Contents

1. [Project Management](#01-project---project-management)
2. [Deployment Guides](#02-deployment---deployment-guides)
3. [Feature Documentation](#03-features---feature-documentation)
4. [Proposals & Ideas](#04-proposals---future-ideas--proposals)
5. [Testing](#05-testing---testing-documentation)

---

## 📁 Directory Structure

```
DOCS/
├── INDEX.md (this file)
├── 01-PROJECT/
├── 02-DEPLOYMENT/
├── 03-FEATURES/
├── 04-PROPOSALS/
└── 05-TESTING/
```

---

## 📂 01-PROJECT - Project Management

**Location:** `DOCS/01-PROJECT/`

### Documents

#### PROJECT-STATUS-20251208.md
**Purpose:** Comprehensive project status report (December 8, 2025)
**Contents:**
- Overall project status and health score
- Completed tasks summary (Tasks #0-4)
- Git repository status
- Production deployment history
- Current features in production
- Security improvements implemented
- Sprint summary and achievements

**When to Use:** Check current project status, review completed work, understand production environment

---

#### TASKS.md
**Purpose:** Master task tracking document
**Contents:**
- All project tasks from inception
- Task #0: Initial deployment & security hardening
- Task #1: Dashboard activity logs enhancement
- Task #2: Tender & quotation expiry management
- Task #3: Automated database backup system
- Task #4: Admin user creation & security hardening
- Task backlog (future enhancements)
- Detailed task specifications and completion criteria

**When to Use:** Track task progress, understand project history, plan future work

---

#### TASK-5-CONSOLE-CLEANUP.md
**Purpose:** Future task plan for cleaning console errors
**Contents:**
- Detailed error analysis (ApexCharts, SweetAlert2, Quill)
- Multiple solution options
- 6-phase implementation plan (2-3 hours)
- Testing checklist
- Rollback procedures
- Git commit template

**When to Use:** When ready to fix console errors, reference for implementation

**Status:** Planned (Not Started)
**Priority:** Low (Non-Critical)

---

## 📂 02-DEPLOYMENT - Deployment Guides

**Location:** `DOCS/02-DEPLOYMENT/`

### Documents

#### SSH-ACCESS-GUIDE.md
**Purpose:** Complete guide for SSH access to production server
**Contents:**
- SSH connection methods (direct, key-based, cPanel Terminal)
- How to find SSH credentials
- File transfer methods (SCP, SFTP)
- Complete deployment workflow
- Useful SSH commands reference
- Security best practices
- Troubleshooting common SSH issues

**When to Use:** Need to SSH into server, deploy files, troubleshoot server issues

**Server:** reseller142.aserv.co.za

---

#### DEPLOYMENT-SUCCESS-20251208.md
**Purpose:** Session timeout feature deployment report
**Contents:**
- Deployment overview (Dec 8, 2025)
- Files deployed (session-timeout.js, Index.php, dashboardLayout.php)
- Deployment method (cPanel Terminal)
- Testing results (all passed)
- Activity Log integration verification
- Performance impact analysis
- Backup information
- Rollback procedure

**When to Use:** Reference for session timeout deployment, verify what was deployed

**Status:** Completed - Feature Live in Production

---

### Deployment Packages

**Location:** `DOCS/02-DEPLOYMENT/deployment-packages/`

All deployment packages are organized here with complete instructions:

#### session-timeout-20251208/
- Session timeout feature (Dec 8, 2025)
- Files: session-timeout.js, Index.php, dashboardLayout.php
- Docs: README.md (32 sections), TESTING-GUIDE.md

#### dashboard-fix-20251208/
- Dashboard crash fixes
- Files: Index.php, dashboardLayout.php modifications

#### user-management-task4-20251207/
- User creation feature
- SQL migrations, controller updates

#### database-backup-patch/
- Automated backup system
- Cron setup, backup scripts

#### tender-quotation-phase1-2-20251204-180842/
- Tender/quotation expiry management (Phases 1-2)

#### tender-quotation-phase3-20251204-232322/
- Public archive pages (Phase 3)

#### Other Packages
- DEPLOYMENT-REPORT-20251208.md
- EMAIL-TO-ADMINISTRATOR.md
- QUICK-DEPLOY-GUIDE.md
- Various .tar.gz and .zip archives

---

## 📂 03-FEATURES - Feature Documentation

**Location:** `DOCS/03-FEATURES/`

### Documents

#### BACKUP-SYSTEM-DOCUMENTATION.md
**Purpose:** Complete documentation for automated database backup system
**Contents:**
- System overview and architecture
- Backup schedule (daily 2:00 AM SAST)
- Intelligent retention policy (7 daily, 4 weekly, 3 monthly)
- Dashboard interface guide (/dashboard/backups)
- Backup script documentation
- Cron job setup
- Restoration procedures
- Troubleshooting guide

**When to Use:** Understand backup system, restore database, troubleshoot backups

**Feature Status:** Live in Production
**Related Task:** Task #3

---

## 📂 04-PROPOSALS - Future Ideas & Proposals

**Location:** `DOCS/04-PROPOSALS/`

### Documents

#### PROPOSAL-UMDONI-MOBILE-APP.md
**Purpose:** Strategic mobile app concepts for uMdoni Municipality
**Contents:**
- 5 app concepts with full business cases
- "My uMdoni" Super App (recommended - ROI 1000%+)
- Tourism & Events app
- Smart Waste Management app
- Business Hub app
- Safe uMdoni app
- Financial impact and ROI calculations
- Grant funding opportunities (70-100% coverage)
- Implementation timelines and costs
- Success stories from other SA municipalities

**When to Use:** Pitch new project ideas, plan mobile app development

**Status:** Proposal (Future Consideration)
**Recommended:** "My uMdoni" Super App

---

## 📂 05-TESTING - Testing Documentation

**Location:** `DOCS/05-TESTING/`

### Documents

#### phpunit.md
**Purpose:** PHPUnit testing setup and configuration
**Contents:**
- PHPUnit installation instructions
- Test configuration
- Running tests
- Writing unit tests

**When to Use:** Set up testing, write tests, run test suites

---

## 🔍 Quick Reference

### By Task Number

- **Task #0:** Initial deployment → `DOCS/01-PROJECT/TASKS.md`
- **Task #1:** Activity logs → `DOCS/01-PROJECT/TASKS.md`
- **Task #2:** Tender/quotation → `DOCS/01-PROJECT/TASKS.md` + `DOCS/02-DEPLOYMENT/deployment-packages/`
- **Task #3:** Database backups → `DOCS/03-FEATURES/BACKUP-SYSTEM-DOCUMENTATION.md`
- **Task #4:** User management → `DOCS/01-PROJECT/TASKS.md` + `DOCS/02-DEPLOYMENT/deployment-packages/`
- **Task #5:** Console cleanup → `DOCS/01-PROJECT/TASK-5-CONSOLE-CLEANUP.md`

### By Topic

**Project Status & Planning:**
- Current status → `DOCS/01-PROJECT/PROJECT-STATUS-20251208.md`
- Task tracking → `DOCS/01-PROJECT/TASKS.md`
- Future tasks → `DOCS/01-PROJECT/TASK-5-CONSOLE-CLEANUP.md`

**Deployment:**
- SSH access → `DOCS/02-DEPLOYMENT/SSH-ACCESS-GUIDE.md`
- Recent deployment → `DOCS/02-DEPLOYMENT/DEPLOYMENT-SUCCESS-20251208.md`
- Deployment packages → `DOCS/02-DEPLOYMENT/deployment-packages/`

**Features:**
- Database backups → `DOCS/03-FEATURES/BACKUP-SYSTEM-DOCUMENTATION.md`
- Session timeout → `DOCS/02-DEPLOYMENT/DEPLOYMENT-SUCCESS-20251208.md`

**Future Work:**
- Mobile app ideas → `DOCS/04-PROPOSALS/PROPOSAL-UMDONI-MOBILE-APP.md`
- Console cleanup → `DOCS/01-PROJECT/TASK-5-CONSOLE-CLEANUP.md`

**Testing:**
- PHPUnit setup → `DOCS/05-TESTING/phpunit.md`

---

## 🎯 Common Workflows

### Deploying a Feature

1. Read: `DOCS/02-DEPLOYMENT/SSH-ACCESS-GUIDE.md`
2. SSH into server
3. Create backup (see guide)
4. Upload files
5. Test deployment
6. Document in deployment report

### Reviewing Project Status

1. Read: `DOCS/01-PROJECT/PROJECT-STATUS-20251208.md`
2. Check: `DOCS/01-PROJECT/TASKS.md` for task details
3. Review: Git repository status

### Understanding a Feature

1. Check: `DOCS/03-FEATURES/` for feature-specific docs
2. Review: Related task in `DOCS/01-PROJECT/TASKS.md`
3. Find: Deployment package in `DOCS/02-DEPLOYMENT/deployment-packages/`

### Planning Future Work

1. Review: `DOCS/01-PROJECT/TASK-5-CONSOLE-CLEANUP.md`
2. Check: Task backlog in `DOCS/01-PROJECT/TASKS.md`
3. Consider: Proposals in `DOCS/04-PROPOSALS/`

---

## 📊 Document Relationships

### Task #3: Database Backups
```
DOCS/01-PROJECT/TASKS.md (Task #3 specification)
    ↓
DOCS/03-FEATURES/BACKUP-SYSTEM-DOCUMENTATION.md (Feature docs)
    ↓
DOCS/02-DEPLOYMENT/deployment-packages/database-backup-patch/ (Deployment)
```

### Task #4: User Management
```
DOCS/01-PROJECT/TASKS.md (Task #4 specification)
    ↓
DOCS/02-DEPLOYMENT/deployment-packages/user-management-task4-20251207/ (Deployment)
    ↓
DOCS/02-DEPLOYMENT/deployment-packages/dashboard-fix-20251208/ (Critical fixes)
```

### Session Timeout Feature
```
User security concern
    ↓
DOCS/01-PROJECT/TASKS.md (Not formally tracked, but documented)
    ↓
DOCS/02-DEPLOYMENT/deployment-packages/session-timeout-20251208/ (Deployment package)
    ↓
DOCS/02-DEPLOYMENT/DEPLOYMENT-SUCCESS-20251208.md (Deployment report)
```

---

## 🏗️ Production Environment

### Live Features
- Dashboard with activity logs and filtering
- Tender & quotation expiry management
- Automated database backups (2:00 AM SAST)
- Admin user creation and management
- Session timeout security (30 minutes)
- SQL injection protection
- Password security (bcrypt hashing)

### Server Information
- **URL:** https://umdoni.gov.za
- **Server:** reseller142.aserv.co.za
- **Database:** umdonigov_umdoni
- **Timezone:** Africa/Johannesburg (SAST, UTC+2)

### Access
- See: `DOCS/02-DEPLOYMENT/SSH-ACCESS-GUIDE.md`

---

## 📞 Support & Contact

**Developer:** Nhlanhla Mnyandu
**Email:** nhlanhla@isutech.co.za
**Company:** ISU Tech

**Repository:** https://github.com/gedeza/umdoni-website.git
**Production:** https://umdoni.gov.za

---

## 🔄 Document Maintenance

### Updating This Index

When adding new documentation:

1. **Place in appropriate numbered folder:**
   - Project management → `01-PROJECT/`
   - Deployment guides → `02-DEPLOYMENT/`
   - Feature docs → `03-FEATURES/`
   - Proposals → `04-PROPOSALS/`
   - Testing → `05-TESTING/`

2. **Update this INDEX.md:**
   - Add to relevant section
   - Update Table of Contents if needed
   - Add to Quick Reference
   - Update Document Relationships if applicable

3. **Commit changes:**
   ```bash
   git add DOCS/
   git commit -m "Documentation: Add [document name]"
   git push origin main
   ```

### Document Naming Convention

- Use descriptive names with dates if version-specific
- Examples:
  - `PROJECT-STATUS-YYYYMMDD.md`
  - `DEPLOYMENT-SUCCESS-YYYYMMDD.md`
  - `FEATURE-NAME-DOCUMENTATION.md`
  - `TASK-N-DESCRIPTION.md`

---

## 🎯 Getting Started

### New to the Project?

**Read in this order:**

1. **Root README.md** - Project overview
2. **DOCS/01-PROJECT/PROJECT-STATUS-20251208.md** - Current status
3. **DOCS/01-PROJECT/TASKS.md** - Task history and details
4. **DOCS/02-DEPLOYMENT/SSH-ACCESS-GUIDE.md** - Server access
5. **DOCS/03-FEATURES/** - Feature-specific documentation

### Need to Deploy?

1. **DOCS/02-DEPLOYMENT/SSH-ACCESS-GUIDE.md** - How to connect
2. **DOCS/02-DEPLOYMENT/deployment-packages/** - Find your package
3. Follow package README for deployment steps

### Need to Understand a Feature?

1. Check **DOCS/03-FEATURES/** for feature docs
2. Review related task in **DOCS/01-PROJECT/TASKS.md**
3. Find deployment package in **DOCS/02-DEPLOYMENT/deployment-packages/**

---

## 🏆 Project Highlights

### Completed Tasks (5)
- ✅ Task #0: Initial deployment & security hardening
- ✅ Task #1: Dashboard activity logs enhancement
- ✅ Task #2: Tender & quotation expiry management
- ✅ Task #3: Automated database backup system
- ✅ Task #4: Admin user creation & security hardening
- ✅ Session timeout security feature (urgent)

### Security Improvements
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Password hashing (bcrypt)
- ✅ Session timeout (30 minutes)
- ✅ Activity logging and audit trail
- ✅ Authentication security fixes

### System Features
- ✅ Automated database backups (daily 2:00 AM)
- ✅ Intelligent retention (7 daily, 4 weekly, 3 monthly)
- ✅ Tender/quotation expiry tracking
- ✅ Public archive pages
- ✅ Dashboard activity logs with filtering
- ✅ Session timeout with warning

---

## 📈 Project Health

**Overall Status:** 🟢 EXCELLENT (100%)

| Category | Status | Score |
|----------|--------|-------|
| Active Tasks | ✅ All Complete | 100% |
| Security | ✅ Vulnerabilities Fixed | 100% |
| Documentation | ✅ Comprehensive | 100% |
| Git Repository | ✅ Up to Date | 100% |
| Production | ✅ Fully Operational | 100% |
| Testing | ✅ All Validated | 100% |

---

**Documentation Index Version:** 1.0
**Last Updated:** December 9, 2025
**Maintained by:** Nhlanhla Mnyandu (nhlanhla@isutech.co.za)

---

*End of Documentation Index*
