# uMdoni Municipality Website - Task Tracking

**Project:** uMdoni Local Municipality Website Enhancement
**Repository:** umdoni-website
**Last Updated:** 2025-12-02

---

## 🎯 ACTIVE TASKS

### Task #1: Dashboard Activity Logs Enhancement

**Priority:** HIGH
**Status:** ✅ COMPLETED
**Assigned:** Development Team
**Start Date:** 2025-12-02
**Completion Date:** 2025-12-02
**Actual Duration:** ~1 hour

#### Problem Statement
The current Activity Logs dashboard only displays basic user login/logout information. After implementing user-friendly error messages in authentication workflows, we need a way to monitor and debug errors from the dashboard. Currently:
- Error logs are written to file system (`logs/YYYY-MM-DD.txt`) but not visible in dashboard
- Rollbar integration exists but requires external service access
- No way to see authentication errors, AWS Cognito errors, or application errors in admin dashboard
- Database `logs` table has unused columns (`actions`, `location`) that could be utilized

#### Objectives
1. ✅ Leverage existing database columns for error logging
2. ✅ Add error logging capability to LogsModel
3. ✅ Display errors alongside activity logs in dashboard
4. ✅ Add filtering by log type (login, logout, error, warning)
5. ✅ Improve dashboard UI with color-coded log types
6. ✅ Keep implementation simple and fast (1-2 hours)

#### Task Breakdown

**Phase 1: Backend Enhancement** ✅ COMPLETED
- [x] 1.1 Update LogsModel.php
  - [x] Add `LogError($message, $level, $context)` method
  - [x] Add `GetByType($type)` filtering method
  - [x] Add `GetRecent($limit, $type)` method
  - [x] Fix SQL injection vulnerabilities (use prepared statements)
- [x] 1.2 Update error handling to log to database
  - [x] Modify Authentication.php catch blocks to log errors
  - [x] Keep UserFriendlyErrors for users, but log technical details to database
  - [x] Add IP address and user agent tracking

**Phase 2: Frontend Enhancement** ✅ COMPLETED
- [x] 2.1 Update Logs Controller
  - [x] Add filtering by log type
  - [x] Add user search filtering
  - [x] Keep simple - use GET parameters
- [x] 2.2 Update dashboard view (index.php)
  - [x] Add filter dropdowns (All, Logins, Errors, Warnings)
  - [x] Add user search input
  - [x] Color-code log types (green=login, red=error, yellow=warning)
  - [x] Display error messages in dedicated column
  - [x] Show IP address and user agent for debugging

**Phase 3: Additional Enhancements** ✅ COMPLETED
- [x] 3.1 Remove Rollbar integration
  - [x] Removed from Core/Error.php
  - [x] Removed from public/index.php
  - [x] Removed from composer.json

#### Technical Implementation Details

**Database Schema (existing columns to utilize):**
```sql
logs table:
- id (int) - Primary key
- userId (varchar) - User ID or "system" for errors
- username (varchar) - Username or error source
- email (varchar) - User email if applicable
- time_log (varchar) - Timestamp
- status (varchar) - Use for log type: "login", "logout", "error", "warning", "info"
- actions (varchar) - UNUSED - Use for error message/action description
- last_login (varchar) - Keep for login tracking
- logout (varchar) - Keep for logout tracking
- location (varchar) - UNUSED - Use for IP address or location context
```

**New LogsModel Methods:**
```php
public static function LogError($message, $level = 'error', $context = [])
public static function GetByType($type)
public static function GetRecent($limit = 100, $type = null)
```

#### Success Criteria
- [x] Authentication errors appear in Activity Logs dashboard
- [x] Can filter logs by type (all/login/error/warning)
- [x] Error messages show technical details for debugging
- [x] Color-coded UI makes it easy to spot errors
- [x] Existing login/logout tracking continues to work
- [x] No SQL injection vulnerabilities
- [x] Rollbar completely removed from codebase

#### Dependencies
- Existing logs table structure (no schema changes needed)
- Existing Logs controller and view

