# Proposal: Website Security & Performance Enhancement
## uMdoni Municipality Website

**Prepared by:** ISU Tech
**Author:** Nhlanhla Mnyandu (nhlanhla@isutech.co.za)
**Date:** 13 June 2026
**Version:** 1.0
**Status:** Pending Client Approval

---

## Executive Summary

This proposal outlines a comprehensive security hardening and performance enhancement package for the uMdoni Municipality website. Following the successful implementation of the Activity Logging system and SQL Injection fixes, this proposal addresses the remaining security vulnerabilities and performance optimizations identified during our security audit.

### Key Objectives

| Objective | Priority | Impact |
|-----------|----------|--------|
| **Security Hardening** | Critical | Protect against OWASP Top 10 vulnerabilities |
| **Performance Optimization** | High | Faster dashboard, improved user experience |
| **Compliance Readiness** | Medium | Meet government security standards |
| **User Experience** | Medium | Enhanced accessibility and usability |

### Summary of Work Packages

| Package | Tasks | Priority | Est. Hours |
|---------|-------|----------|------------|
| **A. Critical Security Fixes** | XSS, CSRF, Session Security | Critical | 16 |
| **B. Security Hardening** | Deserialization, File Upload, RBAC | High | 12 |
| **C. PHP Compatibility** | PHP 8.x fixes | High | 4 |
| **D. Performance Optimization** | Dashboard queries, pagination | Medium | 10 |
| **E. Audit & Monitoring** | Log reports, date filtering, export | Medium | 8 |
| **F. UX Enhancements** | reCAPTCHA, accessibility, loading states | Low | 8 |
| **Total** | | | **58 hours** |

---

## Recent Completed Work (June 2026)

Before detailing the proposed work, here is a summary of security improvements already completed:

### Completed: SQL Injection Fixes
**Date:** 13 June 2026
**Status:** ✅ Deployed

Fixed 17 SQL injection vulnerabilities across 4 model files:

| File | Methods Fixed |
|------|---------------|
| `ProjectsModel.php` | getById, Save, Delete, Restore |
| `CouncillorModel.php` | getCouncillorById, getExcoById, getSeniorManById, Save, Delete, DeleteMan, DeleteExco |
| `Profile.php` | Save |
| `AgendaModel.php` | GetById, Save, Update, Delete, Restore |

**Impact:** Website now protected against SQL injection attacks, one of the most critical OWASP Top 10 vulnerabilities.

### Completed: Dashboard Activity Logging
**Date:** March 2026
**Status:** ✅ Deployed

- 70 activity logging calls across 20 dashboard controllers
- Complete audit trail for all administrative actions
- Activity badge and filtering in logs view

---

## Package A: Critical Security Fixes

### A1. Cross-Site Scripting (XSS) Protection
**Priority:** CRITICAL
**OWASP Category:** A7:2017 - Cross-Site Scripting

#### Current Risk
User-submitted data is rendered in dashboard views without proper escaping, allowing potential injection of malicious JavaScript.

#### Affected Files

| File | Issue |
|------|-------|
| `dashboard/tenders/add.php` | Form values unescaped |
| `dashboard/quotations/add.php` | Form values unescaped |
| `dashboard/news/add.php` | Body content unescaped |
| `dashboard/services/add.php` | Form values unescaped |
| `dashboard/events/add.php` | Form values unescaped |
| `dashboard/index.php` | Event data unescaped |
| `dashboard/users/details.php` | User details unescaped |

#### Solution
Implement `htmlspecialchars()` output escaping:

```php
// Before (Vulnerable)
<input value="<?php echo $title; ?>">

// After (Secure)
<input value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
```

#### Deliverables
- [ ] Create `escape()` helper function for consistent escaping
- [ ] Update all 7 affected view files
- [ ] Test all forms for proper display of special characters

**Estimated Hours:** 6

---

### A2. Cross-Site Request Forgery (CSRF) Protection
**Priority:** CRITICAL
**OWASP Category:** A5:2017 - Broken Access Control

#### Current Risk
Dashboard forms lack CSRF tokens, allowing attackers to trick administrators into performing unwanted actions.

#### Attack Scenario
1. Administrator is logged into uMdoni dashboard
2. Administrator visits a malicious website
3. Malicious site submits hidden form to delete user/tender
4. Action executes with administrator's session

