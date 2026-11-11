# Full Website SEO Audit Report — `mrfixksa.com` (Live Production Audit — November 11, 2026)

- **Target URL:** `https://mrfixksa.com/`
- **Business Name:** Mr Fix KSA / مستر فيكس (`+966 58 245 3440`)
- **Business Type:** Local Service Business — Home Appliance & HVAC Repair (Dammam, Al Khobar & Dhahran, Eastern Province, Saudi Arabia)
- **Languages Audited:** English (`/en/*`) & Arabic (`/ar/*` — `ar-SA`, RTL)
- **Total Indexable URLs Crawled Live:** **24 / 24 (`HTTP 200 OK`)** + 6 Technical/AI Endpoints (`/`, `/robots.txt`, `/sitemap.xml`, `/indexnow-key.txt`, `/llms.txt`, `/.well-known/ai-catalog.json`)
- **Audit Date:** **November 11, 2026** (Live Production Crawl + Drift Comparison vs November 6, 2026)
- **Overall SEO Health Score:** **97 / 100 (Grade A+)** *(Baseline `76` → Nov 6 `96` → Nov 11 **`97 / 100`**)*

---

## 1. Executive Summary & Weighted Score Breakdown

| Category | Official Weight | Baseline (`v5`) | Nov 6 (`v6`) | **Nov 11 (`v7` Live)** | Weighted Points | Key Live Evidence (2026-11-11) |
|---|---:|---:|---:|---:|---:|---|
| **Technical SEO** | 22% | 91 | 99 | **96 / 100** | **21.12** | `24/24` `200 OK`, GSC verified, canonicals & `hreflang` (`x-default` = `/ar`) synced; `-3` for client-side `mode="ar"` redirect on `/en/*` deep links |
| **Content Quality (E-E-A-T)** | 23% | 72 | 97 | **97 / 100** | **22.31** | `1,348–1,548` rendered words on services, SAR pricing tables, `1,059–1,224` words on city hubs, 0 thin pages |
| **On-Page SEO** | 20% | 76 | 99 | **99 / 100** | **19.80** | `24/24` titles `48–59` chars, descriptions `123–158` chars, exact `H1/H2/H3` parity, `26–40` internal links/page |
| **Schema / Structured Data** | 10% | 88 | 98 | **96 / 100** | **9.60** | `104` JSON-LD blocks (`0` errors); `hasMap` still uses generic Maps search fallback & `sameAs` awaits social URLs |
| **Performance & Preload** | 10% | 94 | 98 | **94 / 100** | **9.40** | `preload_check.py` = **100/100** (`speculationrules` `prefetch`+`prerender`); uncached HTML TTFB `574ms–6,052ms` (`max-age=0`) |
| **AI Search Readiness (GEO + Agentic)** | 10% | 55 | 96 | **99 / 100** | **9.90** | `agentic_check.py` = **11 / 11 PASS** (`ARD 1.0` `ai-catalog.json`, `llms.txt`, `Content-Signal`, WebMCP) |
| **Images & Visual** | 5% | 90 | 99 | **99 / 100** | **4.95** | `126` `<img>` tags audited: `0` missing `alt`, `0` missing `width`/`height`, `4.5 KB` `logo-header.webp`, WebP `srcset` |
| **Total SEO Health Score** | **100%** | **76 / 100** | **96 / 100** | **97.08 / 100** | **97 / 100 (A+)** | **0 Critical Drift · 0 Warning Drift** |

---

## 2. What Changed Between November 6 and November 11, 2026 (Drift & Live Diff)

1. **Google Search Console Verification Now Active (`PASS`):**  
   Live HTML across all 24 URLs now emits `<meta name="google-site-verification" content="tFf9DDYVcf26O2j0JLgp3GBglmhM_0shyA9zkYeb6eM">`.
2. **Agentic Readiness Upgraded to `11 / 11 PASS` (`100%`):**  
   Commit `0b0b0e8` upgraded `/.well-known/ai-catalog.json` to Lighthouse 13.5 `ARD 1.0` (`specVersion: "1.0"`, `urn:air:*` IDs), added Markdown links in `/llms.txt`, and wired `document.modelContext` WebMCP entry point. `agentic_check.py` now passes **11 out of 11** applicable checks with `0` warnings.
