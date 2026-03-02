# Umdoni Municipality Website - Task Tracker
**Last Updated:** 2026-03-03
**Owner:** Nhlanhla Mnyandu (nhlanhla@isutech.co.za)
**Branch:** main

---

## Status Legend
- [ ] Pending
- [x] Completed
- [~] In Progress

---

## TASK 1: Dashboard Activity Logging (NEW FEATURE)

**Priority:** HIGH
**Status:** Completed (2026-03-03)
**Description:** Add a `logActivity()` function that records all dashboard CRUD operations in the existing `logs` table, visible on the Activity Logs page.

### Implementation Summary
- Created `logActivity($action, $resource, $details)` helper in `Components/Helpers.php`
- Uses `LogsModel::LogError()` internally with `status = 'activity'`
- Added dark `ACTIVITY` badge + "Activities" filter option to logs view
- 70 `logActivity()` calls across 20 dashboard controllers

### Implementation Details
| # | Step | File | Status |
|---|------|------|--------|
| 1.1 | Create `logActivity()` helper function | `Components/Helpers.php` | [x] |
| 1.2 | Add "activity" badge to logs view | `App/Views/dashboard/logs/index.php` | [x] |
| 1.3 | Add "Activities" filter dropdown option | `App/Views/dashboard/logs/index.php` | [x] |
| 1.4 | Add logging to Tenders (5 ops) | `App/Controllers/Dashboard/Tenders.php` | [x] |
| 1.5 | Add logging to Quotations (5 ops) | `App/Controllers/Dashboard/Quotations.php` | [x] |
| 1.6 | Add logging to Users (5 ops) | `App/Controllers/Dashboard/Users.php` | [x] |
| 1.7 | Add logging to News (3 ops) | `App/Controllers/Dashboard/News.php` | [x] |
| 1.8 | Add logging to Events (3 ops) | `App/Controllers/Dashboard/Events.php` | [x] |
| 1.9 | Add logging to Notices (3 ops) | `App/Controllers/Dashboard/Notices.php` | [x] |
| 1.10 | Add logging to Projects (3 ops) | `App/Controllers/Dashboard/Projects.php` | [x] |
| 1.11 | Add logging to Backups (2 ops) | `App/Controllers/Dashboard/Backups.php` | [x] |
| 1.12 | Add logging to Settings (3 ops) | `App/Controllers/Dashboard/Settings.php` | [x] |
| 1.13 | Add logging to Roles (3 ops) | `App/Controllers/Dashboard/Roles.php` | [x] |
| 1.14 | Add logging to Agendas (3 ops) | `App/Controllers/Dashboard/Agendas.php` | [x] |
| 1.15 | Add logging to Councillors (9 ops) | `App/Controllers/Dashboard/Councillors.php` | [x] |
| 1.16 | Add logging to Documents (3 ops) | `App/Controllers/Dashboard/Documents.php` | [x] |
| 1.17 | Add logging to Meetings (3 ops) | `App/Controllers/Dashboard/Meetings.php` | [x] |
| 1.18 | Add logging to Newsletters (2 ops) | `App/Controllers/Dashboard/Newsletters.php` | [x] |
| 1.19 | Add logging to Publications (2 ops) | `App/Controllers/Dashboard/Publications.php` | [x] |
| 1.20 | Add logging to Rfps (3 ops) | `App/Controllers/Dashboard/Rfps.php` | [x] |
| 1.21 | Add logging to Services (4 ops) | `App/Controllers/Dashboard/Services.php` | [x] |
| 1.22 | Add logging to Vacancies (3 ops) | `App/Controllers/Dashboard/Vacancies.php` | [x] |
| 1.23 | Add logging to Wardinfo (3 ops) | `App/Controllers/Dashboard/Wardinfo.php` | [x] |
| 1.24 | Create deployment package | `deployment/activity-logging-patch/` | [x] |
| 1.25 | Deploy to production | Manual | [ ] |
| 1.26 | Post-deployment verification | Manual | [ ] |

