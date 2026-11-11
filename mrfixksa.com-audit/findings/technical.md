# Technical SEO Findings — `mrfixksa.com` (Updated Live Audit — 2026-11-11)

**Technical SEO Score: 96 / 100**

---

## Executive Summary

- **24 / 24 Sitemap URLs Live (`HTTP 200 OK`)**: All 12 English and 12 Arabic routes resolve cleanly over HTTPS (`HTTP/2`, Hostinger `hcdn`), with self-referencing `<link rel="canonical">` and 100% reciprocal `hreflang` (`en`, `ar`, and `x-default` synced to `/ar` across both HTML `<head>` and `/sitemap.xml`).
- **Google Search Console Verified (`2026-11-11`)**: `<meta name="google-site-verification" content="tFf9DDYVcf26O2j0JLgp3GBglmhM_0shyA9zkYeb6eM">` is present across all 24 URLs.
- **Zero Technical Drift (`drift_compare.py`)**: `0` criticals and `0` warnings across all 17 SEO drift rules compared against `2026-11-06` baselines.
- **High-Priority UX/SEO Issue Caught (`mode = "ar"` on Deep `/en/*` Links)**: Setting `Default website language = Arabic` in Admin sets `var mode = "ar"` in [`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L99-L131). Bots bypass it and index `/en/*`, but first-time human searchers clicking an `/en/*` result from Google Search are redirected client-side to `/ar/*`.
- **Medium-Priority TTFB Variance (`574 ms` – `6,052 ms`)**: `Cache-Control: public, max-age=0, must-revalidate` prevents CDN redirect poisoning on `/`, but leaves deterministic `/en/*` and `/ar/*` pages uncached at `hcdn` edge.

---

## Category Breakdown (2026-11-11 Live)

| # | Category | Status | Score | Notes |
|---|---|---|---:|---|
| 1 | Crawlability | Excellent | 100/100 | `/robots.txt` (`200 OK`) allows all search + AI bots + `Content-Signal`; `/sitemap.xml` (`200 OK`, 24 URLs) |
| 2 | Indexability | Good | 92/100 | Canonicals & `hreflang` 100% valid; GSC verified; `-8` for client-side `mode="ar"` redirect on human `/en/*` visits |
| 3 | Security | Very Good | 95/100 | HTTPS enforced, HSTS, `SAMEORIGIN`, `nosniff`, `Referrer-Policy`, `Permissions-Policy` active; CSP optional |
| 4 | URL Structure | Excellent | 100/100 | Clean `/en/{slug}` and `/ar/{slug}` UTF-8 slugs; legacy & trailing-slash 301 redirects active |
| 5 | Mobile Optimization | Excellent | 98/100 | Responsive viewport, native RTL/LTR, 48px+ tap targets |
| 6 | Performance & Preload | Very Good | 94/100 | `preload_check.py` = `100/100` (`speculationrules` `prefetch`+`prerender`); origin TTFB `0.57s–6.05s` |
| 7 | Structured Data | Very Good | 96/100 | `104` valid JSON-LD blocks (`0` errors); `hasMap` & `sameAs` awaiting live GBP/social links in `/admin` |
| 8 | JS Rendering | Excellent | 100/100 | 100% PHP SSR; first FAQ expanded by default (`aria-expanded="true"`) |
| 9 | IndexNow Protocol | Excellent | 100/100 | `/indexnow-key.txt` (`a778886876f83689598d73d658a4ef8f`) live; 24 URLs submitted (`HTTP 202`) |