#### Solution
Implement CSRF token system:

```php
// Token Generation (in session)
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// In Form
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// In Controller
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

#### Deliverables
- [ ] Create `generateCsrfToken()` function in Helpers.php
- [ ] Create `validateCsrfToken()` function in Helpers.php
- [ ] Add hidden CSRF field to all 23+ dashboard POST forms
- [ ] Add CSRF validation to all controller save/update/delete actions
- [ ] Update JavaScript fetch requests with CSRF headers

**Estimated Hours:** 6

---

### A3. Session Security Hardening
**Priority:** HIGH
**OWASP Category:** A2:2017 - Broken Authentication

#### Current Risk
Session cookies lack security flags, making them vulnerable to theft via XSS or network interception.

#### Configuration Changes

| Setting | Current | Proposed | Purpose |
|---------|---------|----------|---------|
| `session.cookie_httponly` | 0 | 1 | Prevent JavaScript access to cookies |
| `session.cookie_secure` | 0 | 1 | Require HTTPS for cookies |
| `session.cookie_samesite` | None | Strict | Prevent cross-site request attacks |
| `session.use_strict_mode` | 0 | 1 | Reject uninitialized session IDs |

#### Implementation
Add to `public/index.php` before `session_start()`:

```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
```

#### Deliverables
- [ ] Configure secure session settings
- [ ] Test session functionality after changes
- [ ] Verify cookies have correct flags in browser

**Estimated Hours:** 4

---

## Package B: Security Hardening

### B1. Unsafe Deserialization Fix
**Priority:** HIGH
**OWASP Category:** A8:2017 - Insecure Deserialization

#### Current Risk
Settings controller uses PHP's `unserialize()` which can lead to remote code execution if attacker-controlled data is deserialized.

#### Affected Code
```php
// Current (Vulnerable)
$settings = unserialize($services['settings']);

// Proposed (Secure)
$settings = json_decode($services['settings'], true);
```

#### Deliverables
- [ ] Replace `serialize()` with `json_encode()` in Settings controller
- [ ] Replace `unserialize()` with `json_decode()` in Settings controller
- [ ] Migrate existing serialized data to JSON format
- [ ] Test settings save/load functionality

**Estimated Hours:** 4

---

### B2. File Upload Validation
**Priority:** HIGH
**OWASP Category:** A1:2017 - Injection

#### Current Risk
File uploads lack server-side validation, potentially allowing upload of malicious files.

#### Validation Requirements

| Check | Implementation |
|-------|----------------|
| **File Type** | Whitelist allowed MIME types (PDF, images) |
| **File Extension** | Verify extension matches content |
| **File Size** | Limit to reasonable size (10MB) |
| **Filename** | Sanitize to prevent directory traversal |

#### Implementation
```php
function validateUpload($file, $allowedTypes = ['application/pdf']) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type');
    }

    if ($file['size'] > 10 * 1024 * 1024) { // 10MB
        throw new Exception('File too large');
    }

    return true;
}
```

#### Affected Files
- `App/Controllers/Dashboard/Events.php`
- `App/Controllers/Dashboard/Services.php`
- `App/Views/dashboard/quotations/add.php` (syntax error fix)

#### Deliverables
- [ ] Create `validateUpload()` helper function
- [ ] Add validation to Events controller uploads
- [ ] Add validation to Services controller uploads
- [ ] Fix HTML syntax error in quotations add form

**Estimated Hours:** 4

---

### B3. Role-Based Access Control (RBAC)
**Priority:** MEDIUM
**OWASP Category:** A5:2017 - Broken Access Control

#### Current Risk
Authorization is only enforced in the sidebar (client-side). Users can access restricted URLs directly if they know the path.

#### Solution
Implement server-side role validation:

```php
function requireRole($requiredRole) {
    $userRole = $_SESSION['profile']['role'] ?? 'guest';
    $roleHierarchy = ['admin' => 3, 'editor' => 2, 'viewer' => 1, 'guest' => 0];

    if ($roleHierarchy[$userRole] < $roleHierarchy[$requiredRole]) {
        header('HTTP/1.1 403 Forbidden');
        die('Access denied');
    }
}