#### Blockers
- None

---

### Task #2: Tender & Quotation Expiry Management System

**Priority:** HIGH
**Status:** 🟢 Ready to Start
**Assigned:** Development Team
**Start Date:** 2025-12-01
**Target Completion:** TBD

#### Problem Statement
The uMdoni Municipality website currently displays expired tenders and quotations alongside active ones on the main listing pages. This creates clutter and confusion for users who cannot easily distinguish between:
- Active opportunities (still accepting submissions)
- Expired opportunities (past their closing date)

#### Objectives
1. ✅ Automatically identify expired tenders and quotations based on closing dates
2. ✅ Archive expired items separately from active items
3. ✅ Create dedicated archive pages for historical tenders and quotations
4. ✅ Keep active pages clean by showing only current opportunities
5. ✅ Maintain data integrity and accessibility for auditing purposes

#### Task Breakdown

**Phase 1: Analysis & Planning** 🟡 IN PROGRESS
- [ ] 1.1 Analyze current tender implementation
  - [ ] Review tender database schema
  - [ ] Review tender models (`App/Models/TenderModel.php`)
  - [ ] Review tender controllers (`App/Controllers/Tenders.php`, `App/Controllers/Dashboard/Tenders.php`)
  - [ ] Review tender views (`App/Views/tenders/`, `App/Views/dashboard/tenders/`)
  - [ ] Document current data flow
- [ ] 1.2 Analyze current quotation implementation
  - [ ] Review quotation database schema
  - [ ] Review quotation models (`App/Models/QuotationsModel.php`)
  - [ ] Review quotation controllers (`App/Controllers/Quotations.php`, `App/Controllers/Dashboard/Quotations.php`)
  - [ ] Review quotation views (`App/Views/quotations/`, `App/Views/dashboard/quotations/`)
  - [ ] Document current data flow
- [ ] 1.3 Identify existing date fields and business rules
  - [ ] Determine how closing dates are stored
  - [ ] Identify any existing expiry logic
  - [ ] Document timezone considerations
- [ ] 1.4 Design solution architecture
  - [ ] Define "expired" criteria
  - [ ] Design database schema changes (if needed)
  - [ ] Plan archiving mechanism (manual vs automatic)
  - [ ] Design archive page structure
  - [ ] Create wireframes/mockups for archive pages

**Phase 2: Database & Backend** ⚪ NOT STARTED
- [ ] 2.1 Database modifications
  - [ ] Add status/archive field if needed
  - [ ] Create database migration script
  - [ ] Test migration on local environment
  - [ ] Document schema changes
- [ ] 2.2 Model updates
  - [ ] Update TenderModel with expiry detection logic
  - [ ] Update QuotationsModel with expiry detection logic
  - [ ] Add methods to fetch active items only
  - [ ] Add methods to fetch archived items only
  - [ ] Write unit tests for new methods
- [ ] 2.3 Controller updates
  - [ ] Update public tender controller to show active only
  - [ ] Update public quotation controller to show active only
  - [ ] Create archive controller actions
  - [ ] Update dashboard controllers if needed
  - [ ] Add filters for admin to view all/active/archived

**Phase 3: Frontend & Views** ⚪ NOT STARTED
- [ ] 3.1 Update active listing pages
  - [ ] Modify tender index view to show active only
  - [ ] Modify quotation index view to show active only
  - [ ] Add visual indicators for closing soon items
  - [ ] Add link to archive page
- [ ] 3.2 Create archive pages
  - [ ] Design archive page layout
  - [ ] Create tender archive view
  - [ ] Create quotation archive view
  - [ ] Add search/filter functionality for archives
  - [ ] Add pagination for archived items
- [ ] 3.3 Update dashboard admin views
  - [ ] Add status filter dropdown (All/Active/Archived)
  - [ ] Add visual indicators for expired items
  - [ ] Update forms if needed

