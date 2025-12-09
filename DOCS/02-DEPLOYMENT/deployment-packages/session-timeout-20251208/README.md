# Automatic Session Timeout - Deployment Package

**Feature:** Automatic Session Timeout & Idle Detection
**Date:** December 8, 2025
**Author:** Nhlanhla Mnyandu (nhlanhla@isutech.co.za)
**Company:** ISU Tech
**Priority:** URGENT - Security Enhancement

---

## 📋 Executive Summary

This package implements automatic session timeout functionality for the uMdoni Municipality website dashboard. Users who remain idle for 30 minutes will be automatically logged out for security reasons. A 2-minute warning is displayed before logout, allowing users to extend their session.

### Security Benefits
- ✅ Prevents unauthorized access from unattended workstations
- ✅ Reduces risk of session hijacking
- ✅ Complies with security best practices
- ✅ Creates audit trail in Activity Logs
- ✅ Automatic enforcement (no user action required)

---

## 🎯 Feature Overview

### Timeout Configuration
- **Idle Time:** 28 minutes of inactivity
- **Warning Period:** 2 minutes (shows countdown modal)
- **Total Timeout:** 30 minutes from last activity
- **Session Ping:** Every 5 minutes (keeps session alive during activity)

### What Counts as Activity?
The system monitors these user actions:
- Mouse movement
- Mouse clicks
- Keyboard input
- Scrolling
- Touch events (mobile)

### User Experience
1. User works normally in dashboard (no interruption)
2. After 28 minutes of inactivity, warning modal appears
3. Modal shows countdown timer (2:00, 1:59, 1:58...)
4. User can click "Stay Logged In" to continue session
5. User can click "Logout Now" to logout immediately
6. If no action taken, auto-logout at 30 minutes
7. Event logged in Activity Logs

---

## 📦 Package Contents

### Files Included

```
deployment/session-timeout-20251208/
├── README.md                          (this file)
├── TESTING-GUIDE.md                   (testing instructions)
├── files/
│   ├── session-timeout.js             (NEW - client-side timeout logic)
│   ├── Index.php                      (MODIFIED - Dashboard controller)
│   └── dashboardLayout.php            (MODIFIED - includes timeout script)
```

### File Purposes

**1. session-timeout.js** (NEW FILE)
- **Location:** `public/assets/js/session-timeout.js`
- **Purpose:** Client-side idle detection and warning modal
- **Size:** ~15 KB
- **Features:**
  - Activity monitoring
  - Idle timer calculation
  - Warning modal with countdown
  - Session ping mechanism
  - Auto-logout enforcement
  - Activity Log integration

**2. Index.php** (MODIFIED)
- **Location:** `App/Controllers/Dashboard/Index.php`
- **Changes:**
  - Added `use App\Models\LogsModel;` import
  - Added `pingAction()` method (session keep-alive endpoint)
  - Added `logAutoLogoutAction()` method (logging endpoint)
- **New Routes:**
  - `/dashboard/index/ping` (GET) - Session ping
  - `/dashboard/index/logAutoLogout` (POST) - Log auto-logout event

**3. dashboardLayout.php** (MODIFIED)
- **Location:** `public/layouts/dashboardLayout.php`
- **Changes:**
  - Added `<script src="assets/js/session-timeout.js"></script>`
  - Loads session timeout on all dashboard pages

---

## 🚀 Deployment Instructions

### Prerequisites
- Access to production server (SSH or cPanel)
- Backup of current files (CRITICAL)
- PHP 8.0+ (uses `match` expression)
- Active dashboard session to test

### Step 1: Backup Current Files

```bash
# SSH to production server
ssh user@reseller142.aserv.co.za

# Create backup directory
cd /path/to/umdoni-website
mkdir -p backups/session-timeout-backup-$(date +%Y%m%d)

# Backup files we're about to modify
cp App/Controllers/Dashboard/Index.php backups/session-timeout-backup-$(date +%Y%m%d)/
cp public/layouts/dashboardLayout.php backups/session-timeout-backup-$(date +%Y%m%d)/

# Note: session-timeout.js is new, no backup needed
```

### Step 2: Upload Files

**Via SSH/SCP:**
```bash
# From local machine
cd /Users/nhla/Desktop/PROJECTS/2025/umdoni-website
scp deployment/session-timeout-20251208/files/session-timeout.js user@server:/path/to/public/assets/js/
scp deployment/session-timeout-20251208/files/Index.php user@server:/path/to/App/Controllers/Dashboard/
scp deployment/session-timeout-20251208/files/dashboardLayout.php user@server:/path/to/public/layouts/
```

