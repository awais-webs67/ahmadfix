# XML Sitemap Findings — mrfixksa.com

**Sitemap Score: 86 / 100**

---

## Executive Summary

- **Well-Architected Dynamic Sitemap ([`sitemap.php`](file:///c:/xampp/htdocs/FixoraKSA/sitemap.php))**: Generates valid XML with `xmlns`, `xmlns:xhtml`, and `xmlns:image` namespaces, omits deprecated `<priority>` and `<changefreq>` tags, sends `X-Robots-Tag: noindex, follow`, and pairs every English and Arabic URL with bidirectional `xhtml:link` hreflang annotations (`en`, `ar`, `x-default`).
- **Image Sitemap Points to Unoptimized `.jpg` Fallbacks Instead of `.webp` (Low)**: All 14 `<image:image>` entries (on `/en`, `/ar`, `/en/about`, `/ar/about`, and the 8 service pages) reference the heavy `.jpg` files (`119 KB – 166 KB`) rather than the primary `-1024.webp` assets (`52 KB – 104 KB`).
- **Identical `<lastmod>` Timestamp Across All Static & Area Pages (Low)**: [`getStaticPagesLastMod()`](file:///c:/xampp/htdocs/FixoraKSA/sitemap.php#L20-L39) takes the maximum `filemtime()` across all template and translation files and applies that single date (`2026-04-06`) to all 16 static and city URLs, reducing `<lastmod>` signal granularity when only one template changes.

---

## Validation Results (24 URLs Audited)

| Check | Status | Details |
|---|---|---|
| **Valid XML Format & Namespaces** | Pass | Properly declares `sitemaps.org/schemas/sitemap/0.9`, `w3.org/1999/xhtml`, and `google.com/schemas/sitemap-image/1.1` |
| **URL Count & Limits** | Pass | 24 URLs (well under the 50,000 URL / 50 MB limit) |
| **HTTP Status / Canonical Alignment** | Pass (Code) | All 24 `<loc>` URLs match their self-referencing `<link rel="canonical">` tags 1:1 |
| **No Redirecting or Noindex URLs Included** | Pass | Excludes `/admin/*`, `/404`, and the redirecting root `/` (`302`) |
| **Hreflang Reciprocity (`xhtml:link`)** | Pass | Every `<url>` includes `hreflang="en"`, `hreflang="ar"`, and `hreflang="x-default"` (pointing to the English URL) |
| **Deprecated Tags (`<priority>`, `<changefreq>`)** | Pass | Properly omitted |
| **`robots.txt` Sitemap Declaration** | Pass | Declared at `Sitemap: https://mrfixksa.com/sitemap.xml` in [`index.php`](file:///c:/xampp/htdocs/FixoraKSA/index.php#L46) |

---

## Specific Issues & Code Fixes

### 1. Per-Page `<lastmod>` Granularity ([`sitemap.php`](file:///c:/xampp/htdocs/FixoraKSA/sitemap.php#L20-L39))
Currently `getStaticPagesLastMod()` computes a single `$maxMtime` across `pages/home.php`, `pages/about.php`, `pages/contact.php`, `pages/privacy.php`, `pages/terms.php`, `pages/area.php`, `config/areas.php`, `lang/en.php`, and `lang/ar.php`.
- **Fix**: Compute `<lastmod>` per route using only that route's template file (`pages/{route}.php`) + the shared layout/translation file, so modifying `pages/privacy.php` does not tell Googlebot that `/en/appliance-repair-dammam` changed.

### 2. Reference Primary `.webp` Assets in `<image:loc>` ([`sitemap.php`](file:///c:/xampp/htdocs/FixoraKSA/sitemap.php#L101-L137))
In [`sitemap.php`](file:///c:/xampp/htdocs/FixoraKSA/sitemap.php#L101-L137), `hero-main.jpg` (155 KB), `about-team.jpg` (163 KB), and service `.jpg` files (119–166 KB) are listed in `<image:loc>`.
- **Fix**: Reference the 1024px WebP versions (`hero-main-1024.webp`, `about-team-1024.webp`, `{service}-1024.webp`) or use `get OptimizedImageUrl()` so Google Image Search indexes the higher-efficiency WebP asset that browsers actually render via `<picture>`.

