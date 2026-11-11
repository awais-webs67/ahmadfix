# Maps Intelligence, Bing/IndexNow, SEO Drift Baseline & Google/Unlighthouse Report

**Audit Date:** 2026-11-06  
**Skills Executed:** `seo-maps` (v2.5.0), `seo-bing` (v2.5.0), `seo-drift` (v1.0.0), `seo-google` (v2.5.0), `seo-unlighthouse` (v2.5.0)  
**Target Domain:** `https://mrfixksa.com`

---

## 1. Bing Webmaster Tools & IndexNow Live Submission (`seo-bing`)

- **IndexNow Verification Key Endpoint:** `https://mrfixksa.com/indexnow-key.txt` (`HTTP 200 OK`)
- **Live Key Value:** `0f42b6774c13a88479c266d26f977547` (32 hexadecimal chars, strictly conforming to IndexNow spec)
- **Live Submission Executed (`indexnow_submit.py`):**
  ```json
  {
    "ok": true,
    "status_code": 202,
    "submitted": 24,
    "endpoint": "https://api.indexnow.org/indexnow",
    "engines": [
      "Amazon",
      "Bing",
      "Naver",
      "Seznam.cz",
      "Yandex",
      "Yep"
    ]
  }
  ```
- **URL List Saved:** [`indexnow-urls.txt`](file:///c:/xampp/htdocs/FixoraKSA/mrfixksa.com-audit/indexnow-urls.txt) (All 24 canonical EN & AR URLs broadcast to Bing & Microsoft Copilot).

---

## 2. SEO Drift Baseline Capture (`seo-drift`)

Captured live `v6` known-good SQLite baselines (`~/.claude/seo-drift/baselines.db`) using `drift_baseline.py` so any future deployment can be diffed via `drift_compare.py`:

| Baseline ID | Live URL Captured | HTTP Status | H1 | H2 / H3 | JSON-LD Blocks | OG Tags | HTML Hash |
|---|---|---|---|---|---|---|---|
| **`#1`** | `https://mrfixksa.com/ar` | `200 OK` | `صيانة وإصلاح الأجهزة المنزلية بأيدي خبراء محترفين` | `6 H2 / 19 H3` | `2` | `11` | `931f6c6729c8...` |
| **`#2`** | `https://mrfixksa.com/en` | `200 OK` | `Professional Home Appliance Repair & Maintenance` | `6 H2 / 19 H3` | `2` | `11` | `151f01ba3425...` |
| **`#3`** | `https://mrfixksa.com/ar/ac-repair` | `200 OK` | `صيانة وإصلاح المكيفات في الدمام، الخبر والظهران` | `8 H2 / 7 H3` | `5` | `11` | `79e065bd2e04...` |
| **`#4`** | `https://mrfixksa.com/en/ac-repair` | `200 OK` | `AC Repair & Maintenance in Dammam, Al Khobar & Dhahran` | `8 H2 / 7 H3` | `5` | `11` | `4a871d9e3ec7...` |
| **`#7`** | `https://mrfixksa.com/en/appliance-repair-dammam` | `200 OK` | `Home Appliance Repair in Dammam` | `5 H2 / 7 H3` | `5` | `11` | `ae620f548f01...` |
| **`#8`** | `https://mrfixksa.com/ar/صيانة-اجهزة-منزلية-الدمام` | `200 OK` | `صيانة الأجهزة المنزلية في الدمام` | `5 H2 / 7 H3` | `5` | `11` | `3a7b17fb3c83...` |

---

## 3. Maps Intelligence & Geo-Grid Analysis (`seo-maps` — Tier 0 Free Mode)

- **Capability Tier Detected:** **Tier 0 (Free — Static GBP 25-Field Audit + LocalBusiness Schema + Cross-Platform NAP Verification + Geo-Grid Coordinates Setup)**.
- **Maps Health Score (On-Site Signals & Schema):** **94/100** (External GBP profile creation ready via [`GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md`](file:///c:/xampp/htdocs/FixoraKSA/mrfixksa.com-audit/GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md)).
- **Eastern Province Geo-Grid Center Coordinates Configured:**
  1. **Dammam Grid Center (7x7, 5km radius):** `26.4207° N, 50.0888° E` (Al Faisaliyah, Al Shati, Al Mazruiyah, Al Noor, Al Fursan, Al Rayyan)
  2. **Al Khobar Grid Center (7x7, 5km radius):** `26.2794° N, 50.2083° E` (Al Rakah, Al Olaya, Al Aqrabiyah, Al Thuqbah, Corniche)
  3. **Dhahran Grid Center (7x7, 5km radius):** `26.2361° N, 50.0393° E` (Al Dana, Doha, Al Qusur, Al Jamiah)
- **Cross-Platform NAP Consistency Matrix:**

| Field | Official Canonical Value (`mrfixksa.com`) | Status Across Website & Schema |
|---|---|---|
| **Business Name (EN)** | `Mr Fix KSA` | ✅ 100% Consistent |
| **Business Name (AR)** | `مستر فيكس` (`مركز مستر فيكس لصيانة الأجهزة المنزلية`) | ✅ 100% Consistent |
| **Address** | `King Fahd Road, Al Faisaliyah District, Dammam 32272, Eastern Province, Saudi Arabia` | ✅ 100% Consistent |
| **Phone / WhatsApp** | `+966 54 710 9965` (`https://wa.me/966547109965`) | ✅ 100% Consistent |
| **Hours** | `Sat–Thu 08:00–22:00, Fri 14:00–22:00` | ✅ 100% Consistent |

---

## 4. Google SEO APIs (`seo-google`) & Unlighthouse (`seo-unlighthouse`)

- **Google SEO API Credential Check (`google_auth.py --check`):**
  - Currently at `Tier -1` (no `google-api.json` API key / OAuth token stored in `~/.config/claude-seo/google-api.json`).
  - However, the **Executive Google SEO HTML & Excel Reports** (`google_report.py` + `generate_excel_report.py`) have been generated from our full 24-URL crawl and saved at:
    - [`Google-SEO-Report-mrfixksa.com-full.html`](file:///c:/xampp/htdocs/FixoraKSA/mrfixksa.com-audit/Google-SEO-Report-mrfixksa.com-full.html)
    - [`Google-SEO-Report-mrfixksa.com-20261106-0410.xlsx`](file:///c:/xampp/htdocs/FixoraKSA/mrfixksa.com-audit/Google-SEO-Report-mrfixksa.com-20261106-0410.xlsx)
- **Resource Preload & Speculation Rules Check (`preload_check.py`):**
  - Added `<script type="speculationrules">` (`prefetch` + `prerender`) to `includes/header.php` alongside `fetchpriority="high"` and LCP `<link rel="preload" as="image">`, raising the navigation preload architecture score to **100/100**.

