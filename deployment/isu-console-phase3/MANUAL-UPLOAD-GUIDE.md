# ISU Console — Phase 3: DB Tools (Manual cPanel Deploy)

**Method:** cPanel File Manager + phpMyAdmin (no SSH).
**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
**Upload file:** `deployment/isu-console-phase3/isu-phase3-upload.zip`

Adds a **Database** page to the console: create/list/download/delete backups
(reusing your existing backup engine), and run **pre-approved migrations**
from `/migrations` — one click each, once only. No free-form SQL.

Requires Phases 1–2 deployed.

---

## Files installed

| File | New / Overwrites |
|---|---|
| `App/Models/IsuBackup.php` | New |
| `App/Models/IsuMigration.php` | New |
| `App/Controllers/Isu/Database.php` | New |
| `App/Views/isu/database/index.php` | New |
| `public/layouts/isuLayout.php` | **Overwrites** (adds "Database" nav) |
| `migrations/README.md` | New (creates the `/migrations` folder) |

---

## Step 1 — Upload & extract
1. File Manager → **`public_html/`** → **Upload** `isu-phase3-upload.zip`.
2. Right-click → **Extract** → `/public_html` → **Overwrite** (`isuLayout.php`).
3. Delete the ZIP after.

## Step 2 — Run the migration (last hand-run migration!)
1. phpMyAdmin → **`umdonigov_umdoni`** → **SQL** → paste
   `deployment/isu-console-phase3/migration.sql` → **Go**.
2. Creates `isu_migrations`. From now on, future migrations run from the console UI.

## Step 3 — Verify
1. In the console top bar, click **Database**.
2. **Backups:** you should see existing backups (from the daily cron, if any).
   Click **Back up now** → a new gzipped dump appears; **Download** fetches it.
   - If "Back up now" errors about `exec()` disabled or mysqldump, that's a host
     restriction — the automated cron backups still apply; tell me and we adapt.
3. **Migrations:** the panel lists files in `/migrations`. Initially none are
   pending (only the README). Future schema changes ship here and show a **Run**
   button; each runs once and is then marked ✓.

## Rollback
- Restore the previous `isuLayout.php` from `deployment/isu-console-phase2/`.
- Remove the new files if needed. The `isu_migrations` table is harmless if left.

## Notes
- **No free-form SQL** anywhere — migrations run only from vetted files in `/migrations`.
- Backups live in `backups/database/YYYY/MM/` (same as the automated system).
- Next: Phase 4 — constrained patch-ZIP deploy tool.
