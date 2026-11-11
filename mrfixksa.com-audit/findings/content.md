# Content Quality & E-E-A-T Findings — mrfixksa.com

**Content Quality Score: 64 / 100**

---

## Executive Summary

- **Systematic Word Count Deficit on All 8 Service Pages (High)**: Every service detail page falls below the **800-word floor** for service pages (`seo/references/quality-gates.md`). English service pages range from **439 to 532 words**, while Arabic service pages range from **328 to 382 words**.
- **Missing Second Content Section in Arabic Service Pages (High)**: In [`database/setup.sql`](file:///c:/xampp/htdocs/FixoraKSA/database/setup.sql#L207-L267), `full_desc_en` contains two `<h3>` sections (service overview + `<h3>Why Choose Our ... Service?</h3>`), whereas `full_desc_ar` omits the second `<h3>` section and paragraph across all 4 services (`صيانة-مكيفات`, `صيانة-ثلاجات`, `صيانة-غسالات`, `صيانة-افران`), leaving Arabic pages 25–30% thinner than English in a primarily Arabic-speaking market.
- **Title Tag & Meta Description Length Violations Across 17 of 24 Pages (Medium)**:
  - **9 pages exceed the 60-character title tag limit** (all 6 English/Arabic city pages at 67–71 chars, plus EN/AR `/about` and EN `/contact`).
  - **4 legal pages have undersized titles (<30 chars)** (`Privacy Policy | Mr Fix KSA` = 27 chars).
  - **3 English city pages exceed the 160-character meta description limit** (171–178 chars).
  - **6 Arabic pages have undersized meta descriptions (<120 chars)** — notably Arabic `/ar/about` is only **86 chars** because [`config/migrations.php`](file:///c:/xampp/htdocs/FixoraKSA/config/migrations.php#L469-L483) (`seo_lengths` migration step 11) expanded English meta descriptions without updating `about_meta_description_ar`.
- **E-E-A-T Trust & Authenticity Gaps (High)**: No Saudi Commercial Registration (CR / السجل التجاري) number, no real technician/fleet photography, a personal Gmail address (`ahmadarian538@gmail.com`) displayed as the official business email, and no upfront SAR diagnostic/call-out pricing table in the main service body copy.

---

## E-E-A-T Composite Breakdown (100-Point Scale)

| Dimension | Weight | Score | Weighted | Evidence & Gaps |
|---|---|---|---|---|
| **Experience** | 20% | 58/100 | 11.6 | Strong local environmental context in [`config/areas.php`](file:///c:/xampp/htdocs/FixoraKSA/config/areas.php) (45°C Dammam summer loads, Al Khobar coastal salt humidity, KFUPM Dhahran villas), but zero real job photos, case logs, or before/after repair examples. |
| **Expertise** | 25% | 68/100 | 17.0 | Specific component terminology (scroll/rotary compressors, inverter PCB boards, R410A/R22 refrigerant, defrost thermostats, magnetrons) across EN/AR service pages; lacks named lead technicians or certifications. |
| **Authoritativeness** | 25% | 45/100 | 11.25 | Recent rebrand from `Ahmad Fix` to `Mr Fix KSA`; 0 external brand mentions or directory citations on Google Search; `sameAs` social/GBP links unpopulated by default. |
| **Trustworthiness** | 30% | 62/100 | 18.6 | Clear phone/WhatsApp (`+966 57 506 4911`), physical hours, privacy policy & terms pages present; weakened by personal Gmail contact (`ahmadarian538@gmail.com`), missing Saudi CR number, and lack of visible written warranty duration (e.g., 30-day vs 90-day warranty). |
| **Composite E-E-A-T** | **100%** | — | **58.5 / 100** | Needs first-hand proof signals and entity verification to compete in Dammam/Khobar SERPs. |

---

## Google "Who, How, Why" Framework Assessment

1. **Who created the content?** — *Partial*: Attributed to the business entity "Mr Fix KSA" (`مستر فيكس`), but lacks individual author/lead technician attribution or company registration credentials.
2. **How was the content produced?** — *Needs Improvement*: Cleanly structured and free of invisible Unicode watermarks (`U+200B`, `U+FEFF`, `U+00AD`), and [`metadata_template.py`](file:///c:/xampp/htdocs/FixoraKSA/mrfixksa.com-audit/metadata-pairs.json) confirmed **0% templated title-equals-description risk** (`site_risk: low`). However, all 4 illustrations (`hero-main`, `about-team`, `ac-repair`, etc.) are stylized AI/stock illustrations rather than authentic photos of Mr Fix KSA vans, uniforms, or repair work in Dammam/Khobar.
3. **Why was the content created?** — *Good*: Designed to help Eastern Province residents identify appliance symptoms and book same-day repair, though missing concrete price ranges and warranty duration commitments that searchers need to make a booking decision.

---

## Complete 24-Page Content & Metadata Audit Table

| Route | Lang | Words | Floor | Title Len | Desc Len | H1 Count | Key Issues |
|---|---|---|---|---|---|---|---|
| `/en` | EN | 1,039 | 500 | 57 | 145 | 1 | Passes word count and metadata gates |
| `/ar` | AR | 773 | 500 | 53 | 127 | 1 | Passes word count and metadata gates |
| `/en/about` | EN | 490 | 400 | **62** | 126 | 1 | Title > 60 chars (`62`) |
| `/ar/about` | AR | 384 | 400 | **63** | **86** | 1 | Words < 400 (`384`); Title > 60 (`63`); Desc < 120 (`86`) |
| `/en/contact` | EN | 269 | 300 | **61** | 123 | 1 | Title > 60 chars (`61`) |
| `/ar/contact` | AR | 228 | 300 | 56 | **110** | 1 | Desc < 120 chars (`110`) |
| `/en/privacy-policy` | EN | 384 | 300 | **27** | 132 | 1 | Title < 30 chars (`27`) |
| `/ar/privacy-policy` | AR | 310 | 300 | **26** | **112** | 1 | Title < 30 (`26`); Desc < 120 (`112`) |
| `/en/terms` | EN | 378 | 300 | **29** | 127 | 1 | Title < 30 chars (`29`) |
| `/ar/terms` | AR | 334 | 300 | **23** | **103** | 1 | Title < 30 (`23`); Desc < 120 (`103`) |
| `/en/ac-repair` | EN | **532** | 800 | 55 | 148 | 1 | **268 words below 800-word service floor** |
| `/ar/صيانة-مكيفات` | AR | **382** | 800 | 47 | 127 | 1 | **418 words below 800-word service floor**; missing 2nd H3 |
| `/en/refrigerator-repair` | EN | **439** | 800 | 56 | 152 | 1 | **361 words below 800-word service floor**; no SAR price |
| `/ar/صيانة-ثلاجات` | AR | **328** | 800 | 47 | 125 | 1 | **472 words below 800-word service floor**; missing 2nd H3 |
| `/en/washing-machine-repair` | EN | **453** | 800 | 56 | 135 | 1 | **347 words below 800-word service floor**; no SAR price |
| `/ar/صيانة-غسالات` | AR | **328** | 800 | 47 | **112** | 1 | **472 words below floor**; Desc < 120 (`112`); missing 2nd H3 |
| `/en/oven-repair` | EN | **444** | 800 | 59 | 147 | 1 | **356 words below 800-word service floor**; no SAR price |
| `/ar/صيانة-افران` | AR | **334** | 800 | 53 | **107** | 1 | **466 words below floor**; Desc < 120 (`107`); missing 2nd H3 |
| `/en/appliance-repair-dammam` | EN | 596 | 500 | **67** | **178** | 1 | Title > 60 (`67`); Desc > 160 (`178`) |
| `/ar/صيانة-اجهزة-منزلية-الدمام` | AR | **494** | 500 | **70** | 121 | 1 | Title > 60 (`70`); slightly under 500-word floor |
| `/en/appliance-repair-khobar` | EN | 596 | 500 | **70** | **177** | 1 | Title > 60 (`70`); Desc > 160 (`177`) |
| `/ar/صيانة-اجهزة-منزلية-الخبر` | AR | **488** | 500 | **69** | 121 | 1 | Title > 60 (`69`); slightly under 500-word floor |
| `/en/appliance-repair-dhahran` | EN | 546 | 500 | **68** | **171** | 1 | Title > 60 (`68`); Desc > 160 (`171`) |
| `/ar/صيانة-اجهزة-منزلية-الظهران` | AR | **462** | 500 | **71** | 123 | 1 | Title > 60 (`71`); 38 words under 500-word floor |

---

## Additional On-Page & Content Findings

1. **Legacy `<meta name="keywords">` Tag ([`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L59-L61))**:
   - Present on 16 of 24 pages. Ignored by Google and Bing; exposes keyword targeting directly in page source. Remove lines 59–61 in `includes/header.php`.
2. **Boilerplate "Why Customers in {City} Choose Mr Fix KSA" Section ([`pages/area.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/area.php#L89-L98))**:
   - While [`config/areas.php`](file:///c:/xampp/htdocs/FixoraKSA/config/areas.php) defines rich city-specific intros, climate notes, neighbourhoods, and FAQs, the 4 bullet points in `area.php` lines 92–97 (`area_benefit_1` to `area_benefit_4`) are 100% identical across Dammam, Al Khobar, and Dhahran.
3. **Lack of Internal Cross-Links Between Service Pages and City Pages**:
   - City pages link to the 4 service pages ([`pages/area.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/area.php#L52)), and service pages link to the 3 city pages in a small pill bar ([`pages/service-detail.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/service-detail.php#L102-L108)), but the main service descriptions (`full_desc_en` / `full_desc_ar`) contain zero contextual internal links to related services or city hubs.

