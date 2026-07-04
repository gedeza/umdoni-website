# ISU Console — Deployment Patch

Provider-only control panel for ISU Technologies, with a **service kill-switch**
to take the Umdoni site offline (e.g. for non-payment). First iteration = manual
suspend/restore.

**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>

---

## What it does

- Adds a private console at **`/isu/console`** visible **only** to accounts flagged
  `is_isu = 1` in the database. Municipal admins (even super-admins) cannot see it.
- **Suspend:** the entire public site + municipal dashboard return a neutral
  **503 "Service Temporarily Unavailable — contact uMdoni technical staff"** page.
  No mention of payment is shown publicly.
- The `/isu` console stays reachable while suspended, so you can always restore.
- Every suspend/restore is recorded in the `site_control` audit table (who, when, why).

## How the switch works

The on/off state is a **flag file**: `storage/site-suspended.flag`.
It's checked in `public/index.php` on every request (cheap, no DB needed), so the
gate works even if MySQL is down. The DB table is only the audit trail.

---

## Files in this patch

| File | Action |
|------|--------|
| `migration.sql` | **Run once** on the production DB |
| `App/Models/SiteControl.php` | New — switch + audit logic |
| `App/Controllers/Isu/Guarded.php` | New — ISU access gate (+ CSRF) |
| `App/Controllers/Isu/Console.php` | New — console controller |
| `App/Views/isu/console/index.php` | New — console UI |
| `App/Views/errors/service-unavailable.php` | New — public 503 page |
| `public/layouts/isuLayout.php` | New — console layout |
| `public/index.php` | Modified — `/isu` route + kill-switch check |
| `storage/` | New folder — **must be writable by PHP** |

---

## Deployment steps (cPanel)

1. **Back up** the current `public/index.php`.
2. Upload the new/modified files listed above, preserving the directory structure.
3. Create the `storage/` folder at the project root if it doesn't exist and make sure
   PHP can write to it (permissions **755**, owned by the web user).
4. Run **`migration.sql`** via *cPanel → phpMyAdmin* (or the mysql CLI). This adds the
   `is_isu` column, flags `nhlanhla@isutech.co.za`, and creates `site_control`.
5. Log in as your ISU account, then visit **`https://umdoni.gov.za/public/isu/console`**
   (path may be `/isu/console` depending on your web root / `.htaccess`).

## Verify

- As an ISU account → `/isu/console` loads and shows **Service is LIVE**.
- As a normal municipal admin → `/isu/console` redirects to their dashboard.
- Click **Suspend service** → open the public site in a private window → you should see
  the neutral 503 page. The console still works.
- Click **Restore service** → the site is back. Both actions appear under *Recent actions*.

## Rollback

- In the console, click **Restore** (or simply delete `storage/site-suspended.flag`).
- To remove the feature entirely, restore the old `public/index.php`. The DB column and
  table are harmless if left in place.

## Notes / next steps

- To grant another ISU teammate access:
  `UPDATE users SET is_isu = 1 WHERE email = 'their@email';`
- Roadmap: due-date tracking, staged warning banner, activity-log integration,
  optional auto-suspend on overdue invoices.