### Deployment
- Package: `deployment/activity-logging-patch/`
- Script: `bash deployment/activity-logging-patch/deploy.sh`
- No database migrations, no new dependencies
- Rollback: restore from auto-created backup in `backups/pre-activity-logging-*/`

---

## TASK 2: SQL Injection Fixes (CRITICAL SECURITY)

**Priority:** CRITICAL
**Status:** Pending
**Description:** Replace all raw string interpolation in SQL queries with PDO prepared statements.

| # | File | Issue | Status |
|---|------|-------|--------|
| 2.1 | `App/Models/ProjectsModel.php:45` | `WHERE id = '$id'` — direct interpolation | [ ] |
| 2.2 | `App/Models/ProjectsModel.php:115` | INSERT with `$data[title]` etc. — direct interpolation | [ ] |
| 2.3 | `App/Models/CouncillorModel.php:76` | Unsafe query with direct variable | [ ] |
| 2.4 | `App/Models/CouncillorModel.php:88` | Unsafe query with direct variable | [ ] |
| 2.5 | `App/Models/CouncillorModel.php:101` | Unsafe query with direct variable | [ ] |
| 2.6 | `App/Models/Profile.php:74` | INSERT with `$data[name]`, `$data[email]` etc. | [ ] |
| 2.7 | `App/Models/AgendaModel.php` | Multiple unsafe queries | [ ] |

---

## TASK 3: PHP 8.x Compatibility Fixes

**Priority:** HIGH
**Status:** In Progress
**Description:** Fix `strip_tags()` and other functions that no longer accept null parameters in PHP 8.1+.

| # | File | Issue | Status |
|---|------|-------|--------|
| 3.1 | `App/Views/dashboard/tenders/index.php:99` | `strip_tags(null)` | [x] Fixed (2026-03-02) |
| 3.2 | `App/Views/dashboard/quotations/index.php:100` | `strip_tags(null)` | [x] Fixed (2026-03-02) |
| 3.3 | `App/Views/dashboard/projects/index.php` | `strip_tags($project['body'])` — same null issue | [ ] |
| 3.4 | `App/Views/dashboard/news/index.php` | `strip_tags($news['body'])` — same null issue | [ ] |
| 3.5 | `App/Views/dashboard/rfps/index.php` | `strip_tags($service['body'])` — same null issue | [ ] |

---

## TASK 4: XSS Output Escaping

**Priority:** HIGH
**Status:** Pending
**Description:** Add `htmlspecialchars()` to all unescaped user data rendered in views.

| # | File | Issue | Status |
|---|------|-------|--------|
| 4.1 | `App/Views/dashboard/tenders/add.php` | Form values unescaped: `value="'.$title.'"` | [ ] |
| 4.2 | `App/Views/dashboard/quotations/add.php` | Form values unescaped | [ ] |
| 4.3 | `App/Views/dashboard/news/add.php` | Body content unescaped | [ ] |
| 4.4 | `App/Views/dashboard/services/add.php` | Form values unescaped | [ ] |
| 4.5 | `App/Views/dashboard/events/add.php` | Form values unescaped | [ ] |
| 4.6 | `App/Views/dashboard/index.php:177-183` | Event data (title, body, location) unescaped | [ ] |
| 4.7 | `App/Views/dashboard/users/details.php` | User details unescaped in some places | [ ] |

---

## TASK 5: CSRF Protection

**Priority:** HIGH
**Status:** Pending
**Description:** Implement CSRF token generation/validation and add to all POST forms.

| # | Step | Status |
|---|------|--------|
| 5.1 | Create `generateCsrfToken()` and `validateCsrfToken()` in Helpers.php | [ ] |
| 5.2 | Add hidden CSRF field to all dashboard POST forms (23+ forms) | [ ] |
| 5.3 | Add CSRF validation to all controller save/update/delete actions | [ ] |
| 5.4 | Add CSRF token to JavaScript fetch requests (users/index.php) | [ ] |

