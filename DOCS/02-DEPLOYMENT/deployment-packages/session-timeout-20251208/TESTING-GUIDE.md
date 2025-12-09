# Session Timeout Feature - Testing Guide

**Feature:** Automatic Session Timeout & Idle Detection
**Date:** December 8, 2025
**Author:** Nhlanhla Mnyandu (nhlanhla@isutech.co.za)
**Priority:** URGENT - Security Enhancement

---

## 📋 Testing Overview

This guide provides comprehensive testing procedures for the automatic session timeout feature. Follow these tests to ensure the feature works correctly before production deployment.

---

## 🎯 Testing Objectives

1. ✅ Verify idle detection works correctly
2. ✅ Confirm warning modal appears at 28 minutes
3. ✅ Validate countdown timer accuracy
4. ✅ Test "Stay Logged In" functionality
5. ✅ Test "Logout Now" functionality
6. ✅ Verify automatic logout at 30 minutes
7. ✅ Confirm Activity Log integration
8. ✅ Test session ping mechanism
9. ✅ Validate responsive design
10. ✅ Ensure no performance impact

---

## 🚀 Quick Testing (5-Minute Test)

For rapid validation after deployment, use these shortened tests:

### Test 1: JavaScript Loading
1. Login to dashboard
2. Open browser console (F12)
3. Look for: `Session timeout initialized: 30 minutes`
4. **Expected:** ✅ Message appears, no errors

### Test 2: Activity Detection
1. Move mouse around dashboard
2. Check console for activity messages (if debug enabled)
3. Scroll page up and down
4. Type in any input field
5. **Expected:** ✅ Timer resets on each action

### Test 3: Quick Timeout Test
1. Edit CONFIG in session-timeout.js:
   ```javascript
   IDLE_TIMEOUT: 60 * 1000,      // 1 minute
   WARNING_TIMEOUT: 30 * 1000,   // 30 seconds
   ```
2. Reload page
3. Wait 1 minute without moving mouse
4. **Expected:** ✅ Warning modal appears
5. Wait 30 more seconds
6. **Expected:** ✅ Auto-logout occurs

### Test 4: Activity Log Verification
1. After auto-logout, login again
2. Go to Dashboard > Activity Logs
3. Look for recent entry: "Automatic logout due to inactivity (30 minutes)"
4. **Expected:** ✅ Log entry exists with correct user and timestamp

---

## 🧪 Comprehensive Testing

### Test Suite 1: Initialization & Loading

#### Test 1.1: Script Loading
**Purpose:** Verify JavaScript file loads without errors

**Steps:**
1. Clear browser cache (Ctrl+Shift+R)
2. Login to dashboard: https://umdoni.gov.za/dashboard
3. Open browser console (F12)
4. Check Network tab for session-timeout.js
5. Check Console tab for initialization message

**Expected Results:**
- ✅ session-timeout.js loads (Status: 200)
- ✅ File size: ~15 KB
- ✅ Console message: "Session timeout initialized: 30 minutes"
- ✅ No JavaScript errors

**Failure Scenarios:**
- ❌ 404 error → Check file path in dashboardLayout.php
- ❌ JavaScript errors → Check browser compatibility
- ❌ No initialization message → Check script executed

---

#### Test 1.2: Modal HTML Creation
**Purpose:** Verify modal elements created correctly

**Steps:**
1. Login to dashboard
2. Open browser console (F12)
3. Run: `document.getElementById('sessionTimeoutModal')`
4. Run: `document.getElementById('sessionTimeoutBackdrop')`

**Expected Results:**
- ✅ Modal element created (not null)
- ✅ Backdrop element created (not null)
- ✅ Elements hidden initially (display: none)

---

### Test Suite 2: Activity Detection

#### Test 2.1: Mouse Movement Detection
**Purpose:** Verify mouse movement resets timer

**Steps:**
1. Login to dashboard
2. Note current time
3. Move mouse continuously for 1 minute
4. Stop mouse movement
5. Wait 29 minutes (or use shortened timeout for testing)
6. Move mouse again

**Expected Results:**
- ✅ Timer resets on mouse movement
- ✅ Warning modal does NOT appear if mouse moved recently
- ✅ 30-minute countdown restarts with each movement

---

#### Test 2.2: Keyboard Input Detection
**Purpose:** Verify keyboard input resets timer

**Steps:**
1. Login to dashboard
2. Type in search box or any input field
3. Wait 29 minutes (or shortened timeout)
4. Type again

**Expected Results:**
- ✅ Timer resets on keyboard input
- ✅ Warning modal does NOT appear
- ✅ Typing in any input field counts as activity

