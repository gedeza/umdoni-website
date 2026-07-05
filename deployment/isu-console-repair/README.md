# ISU Console — Complete Repair ZIP

A single archive containing EVERY ISU console file at its latest version
(all 4 phases). Use it to guarantee a correct install when a phased upload
only partially landed.

## Apply (cPanel File Manager)
1. Go to **`public_html/`** (root: you must see `App`, `public`, `Components`, `vendor`).
2. **Upload** `isu-console-repair.zip`.
3. Right-click → **Extract** → destination **`/public_html`** → Extract.
4. **Overwrite All** when prompted.
5. Delete the ZIP afterward.

Does NOT touch `public/index.php` or the database. After extracting, reload
`/isu/console/index` and the Database/Deploy pages.