**Phase 4: Automation** ⚪ NOT STARTED
- [ ] 4.1 Implement automatic archiving
  - [ ] Create cron job / scheduled task for archiving
  - [ ] Add logging for archiving operations
  - [ ] Test automated archiving
- [ ] 4.2 Email notifications (optional)
  - [ ] Notify admin when items are auto-archived
  - [ ] Warning emails for items closing soon

**Phase 5: Testing & Deployment** ⚪ NOT STARTED
- [ ] 5.1 Testing
  - [ ] Unit tests for models
  - [ ] Integration tests for controllers
  - [ ] User acceptance testing
  - [ ] Cross-browser testing
  - [ ] Mobile responsiveness testing
- [ ] 5.2 Documentation
  - [ ] Update README with new features
  - [ ] Create user guide for archive pages
  - [ ] Document admin procedures
- [ ] 5.3 Deployment
  - [ ] Deploy to staging environment
  - [ ] Staging environment testing
  - [ ] Backup production database
  - [ ] Deploy to production
  - [ ] Post-deployment verification
  - [ ] Monitor for issues

#### Technical Notes
- **Date Comparison:** Ensure server timezone is correctly set
- **Database:** Check if soft deletes are preferred over hard archiving
- **Performance:** Consider indexing on date fields for faster queries
- **Backwards Compatibility:** Ensure existing tenders/quotations remain accessible

#### Success Criteria
- [ ] No expired tenders appear on main tender listing page
- [ ] No expired quotations appear on main quotation listing page
- [ ] Archive pages are accessible and functional
- [ ] Admin dashboard shows clear status indicators
- [ ] Automated archiving runs successfully
- [ ] All tests pass
- [ ] Documentation is complete

#### Dependencies
- None identified yet

#### Blockers
- None identified yet

---

## ✅ COMPLETED TASKS

### Task #0: Initial Deployment & Security Hardening

**Status:** ✅ COMPLETED
**Completion Date:** 2025-12-01

#### Accomplishments
- ✅ Removed 7 malware files from production server
- ✅ Fixed JavaScript null pointer errors (offcanvas-navbar.js, datatables)
- ✅ Created UserFriendlyErrors helper class for better UX
- ✅ Updated all Authentication controller error handling
- ✅ Implemented Toastify notification system
- ✅ Updated project documentation (README.md)
- ✅ Updated .gitignore to prevent malware commits
- ✅ Synchronized local and production codebases

#### Files Modified
- `Components/UserFriendlyErrors.php` (NEW)
- `App/Controllers/Authentication.php`
- `public/Includes/include-js.php`
- `public/Includes/parts/alerts.php`
- `public/assets/js/offcanvas-navbar.js`
- `.gitignore`
- `README.md`

#### Git Commits
- `e95c837` - Security: Update .gitignore to exclude malware and build files
- `d897d2d` - Add comprehensive README documentation
- `3be3065` - Fix JavaScript errors and improve PHP built-in server compatibility
- `9944aa8` - Add user-friendly error messages with Toastify notifications
- `17a4abc` - Remove debug logging from Authentication controller

---

## 📋 TASK BACKLOG

### Future Enhancements
- [ ] Improve toast notification display for signup form errors
- [ ] Add SSL certificate renewal automation
- [ ] Implement automated database backups
- [ ] Add monitoring and alerting for malware detection
- [ ] Performance optimization (caching, CDN)
- [ ] Accessibility audit and improvements (WCAG 2.1 compliance)
- [ ] Mobile app development consideration

---

## 📝 NOTES & DECISIONS

### 2025-12-01
- Decided to focus on tender/quotation expiry as next priority
- Will create task tracking document for better project management
- Toast notification issue deferred to later (not blocking functionality)

---

## 🔗 USEFUL LINKS

- **Production Site:** https://umdoni.gov.za
- **cPanel:** https://reseller142.aserv.co.za:2083
- **Repository:** (Add GitHub/GitLab URL when available)
- **Documentation:** See README.md

---

**Note:** This document should be updated regularly as tasks progress. Use checkboxes [x] to mark completed items.