**Via cPanel File Manager:**
1. Navigate to File Manager
2. Upload `session-timeout.js` to: `public/assets/js/`
3. Upload `Index.php` to: `App/Controllers/Dashboard/` (overwrite existing)
4. Upload `dashboardLayout.php` to: `public/layouts/` (overwrite existing)

### Step 3: Set Permissions

```bash
chmod 644 public/assets/js/session-timeout.js
chmod 644 App/Controllers/Dashboard/Index.php
chmod 644 public/layouts/dashboardLayout.php
```

### Step 4: Verify Deployment

**Check 1: File Existence**
```bash
ls -la public/assets/js/session-timeout.js
ls -la App/Controllers/Dashboard/Index.php
ls -la public/layouts/dashboardLayout.php
```

**Check 2: File Sizes**
- `session-timeout.js`: ~15 KB
- `Index.php`: Should be larger than before (~5 KB increase)
- `dashboardLayout.php`: Similar size, minor increase

**Check 3: Test Access**
1. Login to dashboard: https://umdoni.gov.za/dashboard
2. Open browser console (F12)
3. Look for message: "Session timeout initialized: 30 minutes"
4. No JavaScript errors should appear

### Step 5: Functional Testing

Follow the comprehensive testing guide in `TESTING-GUIDE.md` (included in this package).

**Quick Test:**
1. Login to dashboard
2. Wait 2-3 minutes (verify no popup yet)
3. Check browser console for activity tracking messages
4. Verify logout button still works normally

---

## 🧪 Testing Checklist

### Before Deployment (Development)
- [x] JavaScript loads without errors
- [x] Activity detection works (mouse, keyboard, scroll)
- [x] Warning modal displays correctly
- [x] Countdown timer works
- [x] "Stay Logged In" button resets timer
- [x] "Logout Now" button logs out immediately
- [x] Auto-logout occurs after 30 minutes
- [x] Activity Log entry created
- [x] Session ping endpoint responds
- [x] Responsive design (mobile, tablet, desktop)

### After Deployment (Production)
- [ ] JavaScript loads without 404 errors
- [ ] No console errors on dashboard pages
- [ ] Initialization message appears in console
- [ ] Test short timeout (edit CONFIG to 1 min for testing)
- [ ] Warning modal appears and functions
- [ ] Verify Activity Log entry created
- [ ] Test across different dashboard pages
- [ ] Test on mobile devices
- [ ] Verify normal logout still works

### Acceptance Criteria
- ✅ Feature works on all dashboard pages
- ✅ No impact on dashboard performance
- ✅ Users can extend session when needed
- ✅ Auto-logout creates Activity Log entry
- ✅ No JavaScript errors in production
- ✅ Mobile and desktop compatibility

---

## 🔧 Configuration

### Adjusting Timeout Duration

If you need to change the timeout duration, edit `public/assets/js/session-timeout.js`:

```javascript
const CONFIG = {
    IDLE_TIMEOUT: 28 * 60 * 1000,      // Change this (minutes * 60 * 1000)
    WARNING_TIMEOUT: 2 * 60 * 1000,    // Change this for warning duration
    TOTAL_TIMEOUT: 30 * 60 * 1000,     // Total timeout
    CHECK_INTERVAL: 1000,              // Don't change (1 second)
    PING_INTERVAL: 5 * 60 * 1000,      // Session ping interval
};
```

**Example: 15-minute timeout**
```javascript
const CONFIG = {
    IDLE_TIMEOUT: 13 * 60 * 1000,      // 13 minutes idle
    WARNING_TIMEOUT: 2 * 60 * 1000,    // 2 minutes warning
    TOTAL_TIMEOUT: 15 * 60 * 1000,     // 15 minutes total
};
```

### Disabling Warning Sound

To disable the beep sound, comment out the call in `session-timeout.js`:

```javascript
function showWarningModal() {
    // ... other code ...

    // Play alert sound (optional)
    // playAlertSound();  // ← Comment this line
}
```

---

## 🐛 Troubleshooting

### Issue: JavaScript not loading
**Symptoms:** No console message, timeout doesn't work
**Solution:**
1. Check file path: `public/assets/js/session-timeout.js`
2. Verify permissions: `chmod 644 session-timeout.js`
3. Check browser console for 404 errors
4. Clear browser cache (Ctrl+Shift+R)