---

## TASK 6: Unsafe Deserialization Fix

**Priority:** MEDIUM
**Status:** Pending
**Description:** Replace `serialize()`/`unserialize()` with `json_encode()`/`json_decode()` in Settings.

| # | File | Issue | Status |
|---|------|-------|--------|
| 6.1 | `App/Controllers/Dashboard/Settings.php:32` | `unserialize($services['settings'])` | [ ] |
| 6.2 | `App/Controllers/Dashboard/Settings.php:119` | `serialize($data)` | [ ] |

---

## TASK 7: File Upload Validation

**Priority:** MEDIUM
**Status:** Pending
**Description:** Add server-side file type, size, and name validation before S3 upload.

| # | File | Issue | Status |
|---|------|-------|--------|
| 7.1 | `App/Controllers/Dashboard/Events.php` | No file type/size validation | [ ] |
| 7.2 | `App/Controllers/Dashboard/Services.php` | No file type/size validation | [ ] |
| 7.3 | `App/Views/dashboard/quotations/add.php:113` | Syntax error: `accept="application/pdf>` (missing closing quote) | [ ] |

---

## TASK 8: Dashboard Performance

**Priority:** MEDIUM
**Status:** Pending
**Description:** Optimize queries and data loading for dashboard pages.

| # | Issue | File | Status |
|---|-------|------|--------|
| 8.1 | Dashboard home loads ALL records (no limits) | `App/Controllers/Dashboard/Index.php` | [ ] |
| 8.2 | Duplicate `Request::getAll()` call | `App/Controllers/Dashboard/Index.php` | [ ] |
| 8.3 | No pagination on users, tenders, quotations lists | Multiple controllers | [ ] |

---

## TASK 9: Error Handling Improvements

**Priority:** MEDIUM
**Status:** Pending
**Description:** Stop exposing stack traces to users; log errors server-side instead.

| # | File | Issue | Status |
|---|------|-------|--------|
| 9.1 | `Core/Error.php:45-64` | Stack traces echoed to browser | [ ] |
| 9.2 | Multiple controllers | `echo $th->getMessage()` in catch blocks | [ ] |

---

## TASK 10: Session Security Hardening

**Priority:** MEDIUM
**Status:** Pending
**Description:** Add secure cookie flags and session configuration.

| # | Item | Status |
|---|------|--------|
| 10.1 | Set `session.cookie_httponly = 1` | [ ] |
| 10.2 | Set `session.cookie_secure = 1` (HTTPS) | [ ] |
| 10.3 | Set `session.cookie_samesite = Strict` | [ ] |
| 10.4 | Set `session.use_strict_mode = 1` | [ ] |

---

## TASK 11: Authorization / RBAC

**Priority:** MEDIUM
**Status:** Pending
**Description:** Implement role-based access control server-side, not just sidebar visibility.

| # | Item | Status |
|---|------|--------|
| 11.1 | Implement role check in `enable_authorize($requiredRole)` | [ ] |
| 11.2 | Add role validation to sensitive controllers (Users, Settings, Roles, Backups) | [ ] |
| 11.3 | Change delete operations from GET to POST | [ ] |

---

## TASK 12: JS Console Errors & Sidebar Fix

**Priority:** HIGH
**Status:** Completed
**Description:** Fix JavaScript errors on all dashboard pages and sidebar active detection.

| # | Issue | Status |
|---|-------|--------|
| 12.1 | Remove theme demo scripts (toastify, sweetalert2 extensions) | [x] (2026-03-02) |
| 12.2 | Move dashboard.js to dashboard home view only | [x] (2026-03-02) |
| 12.3 | Remove unused Quill form-editor.js | [x] (2026-03-02) |
| 12.4 | Fix sidebar active detection for multi-word labels | [x] (2026-03-02) |
| 12.5 | Add mazer.js scrollIntoView null guard | [x] (2026-03-02) |

