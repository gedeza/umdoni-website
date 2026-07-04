# cPanel / Afrihost Console Login Guide — uMdoni Production

**Server:** reseller142.aserv.co.za (aServ reseller platform, via Afrihost)
**cPanel user:** `umdonigov`
**Website:** https://umdoni.gov.za
**Author:** Nhlanhla Mnyandu <nhlanhla@isutech.co.za>

> This is how you reach File Manager + phpMyAdmin to deploy manually
> (the ISU console patch). No SSH required.

---

## Option A — Direct cPanel login (fastest)

1. In your browser go to: **https://reseller142.aserv.co.za:2083**
   - Port **2083** = secure cPanel. (Port 2082 is the non-SSL version — avoid it.)
   - If the browser warns about the certificate for the `aserv.co.za` host, it's the
     shared hostname — proceed / accept to continue.
2. **Username:** `umdonigov`
3. **Password:** your cPanel password (see "Where to find the password" below).
4. Click **Log in**. You land on the cPanel dashboard.

**Shortcut URLs once logged in (or bookmark these):**
- File Manager: cPanel → **Files → File Manager**
- Database: cPanel → **Databases → phpMyAdmin**

---

## Option B — Via Afrihost ClientZone (if you don't have the cPanel password)

1. Go to **https://clientzone.afrihost.com**
2. Log in with your **Afrihost account** email + password (the account that owns the
   hosting product — this is separate from the cPanel password).
3. Open **My Products / Hosting** → select the **uMdoni** hosting package.
4. Click **Manage** → look for **cPanel** / **Login to cPanel** (single sign-on — no
   cPanel password needed), **or** **Reset cPanel Password** if you need to set one.

---

## Where to find the cPanel password

1. **Afrihost ClientZone** (Option B) → Hosting package → often shows or lets you
   **reset** the cPanel password. This is the most reliable source.
2. **Hosting welcome email** from Afrihost/aServ ("Your hosting account is ready") —
   contains the initial cPanel username + password.
3. **Password manager / your records** — check if it was saved previously.
4. **Afrihost support** — 087 820 9000 / support via ClientZone if all else fails.

> Tip: once you can log in, store the cPanel password in a password manager so the
> next deploy is friction-free.

---

## After login — where to go for the ISU deploy

| Task | cPanel location |
|------|-----------------|
| Upload + extract `isu-console-upload.zip` | **File Manager** → `public_html/` |
| Set `storage/` permissions to 755 | **File Manager** → right-click folder → Change Permissions |
| Run `migration.sql` | **phpMyAdmin** → database `umdonigov_umdoni` → **SQL** tab |

Full click-by-click steps: `deployment/isu-console-patch/MANUAL-UPLOAD-GUIDE.md`

---

## Troubleshooting

- **"This site can't be reached" on :2083** — your network/ISP may block the port; try
  Option B (ClientZone SSO) instead, or a different network.
- **Login rejected** — confirm the username is `umdonigov` (all lowercase) and reset the
  password via ClientZone.
- **Can't see `public_html`** — you may be in a subaccount home dir; the site files are
  under `public_html/` (contains `App`, `public`, `Components`, `vendor`).
- **2FA prompt** — if two-factor is enabled on cPanel or ClientZone, use your
  authenticator app; there's no way around it by design.