---

#### Test 2.3: Scroll Detection
**Purpose:** Verify scrolling resets timer

**Steps:**
1. Login to dashboard
2. Scroll page up and down
3. Wait 29 minutes (or shortened timeout)
4. Scroll again

**Expected Results:**
- ✅ Timer resets on scroll
- ✅ Warning modal does NOT appear
- ✅ Both mouse wheel and scrollbar scrolling work

---

#### Test 2.4: Click Detection
**Purpose:** Verify clicks reset timer

**Steps:**
1. Login to dashboard
2. Click various elements (buttons, links, etc.)
3. Wait 29 minutes (or shortened timeout)
4. Click again

**Expected Results:**
- ✅ Timer resets on clicks
- ✅ Warning modal does NOT appear
- ✅ All clickable elements count as activity

---

#### Test 2.5: Touch Events (Mobile)
**Purpose:** Verify touch events reset timer on mobile devices

**Steps:**
1. Login to dashboard on mobile device or tablet
2. Tap and swipe screen
3. Wait 29 minutes (or shortened timeout)
4. Tap again

**Expected Results:**
- ✅ Timer resets on touch events
- ✅ Warning modal does NOT appear
- ✅ Mobile gestures count as activity

---

### Test Suite 3: Warning Modal

#### Test 3.1: Modal Appearance Timing
**Purpose:** Verify modal appears after exactly 28 minutes

**Setup:**
Edit CONFIG for faster testing:
```javascript
IDLE_TIMEOUT: 3 * 60 * 1000,    // 3 minutes
WARNING_TIMEOUT: 1 * 60 * 1000,  // 1 minute
TOTAL_TIMEOUT: 4 * 60 * 1000     // 4 minutes total
```

**Steps:**
1. Login to dashboard
2. Start timer (note exact time)
3. Do NOT touch mouse, keyboard, or screen
4. Wait exactly 3 minutes
5. Observe screen

**Expected Results:**
- ✅ Modal appears after 3 minutes (adjusted timeout)
- ✅ Modal shows with fade-in animation
- ✅ Backdrop darkens screen
- ✅ Modal is centered on screen

**Timing Accuracy:**
- Acceptable: ±2 seconds
- Test 3 times to verify consistency

---

#### Test 3.2: Modal Content & Styling
**Purpose:** Verify modal displays correctly

**Steps:**
1. Trigger warning modal (wait 28 minutes or use shortened timeout)
2. Inspect modal visually
3. Check all elements present

**Expected Results:**
- ✅ Header: Purple gradient background
- ✅ Icon: Clock icon (bi-clock-history) pulsing
- ✅ Title: "Session Timeout Warning"
- ✅ Message: Clear explanation of timeout
- ✅ Countdown: Large number (e.g., "2:00")
- ✅ Countdown circle: Purple gradient
- ✅ Buttons: "Logout Now" (gray) and "Stay Logged In" (purple)
- ✅ Responsive: Works on mobile, tablet, desktop

**Visual Quality:**
- Professional appearance
- No layout issues
- Readable text
- Clear call-to-action buttons

---

#### Test 3.3: Countdown Timer Accuracy
**Purpose:** Verify countdown timer counts correctly

**Steps:**
1. Trigger warning modal
2. Watch countdown timer
3. Use stopwatch to verify accuracy
4. Count down from 2:00 to 0:00

**Expected Results:**
- ✅ Countdown starts at 2:00 (default)
- ✅ Decrements every second: 2:00 → 1:59 → 1:58 ...
- ✅ Format: M:SS (e.g., 1:05, 0:30)
- ✅ Reaches 0:00 exactly when logout occurs
- ✅ Color changes to red at 0:30 or less

**Timing Accuracy:**
- Acceptable: ±2 seconds over 2 minutes
- Test multiple times

---

#### Test 3.4: Alert Sound (Optional)
**Purpose:** Verify audio alert plays (if enabled)

**Steps:**
1. Ensure browser audio is enabled
2. Trigger warning modal
3. Listen for beep sound

**Expected Results:**
- ✅ Beep sound plays when modal appears (if enabled)
- ✅ Sound is not jarring or too loud
- ✅ Sound only plays once

**Note:** Sound is optional and can be disabled. If no sound heard, verify `playAlertSound()` is called in code.

---

### Test Suite 4: User Actions

#### Test 4.1: "Stay Logged In" Button
**Purpose:** Verify button resets timer and closes modal

**Steps:**
1. Trigger warning modal (wait 28 minutes or shortened)
2. Click "Stay Logged In" button
3. Observe modal behavior
4. Wait another 28 minutes
5. Verify modal appears again

