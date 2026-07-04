# ISU Console — Phase 1: Independent Login (Manual cPanel Deploy)

**Method:** cPanel File Manager + phpMyAdmin only (no SSH).
**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
**Upload file:** `deployment/isu-console-phase1/isu-phase1-upload.zip`

Gives the ISU console its **own login** (separate `isu_admins` table + session),
independent of the municipal site. After this, the console is reached via the ISU
login — not your municipal account.

---

## Files installed (all inside the ZIP, production-root layout)

| File | New / Overwrites |
|---|---|
| `App/Models/IsuAdmin.php` | New |
| `App/Controllers/Isu/Auth.php` | New (login / logout / change password) |
| `App/Controllers/Isu/Guarded.php` | **Overwrites** (now uses ISU session) |
| `App/Controllers/Isu/Console.php` | **Overwrites** (uses ISU admin identity) |
| `App/Views/isu/auth/login.php` | New |
| `App/Views/isu/auth/changepassword.php` | New |
| `public/layouts/isuAuthLayout.php` | New |
| `public/layouts/isuLayout.php` | **Overwrites** (Sign-out → ISU logout) |

No change to `public/index.php` this time.

---

## Step 1 — Upload & extract
1. File Manager → go to **`public_html/`** (root: `App`, `public`, `Components`, `vendor`).
2. **Upload** → `isu-phase1-upload.zip` → wait for 100%.
3. Right-click the ZIP → **Extract** → destination **`/public_html`** → **Extract File(s)**.
4. **Overwrite** when prompted (Guarded.php, Console.php, isuLayout.php).
5. Delete the ZIP from the server afterward.

## Step 2 — Run the migration (phpMyAdmin)
1. cPanel → **phpMyAdmin** → database **`umdonigov_umdoni`** → **SQL** tab.
2. Paste all of `deployment/isu-console-phase1/migration.sql` → **Go**.
3. This creates `isu_admins` and seeds your account with a **temporary password**
   (you'll be forced to change it on first login).

## Step 3 — First login
1. Go to **https://umdoni.gov.za/public/isu/auth/login**
2. Email: **nhlanhla@isutech.co.za**
   Password: **(the temporary password provided to you separately)**
3. You'll be sent to **Change password** — set a strong new one
   (≥ 10 chars, upper + lower + a number).
4. After that you land on the console at **/public/isu/console/index**.

## Verify
- Visiting `/public/isu/console/index` while **not** logged in → redirects to the ISU login.
- Logging in with the municipal account no longer grants console access — it's ISU-only now.
- Sign-out (top right of the console) ends the ISU session and returns to the ISU login.

## Rollback
- Restore the previous `Guarded.php`, `Console.php`, `isuLayout.php` from the prior
  `deployment/isu-console-patch/` versions if needed. The `isu_admins` table is harmless
  if left in place.

## Notes
- The ISU login lives under `/isu/`, which is exempt from the kill-switch — so you can log
  in and control the console **even while the public site is suspended**.
- Next phases: ISU user management, DB tools (backups + approved migrations),
  constrained patch-ZIP deploy tool.
