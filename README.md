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
- **Testimonials:** the 5 reviews in `database/setup.sql` are sample text. Replace them with real customer reviews
  (Google's guidelines require reviews to be genuine).

## Local development

XAMPP/WAMP: put the folder in `htdocs` (e.g. `htdocs/AhmadFix`), import `database/setup.sql`, and open `http://localhost/AhmadFix`.
On localhost, HTTPS is not forced and URLs use the local folder automatically.
