# ISU Console — Manual Deployment via cPanel (No SSH)

**Method:** Afrihost/cPanel web console only — File Manager + phpMyAdmin.
No SSH, no Terminal.
**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
**Upload file:** `deployment/isu-console-patch/isu-console-upload.zip`

The ZIP is laid out in **production-root structure**, so extracting it inside
`public_html` drops every file into the correct folder automatically.

---

## What gets installed

| File (inside the ZIP) | New / Overwrites |
|---|---|
| `App/Models/SiteControl.php` | New |
| `App/Controllers/Isu/Guarded.php` | New |
| `App/Controllers/Isu/Console.php` | New |
| `App/Views/isu/console/index.php` | New |
| `App/Views/errors/service-unavailable.php` | New |
| `public/layouts/isuLayout.php` | New |
| `public/index.php` | **OVERWRITES** (adds route + kill-switch) |
| `storage/.gitkeep` | New (creates the `storage/` folder) |

Only **`public/index.php`** is overwritten — that's the one to back up first.

---

## Step 1 — Back up the current index.php (2 min)

1. Log into cPanel: <https://reseller142.aserv.co.za:2083>
2. Open **File Manager** → go to `public_html/public/`.
3. Right-click **`index.php`** → **Download** (keep this copy — it's your rollback).
   *(Optional: also right-click → Copy → save as `index.php.bak` in the same folder.)*

## Step 2 — Upload & extract the ZIP (3 min)

1. In File Manager, navigate to **`public_html/`** (the site root — you should see
   `App`, `public`, `Components`, `vendor`, etc.).
2. Click **Upload** (top toolbar) → choose
   `deployment/isu-console-patch/isu-console-upload.zip` from your computer → wait for 100%.
3. Back in `public_html/`, right-click the uploaded **`isu-console-upload.zip`** → **Extract**.
   - When asked for the destination, keep it as the current folder (`/public_html`).
   - It will create the new `App/Controllers/Isu`, `App/Views/isu`, etc., and
     overwrite `public/index.php`. Confirm the overwrite when prompted.
4. Delete `isu-console-upload.zip` from the server afterwards (tidy-up).

## Step 3 — Make `storage/` writable (1 min)

The kill-switch writes a flag file into `storage/`, so PHP must be able to write there.

1. In File Manager, confirm the folder **`public_html/storage/`** now exists.
2. Right-click **`storage`** → **Change Permissions** → set to **755**
   (Owner: Read+Write+Execute; Group/World: Read+Execute) → Save.

## Step 4 — Run the database migration (phpMyAdmin, 2 min)

1. In cPanel, open **phpMyAdmin**.
2. Left panel → click the database **`umdonigov_umdoni`**.
3. Top tab → **SQL**.
4. Open `deployment/isu-console-patch/migration.sql`, copy **all** of it, paste into the
   SQL box, click **Go**.

This adds the `is_isu` column, flags **nhlanhla@isutech.co.za** as ISU admin, and
creates the `site_control` audit table. You should see “successfully executed”.

*If the `ALTER TABLE ... is_isu` line errors with “Duplicate column”, the column
already exists — safe to ignore; run the rest.*

---

## Step 5 — Verify (3 min)

1. Log into the site as **nhlanhla@isutech.co.za**.
2. Go to **`https://umdoni.gov.za/isu/console`**
   *(if that 404s, try `https://umdoni.gov.za/public/isu/console` — depends on the web root).*
   → You should see **“Service is LIVE”**.
3. In a **private/incognito** window, log in as a normal municipal admin and open the same
   URL → you should be redirected to the dashboard (no access). ✅ gate works.
4. Back in the console, click **Suspend service** (add a test note) → open the public
   site in a private window → you should see the neutral **“Service Temporarily
   Unavailable”** page. The console still works.
5. Click **Restore service** → site is live again. Both actions show under *Recent actions*.

---

## Rollback

- Fastest: in File Manager delete `public_html/storage/site-suspended.flag` (brings the
  site back instantly), **or** click **Restore** in the console.
- Full rollback of the code: re-upload the `index.php` you downloaded in Step 1 to
  `public_html/public/`, overwriting.

The `is_isu` column and `site_control` table are harmless if left in place.

---

## Notes

- To grant another ISU teammate access later, in phpMyAdmin SQL tab:
  `UPDATE users SET is_isu = 1 WHERE email = 'their@email';`
- The public suspension page never mentions payment — it only says the service is
  temporarily unavailable and to contact uMdoni technical staff.
