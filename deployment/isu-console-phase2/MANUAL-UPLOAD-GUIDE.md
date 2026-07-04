# ISU Console — Phase 2: ISU User Management (Manual cPanel Deploy)

**Method:** cPanel File Manager + phpMyAdmin (no SSH).
**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
**Upload file:** `deployment/isu-console-phase2/isu-phase2-upload.zip`

Adds an **ISU Admins** page to the console: list admins, add new ones (with a
one-time on-screen password), reset passwords, and deactivate/reactivate — all
audited. Requires Phase 1 to be deployed first.

---

## Files installed (production-root layout in the ZIP)

| File | New / Overwrites |
|---|---|
| `App/Models/IsuAudit.php` | New (general ISU audit log) |
| `App/Models/IsuAdmin.php` | **Overwrites** (adds management methods) |
| `App/Controllers/Isu/Users.php` | New |
| `App/Views/isu/users/index.php` | New |
| `public/layouts/isuLayout.php` | **Overwrites** (adds nav: Site Control / ISU Admins) |

---

## Step 1 — Upload & extract
1. File Manager → **`public_html/`** → **Upload** `isu-phase2-upload.zip`.
2. Right-click → **Extract** → destination `/public_html` → **Overwrite** when asked
   (`IsuAdmin.php`, `isuLayout.php`).
3. Delete the ZIP afterward.

## Step 2 — Run the migration (phpMyAdmin)
1. phpMyAdmin → database **`umdonigov_umdoni`** → **SQL** tab.
2. Paste all of `deployment/isu-console-phase2/migration.sql` → **Go**.
3. Creates the `isu_audit` table.

## Step 3 — Verify (logged into the console)
1. In the console top bar you'll now see **Site Control** and **ISU Admins**.
2. Open **ISU Admins** → you should see your own account (marked **You**).
3. **Add ISU Admin:** enter a name + email → a **one-time password** box appears.
   Copy it; the new admin uses it at `/public/isu/auth/login` and is forced to change it.
4. **Reset pw** on another admin → shows a fresh one-time password.
5. **Deactivate** another admin → their access is cut immediately (Guarded re-checks the DB
   every request). You cannot deactivate yourself or the last active admin.

## Rollback
- Restore the previous `IsuAdmin.php` and `isuLayout.php` from `deployment/isu-console-phase1/`.
- The `isu_audit` table is harmless if left in place.

## Notes
- Temp passwords are shown **once** and never stored in plaintext (only a bcrypt hash).
- Next: Phase 3 (DB tools — backups + pre-approved migrations), Phase 4 (constrained
  patch-ZIP deploy tool).
