# Tender & Quotation Expiry Management - Phase 1 & 2 Deployment

**Date:** 2025-12-04
**Task:** #2 - Tender & Quotation Expiry Management System
**Phase:** 1 & 2 (Backend + Dashboard Buttons)
**Status:** Ready for Production Deployment

---

## 📦 What's Included

This deployment package contains **8 files**:

### Models (Backend Logic)
- `App/Models/TenderModel.php`
- `App/Models/QuotationsModel.php`

### Controllers (Public)
- `App/Controllers/Tenders.php`
- `App/Controllers/Quotations.php`

### Controllers (Dashboard)
- `App/Controllers/Dashboard/Tenders.php`
- `App/Controllers/Dashboard/Quotations.php`

### Views (Dashboard UI)
- `App/Views/dashboard/tenders/index.php`
- `App/Views/dashboard/quotations/index.php`

---

## 🎯 What This Does

### New Functionality
1. **Expiry Detection** - System can identify expired tenders/quotations based on `dueDate`
2. **Manual Archiving** - Dashboard button to archive all expired items
3. **Status Management** - Uses `status = 4` for "Expired/Archived" items
4. **Public Filtering** - Public pages now show only active (non-expired) items

### Security Improvements
- Fixed SQL injection vulnerabilities in all models
- All queries now use prepared statements with parameter binding

---

## 📋 Deployment Steps

### Step 1: Backup Production Files
In cPanel File Manager, backup these files:
1. Navigate to `public_html/App/Models/`
   - Right-click `TenderModel.php` → Copy → Rename to `TenderModel.php.backup-20251204`
   - Right-click `QuotationsModel.php` → Copy → Rename to `QuotationsModel.php.backup-20251204`

2. Navigate to `public_html/App/Controllers/`
   - Backup `Tenders.php`
   - Backup `Quotations.php`

3. Navigate to `public_html/App/Controllers/Dashboard/`
   - Backup `Tenders.php`
   - Backup `Quotations.php`

4. Navigate to `public_html/App/Views/dashboard/tenders/`
   - Backup `index.php`

5. Navigate to `public_html/App/Views/dashboard/quotations/`
   - Backup `index.php`

### Step 2: Upload Deployment Package
1. **Option A:** Upload ZIP file
   - Upload `tender-quotation-phase1-2.zip` to `public_html/`
   - Extract it in place
   - Files will overwrite existing ones with correct paths

2. **Option B:** Upload files individually
   - Upload each file to its corresponding location in `public_html/`

### Step 3: Verify Upload
Check that all 8 files were uploaded successfully:
- ✅ Models (2 files)
- ✅ Controllers (4 files)
- ✅ Dashboard views (2 files)

---

## 🧪 Testing Instructions

### Test 1: Dashboard Button Visibility
1. Login to dashboard: https://umdoni.gov.za/dashboard
2. Navigate to **Tenders** page
3. **Verify:** Yellow "Archive Expired" button appears next to "Add" and "Save" buttons
4. Navigate to **Quotations** page
5. **Verify:** Yellow "Archive Expired" button appears

### Test 2: Archive Expired Items (Tenders)
1. Go to **Dashboard → Tenders**
2. Check current tenders in the table (note the count)
3. Click **"Archive Expired"** button
4. **Confirm** when prompted
5. **Expected Results:**
   - Success message: "Successfully archived X expired tender(s)!" OR
   - Info message: "No expired tenders to archive."
6. **Verify:** Tenders with `dueDate < today` are gone from the list

### Test 3: Archive Expired Items (Quotations)
1. Go to **Dashboard → Quotations**
2. Check current quotations in the table (note the count)
3. Click **"Archive Expired"** button
4. **Confirm** when prompted
5. **Expected Results:**
   - Success message: "Successfully archived X expired quotation(s)!" OR
   - Info message: "No expired quotations to archive."
6. **Verify:** Quotations with `dueDate < today` are gone from the list

### Test 4: Public Pages (After Archiving)
1. **Logout** from dashboard
2. Go to public tender page: https://umdoni.gov.za/tenders
3. **Verify:** No expired tenders are visible
4. Go to public quotation page: https://umdoni.gov.za/quotations
5. **Verify:** No expired quotations are visible

### Test 5: Check Database (Optional)
In phpMyAdmin:
1. Open `tenders` table → Browse
2. **Verify:** Expired items have `status = 4`
3. Open `quotations` table → Browse
4. **Verify:** Expired items have `status = 4`

---

## 🚨 Rollback Plan (If Issues Occur)

If anything goes wrong:

1. **Restore from backups:**
   - In cPanel File Manager, rename `.backup-20251204` files back to original names
   - OR use the backups created in Step 1

2. **Alternative:** Restore from local backups
   - Located at: `backups/tender-quotation-expiry-20251204-174536/`
   - Upload original files back to production

---

## 📊 Database Schema

**No database changes required!** This deployment uses existing columns:
- `status` column - Now uses value `4` for "Expired/Archived"
- `dueDate` column - Used for expiry detection
- `isActive` column - Existing soft delete mechanism (unchanged)

---

## ✅ Success Criteria

- [x] All 8 files deployed successfully
- [ ] Dashboard "Archive Expired" buttons visible
- [ ] Clicking button archives expired items
- [ ] Success/info messages display correctly
- [ ] Public pages exclude expired items
- [ ] No PHP errors in browser or logs

---

## 📝 What's NOT Included (Future Phase 3)

**Archive View Pages** (Coming in Phase 3):
- Public cannot yet view archived (expired) tenders/quotations
- Archive pages will be created separately: `/tenders/archive` and `/quotations/archive`
- "View Archive" links will be added to public pages

---

## 🆘 Support

If issues occur during deployment:
1. Check browser console for JavaScript errors
2. Check PHP error logs in cPanel
3. Verify file permissions (755 for directories, 644 for files)
4. Contact Nhlanhla for assistance

---

**Deployed By:** Nhlanhla Mnyandu
**Deployment Date:** 2025-12-04
**Git Commit:** `e1b629e`