// Usage in controller
public function usersAction() {
    requireRole('admin');
    // ... rest of action
}
```

#### Additional Security
- Change delete operations from GET to POST requests
- Add confirmation dialogs for destructive actions

#### Deliverables
- [ ] Implement `requireRole()` authorization function
- [ ] Add role validation to Users, Settings, Roles, Backups controllers
- [ ] Convert delete operations from GET to POST
- [ ] Add confirmation modals for delete actions

**Estimated Hours:** 4

---

## Package C: PHP 8.x Compatibility

### C1. Null Parameter Fixes
**Priority:** HIGH
**Reason:** PHP 8.1+ deprecates passing null to many string functions

#### Current Warnings
```
Deprecated: strip_tags(): Passing null to parameter #1 ($string) of type string is deprecated
```

#### Affected Files

| File | Issue |
|------|-------|
| `dashboard/projects/index.php` | `strip_tags($project['body'])` |
| `dashboard/news/index.php` | `strip_tags($news['body'])` |
| `dashboard/rfps/index.php` | `strip_tags($service['body'])` |

#### Solution
```php
// Before
strip_tags($project['body'])

// After
strip_tags($project['body'] ?? '')
```

#### Deliverables
- [ ] Fix 3 remaining strip_tags null issues
- [ ] Scan codebase for other PHP 8.x deprecations
- [ ] Test all dashboard pages for warnings

**Estimated Hours:** 4

---

## Package D: Performance Optimization

### D1. Dashboard Query Optimization
**Priority:** MEDIUM
**Impact:** Faster page loads, reduced server load

#### Current Issues

| Issue | Location | Impact |
|-------|----------|--------|
| No query limits | Dashboard home | Loads ALL records |
| Duplicate queries | Dashboard home | Same data fetched twice |
| No pagination | User/Tender/Quotation lists | Slow with large datasets |

#### Solutions

**Query Limits:**
```php
// Before
$tenders = TendersModel::Get();