---

## TASK 13: UX Enhancements (Future)

**Priority:** LOW
**Status:** Pending

| # | Enhancement | Status |
|---|-------------|--------|
| 13.1 | Add reCAPTCHA to public contact form | [ ] |
| 13.2 | Add loading/disabled state on form submit buttons | [ ] |
| 13.3 | Add empty states to all list pages | [ ] |
| 13.4 | Move inline CSS/JS to external files | [ ] |
| 13.5 | Improve accessibility (aria labels on icon-only buttons) | [ ] |

---

## TASK 14: Activity Logs Monitoring & Reports

**Priority:** MEDIUM
**Status:** Pending
**Depends On:** Task 1 deployed to production
**Description:** Enhance the Activity Logs page with date range filtering, CSV export, and summary statistics for audit reporting and monitoring.

### Features

#### 14.1 — Date Range Filter
Add "From" and "To" date pickers to the existing filter form so logs can be queried for a specific period (e.g., "all activity in February" or "last 7 days").

| # | Step | File | Status |
|---|------|------|--------|
| 14.1.1 | Add `GetByDateRange($from, $to, $type)` method | `App/Models/LogsModel.php` | [ ] |
| 14.1.2 | Add date params to controller `indexAction()` | `App/Controllers/Dashboard/Logs.php` | [ ] |
| 14.1.3 | Add date picker inputs to filter form | `App/Views/dashboard/logs/index.php` | [ ] |

#### 14.2 — CSV Export
Add an "Export CSV" button that downloads the currently filtered logs as a spreadsheet. Columns: Type, Username, Email, Action, Timestamp, IP Address.

| # | Step | File | Status |
|---|------|------|--------|
| 14.2.1 | Add `exportAction()` method | `App/Controllers/Dashboard/Logs.php` | [ ] |
| 14.2.2 | Generate CSV with headers + filtered data | `App/Controllers/Dashboard/Logs.php` | [ ] |
| 14.2.3 | Add "Export CSV" button to view (passes current filters) | `App/Views/dashboard/logs/index.php` | [ ] |

#### 14.3 — Summary Statistics Cards
Add a row of stat cards above the table showing counts for the current filter: total logs, activities, logins, errors. Quick at-a-glance monitoring.

| # | Step | File | Status |
|---|------|------|--------|
| 14.3.1 | Add `GetCounts($from, $to)` method | `App/Models/LogsModel.php` | [ ] |
| 14.3.2 | Pass stats to view from controller | `App/Controllers/Dashboard/Logs.php` | [ ] |
| 14.3.3 | Add Bootstrap stat cards row above table | `App/Views/dashboard/logs/index.php` | [ ] |

### Technical Notes
- All within existing files — no new tables, controllers, or dependencies
- Date filtering uses SQL `BETWEEN` on `time_log` column
- CSV export streams directly (no temp file) using `php://output`
- Stats query uses `COUNT(*) ... GROUP BY status` for efficiency
- Default date range: last 30 days (prevent loading entire log history)

### Files Modified
1. `App/Models/LogsModel.php` — `GetByDateRange()`, `GetCounts()`
2. `App/Controllers/Dashboard/Logs.php` — date params, `exportAction()`
3. `App/Views/dashboard/logs/index.php` — date pickers, stats cards, export button

---

## Completed Work Log

| Date | Commit | Description |
|------|--------|-------------|
| 2026-03-03 | pending | Dashboard Activity Logging — 70 logActivity() calls across 20 controllers |
| 2026-03-02 | `61dac7d` | Fix JS console errors + strip_tags null on tenders/quotations |
| 2026-03-02 | `ffe9acb` | Fix sidebar active detection for multi-word menu labels |

---

## Notes
- Production server: reseller142.aserv.co.za
- Database: umdonigov_umdoni
- Deploy via cPanel File Manager or SCP
- Always backup production files before deploying
- Test locally with `php -S localhost:8000 -t public/` before deploying