**Expected Results:**
- ✅ Modal closes with fade-out animation
- ✅ Timer resets to 0 (starts fresh 30-minute countdown)
- ✅ Session remains active
- ✅ Session ping sent to server
- ✅ User can continue working
- ✅ Modal appears again after another 28 minutes

**Network Verification:**
- Check Network tab (F12)
- Look for request to `/dashboard/index/ping`
- Verify 200 OK response

---

#### Test 4.2: "Logout Now" Button
**Purpose:** Verify button logs out immediately

**Steps:**
1. Trigger warning modal
2. Click "Logout Now" button
3. Observe navigation

**Expected Results:**
- ✅ Immediately redirects to logout page
- ✅ Session destroyed
- ✅ Cannot access dashboard without re-login
- ✅ Redirects to login page
- ✅ Activity Log entry created (reason: 'manual')

**Activity Log Verification:**
1. Login again
2. Go to Activity Logs
3. Look for: "Manual logout from timeout warning"
4. Verify timestamp matches logout time

---

#### Test 4.3: Auto-Logout (No Action)
**Purpose:** Verify automatic logout when user takes no action

**Setup:**
Use shortened timeout for testing:
```javascript
IDLE_TIMEOUT: 3 * 60 * 1000,    // 3 minutes
WARNING_TIMEOUT: 1 * 60 * 1000,  // 1 minute
```

**Steps:**
1. Login to dashboard
2. Do NOT move mouse or touch keyboard
3. Wait 3 minutes for modal to appear
4. Do NOT click any buttons
5. Wait additional 1 minute
6. Observe behavior

**Expected Results:**
- ✅ Modal appears at 3 minutes
- ✅ Countdown from 1:00 to 0:00
- ✅ At 0:00, automatic redirect to logout page
- ✅ Session destroyed
- ✅ Activity Log entry created (reason: 'auto-logout')

**Timing:**
- Total time: 4 minutes (3 min idle + 1 min warning)
- Acceptable variance: ±2 seconds

---

#### Test 4.4: Activity During Warning
**Purpose:** Verify moving mouse during warning closes modal

**Steps:**
1. Trigger warning modal (wait 28 minutes)
2. Do NOT click any buttons
3. Move mouse or type on keyboard
4. Observe modal behavior

**Expected Results:**
- ✅ Modal closes immediately
- ✅ Timer resets to 0
- ✅ Fresh 30-minute countdown starts
- ✅ Session remains active

---

### Test Suite 5: Activity Logs Integration

#### Test 5.1: Auto-Logout Log Entry
**Purpose:** Verify auto-logout creates correct log entry

