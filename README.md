# Ahmad Fix — Website

Bilingual (English / Arabic) website for **Ahmad Fix**, home appliance repair in **Dammam, Al Khobar and Dhahran**.
PHP 8 + MySQL, no framework. Every service (AC, fridge, washing machine, oven) is offered in every city.

---

## ⚠️ Before you upload these files (do this first)

1. **Revoke the old Gmail app password.** It was saved in `config/config.php` in an earlier commit, and this repo is public.
   Google Account → Security → 2-Step Verification → App passwords → delete the old one.
2. **Create `config/config.local.php`** on your server: copy `config/config.local.example.php`, rename the copy, and fill in
   your **database name, user and password** (from your hosting panel). That is the only thing ever written in a file.
3. Upload all the files. On the first page visit, the database **upgrades itself automatically**. You don't need to run any SQL.
4. Open `/admin`. If you never set a password, log in with `admin` / `ahmadfix2024`. You'll be asked to choose a new password straight away.
5. Go to **Admin → Settings** and fill in:
   - **Business Information → Website address**: your live address, e.g. `https://ahmadfix.com`. Every page then redirects to it (https, with or without www).
   - **Email (SMTP)**: your Gmail address and a **new** Gmail App password, then press **Send test email**.

---

## Admin panel (`/admin`)

| Page | What it does |
|---|---|
| 📋 **Leads** | Every booking and contact message, with search, filters, status and CSV export. Click a lead to open it. |
| Lead page | Booking details (service, city, address, date, time), private notes, status, **email the customer** (ready-made replies in English or Arabic), WhatsApp/Call buttons, and the email history for that customer. |
| ✉️ **Emails** | Write an email to anyone, and see every email the site sent (lead alerts, automatic thank-you emails, your replies). Failed emails show the reason. |
| ⭐ **Reviews** | Add, edit, hide or delete the reviews shown on the homepage. |
| ⚙️ **Settings** | Business name, website address, phone, WhatsApp, email, address, working hours, map location, brands, Google Analytics / Search Console / Bing codes, Google Business and social links, SMTP email, all email texts, Google titles & descriptions for every page, and your admin password. |

Nothing about the business is written in the code. The domain is not in `.htaccess` or `config.php`, and `robots.txt`, `sitemap.xml` and
`site.webmanifest` are generated with whatever address is saved in Settings.

### Emails
- **New booking or contact message:** a branded alert goes to the addresses in *Settings → Email → Send new-lead alerts to*.
  Press **Reply** in your inbox and you answer the customer directly.
- **Customer:** if they entered an email, they automatically get "Thank you, we received your request" in their own language (English or Arabic),
  with a summary of their booking. You can edit the text in *Settings → Email Texts* (placeholders like `{name}`, `{service}`, `{date}`).
- **From the admin panel:** send emails and replies with your logo and signature. When customers reply, the reply arrives in your normal inbox (the business email).
- **Gmail:** SMTP server `smtp.gmail.com`, port `587`, security `TLS`, username = your Gmail address, password = a 16-letter **App password**
  (Google Account → Security → 2-Step Verification → App passwords). The password is stored encrypted.
- **Locked out by a wrong website address?** Add `define('SITE_URL_OVERRIDE', 'https://yourdomain.com');` to `config/config.local.php`.

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
   - Paste your profile link into **Admin → Settings → Google & Social → Google Business Profile link**, and the "Ask for reviews" link into *"Write a review" link*.
2. **Google Search Console** at <https://search.google.com/search-console>.
   - Add your website address, choose the "HTML tag" method, and paste the code into **Admin → Settings → Google & Social → Google Search Console code**.
   - Submit `https://yourdomain.com/sitemap.xml`.
   - Use "URL inspection" → "Request indexing" for the home, service and city pages.
3. **Google Analytics 4** at <https://analytics.google.com>. Create a property and paste the `G-XXXX` ID into **Admin → Settings → Google & Social**.
   The site then automatically records **Call clicks**, **WhatsApp clicks** and **contact form leads**.
   In GA4, mark `phone_call_click`, `whatsapp_click` and `generate_lead` as *key events*.
4. **Bing Webmaster Tools** (optional, quick): import from Search Console.
5. **Social profiles** (Instagram, Snapchat, TikTok are big in KSA). Add their links in **Admin → Settings → Google & Social**.
   They then appear in the footer and are shared with Google.
6. **Business directories.** List the business, with the same name, phone and website, on directories people use in Saudi Arabia
   (Apple Business Connect / Apple Maps, Bing Places, local directories).

---

## Editing content

- **Service text, prices in FAQs, page titles:** stored in the database (`services`, `pages_seo` tables, editable in phpMyAdmin).
- **City pages (text, districts, FAQs):** `config/areas.php`. Add a city by copying one block. Pages, links and the sitemap update automatically.
- **Labels and buttons:** `lang/en.php` and `lang/ar.php`.
- **Phone, WhatsApp, email, hours, address, brands:** Admin → Settings.
- **Google titles & descriptions:** Admin → Settings → Google Titles (with a live character counter).
- **Images:** after replacing a photo, also replace the matching `-640.webp` and `-1024.webp` files, or delete them (the JPG is used as a fallback).
  Then bump `ASSETS_VERSION` in `config/config.php` so visitors get the new files.
- **Customer reviews (homepage):** Admin panel → **⭐ Reviews**. You can add, edit, hide or delete reviews there.
  The 5 reviews that come with the site are sample text, so replace them with real ones (Google's guidelines require reviews to be genuine).
  If you write a review in only one language, it is shown in both.

## Launch checklist

- [ ] Hosting runs **PHP 8.0+** and **MySQL 5.7+ / MariaDB 10.3+**, with Apache `mod_rewrite` turned on
- [ ] **SSL certificate** installed for `ahmadfix.com` (the site forces `https://`). Most hosts offer free Let's Encrypt.
- [ ] `config/config.local.php` created with the database login
- [ ] Admin → Settings: website address saved, SMTP filled in, **Send test email** works
- [ ] Booking test: send a message from the Contact page with your own email → you get the alert, and the "thank you" email arrives
- [ ] Database imported (`database/setup.sql`) or the existing one kept; it upgrades itself on the first visit
- [ ] Admin password changed (first login forces this)
- [ ] Sample reviews replaced in Admin → ⭐ Reviews
- [ ] Google Business Profile, Search Console (submit the sitemap) and Analytics set up (see above)
- [ ] Check `https://ahmadfix.com/sitemap.xml` and `https://ahmadfix.com/robots.txt` open in the browser

Quality check before launch (Google Lighthouse, mobile): Performance 96–100, Accessibility 96–100,
Best Practices 100, SEO 100 on the home, about, service, city and contact pages.

## Local development

XAMPP/WAMP: put the folder in `htdocs` (e.g. `htdocs/AhmadFix`), import `database/setup.sql`, and open `http://localhost/AhmadFix`.
On localhost, HTTPS is not forced and URLs use the local folder automatically.