// After
$tenders = TendersModel::GetRecent(10); // Only latest 10
```

**Pagination:**
```php
// Add to model
public static function GetPaginated($page = 1, $perPage = 20) {
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT * FROM tenders LIMIT :offset, :perPage";
    // ...
}
```

#### Deliverables
- [ ] Add `GetRecent($limit)` methods to dashboard models
- [ ] Remove duplicate `Request::getAll()` call
- [ ] Implement pagination for Users list
- [ ] Implement pagination for Tenders list
- [ ] Implement pagination for Quotations list

**Estimated Hours:** 10

---

## Package E: Audit & Monitoring Enhancements

### E1. Activity Logs Reporting
**Priority:** MEDIUM
**Benefit:** Better audit compliance, easier monitoring

#### Features

**Date Range Filter:**
- Add "From" and "To" date pickers
- Filter logs by specific time periods
- Default to last 30 days

**CSV Export:**
- Export filtered logs to spreadsheet
- Include: Type, Username, Email, Action, Timestamp, IP Address
- Useful for compliance audits

**Summary Statistics:**
- Total logs count
- Login attempts (successful/failed)
- Activities by type
- Error count

#### Deliverables
- [ ] Add `GetByDateRange($from, $to, $type)` method to LogsModel
- [ ] Add date picker inputs to filter form
- [ ] Create `exportAction()` for CSV download
- [ ] Add statistics cards above logs table

**Estimated Hours:** 8

---

## Package F: UX Enhancements

### F1. Security & Usability Improvements
**Priority:** LOW
**Benefit:** Better user experience, reduced spam

#### Features

| Enhancement | Benefit |
|-------------|---------|
| **reCAPTCHA** | Prevent spam on contact form |
| **Loading States** | Visual feedback during form submission |
| **Empty States** | Helpful messages when lists are empty |
| **External CSS/JS** | Cleaner code, better caching |
| **Accessibility** | WCAG compliance, aria labels |

#### Deliverables
- [ ] Integrate Google reCAPTCHA on public contact form
- [ ] Add loading/disabled state to form submit buttons
- [ ] Add empty state messages to all list pages
- [ ] Move inline CSS/JS to external files
- [ ] Add aria-labels to icon-only buttons

**Estimated Hours:** 8

---

## Error Handling Improvements

### E2. Secure Error Display
**Priority:** MEDIUM
**OWASP Category:** A3:2017 - Sensitive Data Exposure

#### Current Risk
Stack traces and detailed error messages are displayed to users, revealing internal system information.

#### Solution
```php
// In Core/Error.php
if (Config::SHOW_ERRORS) {
    // Development: show details
    echo $exception->getMessage();
    echo $exception->getTraceAsString();
} else {
    // Production: log only
    error_log($exception->getMessage());
    include 'Views/errors/500.php'; // User-friendly page
}
```

#### Deliverables
- [ ] Update error handler to check environment
- [ ] Create user-friendly error pages (500, 404)
- [ ] Remove `echo $th->getMessage()` from catch blocks
- [ ] Ensure all errors are logged server-side

**Estimated Hours:** 4

---

## Implementation Timeline

### Phase 1: Critical Security (Week 1-2)
| Package | Tasks | Hours |
|---------|-------|-------|
| A1 | XSS Protection | 6 |
| A2 | CSRF Protection | 6 |
| A3 | Session Security | 4 |
| **Subtotal** | | **16** |

### Phase 2: Security Hardening (Week 3)
| Package | Tasks | Hours |
|---------|-------|-------|
| B1 | Deserialization Fix | 4 |
| B2 | File Upload Validation | 4 |
| B3 | RBAC Implementation | 4 |
| **Subtotal** | | **12** |

### Phase 3: Compatibility & Performance (Week 4)
| Package | Tasks | Hours |
|---------|-------|-------|
| C1 | PHP 8.x Fixes | 4 |
| D1 | Query Optimization | 10 |
| **Subtotal** | | **14** |

### Phase 4: Monitoring & UX (Week 5)
| Package | Tasks | Hours |
|---------|-------|-------|
| E1 | Activity Logs Reporting | 8 |
| E2 | Error Handling | 4 |
| F1 | UX Enhancements | 8 |
| **Subtotal** | | **20** |

---

## Cost Estimate

| Phase | Hours | Rate | Cost |
|-------|-------|------|------|
| Phase 1: Critical Security | 16 | R800/hr | R12,800 |
| Phase 2: Security Hardening | 12 | R800/hr | R9,600 |
| Phase 3: Compatibility & Performance | 14 | R800/hr | R11,200 |
| Phase 4: Monitoring & UX | 20 | R800/hr | R16,000 |
| **Total** | **62** | | **R49,600** |

### Payment Options

**Option A: Full Implementation**
- All 4 phases
- Total: R49,600
- Timeline: 5 weeks

**Option B: Security Priority**
- Phase 1 + Phase 2 only
- Total: R22,400
- Timeline: 3 weeks
- *Recommended minimum for security compliance*

**Option C: Phased Approach**
- Implement one phase at a time
- Payment per phase
- Flexible timeline

---

## Security Compliance

Upon completion, the uMdoni Municipality website will be protected against:

| OWASP Top 10 | Status |
|--------------|--------|
| A1: Injection | ✅ Fixed (SQL Injection) |
| A2: Broken Authentication | ✅ Session hardening |
| A3: Sensitive Data Exposure | ✅ Error handling |
| A5: Broken Access Control | ✅ RBAC, CSRF |
| A7: Cross-Site Scripting | ✅ XSS protection |
| A8: Insecure Deserialization | ✅ JSON migration |

---

## Acceptance Criteria

### Security Tests
- [ ] All forms protected against XSS injection
- [ ] CSRF tokens validated on all POST requests
- [ ] Session cookies have secure flags
- [ ] File uploads validated server-side
- [ ] Role-based access enforced on all sensitive pages

### Performance Tests
- [ ] Dashboard home loads in < 2 seconds
- [ ] Pagination working on all list pages
- [ ] No duplicate database queries

### Compatibility Tests
- [ ] No PHP 8.x deprecation warnings
- [ ] All functionality working in latest browsers

---

## Next Steps

1. **Review this proposal** and select preferred implementation option
2. **Schedule kickoff meeting** to confirm scope and priorities
3. **Approve Phase 1** to begin critical security fixes
4. **Receive regular updates** during implementation
5. **Sign-off on each phase** before proceeding to next

---

## Contact

**ISU Tech**
Nhlanhla Mnyandu
Email: nhlanhla@isutech.co.za
Phone: [Contact Number]

---

*This proposal is valid for 30 days from the date of issue.*

**Document Version:** 1.0
**Last Updated:** 13 June 2026
