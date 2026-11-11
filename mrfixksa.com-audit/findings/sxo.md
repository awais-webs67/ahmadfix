# Search Experience Optimization (SXO) & Intent Alignment Findings — mrfixksa.com

**SXO Readiness Score: 71 / 100** *(Average SXO Gap Score across 4 core SERP personas: **34 / 100 — Moderate Friction**)*

---

## Executive Summary

- **Page-Type Alignment with Eastern Province SERPs**:
  - Live SERP analysis for `appliance repair Dammam Khobar Dhahran` and `صيانة أجهزة منزلية الدمام الخبر الظهران` shows top-ranking competitors (`khobara-ksa.com`, `dammamclean.com`, `fix-serv.com`, `elsyanh.com`, `idealservices.com.sa`) win with **Local Service Landing Pages** featuring immediate click-to-call/WhatsApp triggers, explicit diagnostic fee callouts (e.g., **50 SAR inspection fee**), **30-day to 90-day written warranty badges**, brand-specific repair lists (Samsung, LG, Carrier, Gree, Zamil, Toshiba), and neighborhood dispatch tables.
  - `mrfixksa.com` has the right **page types** (Bilingual Homepage + 4 Service Detail Pages + 3 City Landing Pages), and its mobile conversion chrome (sticky header + floating phone/WhatsApp buttons) is cleaner than most local competitors.
- **Top 3 SXO Friction Points**:
  1. **Missing Upfront Diagnostic Price & Written Warranty Duration Above the Fold**: Competitors in Dammam/Khobar state *"50 SAR Inspection + 30-Day Warranty"* directly in hero subheadings and meta descriptions. On `mrfixksa.com`, `refrigerator-repair`, `washing-machine-repair`, and `oven-repair` mention **zero SAR figures**, and `ac-repair` hides *"starting from SAR 150"* inside collapsed FAQ #1 (`aria-expanded="false"`).
  2. **WhatsApp-Only Contact Form Opens Popup Without Server Backup ([`pages/contact.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/contact.php#L134-L165))**: Submitting `#contact-form` intercepts the submit event via JavaScript and calls `window.open('https://wa.me/...', '_blank')` without saving the lead to the `contact_messages` database table. Users on desktop without WhatsApp Web logged in, or mobile browsers blocking popups, hit a dead end.
  3. **No Supported Appliance Brand Logos or Matrix**: Local searchers frequently search by brand (`صيانة مكيفات جري الدمام`, `Samsung fridge repair Khobar`). While brand names appear inside FAQ text on city pages, there is no visual "Brands We Repair" grid on the homepage or service detail pages.

---

## SERP-Derived User Stories

1. **Emergency AC Failure (Arabic Mobile Searcher — Dammam)**:
   > *"As a villa resident in Al Faisaliyah, Dammam whose split AC stopped cooling in 46°C afternoon heat, I want to see immediate confirmation that a technician is in Dammam right now, what the inspection fee is, and tap WhatsApp in 1 second so my family isn't sleeping in a hot house tonight."*
2. **Expat Family Appliance Breakdown (English Searcher — Al Khobar / Dhahran)**:
   > *"As an English-speaking expat in Al Rakah or KFUPM Dhahran with a leaking Bosch/Samsung washing machine, I want an English-speaking booking experience with transparent SAR pricing and a clear warranty guarantee so I don't get overcharged by an unverified technician."*
3. **Price & Warranty Comparison Shopper (Bilingual Searcher)**:
   > *"As a homeowner comparing 3 Dammam repair sites open in browser tabs, I want to compare diagnostic fees, spare-parts warranty duration (30 vs 90 days), and supported appliance brands without having to call all 3 numbers first."*
4. **Property Manager / Compound Coordinator (Desktop Searcher — Eastern Province)**:
   > *"As a facility coordinator managing multiple apartments in Al Khobar, I want a reliable web form, official company email (`@mrfixksa.com`), and Saudi Commercial Registration (CR) details so I can request multi-unit maintenance and keep an audit trail."*

---

## 7-Dimension SXO Gap Matrix Across Personas

*(Scale: 0 = No Gap / Frictionless, 100 = Severe Blocker)*

| Dimension | Weight | Persona 1: Emergency Arabic AC (Dammam) | Persona 2: Expat Homeowner (Khobar/Dhahran) | Persona 3: Price & Warranty Comparer | Persona 4: Compound / Facility Manager | Weighted Avg Gap |
|---|---|---|---|---|---|---|
| **1. Intent Match** | 20% | 15 | 15 | 40 | 35 | **26.3** |
| **2. Above-the-Fold Clarity** | 15% | 20 | 15 | 45 | 30 | **27.5** |
| **3. Trust & Proof Signals** | 20% | 35 | 40 | 55 | 70 | **50.0** |
| **4. Information Completeness** | 15% | 30 | 25 | 60 | 50 | **41.3** |
| **5. Action & CTA Friction** | 15% | 10 | 15 | 25 | 55 | **26.3** |
| **6. Mobile & RTL Ergonomics** | 10% | 15 | 15 | 20 | 15 | **16.3** |
| **7. Layout & Visual Scanability** | 5% | 20 | 20 | 35 | 25 | **25.0** |
| **Persona SXO Gap Score** | **100%** | **21.5 / 100** *(Low Gap)* | **22.0 / 100** *(Low Gap)* | **43.0 / 100** *(Moderate Gap)* | **45.8 / 100** *(Moderate Gap)* | **33.7 / 100** |

---

## High-Impact SXO Fixes

1. **Add an Upfront "Transparent Pricing & Warranty" Strip to Service & City Pages**:
   - Display 3 scannable stat cards above the fold on every service page: **Diagnostic Visit: SAR XX (Free with Repair)**, **Written Warranty: 30–90 Days on Parts & Labor**, and **Dispatch Time: 60–120 Mins Across Dammam, Khobar & Dhahran**.
2. **Save `#contact-form` Submissions to `contact_messages` Before Opening WhatsApp**:
   - Post the form via `fetch()` (or standard POST fallback) to save the lead in the existing `contact_messages` database table ([`database/setup.sql`](file:///c:/xampp/htdocs/FixoraKSA/database/setup.sql#L28-L39)) and show an on-page confirmation with a direct "Continue to WhatsApp" button.
3. **Add a "Brands We Service" Visual Grid**:
   - List major brands serviced in Saudi Arabia (Samsung, LG, Carrier, Gree, Zamil, Midea, York, Toshiba, Whirlpool, Bosch, Ariston, Hitachi, Daewoo) on `/en`, `/ar`, and the 4 service pages to capture brand + city long-tail queries and reassure comparison shoppers.

