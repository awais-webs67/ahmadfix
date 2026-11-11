# Performance & Image Optimization Findings — mrfixksa.com

**Resource & Asset Optimization Score: 81 / 100** *(Core Web Vitals Field/Lab Metrics: `Not Measured` due to live host TCP timeout & PSI v5 anonymous quota HTTP 429)*

---

## Executive Summary

- **Lightweight Architecture**: Zero heavy frontend frameworks, only 159 lines of vanilla JS ([`assets/js/main.js`](file:///c:/xampp/htdocs/FixoraKSA/assets/js/main.js) loaded with `defer`), a single self-hosted stylesheet ([`assets/css/style.css`](file:///c:/xampp/htdocs/FixoraKSA/assets/css/style.css)), non-blocking Google Fonts (`media="print" onload="this.media='all'"` pattern in [`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L95)), and responsive `<picture>` tags with `400w / 640w / 800w / 1024w` WebP `srcset` + hero `<link rel="preload" as="image" type="image/webp">`.
- **Bloated `logo-header.webp` Is 3.39x Larger Than `logo-header.png` (Medium)**: [`assets/images/logo-header.webp`](file:///c:/xampp/htdocs/FixoraKSA/assets/images/logo-header.webp) is **18,982 bytes (18.5 KB)** whereas the fallback [`assets/images/logo-header.png`](file:///c:/xampp/htdocs/FixoraKSA/assets/images/logo-header.png) is only **5,594 bytes (5.5 KB)**. Because `<picture>` serves `logo-header.webp` first in both the header and footer on every page, every visitor downloads 13.4 KB of unnecessary payload on a critical above-the-fold asset.
- **Mismatched Intrinsic `width` and `height` Attributes on `<img>` Elements (Medium — CLS Risk)**:
  - All hero, about, and service image files are natively **1:1 square (`1024x1024`, `800x800`, `640x640`, `400x400`)**, and `logo-header` is **`360x205` (`1.76:1`)**.
  - However, HTML `width`/`height` attributes declare `width="300" height="180"` (`5:3`) on service cards ([`pages/home.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/home.php#L116), [`pages/area.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/area.php#L55)), `width="600" height="450"` (`4:3`) on the about image ([`pages/about.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/about.php#L58)), and `width="120" height="40"` (`3:1`) / `width="150" height="48"` (`3.125:1`) on `logo-header` ([`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L116), [`includes/footer.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/footer.php#L22)).

---

## Asset & Image Inventory Audit

| Asset Path | File Size | Actual Dimensions | Declared HTML `width x height` | Status & Recommendation |
|---|---|---|---|---|
| `assets/images/logo-header.webp` | **18.5 KB** | `360x205` (`1.76:1`) | `120x40` (`3:1`) / `150x48` (`3.1:1`) | **3.39x larger than PNG (5.5 KB)** + wrong aspect ratio attributes. Re-encode WebP with lossy `-q 80` (target <4 KB) and set `width="120" height="68"` (header) / `width="150" height="85"` (footer). |
| `assets/images/logo-header.png` | 5.5 KB | `360x205` (`1.76:1`) | `120x40` / `150x48` | Fallback PNG is smaller than primary WebP. |
| `assets/images/hero/hero-main-1024.webp` | 88.1 KB | `1024x1024` (`1:1`) | `640x640` (`1:1`) | Good (`400w` = 22.9 KB, `640w` = 46.0 KB, `800w` = 62.0 KB). Properly preloaded on homepage. |
| `assets/images/about/about-team-1024.webp` | 93.5 KB | `1024x1024` (`1:1`) | `600x450` (`4:3`) | **Aspect-ratio mismatch**: HTML declares `4:3` on a `1:1` image ([`pages/about.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/about.php#L58)). Also preloaded on `/about`, though on `/about` the hero banner sits above the image. |
| `assets/images/services/ac-repair-*.webp` | 16.1–60.4 KB | `400x400`–`1024x1024` | `300x180` (cards) / `1024x1024` (detail) | Card HTML attributes declare `5:3` (`300x180`) on `1:1` image files. |
| `assets/images/services/fridge-repair-*.webp` | 14.2–51.3 KB | `400x400`–`1024x1024` | `300x180` (cards) / `1024x1024` (detail) | Card HTML attributes declare `5:3` (`300x180`) on `1:1` image files. |
| `assets/images/services/oven-repair-*.webp` | 21.3–95.5 KB | `400x400`–`1024x1024` | `300x180` (cards) / `1024x1024` (detail) | Card HTML attributes declare `5:3` (`300x180`) on `1:1` image files. |
| `assets/images/services/washing-machine-repair-*.webp` | 20.5–102.0 KB | `400x400`–`1024x1024` | `300x180` (cards) / `1024x1024` (detail) | `1024.webp` is 102 KB (slightly above 100 KB target). Card HTML declares `300x180`. |
| `assets/images/og-image.jpg` | 126.4 KB | `1200x630` (`1.91:1`) | `1200x630` (`og:image`) | Standard OpenGraph dimensions; can be compressed to ~65 KB with progressive JPEG (`mozjpeg -quality 78`). |

---

## Font, CSS & Caching Analysis

1. **Google Fonts Loading ([`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L92-L96))**:
   - Uses `preconnect` to `fonts.googleapis.com` and `fonts.gstatic.com` and loads `Cairo` (400, 600, 700, 800) + `Plus Jakarta Sans` (400, 500, 600, 700, 800) asynchronously via `media="print" onload="this.media='all'"`.
   - **Optimization**: On English pages (`$currentLang === 'en'`), `Cairo` is still downloaded even though only the tiny language switcher pill says `العربية`. Conversely, on Arabic pages (`$currentLang === 'ar'`), `Plus Jakarta Sans` (5 weights) is still downloaded. Conditionally loading only the active language's primary font weights saves ~60–90 KB of WOFF2 requests.
2. **Browser Caching & Compression ([`.htaccess`](file:///c:/xampp/htdocs/FixoraKSA/.htaccess#L38-L96))**:
   - Comprehensive `mod_deflate` and `mod_expires` rules (1 year immutable cache for images/fonts, 1 month for CSS/JS) plus LiteSpeed Cache (`LSCACHE`) public caching for GET requests.
   - **Cache-Busting Warning**: In [`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L99) and [`includes/footer.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/footer.php#L101), `style.css` and `main.js` are loaded without a query version or hash (`assets/css/style.css` instead of `assets/css/style.css?v=<?= filemtime(...) ?>`), meaning returning visitors will hold stale CSS/JS for up to 1 month (`max-age=2592000`) after a deployment.