**Steps:**
1. Use shortened timeout for testing
2. Allow auto-logout to occur (don't click buttons)
3. Login again
4. Navigate to Dashboard > Activity Logs
5. Find most recent entry for your user

**Expected Results:**
- ✅ Log entry exists
- ✅ Type: "info"
- ✅ Message: "Automatic logout due to inactivity (30 minutes)"
- ✅ User: Your username and email
- ✅ Timestamp: Matches logout time
- ✅ Location: Your IP address

**Database Verification (Optional):**
```sql
SELECT * FROM logs
WHERE status = 'info'
AND actions LIKE '%Automatic logout%'
ORDER BY time_log DESC
LIMIT 1;
```

---

#### Test 5.2: Manual Logout Log Entry
**Purpose:** Verify "Logout Now" button creates correct log entry

**Steps:**
1. Trigger warning modal
2. Click "Logout Now" button
3. Login again
4. Navigate to Activity Logs
5. Find most recent entry

**Expected Results:**
- ✅ Log entry exists
- ✅ Type: "info"
- ✅ Message: "Manual logout from timeout warning"
- ✅ User: Your username
- ✅ Timestamp: Matches logout time

---

#### Test 5.3: Log Endpoint Availability
**Purpose:** Verify logging endpoint responds correctly

**Steps:**
1. Login to dashboard
2. Open browser console (F12)
3. Run test request:
```javascript
fetch('/dashboard/index/logAutoLogout', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'reason=test&timestamp=' + new Date().toISOString()
})
.then(r => r.json())
.then(d => console.log('Response:', d));
```
4. Check response

**Expected Results:**
- ✅ Status: 200 OK
- ✅ Response: `{ status: 'success', logged: true }`
- ✅ Activity Log entry created

---

### Test Suite 6: Session Ping Mechanism

#### Test 6.1: Ping Endpoint Response
**Purpose:** Verify ping endpoint responds correctly

**Steps:**
1. Login to dashboard
2. Open browser console (F12)
3. Run test request:
```javascript
fetch('/dashboard/index/ping', {
    method: 'GET',
    credentials: 'same-origin'
})
.then(r => r.json())
.then(d => console.log('Ping response:', d));
```
4. Check response

**Expected Results:**
- ✅ Status: 200 OK
- ✅ Response includes:
  ```json
  {
    "status": "success",
    "timestamp": 1234567890,
    "session_active": true
  }
  ```

---

#### Test 6.2: Automatic Ping Intervals
**Purpose:** Verify pings sent automatically every 5 minutes

**Steps:**
1. Login to dashboard
2. Open browser Network tab (F12)
3. Filter requests to show only "ping"
4. Wait 5 minutes
5. Check for ping request

**Expected Results:**
- ✅ Ping request sent every 5 minutes
- ✅ Request URL: `/dashboard/index/ping`
- ✅ Method: GET
- ✅ Status: 200 OK
- ✅ Response time: <100ms

**Timing:**
- First ping: Immediate (on page load)
- Subsequent pings: Every 5 minutes (300,000ms)

---

#### Test 6.3: Ping After "Stay Logged In"
**Purpose:** Verify ping sent when "Stay Logged In" clicked

**Steps:**
1. Open Network tab (F12)
2. Trigger warning modal
3. Click "Stay Logged In"
4. Check Network tab for ping request

**Expected Results:**
- ✅ Ping request sent immediately after button click
- ✅ Status: 200 OK
- ✅ Modal closes
- ✅ Session extended

---

### Test Suite 7: Responsive Design

#### Test 7.1: Desktop Display
**Purpose:** Verify modal displays correctly on desktop

**Steps:**
1. Open dashboard on desktop browser (1920x1080)
2. Trigger warning modal
3. Inspect visual appearance

**Expected Results:**
- ✅ Modal centered on screen
- ✅ Width: ~500px (not full screen)
- ✅ Buttons side by side
- ✅ All text readable
- ✅ No horizontal scrolling

---

#### Test 7.2: Tablet Display
**Purpose:** Verify modal displays correctly on tablets

**Steps:**
1. Open dashboard on tablet (768x1024) or use responsive mode (F12)
2. Trigger warning modal
3. Inspect layout

**Expected Results:**
- ✅ Modal width: 90% of screen
- ✅ Content fits without scrolling
- ✅ Buttons remain side by side (if width > 576px)
- ✅ Touch-friendly button sizes

---

#### Test 7.3: Mobile Display
**Purpose:** Verify modal displays correctly on mobile devices

**Steps:**
1. Open dashboard on mobile (375x667) or use responsive mode
2. Trigger warning modal
3. Check layout adapts

**Expected Results:**
- ✅ Modal width: 95% of screen
- ✅ Buttons stack vertically (one above other)
- ✅ Buttons full width
- ✅ All text readable without zooming
- ✅ Countdown circle appropriately sized
- ✅ No horizontal scrolling

**Responsive Breakpoint:**
- Stacked layout triggers at width ≤ 576px

---

### Test Suite 8: Edge Cases & Error Handling

#### Test 8.1: Session Already Expired
**Purpose:** Verify behavior when session expires server-side

**Steps:**
1. Login to dashboard
2. Wait for server-side PHP session timeout (usually 24 minutes)
3. Trigger warning modal by waiting 28 minutes
4. Click "Stay Logged In"
5. Observe behavior

**Expected Results:**
- ✅ Ping request returns 401 Unauthorized
- ✅ User redirected to login page
- ✅ No JavaScript errors

---

#### Test 8.2: Network Failure During Ping
**Purpose:** Verify graceful handling of network errors

**Steps:**
1. Login to dashboard
2. Disconnect from network (airplane mode or disable WiFi)
3. Wait 5 minutes for automatic ping
4. Observe behavior

**Expected Results:**
- ✅ Ping fails silently (no user-visible error)
- ✅ Console logs error (check console)
- ✅ Dashboard continues to function
- ✅ No JavaScript exceptions

---

#### Test 8.3: Multiple Browser Tabs
**Purpose:** Verify behavior with multiple dashboard tabs

**Steps:**
1. Login to dashboard
2. Open 3 tabs: Tab A, Tab B, Tab C
3. Stay active in Tab A only
4. Wait 28 minutes without touching Tab B or Tab C
5. Observe all tabs

**Expected Results:**
- ✅ Each tab has independent timer
- ✅ Modal appears in Tab B and Tab C only
- ✅ Tab A remains normal (due to activity)
- ✅ Clicking "Stay Logged In" in Tab B only affects Tab B

**Note:** Each tab operates independently. This is expected behavior. To implement shared timer across tabs, would need localStorage integration (future enhancement).

---

#### Test 8.4: Browser Back Button After Logout
**Purpose:** Verify logout is enforced even if user presses back

**Steps:**
1. Login to dashboard
2. Allow auto-logout to occur
3. Press browser back button
4. Attempt to access dashboard

**Expected Results:**
- ✅ Redirected to login page
- ✅ Session no longer valid
- ✅ Cannot access dashboard without re-login
- ✅ No cached sensitive data displayed

---

### Test Suite 9: Performance Testing

#### Test 9.1: JavaScript Performance
**Purpose:** Verify no performance degradation

**Steps:**
1. Login to dashboard
2. Open Performance tab (F12)
3. Record performance for 2 minutes
4. Analyze results

**Expected Results:**
- ✅ CPU usage: <1% for session timeout checks
- ✅ Memory usage: <1 MB
- ✅ No memory leaks over time
- ✅ Event handlers optimized (passive listeners)

---

#### Test 9.2: Page Load Impact
**Purpose:** Verify no impact on page load times

**Steps:**
1. Measure page load time WITHOUT session-timeout.js
2. Add session-timeout.js
3. Measure page load time WITH session-timeout.js
4. Compare results

**Expected Results:**
- ✅ Load time increase: <100ms
- ✅ No noticeable difference to users
- ✅ Script loads asynchronously (doesn't block rendering)

---

#### Test 9.3: Long-Term Stability
**Purpose:** Verify feature remains stable over extended use

**Steps:**
1. Login to dashboard
2. Keep session active for 2+ hours
3. Use dashboard normally (navigate, click, type)
4. Monitor console for errors

**Expected Results:**
- ✅ No JavaScript errors over time
- ✅ No memory leaks
- ✅ Timers continue working accurately
- ✅ Pings continue every 5 minutes
- ✅ Dashboard performance remains stable

---

## 🎯 Acceptance Criteria

### Feature Must Pass All:
- ✅ Modal appears after 28 minutes of inactivity
- ✅ Countdown timer displays accurately
- ✅ "Stay Logged In" extends session
- ✅ "Logout Now" logs out immediately
- ✅ Auto-logout occurs after 30 minutes
- ✅ Activity Log entries created correctly
- ✅ Responsive on mobile, tablet, desktop
- ✅ No JavaScript errors in console
- ✅ No performance degradation
- ✅ Normal logout still works

### Nice-to-Have (Optional):
- ⭐ Alert sound on warning (can be disabled)
- ⭐ Multiple tabs awareness (future enhancement)
- ⭐ Form auto-save integration (future enhancement)

---

## 📊 Test Results Template

Use this template to document your testing:

```
# Session Timeout Testing - [Date]
Tester: [Name]
Environment: [Development/Staging/Production]
Browser: [Chrome/Firefox/Safari] [Version]

## Test Results Summary
- Total Tests: X
- Passed: Y
- Failed: Z
- Skipped: W

## Failed Tests
[List any failed tests with details]

## Notes
[Any observations or issues encountered]

## Recommendation
[ ] Approve for production
[ ] Minor fixes needed
[ ] Major issues - do not deploy
```

---

## 🐛 Common Issues & Solutions

### Issue: Modal doesn't appear
**Solution:**
1. Check console for errors
2. Verify CONFIG timeout values
3. Ensure you're actually idle (any mouse movement resets timer)
4. Test with shortened timeout (1 minute) for debugging

### Issue: Countdown jumps or skips numbers
**Solution:**
1. Check browser performance (close other tabs)
2. Verify CHECK_INTERVAL = 1000 (1 second)
3. Test on different browser

### Issue: "Stay Logged In" doesn't work
**Solution:**
1. Check Network tab for ping request
2. Verify `/dashboard/index/ping` endpoint responds
3. Check for JavaScript errors
4. Ensure session still valid server-side

### Issue: No Activity Log entry
**Solution:**
1. Check `/dashboard/index/logAutoLogout` endpoint
2. Verify LogsModel imported in Index.php
3. Check PHP error logs
4. Test endpoint manually (see Test 5.3)

---

## 📧 Support

For testing issues or questions:
- **Developer:** Nhlanhla Mnyandu
- **Email:** nhlanhla@isutech.co.za
- **Company:** ISU Tech

---

**Testing Guide Version:** 1.0
**Created:** December 8, 2025
**Status:** Ready for Testing

---

*End of Testing Guide*
