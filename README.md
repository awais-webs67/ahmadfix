# Ahmad Fix — Website

Bilingual (English / Arabic) website for **Ahmad Fix**, home appliance repair in **Dammam, Al Khobar and Dhahran**.
PHP 8 + MySQL, no framework. Every service (AC, fridge, washing machine, oven) is offered in every city.

---

## ⚠️ Before you upload these files (do this first)

1. **Revoke the old Gmail app password.** It was saved in `config/config.php` in an earlier commit, and this repo is public.
   Google Account → Security → 2-Step Verification → App passwords → delete the old one, then create a new one.
2. **Create `config/config.local.php`** on your server: copy `config/config.local.example.php`, rename the copy, and fill in:
   - your database name, user and password (from your hosting panel)
   - `SMTP_USER` and the **new** Gmail app password in `SMTP_PASS`

   This file is never pushed to GitHub. If it's missing, the site uses `root` with no password for the database, and new-lead emails are not sent.
3. Upload all the files. On the first page visit, the database **upgrades itself automatically**
   (new service URLs, lead status column, map link fix, and so on). You don't need to run any SQL.
4. **Admin panel:** go to `/admin`. If you never set a password, log in with `admin` / `ahmadfix2024`.
   You'll be asked to choose a new password straight away, and the default password stops working after that.

---

## Pages and URLs

| Page | English | Arabic |
|---|---|---|
| Home | `/en` | `/ar` |
| About / Contact | `/en/about`, `/en/contact` | `/ar/about`, `/ar/contact` |
| Services | `/en/ac-repair`, `/en/refrigerator-repair`, `/en/washing-machine-repair`, `/en/oven-repair` | `/ar/صيانة-مكيفات`, `/ar/صيانة-ثلاجات`, `/ar/صيانة-غسالات`, `/ar/صيانة-افران` |
| Cities (new) | `/en/appliance-repair-dammam`, `-khobar`, `-dhahran` | `/ar/صيانة-اجهزة-منزلية-الدمام`, `-الخبر`, `-الظهران` |
| Legal (new) | `/en/privacy-policy`, `/en/terms` | `/ar/privacy-policy`, `/ar/terms` |
| Sitemap | `/sitemap.xml` (built automatically) | |

The old service addresses (`/en/ac-repair-dammam` and the rest) **permanently redirect** to the new ones,
so links people already shared, and anything Google already indexed, keep working.

---

## Google setup checklist (done on Google's side, not in the code)

These matter as much as the website itself for getting customers from Google.

1. **Google Business Profile.** This is the most important one. It's how you appear on Google Maps and in the "near me" map results.
   - Create it at <https://business.google.com>. Choose "I deliver goods and services to my customers" and add Dammam, Al Khobar and Dhahran as service areas.
   - Category: *Appliance repair service*. Add *Air conditioning repair service* as an extra category.
   - Use exactly the same name, phone and hours as the website.
   - Add real photos of your work and technicians.
   - After every job, ask happy customers to leave a Google review. Reply to every review.
   - Paste your profile link into `GOOGLE_BUSINESS_URL` in `config/config.php`.
2. **Google Search Console** at <https://search.google.com/search-console>.
   - Add the property `https://ahmadfix.com`, choose the "HTML tag" method, and paste the code into `GOOGLE_SITE_VERIFICATION` in `config/config.php`.
   - Submit `https://ahmadfix.com/sitemap.xml`.
   - Use "URL inspection" → "Request indexing" for the home, service and city pages.
3. **Google Analytics 4** at <https://analytics.google.com>. Create a property and paste the `G-XXXX` ID into `GA4_ID`.
   The site then automatically records **Call clicks**, **WhatsApp clicks** and **contact form leads**.
   In GA4, mark `phone_call_click`, `whatsapp_click` and `generate_lead` as *key events*.
4. **Bing Webmaster Tools** (optional, quick): import from Search Console.
5. **Social profiles** (Instagram, Snapchat, TikTok are big in KSA). Add their links in `config/config.php`.
   They then appear in the footer and are shared with Google.
6. **Business directories.** List the business, with the same name, phone and website, on directories people use in Saudi Arabia
   (Apple Business Connect / Apple Maps, Bing Places, local directories).

---

## Editing content

- **Service text, prices in FAQs, page titles:** stored in the database (`services`, `pages_seo` tables, editable in phpMyAdmin).
- **City pages (text, districts, FAQs):** `config/areas.php`. Add a city by copying one block. Pages, links and the sitemap update automatically.
- **Labels and buttons:** `lang/en.php` and `lang/ar.php`.
- **Phone, WhatsApp, email:** Admin panel → Settings.
- **Images:** after replacing a photo, also replace the matching `-640.webp` and `-1024.webp` files, or delete them (the JPG is used as a fallback).
  Then bump `ASSETS_VERSION` in `config/config.php` so visitors get the new files.
- **Customer reviews (homepage):** Admin panel → **⭐ Reviews**. You can add, edit, hide or delete reviews there.
  The 5 reviews that come with the site are sample text, so replace them with real ones (Google's guidelines require reviews to be genuine).
  If you write a review in only one language, it is shown in both.

## Launch checklist

- [ ] Hosting runs **PHP 8.0+** and **MySQL 5.7+ / MariaDB 10.3+**, with Apache `mod_rewrite` turned on
- [ ] **SSL certificate** installed for `ahmadfix.com` (the site forces `https://`). Most hosts offer free Let's Encrypt.
- [ ] `config/config.local.php` created with the database login and the **new** Gmail app password
- [ ] Database imported (`database/setup.sql`) or the existing one kept; it upgrades itself on the first visit
- [ ] Admin password changed (first login forces this)
- [ ] Sample reviews replaced in Admin → ⭐ Reviews
- [ ] Send yourself a test message from the Contact page and check that the email arrives
- [ ] Google Business Profile, Search Console (submit the sitemap) and Analytics set up (see above)
- [ ] Check `https://ahmadfix.com/sitemap.xml` and `https://ahmadfix.com/robots.txt` open in the browser

Quality check before launch (Google Lighthouse, mobile): Performance 96–100, Accessibility 96–100,
Best Practices 100, SEO 100 on the home, about, service, city and contact pages.

## Local development

XAMPP/WAMP: put the folder in `htdocs` (e.g. `htdocs/AhmadFix`), import `database/setup.sql`, and open `http://localhost/AhmadFix`.
On localhost, HTTPS is not forced and URLs use the local folder automatically.
