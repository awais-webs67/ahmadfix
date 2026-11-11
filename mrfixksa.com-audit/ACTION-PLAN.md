# Prioritized SEO Action Plan — `mrfixksa.com` (Updated November 11, 2026)

**Current SEO Health Score:** **97 / 100 (Grade A+)** *(Baseline `76` → Nov 6 `96` → Nov 11 **`97 / 100`**)*

---

## Completed & Verified Live on `https://mrfixksa.com/`

- [x] **Google Search Console Verified (`2026-11-11` Live):** `<meta name="google-site-verification" content="tFf9DDYVcf26...">` active across all 24 URLs.
- [x] **100% Agentic Readiness (`11 / 11 PASS`):** Lighthouse 13.5 `ARD 1.0` `/.well-known/ai-catalog.json`, `/llms.txt`, `/llms-full.txt`, `Content-Signal` in `/robots.txt`, and WebMCP `book_appliance_repair` + `document.modelContext`.
- [x] **100 / 100 Speculation Rules & Preload:** `<script type="speculationrules">` (`prefetch` + `prerender`) and responsive WebP `fetchpriority="high"` LCP preloading live.
- [x] **100% On-Page & Content Compliance (24/24 URLs):** Titles `48–59 chars`, descriptions `123–158 chars`, `H1/H2/H3` parity, `1,348–1,548` rendered words on services with SAR pricing tables, `1,059–1,224` rendered words on city hubs, `104` validated JSON-LD blocks (`0` errors), `126` images with `0` missing `alt` or dimensions.
- [x] **IndexNow Submitted:** `/indexnow-key.txt` live and all 24 URLs submitted (`HTTP 202`).

---

## Priority 1 (High) — Deep-Link Language Redirect & Sitemap

- [x] **1. Prevent Forced Arabic Redirect from Bouncing English Google Searchers Off `/en/*` Pages (RESOLVED in Schema `v8`):**
  - Updated `includes/header.php` so search/AI referrals (`Google`, `Bing`, `ChatGPT`, `Perplexity`, etc.) and explicit `/en/*` & `/ar/*` deep links are never overridden by client-side redirects, while `mrfixksa.com/` root continues to honor `Admin → Settings → Default website language`.
- [ ] **2. Submit Sitemap in Google Search Console:**
  - Confirm `https://mrfixksa.com/sitemap.xml` is submitted under **Sitemaps** in your verified GSC property and request indexing on your top priority URLs (`/ar`, `/en`, `/ar/صيانة-مكيفات`, `/en/ac-repair`, `/ar/صيانة-اجهزة-منزلية-الدمام`, `/ar/صيانة-اجهزة-منزلية-الخبر`).

---

## Priority 2 (Medium) — Edge TTFB Speed & Local Entity Graph (This Week)

- [ ] **3. Paste Verified Google Business Profile URL & Social `sameAs` Links in `/admin`:**
  - Create/verify your GBP using `GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md`.
  - Paste the live GBP Maps URL & Review URL in `/admin` → **Settings → Google & Social** (replaces the generic `maps/search/?api=1&query=...` fallback in `LocalBusiness.hasMap`).
  - Paste `@mrfixksa` social URLs (Instagram, TikTok, Snapchat, X, Facebook) to populate `LocalBusiness.sameAs`.
- [x] **4. Re-Enable Short Edge Caching (`s-maxage=300`) on `/en/*` & `/ar/*` Routes (RESOLVED in Schema `v8`):**
  - Enabled `Cache-Control: public, max-age=0, s-maxage=300, stale-while-revalidate=600` on deterministic `/en/*` & `/ar/*` pages while keeping root `/` uncached (`no-store, Vary: Accept-Language, Cookie`).

---

## Priority 3 (Low) — Analytics, Bing Copilot & Citations (Weeks 2–4)

- [ ] **5. Connect GA4 & Bing Webmaster Tools:**
  - Paste GA4 Measurement ID (`G-XXXXXXXXXX`) in `/admin` → **Settings → Google & Social**.
  - Use **Import from Google Search Console** inside Bing Webmaster Tools (powers Microsoft Copilot local answers).
- [ ] **6. Build Saudi Local Citations & First 10 Google Reviews:**
  - Register consistent NAP (`+966 58 245 3440`) on Haraj, Maroof, Saudi Yellow Pages, Daleeli, Apple Business Connect, and Bing Places.