### Issue: Modal doesn't appear after 28 minutes
**Symptoms:** No warning modal shows
**Solution:**
1. Check browser console for JavaScript errors
2. Verify you're actually idle (any mouse movement resets timer)
3. Check if Bootstrap is loaded (required for modal styling)
4. Test with shorter timeout (CONFIG.IDLE_TIMEOUT = 60000 for 1 min)

### Issue: "Stay Logged In" doesn't work
**Symptoms:** Clicking button doesn't reset timer
**Solution:**
1. Check browser console for errors
2. Verify ping endpoint works: `/dashboard/index/ping`
3. Check network tab (F12) for successful ping responses
4. Ensure session is still valid server-side

### Issue: No Activity Log entry created
**Symptoms:** Auto-logout works but no log entry
**Solution:**
1. Check `/dashboard/index/logAutoLogout` endpoint responds
2. Verify LogsModel is imported in Index.php
3. Check PHP error log for exceptions
4. Test endpoint manually with POST request
5. Verify database connection

### Issue: Timeout happens too quickly
**Symptoms:** Logs out before 30 minutes
**Solution:**
1. Check CONFIG values in session-timeout.js
2. Ensure IDLE_TIMEOUT + WARNING_TIMEOUT = TOTAL_TIMEOUT
3. Clear browser cache and reload
4. Check server-side PHP session timeout settings

### Issue: Modal styling broken
**Symptoms:** Modal appears but looks wrong
**Solution:**
1. Verify Bootstrap CSS is loaded
2. Check Bootstrap Icons CSS is loaded
3. Clear browser cache
4. Check browser console for CSS loading errors

---

## 📊 Technical Details

### Client-Side Implementation

**Activity Tracking:**
- Event listeners on: mousedown, mousemove, keypress, scroll, touchstart, click
- All events update `lastActivityTime` timestamp
- Passive event listeners for performance

**Idle Detection:**
- Checks every 1 second (CONFIG.CHECK_INTERVAL)
- Calculates: `currentTime - lastActivityTime`
- Triggers warning at 28 minutes
- Triggers logout at 30 minutes

**Warning Modal:**
- Created dynamically on first show
- Uses Bootstrap styling
- Countdown timer updates every second
- Changes color when < 30 seconds remain
- Prevents backdrop click (must use buttons)