3. **Speculation Rules & Preload Score `100 / 100`:**  
   `<script type="speculationrules">` (`prefetch` moderate + `prerender` conservative) is live on all 24 pages (`preload_check.py` score: **100/100**, `0` recommendations).
4. **Zero SEO Regressions (`drift_compare.py`):**  
   Comparing live `2026-11-11` snapshots against `2026-11-06` SQLite baselines across `/ar`, `/en`, `/en/ac-repair`, and `/en/appliance-repair-dammam` returned **`0 criticals` and `0 warnings`** across all 17 drift rules.
5. **Owner Set Default Website Language to Arabic (`mode = "ar"`):**  
   - Both HTML `<head>` and `/sitemap.xml` (`13,737 B`) automatically updated `hreflang="x-default"` to the `/ar/*` canonical URLs with **100% consistency**.
   - Root `https://mrfixksa.com/` cleanly returns `302 Found -> https://mrfixksa.com/ar` with `Vary: Accept-Language, Cookie` and `Cache-Control: public, max-age=0, must-revalidate`.
   - **Important Side-Effect Caught (See Finding #1 Below):** Because `mode = "ar"` also runs client-side on deep `/en/*` URLs, human users clicking an `/en/*` result in Google Search get redirected to `/ar/*` on their first tab visit.

---

## 3. Prioritized Findings & Actionable Fixes (2026-11-11)

### High Priority

1. **Client-Side Forced Arabic (`mode = "ar"`) Redirects Human Searchers Off `/en/*` Landing Pages**
   - **Evidence:** Live `/en` and `/en/ac-repair` embed `var mode = "ar";` in `includes/header.php` (lines 99–131). While search engine and AI bots (`Googlebot`, `Bingbot`, `ChatGPT`, `ClaudeBot`, `Perplexity`) bypass the script and index `/en/*` properly, a **real human user** clicking `https://mrfixksa.com/en/ac-repair` from Google Search on a fresh browser session (`!user_lang_choice` cookie, `!sessionStorage['lang_synced']`) is immediately redirected via `window.location.replace(altAr)` to `https://mrfixksa.com/ar/صيانة-مكيفات`.
   - **Impact:** Hurts English user conversion/SXO (causes English-speaking expats/Aramco residents in Dhahran & Al Khobar to bounce back to Google SERPs unless they manually click the language toggle).
   - **Fix Options:**
     - **No-Code Admin Fix (Recommended — 30 seconds):** In `https://mrfixksa.com/admin` → **Settings**, set **Default website language** to **`Automatic (match visitor's browser)`**. Arabic phones/browsers open `/ar`, English phones/browsers open `/en`.
     - **Code Fix (If you want `mrfixksa.com/` root to always force Arabic for everyone, while keeping `/en/*` Google links in English):** Scope the forced `mode` redirect strictly to root `/` (`index.php` already redirects `/` → `/ar` on the server!) and remove client-side forced override on explicit `/en/*` and `/ar/*` paths.

### Medium Priority

2. **Uncached Shared-Hosting TTFB Spikes (`574 ms` – `6,052 ms`, Median `1,279 ms`)**
   - **Evidence:** In our live 24-URL crawl, 12 pages responded in `574–1,219 ms`, 9 pages in `1,339–2,570 ms`, and 3 cold PHP hits spiked above `2.8s` (`/ar/صيانة-اجهزة-منزلية-الظهران`: `2,860 ms`, `/en/contact`: `3,468 ms`, `/en/terms`: `6,052 ms`). This occurs because `Cache-Control: public, max-age=0, must-revalidate` currently forces Hostinger CDN (`hcdn`) to revalidate every HTML request at origin.
   - **Recommendation:** Only `https://mrfixksa.com/` (root dispatcher) varies by `Accept-Language`. Explicit `/en/*` and `/ar/*` URLs serve deterministic HTML and can safely emit `Cache-Control: public, max-age=60, s-maxage=300, stale-while-revalidate=600` once client-side forced redirect is removed from inner pages — cutting global TTFB to `<100 ms` from `hcdn` edge cache!

3. **Populate Verified Google Business Profile URL & Social `sameAs` Links in `/admin`**
   - **Evidence:** Live `LocalBusiness` JSON-LD currently outputs the generic fallback `"hasMap": "https://www.google.com/maps/search/?api=1&query=Dammam%2C+Al+Khobar%2C+Dhahran"` and omits `sameAs` (`null`) because those fields are still blank in `Admin → Settings → Google & Social`.
   - **Recommendation:** Once your Google Business Profile is live (using `GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md`), paste the real GBP Maps link, Review link, and `@mrfixksa` social URLs into `/admin`.

### Low / Info Priority

4. **Add GA4 Measurement ID (`G-XXXXXXXXXX`) & Bing Verification (`msvalidate.01`) in `/admin`**
   - **Evidence:** `has_ga4: false` and `has_bing: false` across all 24 pages (`window.dataLayer` conversion events in `assets/js/main.js` are already active and ready).
   - **Recommendation:** Paste `G-XXXXXXXXXX` and import your verified GSC property directly into Bing Webmaster Tools (1-click GSC import requires zero extra verification tags and feeds Microsoft Copilot!).

5. **Hostinger CDN (`hcdn`) Reverse-DNS Bot Firewall (`Info` — Expected Behavior)**
   - **Evidence:** Synthetic HTTP requests spoofing `User-Agent: Googlebot/2.1` from non-Google IPs receive `403 Forbidden` from Hostinger's `hcdn` anti-spoofing WAF, while genuine Googlebot IPs (proven by live GSC verification) and standard browser/AI user agents receive `200 OK`.
   - **Recommendation:** None required — verify via GSC **URL Inspection → Test Live URL** on `/ar` and `/en` when submitting your sitemap.

---

## 4. Complete 24-URL Live Crawl Matrix (November 11, 2026)

*Note: `Rendered Words` counts full visible page text (including shared header/footer trust copy); body-only copy is `~350` words lower, matching the `993–1,198` service body words and `709–879` city body words.*

| # | Live URL | HTTP | TTFB (`ms`) | Title Chars | Desc Chars | Rendered Words | Headings (`H1/H2/H3`) | Imgs (`Missing Alt`) | Internal Links | JSON-LD Blocks | `x-default` Synced |
|---:|---|---:|---:|---:|---:|---:|---|---|---|---|---|
| 1 | `https://mrfixksa.com/en` | `200` | 824 | 54 | 153 | 957 | `1 / 6 / 19` | `8 (0)` | 32 | 2 | `/ar` (PASS) |
| 2 | `https://mrfixksa.com/ar` | `200` | 1,869 | 54 | 143 | 873 | `1 / 6 / 19` | `8 (0)` | 32 | 2 | `/ar` (PASS) |
| 3 | `https://mrfixksa.com/en/about` | `200` | 2,279 | 51 | 152 | 1,156 | `1 / 7 / 19` | `8 (0)` | 34 | 5 | `/ar/about` (PASS) |
| 4 | `https://mrfixksa.com/ar/about` | `200` | 2,555 | 52 | 142 | 1,023 | `1 / 7 / 19` | `8 (0)` | 34 | 5 | `/ar/about` (PASS) |
| 5 | `https://mrfixksa.com/en/contact` | `200` | 3,468 | 51 | 155 | 524 | `1 / 1 / 8` | `3 (0)` | 27 | 4 | `/ar/contact` (PASS) |
| 6 | `https://mrfixksa.com/ar/contact` | `200` | 1,762 | 53 | 148 | 515 | `1 / 1 / 8` | `3 (0)` | 27 | 4 | `/ar/contact` (PASS) |
| 7 | `https://mrfixksa.com/en/privacy-policy` | `200` | 574 | 51 | 135 | 580 | `1 / 6 / 3` | `3 (0)` | 26 | 3 | `/ar/privacy-policy` (PASS) |
| 8 | `https://mrfixksa.com/ar/privacy-policy` | `200` | 702 | 48 | 149 | 534 | `1 / 6 / 3` | `3 (0)` | 26 | 3 | `/ar/privacy-policy` (PASS) |
| 9 | `https://mrfixksa.com/en/terms` | `200` | 6,052 | 48 | 123 | 561 | `1 / 6 / 3` | `3 (0)` | 26 | 3 | `/ar/terms` (PASS) |
| 10 | `https://mrfixksa.com/ar/terms` | `200` | 664 | 52 | 145 | 521 | `1 / 6 / 3` | `3 (0)` | 26 | 3 | `/ar/terms` (PASS) |
| 11 | `https://mrfixksa.com/en/ac-repair` | `200` | 799 | 50 | 152 | 1,548 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-مكيفات` (PASS) |
| 12 | `https://mrfixksa.com/ar/صيانة-مكيفات` | `200` | 1,590 | 48 | 146 | 1,408 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-مكيفات` (PASS) |
| 13 | `https://mrfixksa.com/en/refrigerator-repair` | `200` | 2,570 | 57 | 155 | 1,445 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-ثلاجات` (PASS) |
| 14 | `https://mrfixksa.com/ar/صيانة-ثلاجات` | `200` | 1,125 | 48 | 146 | 1,348 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-ثلاجات` (PASS) |
| 15 | `https://mrfixksa.com/en/washing-machine-repair` | `200` | 983 | 54 | 150 | 1,469 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-غسالات` (PASS) |
| 16 | `https://mrfixksa.com/ar/صيانة-غسالات` | `200` | 1,636 | 48 | 153 | 1,351 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-غسالات` (PASS) |
| 17 | `https://mrfixksa.com/en/oven-repair` | `200` | 791 | 55 | 156 | 1,482 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-افران` (PASS) |
| 18 | `https://mrfixksa.com/ar/صيانة-افران` | `200` | 1,035 | 50 | 157 | 1,365 | `1 / 8 / 7` | `4 (0)` | 40 | 5 | `/ar/صيانة-افران` (PASS) |
| 19 | `https://mrfixksa.com/en/appliance-repair-dammam` | `200` | 1,219 | 58 | 158 | 1,224 | `1 / 5 / 7` | `7 (0)` | 38 | 5 | `/ar/صيانة-اجهزة-منزلية-الدمام` (PASS) |
| 20 | `https://mrfixksa.com/ar/صيانة-اجهزة-منزلية-الدمام` | `200` | 765 | 55 | 146 | 1,117 | `1 / 5 / 7` | `7 (0)` | 38 | 5 | `/ar/صيانة-اجهزة-منزلية-الدمام` (PASS) |
| 21 | `https://mrfixksa.com/en/appliance-repair-khobar` | `200` | 1,339 | 59 | 157 | 1,207 | `1 / 5 / 7` | `7 (0)` | 38 | 5 | `/ar/صيانة-اجهزة-منزلية-الخبر` (PASS) |
| 22 | `https://mrfixksa.com/ar/صيانة-اجهزة-منزلية-الخبر` | `200` | 2,120 | 54 | 151 | 1,083 | `1 / 5 / 7` | `7 (0)` | 38 | 5 | `/ar/صيانة-اجهزة-منزلية-الخبر` (PASS) |
| 23 | `https://mrfixksa.com/en/appliance-repair-dhahran` | `200` | 871 | 59 | 153 | 1,142 | `1 / 5 / 7` | `7 (0)` | 38 | 5 | `/ar/صيانة-اجهزة-منزلية-الظهران` (PASS) |
| 24 | `https://mrfixksa.com/ar/صيانة-اجهزة-منزلية-الظهران` | `200` | 2,860 | 56 | 150 | 1,059 | `1 / 5 / 7` | `7 (0)` | 38 | 5 | `/ar/صيانة-اجهزة-منزلية-الظهران` (PASS) |

---

## 5. Google Search Updates Context (`seo_updates.py` — 2026 Ledger)

- **March 2026 Core Update (`2026-03-27` – `2026-04-08`) & August 2026 Core Update:** Reward first-hand local operational detail, transparent pricing (`50 SAR` diagnostic fee waived on repair + SAR tables on all 8 service pages), and genuine entity consistency (`NAP` + `postalCode: 32272`).
- **March 2026 & September 2026 Spam Updates (`2026-09-24` – `2026-10-08`):** Strictly penalize doorway city pages and fake self-serving `AggregateRating` markup. `mrfixksa.com` is 100% compliant: only 3 genuine metropolitan hubs (`Dammam`, `Al Khobar`, `Dhahran`) with unique local content, and `AggregateRating` is intentionally withheld until real Google Business Profile reviews exist.
