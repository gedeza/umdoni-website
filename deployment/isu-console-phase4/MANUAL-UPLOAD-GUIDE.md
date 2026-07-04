# ISU Console — Phase 4: Patch Deploy Tool (Manual cPanel Deploy)

**Method:** cPanel File Manager (this is the **last** time — after this you can
deploy from the console). No SSH.
**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
**Upload file:** `deployment/isu-console-phase4/isu-phase4-upload.zip`

Adds a **Deploy** page: upload a patch ZIP → **review the exact file list** →
apply only if every file is allowed → **one-click rollback**. Files may only
land under `App/`, `public/`, `Components/`, `migrations/`; protected files
(`App/Config.php`, `.env`, `.htaccess`, `vendor/`) and odd file types are refused.

Requires Phases 1–3.

---

## Files installed

| File | New / Overwrites |
|---|---|
| `App/Models/IsuPatch.php` | New (apply/rollback engine) |
| `App/Controllers/Isu/Deploy.php` | New |
| `App/Views/isu/deploy/index.php` | New |
| `App/Views/isu/deploy/review.php` | New |
| `public/layouts/isuLayout.php` | **Overwrites** (adds "Deploy" nav) |
| `storage/.htaccess` | New (blocks web access to staged patches/backups) |
| `migrations/2026-07-04-0001-isu-patches.sql` | New (the DB change, run from the console) |

---

## Step 1 — Upload & extract
1. File Manager → **`public_html/`** → **Upload** `isu-phase4-upload.zip`.
2. Right-click → **Extract** → `/public_html` → **Overwrite** (`isuLayout.php`).
3. Delete the ZIP after.
4. Confirm **`public_html/storage/`** is writable (**755**) — the deploy tool stages
   ZIPs and backups there.

## Step 2 — Run the DB migration FROM THE CONSOLE (dogfood Phase 3)
1. In the console, open **Database**.
2. Under **Migrations** you'll now see **`2026-07-04-0001-isu-patches.sql`** as *pending*.
3. Click **Run** → it creates the `isu_patches` table and marks itself ✓.
   *(Fallback: if anything's off, paste `deployment/isu-console-phase4/migration.sql`
   into phpMyAdmin instead.)*

## Step 3 — Verify the deploy tool
1. Top nav → **Deploy**.
2. Make a tiny test ZIP on your Mac containing e.g. `public/assets/deploy-test.txt`
   (path relative to project root) and upload it.
3. On the **review** screen you'll see the file list, each marked *allowed / new*.
   Click **Apply patch**.
4. It appears under **Recent Patches** with a **Roll back** button. Click **Roll back**
   → the test file is removed again.
5. (Optional) Upload a ZIP containing a blocked path (e.g. `App/Config.php`) — the review
   screen marks it **✗ blocked** and refuses to apply. That's the safety net working.

## Rollback (of this deploy)
- Restore the previous `isuLayout.php` from `deployment/isu-console-phase3/`.
- Remove the new Phase 4 files. The `isu_patches` table is harmless if left.

## Safety notes
- **No arbitrary file writes:** only whitelisted folders, no traversal, protected files refused.
- **Nothing applies partially:** a ZIP with any blocked entry is rejected wholesale.
- **Every apply is reversible:** overwritten files are backed up and new files tracked, so
  one click restores the previous state.
- Staged ZIPs + backups live under `storage/isu-patches/` and are blocked from the web by
  `storage/.htaccess`.
