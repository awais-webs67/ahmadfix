# AI Search / GEO Optimization Audit (`GEO-ANALYSIS.md`) — `https://mrfixksa.com`

- **Target Domain:** `https://mrfixksa.com/`
- **Entity Identity:** Mr Fix KSA / مستر فيكس (`@id: https://mrfixksa.com/#business`)
- **Service Area:** Dammam (الدمام), Al Khobar (الخبر), Dhahran (الظهران) — Eastern Province, Saudi Arabia
- **Audit Date:** **November 15, 2026** (`Schema v9` On-Site GEO Implementation + Live Google/Vertex AI Grounding Check)
- **GEO Readiness Score:** **96 / 100 (Grade A+)** *(Heuristic score across 5 weighted criteria — up from `93 / 100` in v8 and `55 / 100` initial baseline)*
- **Primary Reference Standard:** [Google Search Central — AI Optimization Guide](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide) (`2026-05-15`, updated `2026-07-10`) & [Using Third-Party SEO Tools](https://developers.google.com/search/docs/fundamentals/third-party-seo) (`2026-06-05`):
  > *"From Google Search's perspective, optimizing for generative AI search is optimizing for the search experience, and thus **still SEO**."*

> **Transparency Note:** Third-party SEO/GEO scores are heuristic evaluations based on observable HTML, crawl rules, structured data, and public index checks — they have no access to Google's internal ranking signals. Google Search Console (including its **Generative AI performance report**, rolled out worldwide `2026-08-31`) is the authoritative first-party source.

---

## 1. GEO Readiness Score Breakdown: **96 / 100**

| # | Criterion | Weight | Score | Weighted Points | Evidence & Remaining Gap (Nov 15, 2026 — `Schema v9`) |
|---|---|---|---|---:|---|
| 1 | **Citability** | 25% | **100 / 100** | **25.00** | Front-loaded Answer-First lead blocks in the top 15% of all 8 service pages & 6 city pages (`50 SAR` diagnostic waived on repair, `100–450 SAR` repair bands, `60–90 min` dispatch, `30–90 day` written warranty), **plus `Schema v9` comparative `.seo-pricing-table` on all 6 city pages** and **32 natural query-aligned Question `H2`s across EN & AR service pages**. |
| 2 | **Structural Readability** | 20% | **100 / 100** | **20.00** | Clean `H1 → H2 → H3` hierarchy (`100%` EN/AR parity: `H1:1 / H2:8 / H3:7` on services, `H1:1 / H2:5 / H3:7` on cities), comparative HTML tables (`<caption>`, `<thead>`, `<tbody>`) on all 14 money pages, `<ol>` 4-step diagnostic checklists, `<ul>` fault/district lists, and 5–6 Q&As per page (`1st` FAQ open in SSR HTML). |
| 3 | **Multi-Modal Content** | 15% | **88 / 100** | **13.20** | `126` responsive WebP `<picture>` images (`400w–1024w`) with 100% bilingual `alt` text, city Google Maps embeds, `ImageObject` + `Service` JSON-LD, and interactive `#contact-form` with WebMCP tool registration. *Gap (`-12`): Off-site/media — record 4–5 technician diagnostic videos (`VideoObject` / YouTube Shorts) and add real field photos.* |
| 4 | **Authority & Brand Signals** | 20% | **89 / 100** | **17.80** | **`7+` EN & AR pages indexed & grounded by Google Search / Vertex AI** + **`Schema v9` genuine E-E-A-T reviewer bar (`.eeat-meta-bar` with `<time datetime="...">`) & `WebPage` (`dateModified`, `reviewedBy`, `specialty`) JSON-LD** synced 100% with `/sitemap.xml` `<lastmod>`. *Gap (`-11`): Off-site — launch GBP (`hasMap`), YouTube (`@mrfixksa`), and Saudi directory mentions (`sameAs`).* |
| 5 | **Technical Accessibility** | 20% | **99 / 100** | **19.80** | `100%` PHP 8 SSR (`0%` JS content dependency), `max-snippet:-1, max-image-preview:large`, `Schema v8` search/AI referrer deep-link protection, **22 explicit named search & AI crawlers in `/robots.txt`** (including `Google-CloudVertexBot`, `Google-GeminiNotebook`, `Bytespider`, `cohere-ai`), `Content-Signal`, `/indexnow-key.txt`. *(`llms.txt` present; `0%` weight per Google docs).* |
| — | **Total GEO Score** | **100%** | **96 / 100** | **95.80 → 96** | **Grade A+ — 100% On-Site GEO Complete; Off-Site Entity Signals Next** |

---

## 2. Platform-by-Platform AI Search Breakdown

*(Paid third-party LLM rank-tracking APIs such as DataForSEO / SE Ranking were not connected, so non-Google platforms are reported qualitatively rather than given synthetic numerical scores. Live Google index/grounding retrieval WAS verified directly on `2026-11-14`.)*

| AI Platform / Surface | Measurement Status | Readiness / Live Status (Nov 14, 2026) | Key Surface Mechanics & Optimization Focus |
|---|---|---|---|
| **Google AI Overviews** | **Live Index Verified** (`7+` URLs grounded) | **Active & Eligible** | Strongly correlated with classic top-10 Google rankings (`Googlebot` index + `max-snippet:-1` + GSC `"Search generative AI" = Include`). Front-loaded SAR tables and 6-item FAQs match local repair cost & symptom queries. |
| **Google AI Mode** | **Live Grounding Verified** (EN & AR) | **Active & Eligible** *(Distinct engine from AIO — cites same URLs only ~13.7% of the time, Ahrefs 540K-query study)* | Uses **query fan-out** across sub-queries (45°C heat faults, R410A Freon cost, warranty length, specific neighborhoods like Al Doha & Al Qusur). Adding visible genuine review dates (`<time>`) and off-site entity mentions boosts AI Mode selection beyond raw SERP rank. |
| **ChatGPT Search** | Qualitative (Not Measured via API) | **Technically Ready — Needs Off-Site Mentions** | `OAI-SearchBot` is explicitly allowed in `/robots.txt`, `/en/*` & `/ar/*` are 100% SSR HTML, and `chatgpt.com` referrals are protected from language redirects in `includes/header.php`. Needs Bing Places, GBP, and Saudi directory mentions for brand retrieval. |
| **Perplexity AI** | Qualitative (Not Measured via API) | **Technically Ready — Needs Community Mentions** | `PerplexityBot` & `Perplexity-User` are explicitly allowed; SSR tables & phone numbers extract cleanly without JS. Community discussions (Haraj, Reddit `r/saudiarabia` / `r/dammam`, YouTube) drive Perplexity's citation pool. |
| **Microsoft Copilot** | Qualitative (Not Measured via API) | **Technically Ready — Connect Bing Webmaster** | `Bingbot` allowed + deterministic IndexNow key (`a778886876f83689598d73d658a4ef8f`) submitted (`HTTP 202`). Complete 1-click GSC import into Bing Webmaster Tools + Bing Places listing. |
| **Claude Search** | Qualitative (Not Measured via API) | **Technically Ready** | `Claude-SearchBot` & `Claude-User` (both honor `robots.txt`) are explicitly allowed with clean semantic `<main>`, `<table>`, and `<ol>` markup. |
| **Apple Siri / Spotlight** | Qualitative (Not Measured via API) | **Technically Ready** | `Applebot` explicitly allowed + full `PostalAddress`, `GeoCoordinates`, and `+966582453440` in `LocalBusiness` JSON-LD. Register on Apple Business Connect for Maps/Siri voice queries. |

---

## 3. AI Crawler Access Status (Strictly Separated by Capability Governed)

Per OpenAI, Google, Anthropic, Perplexity, and Apple documentation, **search-citability crawlers** and **foundation-model training signals** govern completely separate capabilities and are audited independently below:

### 3A. Search Citability Crawlers (Govern Live Citations & Links in AI Search Answers)

| User-Agent | Owner | Exact Capability Governed | Rule in `mrfixksa.com/robots.txt` | Status |
|---|---|---|---|---|
| `Googlebot` | Google | Inclusion in Google Search, **Google AI Overviews**, and **Google AI Mode** *(NOT `Google-Extended`)* | Explicit `User-agent: Googlebot` → `Allow: /` (+ `max-snippet:-1` meta) | **ALLOWED** |
| `OAI-SearchBot` | OpenAI | **ChatGPT Search citability** *(NOT `GPTBot`)* | Explicit `User-agent: OAI-SearchBot` → `Allow: /` | **ALLOWED** |
| `Claude-SearchBot` | Anthropic | **Claude / Claude.ai search-result citability** *(NOT `ClaudeBot`)* | Explicit `User-agent: Claude-SearchBot` → `Allow: /` | **ALLOWED** |
| `PerplexityBot` | Perplexity | **Perplexity AI search index & citations** | Explicit `User-agent: PerplexityBot` → `Allow: /` | **ALLOWED** |
| `Bingbot` | Microsoft | Inclusion in Bing index & **Microsoft Copilot** citations | Explicit `User-agent: Bingbot` → `Allow: /` | **ALLOWED** |
| `Applebot` | Apple | **Siri, Spotlight, and Safari search discoverability** *(NOT `Applebot-Extended`)* | Explicit `User-agent: Applebot` → `Allow: /` | **ALLOWED** |
| `DuckAssistBot` / `YouBot` | DuckDuckGo / You.com | DuckAssist & You.com AI search answers | Explicit `Allow: /` | **ALLOWED** |

### 3B. User-Triggered AI Fetchers (Live Browsing / Grounding Requested by a User)

| User-Agent | Owner | Exact Capability Governed | Honors `robots.txt`? | Rule on `mrfixksa.com` |
|---|---|---|---|---|
| `ChatGPT-User` | OpenAI | User-triggered live browsing in ChatGPT | "May not apply" (OpenAI docs) | Explicit `Allow: /` |
| `Claude-User` | Anthropic | User-triggered browsing in Claude | **Yes** (Anthropic docs) | Explicit `Allow: /` |
| `Perplexity-User` | Perplexity | User-triggered live fetch in Perplexity | Generally ignores | Explicit `Allow: /` |
| `Google-Agent` | Google | User-triggered agentic browsing (RFC 9421 Web Bot Auth) | **No** (user-triggered) | Explicit `Allow: /` |
| `Google-GeminiNotebook` | Google | User-added URLs in NotebookLM *(replaced `Google-NotebookLM`)* | **No** (user-triggered) | Explicit `Allow: /` (`Schema v9`) |

### 3C. Foundation-Model Training & Grounding Signals (Do NOT Govern Search Citability)

| User-Agent | Owner | Exact Capability Governed | Rule in `mrfixksa.com/robots.txt` | Status |
|---|---|---|---|---|
| `GPTBot` | OpenAI | **OpenAI model training only** (blocking/allowing has zero effect on ChatGPT Search) | Explicit `User-agent: GPTBot` → `Allow: /` | **ALLOWED** *(Intentional)* |
| `ClaudeBot` | Anthropic | **Anthropic model training only** (zero effect on Claude Search) | Explicit `User-agent: ClaudeBot` → `Allow: /` | **ALLOWED** *(Intentional)* |
| `Google-Extended` | Google | **Gemini / Vertex AI training & grounding, and Search gen-AI model training** (zero effect on Google Search, AI Overviews, or AI Mode inclusion) | Explicit `User-agent: Google-Extended` → `Allow: /` | **ALLOWED** *(Intentional)* |
| `Applebot-Extended` | Apple | **Apple Intelligence training opt-out label only** (does not crawl; zero effect on Siri/Spotlight) | Explicit `User-agent: Applebot-Extended` → `Allow: /` | **ALLOWED** *(Intentional)* |
| `CCBot` | Common Crawl | Open training dataset crawls | Explicit `User-agent: CCBot` → `Allow: /` | **ALLOWED** *(Intentional)* |
| `Google-CloudVertexBot` / `Bytespider` / `cohere-ai` | Google Cloud / ByteDance / Cohere | Vertex AI Agent crawls / TikTok AI / Cohere models | Explicit `Allow: /` (`Schema v9`) | **ALLOWED** *(Explicit)* |

> **WAF & Regional Edge Note (`Hostinger hcdn`):** Hostinger's Anycast CDN enforces reverse-DNS verification on spoofed `Googlebot` UAs (real Googlebot IPs pass cleanly — proven by live Google indexation of 7+ URLs on `2026-11-14`), and South Asian ISP routes intermittently experience TCP timeouts to Latin-American/EU `hcdn` edge IPs (`191.101.228.33` / `179.61.189.123`). Check Hostinger Access Logs periodically to confirm `OAI-SearchBot` and `PerplexityBot` receive `200 OK` without challenge pages.

---

## 4. `llms.txt` & RSL 1.0 Licensing Status

> **Google Search ignores `llms.txt` (Google Search Central AI Optimization Guide, `2026-06-15` / `2026-07-10`):**  
> Google states explicitly: *"You don't need to create new machine readable files, AI text files, markup, or Markdown to appear in Google Search (including its generative AI capabilities), as Google Search itself doesn't use them."* Independent studies (SE Ranking 300k-domain study; OtterlyAI server-log audit showing `0.1%` bot traffic) confirm no major AI search engine currently uses `/llms.txt` as a citation lever. **Accordingly, `/llms.txt` carries `0%` weight in our GEO score**, and is maintained solely as zero-cost optionality for non-Google tools and coding/RAG agents.

- **`/llms.txt` (`Present` — Valid Mintlify-Style Markdown):** Dynamically generated in [`index.php`](file:///c:/xampp/htdocs/FixoraKSA/index.php#L57-L114), serving bilingual entity facts, phone/WhatsApp links, `50 SAR` diagnostic policy, `30–90 day` warranty, Markdown SAR pricing table, Markdown links to all 24 canonical EN/AR pages, and all 37 Eastern Province districts.
- **`/llms-full.txt` (`Present`):** Full-text bilingual technical documentation across all 4 appliance categories.
- **`/.well-known/ai-catalog.json` (`Present` — ARD `1.0`):** Uses dynamic `SITE_DOMAIN` (`urn:air:mrfixksa.com:skills:*`) after commit `cecc632`.
- **`RSL 1.0` (Really Simple Licensing, Dec 2025):** **Not implemented** (`Content-Signal: search=yes, ai-input=yes, ai-train=yes` is declared in `/robots.txt`; since Mr Fix KSA is a local service business seeking maximum citation rather than a publisher licensing paywalled articles, RSL 1.0 is optional low-priority).

---

## 5. Brand Mention & Entity Authority Analysis (`2026-11-14` Live Search Check)

**Brand mentions correlate ~3× more strongly with AI visibility than backlinks** (`~0.737` for YouTube mentions vs `~0.266` for Domain Rating — Ahrefs 75,000-brand study, `2025-12-12`).

| Signal / Platform | Live Status (`2026-11-14`) | Evidence & Impact |
|---|---|---|
| **Google Search & Vertex AI Index (`site:mrfixksa.com`)** | **LIVE (`7+` URLs Grounded)** | Major progress since Nov 6 (`0` URLs): both English (`"Mr Fix KSA" Dammam Khobar`) and Arabic (`"مستر فيكس" "0582453440" الدمام الخبر`) queries retrieve and synthesize `mrfixksa.com` service pages, hours, districts, and phone number. |
| **Google Business Profile (Maps / Knowledge Graph)** | **Not Yet Linked** | `LocalBusiness.hasMap` still falls back to `maps/search/?api=1&query=Dammam...`. Ready-to-deploy brief is at [`GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md`](file:///c:/xampp/htdocs/FixoraKSA/mrfixksa.com-audit/GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md). |
| **YouTube (`@mrfixksa`)** | **Absent (`0` videos)** | Highest-leverage missing AI citation signal (`~0.737` correlation) + trackable in GSC Platform Properties (`2026-07-29` update). |
| **Reddit / Saudi Forums (`Haraj`, `Hawamer`, `r/saudiarabia`)** | **Absent (`0` mentions)** | `"mrfixksa" -site:mrfixksa.com` returns `0` external web mentions. Critical for Perplexity (`46.7%` Reddit share among top-10 cited sources) & ChatGPT (`11.3%`). |
| **Wikidata / Wikipedia** | **Geographic Linked (`Q269488`, `Q244232`, `Q269062`, `Q269985`)** | City & province entities are linked via Wikidata `sameAs` in [`functions.php`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L559-L565) (local SMBs do not qualify for standalone Wikipedia articles; do not attempt spammy wiki creation). |
| **LinkedIn / Instagram / TikTok / X (`sameAs`)** | **Not Yet Linked in `/admin`** | `socialLinks()` in [`functions.php`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L417-L428) is wired and waiting for profile URLs in `Admin → Settings → Google & Social`. |

---

## 6. Passage-Level Citability & Structural Readability Analysis

*(Note: ~130–170 words per answer block is a third-party readability heuristic, **not** a Google requirement — Google's AI Optimization Guide explicitly states you do not need to artificially "chunk" content for AI. However, ~44% of AI citations come from the first 30% of a page [SE Ranking], so front-loading clear, self-contained facts helps both human readers and RAG grounding.)*

### What Already Excels on `mrfixksa.com`:
- **Hero Summary + Opening Lead Block (`First 15%` of `/en/ac-repair` — `136 words` combined):**  
  [`config/seo-v6-content.php`](file:///c:/xampp/htdocs/FixoraKSA/config/seo-v6-content.php#L42-L46) immediately answers *who* (`Mr Fix KSA`), *where* (`Dammam, Al Khobar, Dhahran`), *environmental context* (`45°C / 113°F` summer heat + Gulf humidity), *response time* (`60 to 90 minutes`), *diagnostic price* (`50 SAR`, waived on repair), and *warranty* (`30-to-90-day written warranty`).
- **Structured Comparative Pricing Tables (`.seo-pricing-table`):**  
  Every service page (EN & AR) includes a 6-row, 3-column HTML `<table>` (`Service / Repair Item` | `Estimated Price (SAR)` | `Warranty`) — directly extractable into AI Overview & Perplexity comparison tables.
- **First-Hand Technical Specificity (Google's Lived-Experience Standard):**  
  Includes real error codes (`E1, E4, E5, CH05, H6`), refrigerant types (`R410A`, `R22`, `R600a`, `R134a`), true-RMS clamp meter capacitance (`µF`) checks, and coastal corrosion details rather than generic AI filler.

---

## 7. Server-Side Rendering (SSR) & Agentic Deep-Link Check

- **100% PHP 8 SSR:** Zero client-side JS rendering (`CSR`) required for any text, table, internal link, or JSON-LD block. Non-JS crawlers (`GPTBot`, `OAI-SearchBot`, `PerplexityBot`, `Claude-SearchBot`) receive the exact same complete HTML as `Googlebot`.
- **`Schema v8` AI Referrer Protection (`cecc632`):** [`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L112-L116) explicitly exempts referrals from `google.`, `bing.`, `chatgpt.com`, `perplexity.ai`, `claude.ai`, and `gemini.google` (as well as all inner `/en/*` and `/ar/*` deep links) from client-side language redirects, ensuring users clicking English AI citations land on English content 100% of the time.

---

## 8. Google Update Correlation (`seo_updates.py` — Verified `2026-10-22`, Fresh)

Three official Google product launches in Q3 2026 directly impact `mrfixksa.com`'s GEO workflow:

1. **`2026-08-31` — Search Console "Search generative AI" Control & Generative AI Reports:**  
   In your newly verified Google Search Console property (`tFf9DDYVcf26...`), open **Settings → Search generative AI** and confirm it is set to **Include (default)**. Check the new **Generative AI performance report** weekly to see exact AI Overview & AI Mode impressions/clicks.
2. **`2026-09-24` — Search Console Web Multimodal Performance Reporting:**  
   Google rolled out multimodal filters (**Google Lens, Circle to Search, image uploads**) inside Search & Generative AI reports. In Saudi Arabia, homeowners frequently snap a photo of an AC/washer error code (`E4`, `OE`, `UE`) with Google Lens! Adding real photos of appliance error codes and outdoor units with descriptive `alt` text captures multimodal AI queries.
3. **`2026-07-29` — Search Console Platform Properties (YouTube, TikTok, Instagram, X):**  
   Once you launch `@mrfixksa` on YouTube, TikTok, Instagram, and X, you can now verify and track those social video accounts directly inside Google Search Console!

---

## 9. Top 5 Highest-Impact Changes (Status After `Schema v9` Implementation)

1. **[Off-Site — Owner Action Remaining] Launch Google Business Profile + Saudi Citations (`Haraj`, `Maroof`, `Bing Places`, `Apple Business Connect`):**  
   Use [`GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md`](file:///c:/xampp/htdocs/FixoraKSA/mrfixksa.com-audit/GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md), then paste your live GBP URL & social URLs in `Admin → Settings → Google & Social` so `LocalBusiness.hasMap` and `sameAs` populate across all 24 pages.
2. **[Off-Site — Owner Action Remaining] Publish 4–5 Short Technician Diagnostic Videos on YouTube (`@mrfixksa`) & Embed on Service Pages:**  
   Record 45-second real clips in Dammam/Khobar (e.g., testing an AC capacitor or clearing a washer drain pump, mentioning *"Mr Fix KSA / مستر فيكس — 0582453440"* in spoken audio, title, and description) and embed one per service page with `VideoObject` schema.
3. **[On-Page / E-E-A-T — COMPLETED in `Schema v9`] Genuine "Technically Reviewed by Certified Team + Verified Date" Attribution on All 14 Service & City Pages:**  
   Implemented `.eeat-meta-bar` inside `.service-hero` in [`pages/service-detail.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/service-detail.php#L67-L79) and [`pages/area.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/area.php#L56-L68) with real `<time datetime="YYYY-MM-DD">` derived from DB `updated_at` and template `filemtime` (never faked).
4. **[Schema — COMPLETED in `Schema v9`] `WebPage` (`dateModified`, `reviewedBy`, `specialty`) Schema on Service & City Pages:**  
   Implemented [`getWebPageSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L855-L881) linking `WebPage.mainEntity` to `#service`, `reviewedBy` to `#business`, and exposing genuine `dateModified` synced 100% with `<lastmod>` in [`sitemap.php`](file:///c:/xampp/htdocs/FixoraKSA/sitemap.php#L46-L54).
5. **[Content Reformatting — COMPLETED in `Schema v9`] Query-Aligned Question `H2`s + City Dispatch & Pricing Summary Tables:**  
   Converted 32 body `H2`s across all 8 EN & AR service pages in [`config/seo-v6-content.php`](file:///c:/xampp/htdocs/FixoraKSA/config/seo-v6-content.php) (auto-synced to live DB via `SCHEMA_VERSION = 9` in [`config/migrations.php`](file:///c:/xampp/htdocs/FixoraKSA/config/migrations.php#L14)) while preserving exact `H1:1 / H2:8 / H3:7` parity, and added comparative 6-row `.seo-pricing-table` summary tables across all 6 EN & AR city pages in [`pages/area.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/area.php#L80-L115).

---

## 10. Delivered `Schema v9` Schema & Content Upgrades

### A. `WebPage` JSON-LD + `sameAs` Wiring
- [`getWebPageSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L855-L881) is live on all 8 service pages and all 6 city pages with `"@type": "WebPage"`, `"dateModified"` (synced with `/sitemap.xml`), `"reviewedBy": {"@id": "https://mrfixksa.com/#business"}`, and `"specialty": "Home Appliance & HVAC Repair"` (`"صيانة الأجهزة المنزلية والتكييف"`).
- `sameAs` (`Instagram`, `YouTube`, `TikTok`, `X`, `Snapchat`, `GBP`) remains wired via `Admin → Settings → Google & Social` in [`socialLinks()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L426-L437).

### B. Live Question `H2`s & City Summary Tables (`Schema v9`)

| Page | Previous `H2` (Statement Style) | Live `Schema v9` `H2` / Table Upgrade |
|---|---|---|
| `/en/ac-repair` | `Common AC Faults We Fix in the Eastern Province Climate` | `Why Is My AC Not Cooling or Leaking Water in the Eastern Province Climate?` |
| `/en/ac-repair` | `Transparent AC Repair & Maintenance Price Guide (SAR)` | `How Much Does AC Repair & Freon Refill Cost in Dammam & Al Khobar (SAR)?` |
| `/ar/صيانة-مكيفات` | `أعطال المكيفات الشائعة التي نصلحها في أجواء المنطقة الشرقية` | `لماذا المكيف لا يبرد أو يهرب ماء في أجواء المنطقة الشرقية؟` |
| `/ar/صيانة-مكيفات` | `جدول أسعار صيانة وإصلاح المكيفات التقريبي (بالريال السعودي)` | `كم تكلفة صيانة المكيفات وشحن الفريون بالدمام والخبر (بالريال السعودي)؟` |
| `/en/appliance-repair-*` & `/ar/صيانة-اجهزة-منزلية-*` | *(No table previously)* | **6-Row Comparative City Dispatch, Coverage & SAR Price Summary Table** (`Neighborhoods Covered` · `60–90 min Dispatch` · `50 SAR Diagnostic Waived` · `100–450 SAR Range` · `30–90 Day Written Warranty` · `Cash/Mada/STC Pay/Apple Pay`) |
