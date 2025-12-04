# Tender & Quotation Expiry Management - Phase 3 Deployment

**Date:** 2025-12-04
**Task:** #2 - Tender & Quotation Expiry Management System
**Phase:** 3 (Archive View Pages)
**Status:** Ready for Production Deployment

---

## 📦 What's Included

This deployment package contains **4 files**:

### New Archive Views (2 files)
- `App/Views/tenders/archive.php` (NEW)
- `App/Views/quotations/archive.php` (NEW)

### Updated Index Views (2 files)
- `App/Views/tenders/index.php` (UPDATED - added "View Archive" link)
- `App/Views/quotations/index.php` (UPDATED - added "View Archive" link)

---

## 🎯 What This Does

### New Public Archive Pages
Citizens can now browse historical (expired) tenders and quotations:
- **URL:** https://umdoni.gov.za/tenders/archive
- **URL:** https://umdoni.gov.za/quotations/archive

### Features
1. **Archive Notice** - Informs visitors these are historical/closed opportunities
2. **Three Tabs:**
   - All Archived (all status = 4 items)
   - Awarded (status = 3 items)
   - Other (status = 1, 2 items that were archived)
3. **Visual Badges** - "Expired", "Awarded", "Current", "Open" badges for clarity
4. **Link Back** - "View current opportunities" link to active pages
5. **Document Downloads** - All PDFs/documents remain accessible

### Updated Active Pages
- Added "View Archive" button next to "Tenders" and "Quotations" headings
- Users can easily switch between current and historical opportunities

---

## 📋 Deployment Steps

### Step 1: Upload Files to Production

**Via cPanel File Manager:**

1. **Upload tender archive view:**
   - Navigate to: `public_html/App/Views/tenders/`
   - Upload: `archive.php`

2. **Upload tender index view (updated):**
   - Navigate to: `public_html/App/Views/tenders/`
   - Upload: `index.php` (overwrite existing)

3. **Upload quotation archive view:**
   - Navigate to: `public_html/App/Views/quotations/`
   - Upload: `archive.php`

4. **Upload quotation index view (updated):**
   - Navigate to: `public_html/App/Views/quotations/`
   - Upload: `index.php` (overwrite existing)

**Alternative - Upload ZIP:**
1. Upload `tender-quotation-phase3.zip` to `public_html/`
2. Extract it
3. Move files to correct locations

---

## 🧪 Testing Instructions

### Test 1: Tender Archive Page
1. **Go to:** https://umdoni.gov.za/tenders
2. **Verify:** "View Archive" button appears next to "Tenders" heading
3. **Click:** "View Archive" button
4. **Expected:**
   - Archive page loads at `/tenders/archive`
   - Shows "Archived Tenders (32 Total)" (or current count)
   - Notice box at top explains these are historical
   - Three tabs: All Archived, Awarded, Other
   - All tenders have "Expired" or status badges
5. **Click:** "View current opportunities" link
6. **Expected:** Returns to active tenders page

### Test 2: Quotation Archive Page
1. **Go to:** https://umdoni.gov.za/quotations
2. **Verify:** "View Archive" button appears next to "Quotations" heading
3. **Click:** "View Archive" button
4. **Expected:**
   - Archive page loads at `/quotations/archive`
   - Shows "Archived Quotations (534 Total)" (or current count)
   - Notice box at top explains these are historical
   - Three tabs: All Archived, Awarded, Other
   - All quotations have "Expired" or status badges
5. **Click:** "View current opportunities" link
6. **Expected:** Returns to active quotations page

### Test 3: Document Downloads from Archive
1. On archive pages, **click** any tender/quotation download icon
2. **Expected:** PDF/document opens in new tab
3. **Verify:** All documents are still accessible

### Test 4: Navigation Flow
1. Start at `/tenders`
2. Click "View Archive"
3. On archive page, click "View current opportunities"
4. Verify you're back at `/tenders`
5. Repeat for quotations

---

## 🚨 Rollback Plan

If issues occur:

**Rollback tenders/index.php:**
- The only change is adding the "View Archive" button
- Remove lines 93-100 and restore original lines 93-96

**Delete archive pages (if needed):**
- Delete `/App/Views/tenders/archive.php`
- Delete `/App/Views/quotations/archive.php`

---

## ✅ Success Criteria

- [ ] "View Archive" buttons visible on /tenders and /quotations
- [ ] Archive pages load at /tenders/archive and /quotations/archive
- [ ] Archive pages show correct count of expired items
- [ ] Three tabs work correctly (All Archived, Awarded, Other)
- [ ] "View current opportunities" link returns to active pages
- [ ] Document downloads work from archive pages
- [ ] No PHP errors or broken links

---

## 📊 What's Already Deployed (Phase 1 & 2)

**Backend is already live:**
- ✅ Models with expiry logic
- ✅ Controllers with archive actions
- ✅ Dashboard "Archive Expired" buttons
- ✅ 566 items already archived (32 tenders, 534 quotations)
- ✅ Public pages already exclude expired items

**This Phase 3 completes the feature by:**
- Adding public archive view pages
- Adding navigation links between active and archive pages

---

## 🎉 Feature Complete After This Deployment

**Full Tender & Quotation Expiry Management System:**
1. ✅ Automatic expiry detection based on `dueDate`
2. ✅ Manual archiving via dashboard (Admin can click "Archive Expired")
3. ✅ Public pages show only active opportunities
4. ✅ Public archive pages for browsing historical opportunities
5. ✅ Easy navigation between current and archived items
6. ✅ Transparency - all documents remain publicly accessible

---

**Deployed By:** Nhlanhla Mnyandu
**Deployment Date:** 2025-12-04
**Git Commit:** (to be added after commit)