**Session Ping:**
- Pings server every 5 minutes during activity
- Keeps PHP session alive
- Silent failure (doesn't interrupt user)

### Server-Side Implementation

**Ping Endpoint:** `/dashboard/index/ping`
```php
public function pingAction()
{
    // Updates $_SESSION['last_activity']
    // Returns JSON: { status: 'success', timestamp: time() }
}
```

**Logging Endpoint:** `/dashboard/index/logAutoLogout`
```php
public function logAutoLogoutAction()
{
    // Logs to Activity Logs via LogsModel::LogError()
    // Accepts POST: { reason: 'auto-logout', timestamp: '...' }
    // Returns JSON: { status: 'success', logged: true }
}
```

**Activity Log Entry:**
- **Type:** info
- **Message:** "Automatic logout due to inactivity (30 minutes)"
- **User:** From session (userId, username, email)
- **Location:** IP address + User Agent
- **Timestamp:** Auto-generated

### Database Schema

No database changes required. Uses existing `logs` table:

```sql
-- logs table (existing schema)
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `time_log` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,    -- Uses 'info' for auto-logout
  `actions` text DEFAULT NULL,          -- Stores logout message
  `location` varchar(255) DEFAULT NULL, -- IP + User Agent
  `last_login` datetime DEFAULT NULL,
  `logout` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

---

## 🔒 Security Considerations

### Security Improvements
- ✅ Prevents unauthorized access from unattended sessions
- ✅ Creates audit trail of all auto-logout events
- ✅ Client + server validation (defense in depth)
- ✅ No sensitive data in JavaScript
- ✅ Session validation on every ping
- ✅ Secure logout URL (CSRF tokens respected)

### Potential Concerns
- ⚠️ JavaScript can be disabled (server-side timeout still applies)
- ⚠️ User might lose unsaved work (add to forms if needed)
- ⚠️ Multiple tabs behavior (each tab has own timer)

### Mitigation Strategies
1. **JavaScript Disabled:** Server-side PHP session timeout provides backup
2. **Unsaved Work:** Add form auto-save or warning on navigation
3. **Multiple Tabs:** Consider localStorage for shared timer (future enhancement)

---

## 📈 Performance Impact

### Client-Side
- **JavaScript Size:** ~15 KB (minified: ~8 KB)
- **Memory Usage:** Negligible (<1 MB)
- **CPU Usage:** Minimal (1 check per second)
- **Network:** 1 ping every 5 minutes (~100 bytes)

### Server-Side
- **New Endpoints:** 2 (ping, logAutoLogout)
- **Database Writes:** 1 per auto-logout event
- **Response Time:** <50ms per endpoint
- **Impact:** Negligible

### Overall Impact
✅ **Minimal performance impact**
- No noticeable slowdown
- No impact on page load times
- Lightweight JavaScript
- Efficient event handling

---

## 📝 Rollback Procedure

If issues occur after deployment, follow these steps:

### Quick Rollback (Disable Feature)

**Option 1: Remove Script Include**
Edit `public/layouts/dashboardLayout.php` and comment out:
```php
<!-- Session Timeout - Auto-logout after inactivity -->
<!-- <script src="<?php echo url("assets/js/session-timeout.js") ?>"></script> -->
```

**Option 2: Restore Backup Files**
```bash
cd /path/to/umdoni-website
cp backups/session-timeout-backup-YYYYMMDD/Index.php App/Controllers/Dashboard/
cp backups/session-timeout-backup-YYYYMMDD/dashboardLayout.php public/layouts/
rm public/assets/js/session-timeout.js
```

### Verify Rollback
1. Clear browser cache
2. Login to dashboard
3. Check console - should NOT see "Session timeout initialized"
4. Verify normal operation

---

## 📧 Support & Contact

**Developer:** Nhlanhla Mnyandu
**Email:** nhlanhla@isutech.co.za
**Company:** ISU Tech

**Repository:** https://github.com/gedeza/umdoni-website.git
**Production:** https://umdoni.gov.za
**Server:** reseller142.aserv.co.za

For issues or questions:
1. Check TESTING-GUIDE.md in this package
2. Review Troubleshooting section above
3. Check browser console for errors
4. Contact Nhlanhla Mnyandu

---

## 📚 Related Documentation

- **TESTING-GUIDE.md** - Comprehensive testing instructions
- **PROJECT-STATUS-20251208.md** - Current project status
- **TASKS.md** - Task tracking document
- **Activity Logs Documentation** - Dashboard logs interface

---

## ✅ Deployment Checklist

### Pre-Deployment
- [ ] Reviewed README.md (this file)
- [ ] Reviewed TESTING-GUIDE.md
- [ ] Backed up current files
- [ ] Have SSH/cPanel access
- [ ] Scheduled deployment window (low-traffic period)
- [ ] Notified stakeholders (if required)

### Deployment
- [ ] Uploaded session-timeout.js
- [ ] Uploaded modified Index.php
- [ ] Uploaded modified dashboardLayout.php
- [ ] Set correct file permissions
- [ ] Verified files uploaded correctly

### Post-Deployment
- [ ] Tested JavaScript loads without errors
- [ ] Verified console initialization message
- [ ] Tested activity detection
- [ ] Tested warning modal appearance
- [ ] Verified Activity Log integration
- [ ] Tested on mobile device
- [ ] Confirmed normal logout still works
- [ ] Monitored for 30 minutes (no errors)

### Documentation
- [ ] Updated project documentation
- [ ] Notified administrator (if required)
- [ ] Created git commit with changes
- [ ] Pushed to repository

---

## 🎯 Success Criteria

**Feature Acceptance:**
- ✅ Users are automatically logged out after 30 minutes of inactivity
- ✅ Warning modal appears 2 minutes before logout
- ✅ "Stay Logged In" button extends session
- ✅ Auto-logout events logged in Activity Logs
- ✅ No JavaScript errors in production
- ✅ Feature works on all dashboard pages
- ✅ Mobile and desktop compatibility
- ✅ No impact on dashboard performance

**User Experience:**
- ✅ Clear warning before logout
- ✅ Easy to extend session
- ✅ Professional modal design
- ✅ Countdown timer shows remaining time
- ✅ No disruption during normal use

---

**Package Created:** December 8, 2025
**Version:** 1.0
**Status:** Ready for Deployment

---

*End of Deployment Package README*
