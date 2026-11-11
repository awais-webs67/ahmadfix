# Hosting Migration & Zero-Downtime Redeploy Guide — `Mr Fix KSA`

This repository is engineered for **100% plug-and-play portability** across **Hostinger, cPanel, Plesk, LiteSpeed, Apache, Cloudways, and Nginx VPS**.

Moving to a new hosting provider takes **only 3 steps** — no code edits, no lost settings, and no broken SEO/encryption keys.

---

## Step 1 — Export From Your Old Hosting (2 Files Only)

1. **Database Backup (`.sql`):**
   - **Easiest (1 Click inside Website Admin):** Open `https://mrfixksa.com/admin/settings#system` and click **`⬇ Download Database Backup (.sql)`**.
   - **Alternative (phpMyAdmin):** Open phpMyAdmin on your old host → select your database → **Export** → **Quick (`SQL`)**.
2. **Private Config File (`config/config.php`):**
   - Download `config/config.php` from your old hosting File Manager (contains your SMTP/email settings).
   - *(Note: Encrypted passwords in the database automatically work on the new server because `app_key` is mirrored safely inside `site_settings` in Schema `v8`.)*

---

## Step 2 — Deploy Repository on Your New Hosting

1. Connect your GitHub repository (`main` branch) to your new hosting's **Git Deployment** (Hostinger Git / cPanel *Git Version Control* / `git clone` into `public_html`).
2. Create a new MySQL database + user in your new hosting panel (with **ALL PRIVILEGES**).
3. Open **phpMyAdmin** on your new hosting, click your new database name on the left, go to **Import**, and upload the `.sql` backup from Step 1.

---

## Step 3 — Drop In `config/config.php` & Update DB Login

1. Upload your old `config/config.php` into `public_html/config/config.php` (or copy `config/config.example.php` → `config/config.php`).
2. Edit **only these 4 lines** in `config/config.php` to match the new hosting's MySQL database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_new_db_name');
   define('DB_USER', 'your_new_db_user');
   define('DB_PASS', 'your_new_db_password');
   ```
   *(If your new host uses a custom MySQL port like `3307` or `25060`, set `define('DB_PORT', '25060');`.)*
3. Point your domain DNS (`A` / `CNAME`) to the new hosting IP and enable free **SSL (HTTPS)**.

**Done!** Your entire bilingual website (`/en/*` & `/ar/*`), Admin panel, leads, reviews, SMTP email, Google Search Console tag, Sitemap, IndexNow, Schema, `/llms.txt`, and `/.well-known/ai-catalog.json` will run identically on the new server.

---

## Notes for Special Cases

- **Testing on a Temporary Domain / Staging URL Before DNS Cutover:**  
  Uncomment `define('SITE_URL_OVERRIDE', 'https://staging.yourdomain.com');` in `config/config.php` so it doesn't redirect to `https://mrfixksa.com` while testing. Comment it back out when going live.
- **Nginx VPS Hosting:**  
  Apache and LiteSpeed hosts (Hostinger, cPanel, Namecheap, SiteGround) automatically load `.htaccess`. If you ever move to a bare Nginx VPS, copy the rules from `nginx.conf.example`.
- **Fresh Brand-New Installation (Without Old Database):**  
  Import `database/setup.sql` into any empty MySQL database, copy `config/config.example.php` → `config/config.php`, and open the site — automatic migrations (`Schema v8`) will immediately upgrade all SEO content on first load.
