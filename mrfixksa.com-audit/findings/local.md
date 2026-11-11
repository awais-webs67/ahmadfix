# Local SEO & Saudi Eastern Province Market Findings — mrfixksa.com

**Local SEO Score: 66 / 100**

---

## Executive Summary

- **Business Model & Vertical Detection**:
  - **Model**: Service Area Business (SAB) / Hybrid — Technician dispatch to customer homes across **Dammam, Al Khobar, and Dhahran** (Eastern Province / المنطقة الشرقية, Saudi Arabia), with a single map pin coordinate centered in Dammam (`26.4207, 50.0888`).
  - **Industry Vertical**: Home Services (`HomeAndConstructionBusiness` + `HVACBusiness` / Appliance Repair Service).
- **Strong Local Content Architecture (`config/areas.php`)**:
  - Dedicated bilingual landing pages for the 3 core Eastern Province metro cities (`/en/appliance-repair-dammam`, `/en/appliance-repair-khobar`, `/en/appliance-repair-dhahran` + Arabic equivalents).
  - Rich neighborhood coverage: **17 districts in Dammam** (Al Faisaliyah, Al Shati, Al Mazruiyah, Al Rayyan, Al Nada, etc.), **14 districts in Al Khobar** (Al Aqrabiyah, Al Rakah, Al Thuqbah, Al Olaya, Al Hizam Al Dhahabi, etc.), and **6 districts in Dhahran** (Al Doha, Al Dana, KFUPM Area, Al Qusoor, etc.).
- **Critical Local SEO Gaps**:
  1. **Unlinked / Unverified Google Business Profile (GBP) Signal (`google_business_url` empty by default)**: In [`config/settings-schema.php`](file:///c:/xampp/htdocs/FixoraKSA/config/settings-schema.php#L47), `google_business_url` defaults to `''` and no verified Google Business Profile or Google Maps CID link is embedded on the site.
  2. **NAP Address Incompleteness (City-Only Address vs Street Address)**: Both visible HTML (`Dammam, Al Khobar, Dhahran - Saudi Arabia`) and `PostalAddress` JSON-LD omit a street address and postal code, while the contact page embeds an iframe pointing to generic Dammam city center coordinates (`26.4207°N, 50.0888°E`).
  3. **Dammam Coordinates Hardcoded Globally on Al Khobar and Dhahran Pages**: [`getLocalBusinessSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L564-L567) outputs Dammam coordinates (`26.4207, 50.0888`) and `addressLocality: Dammam` on every page, and [`pages/area.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/area.php) does not inject city-specific `Service` + `City` GeoCoordinates JSON-LD for Al Khobar (`26.2172, 50.1971`) or Dhahran (`26.2361, 50.0393`).
  4. **Zero Saudi Directory Citations Post-Rebrand (`Ahmad Fix` → `Mr Fix KSA`)**: Web search for `"Mr Fix KSA"` or `"+966 57 506 4911"` returns 0 external directory citations.

---

## Local SEO Score Breakdown (100-Point Scale)

| Component | Weight | Score | Weighted | Key Observations |
|---|---|---|---|---|
| **Google Business Profile Signals** | 25% | 40/100 | 10.0 | `google_business_url` unpopulated in default config; no GBP link, review widget, or Maps CID on site. |
| **NAP Consistency & Trust** | 20% | 72/100 | 14.4 | Phone (`+966 57 506 4911`) and hours (`Sat–Thu 8 AM–10 PM`) consistent across EN/AR HTML and JSON-LD; lacks street address, postal code, and domain email (`ahmadarian538@gmail.com` used). |
| **Local Citations & Directories** | 15% | 25/100 | 3.75 | 0 external Saudi citations indexed after rebrand from `Ahmad Fix`. |
| **Reviews & Social Proof** | 15% | 65/100 | 9.75 | 5 localized customer testimonials on homepage (Dammam, Khobar, Dhahran); not backed by third-party GBP review link or verifiable review source. |
| **Local Schema Markup** | 15% | 78/100 | 11.7 | `HomeAndConstructionBusiness` + `HVACBusiness` with `areaServed` (`Dammam`, `Al Khobar`, `Dhahran`), `openingHoursSpecification`, and `GeoCoordinates`; missing `streetAddress`/`postalCode` and per-city `Service` geo. |
| **Location Page Quality** | 10% | 84/100 | 8.4 | 3 well-scoped city pages (6 URLs total, well below 30-page warning threshold) with climate context and 6–17 named districts; 4 benefit bullets in `area.php` are duplicated boilerplate and titles exceed 60 chars. |
| **Total Local SEO Score** | **100%** | — | **58.0 → 66 / 100*** | *(Adjusted for strong bilingual Arabic/English district targeting in `config/areas.php`)* |

---

## Saudi Arabia / Eastern Province Citation & GBP Action Plan

1. **Google Business Profile (GBP) Setup & Category Mapping**:
   - **Primary Category**: `Appliance repair service` (`خدمة إصلاح الأجهزة المنزلية`)
   - **Additional Categories**: `Air conditioning repair service` (`خدمة إصلاح مكيفات الهواء`), `Refrigerator repair service`, `Washer & dryer repair service`
   - **Service Areas**: Explicitly add `Dammam`, `Al Khobar`, and `Dhahran` (or up to 20 key districts such as Al Faisaliyah Dammam, Al Shati Dammam, Al Aqrabiyah Al Khobar, Al Rakah, Al Doha Dhahran).
   - **SAB Address Rule**: If operating purely as a mobile dispatch service without walk-in workshop signage, hide the street address in GBP (Service Area Business mode) and link the GBP share URL in Admin → Settings → `google_business_url`. If a physical workshop with signage exists in Dammam, display the exact Saudi National Address (`العنوان الوطني` — Building No., Street, District, City, 5-digit Postal Code + 4-digit Additional No.) identically in GBP, footer HTML, and `PostalAddress` JSON-LD.
2. **Tier-1 Saudi Local Directory Citations to Build**:
   - **Haraj (`haraj.com.sa`)** & **OpenSooq Saudi (`sa.opensooq.com`)** (dominant local service marketplaces in Dammam/Khobar)
   - **Saudi Yellow Pages (`yellowpages.com.sa`)** & **Dalil KSA (`dalilksa.com`)**
   - **Maroof (`maroof.sa`) / Saudi Business Center (`business.sa`)** (high-trust Saudi Ministry of Commerce verification badge)
   - **Apple Business Connect** & **Bing Places for Business**

