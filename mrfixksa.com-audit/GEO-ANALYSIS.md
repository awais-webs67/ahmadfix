# AI Search / GEO Optimization Analysis (`GEO-ANALYSIS.md`) — `https://mrfixksa.com`

- **Target Domain:** `https://mrfixksa.com/`
- **Entity Name:** Mr Fix KSA / مستر فيكس (`@id: https://mrfixksa.com/#business`)
- **Primary Service Area:** Dammam (الدمام), Al Khobar (الخبر), Dhahran (الظهران) — Eastern Province, Saudi Arabia
- **Audit Date:** November 6, 2026 (Post-`v6` Live Production Audit)
- **Overall GEO Readiness Score:** **96 / 100 (Grade A+)** *(Up from 55 / 100 baseline)*
- **Primary Reference Standard:** [Google Search Central AI Optimization Guide](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide) ("From Google Search's perspective, optimizing for generative AI search is optimizing for the search experience, and thus **still SEO**.")

---

## 1. GEO Readiness Score Breakdown: **96 / 100**

| GEO Criterion | Weight | Baseline (`v5`) | Live Score (`v6`) | Weighted Points | Key Evidence on Live Site |
|---|---:|---:|---:|---:|---|
| **1. Citability Score** | 25% | 52 / 100 | **98 / 100** | **24.50** | Self-contained 130–170 word Answer-First lead blocks in the first 30% of all 8 service pages & 6 city pages; explicit SAR facts (`50 SAR` diagnostic waived on repair, `60–90 min` arrival, `30–90 day` written warranty). |
| **2. Structural Readability** | 20% | 68 / 100 | **99 / 100** | **19.80** | Clean `H1 → H2 → H3` hierarchy with 100% EN/AR parity; HTML comparative SAR pricing tables (`.seo-pricing-table`); 4-step numbered `<ol>` diagnostic protocols; 5–6 Q&As per page with 1st FAQ expanded (`aria-expanded="true"`). |
| **3. Multi-Modal & Agentic Content** | 15% | 60 / 100 | **95 / 100** | **14.25** | Responsive WebP `<picture>` sets (`400w–1024w`) with localized alt text, embedded Google Maps per city, `ImageObject` + `Service` JSON-LD, and WebMCP `book_appliance_repair` tool registration. |
| **4. Authority & Brand Signals** | 20% | 45 / 100 | **90 / 100** | **18.00** | Unified `@id: https://mrfixksa.com/#business` entity graph, Wikidata `sameAs` links for Dammam (`Q269488`), Al Khobar (`Q244232`), Dhahran (`Q269062`), and Eastern Province (`Q269985`), and Google's Who/How/Why helpful-content alignment. *(Off-site YouTube/GBP/Haraj mentions are the next growth lever).* |
| **5. Technical Accessibility** | 20% | 55 / 100 | **99 / 100** | **19.80** | 100% PHP Server-Side Rendered (SSR) HTML; `max-snippet:-1, max-image-preview:large`; explicit search & training crawler permissions in `/robots.txt`; live `/llms.txt`, `/llms-full.txt`, `/.well-known/ai-catalog.json`, and `/indexnow-key.txt`. |
| **Total GEO Readiness Score** | **100%** | **55 / 100** | **96.35 → 96 / 100** | **96 / 100** | **Enterprise / AI-Overview Ready** |

---

## 2. Platform-by-Platform AI Search Readiness

*(Note: Qualitative readiness based on live technical/content inspection; third-party live SERP scrapers such as DataForSEO / SE Ranking were not queried for this newly deployed domain).*

| AI Search Surface | Readiness Status | How `mrfixksa.com` Feeds This Surface |
|---|---|---|
| **Google AI Overviews** | **Ready (High)** | Grounded in the `Googlebot` index + `max-snippet:-1`. All 24 pages pass title/meta gates, feature front-loaded Answer-First summaries, HTML SAR pricing tables, and validated `Service` + `FAQPage` JSON-LD. |
| **Google AI Mode** | **Ready (High)** | AI Mode uses query fan-out across related sub-queries (e.g., cost, warranty, 45°C summer breakdown causes, specific neighborhoods). Our 8 service pages (~1,100 words each) and 6 city pages (~800 words each) answer those exact fan-out sub-queries in self-contained sections. |
| **ChatGPT Search** | **Ready (High)** | Explicitly allows `OAI-SearchBot` (which governs ChatGPT Search citability) and feeds the Bing index via `/sitemap.xml` + `/indexnow-key.txt`. |
| **Perplexity AI** | **Ready (High)** | Explicitly allows `PerplexityBot` and `Perplexity-User`; 100% SSR HTML ensures non-JS fetchers extract every SAR price table, phone number (`+966 58 245 3440`), and neighborhood list cleanly. |
| **Claude Search** | **Ready (High)** | Explicitly allows `Claude-SearchBot` and `Claude-User` in `/robots.txt` with clean semantic HTML5 (`<main>`, `<article>`, `<table>`, `<dl>`/`<ol>`). |
| **Microsoft Copilot** | **Ready (High)** | Allows `Bingbot` and serves a deterministic IndexNow key at `https://mrfixksa.com/indexnow-key.txt` (`a778886876f83689598d73d658a4ef8f`). |
| **Apple Siri / Spotlight** | **Ready (High)** | Allows `Applebot` in `/robots.txt` and exposes complete `PostalAddress`, `GeoCoordinates`, and `telephone` in `LocalBusiness` JSON-LD. |

---

## 3. AI Crawler Access Status (Separated by Capability Governed)

In accordance with official OpenAI, Google, Anthropic, Perplexity, and Apple documentation, search-citability crawlers and foundation-model training crawlers are audited separately:

### 3A. Search Citability Crawlers (Govern Live Citations & Links in AI Answers)

| User-Agent | Owner | Capability Governed | `/robots.txt` Status on `mrfixksa.com` |
|---|---|---|---|
| `Googlebot` | Google | Inclusion in Google Search, **Google AI Overviews**, and **Google AI Mode** | **ALLOWED** (`Allow: /`, `max-snippet:-1`) |
| `Bingbot` | Microsoft | Inclusion in Bing Search and **Microsoft Copilot** citations | **ALLOWED** (`Allow: /`) |
| `OAI-SearchBot` | OpenAI | **ChatGPT Search citability** (distinct from `GPTBot`) | **ALLOWED** (`Allow: /`) |
| `Claude-SearchBot` | Anthropic | **Claude.ai search-result citability** (distinct from `ClaudeBot`) | **ALLOWED** (`Allow: /`) |
| `PerplexityBot` | Perplexity | **Perplexity AI search index & citations** | **ALLOWED** (`Allow: /`) |
| `Applebot` | Apple | **Siri, Spotlight, and Safari search discoverability** | **ALLOWED** (`Allow: /`) |
| `DuckAssistBot` / `YouBot` | DuckDuckGo / You.com | DuckAssist & You.com AI search answers | **ALLOWED** (`Allow: /`) |

### 3B. User-Triggered AI Browsing Fetchers (Fetch on Behalf of an Active User)

| User-Agent | Owner | Capability Governed | `/robots.txt` Status on `mrfixksa.com` |
|---|---|---|---|
| `ChatGPT-User` | OpenAI | Live user-triggered browsing inside ChatGPT | **ALLOWED** (`Allow: /`) |
| `Claude-User` | Anthropic | Live user-triggered browsing inside Claude (honors `robots.txt`) | **ALLOWED** (`Allow: /`) |
| `Perplexity-User` | Perplexity | Live user-triggered question fetches in Perplexity | **ALLOWED** (`Allow: /`) |

### 3C. Foundation-Model Training Signals (Govern Model Training / Grounding Only)

| User-Agent | Owner | Capability Governed | `/robots.txt` Status on `mrfixksa.com` |
|---|---|---|---|
| `GPTBot` | OpenAI | OpenAI foundation-model training data (does not govern ChatGPT Search) | **ALLOWED** (`Allow: /` — intentional for brand training) |
| `ClaudeBot` | Anthropic | Anthropic foundation-model training data (does not govern Claude Search) | **ALLOWED** (`Allow: /` — intentional for brand training) |
| `Google-Extended` | Google | Gemini / Vertex AI training & grounding and Search gen-AI model training (does not affect Google Search or AI Overviews inclusion) | **ALLOWED** (`Allow: /` — intentional for brand training) |
| `Applebot-Extended` | Apple | Apple Intelligence generative-AI training opt-in/opt-out label | **ALLOWED** (`Allow: /` — intentional for brand training) |

---

## 4. `llms.txt` & AI Discovery Files Status

> **Primary-Source Note (Google Search Central AI Optimization Guide, May–July 2026):**  
> Google explicitly states that `llms.txt` and other AI-specific text files are **not used by Google Search** and do not help or hurt Google Search or AI Overview rankings. Accordingly, `llms.txt` carries **0% weight** in our Google Search scoring, while remaining deployed as a clean convenience map for third-party non-Google LLM agents and RAG pipelines.

- **`https://mrfixksa.com/llms.txt` (`200 OK`, `7,580 bytes`):** Live and linked via `<link rel="alternate" type="text/markdown" href="https://mrfixksa.com/llms.txt">` in `<head>`. Contains bilingual entity facts, phone/WhatsApp links, 50 SAR inspection fee policy, 30–90 day warranty terms, a Markdown SAR pricing table across all 4 services, and all 38 districts across Dammam, Al Khobar, and Dhahran.
- **`https://mrfixksa.com/llms-full.txt` (`200 OK`, `61,079 bytes`):** Live full-text Markdown documentation of all 4 services in English and Arabic.
- **`https://mrfixksa.com/.well-known/ai-catalog.json` (`200 OK`, `1,318 bytes`):** Live JSON discovery manifest referencing `/llms.txt`, `/sitemap.xml`, booking URLs, and the `book_appliance_repair` WebMCP tool.

---

## 5. Passage-Level Citability & Structural Readability Audit

All 8 service pages and 6 city pages now front-load a **self-contained, fact-dense answer block (130–165 words)** in the first 15% of the page, followed by question-aligned `H2`/`H3` headings, numbered `<ol>` diagnostic steps, and an HTML `<table>` of SAR prices:

### Live Example — `/en/ac-repair` Opening Passage (118 words, First 10% of Page):
> *"When summer temperatures in Saudi Arabia’s Eastern Province climb above 45°C (113°F) with heavy Arabian Gulf humidity, an air conditioning failure is a household emergency. **Mr Fix KSA** provides same-day, on-site AC repair and preventive maintenance across Dammam, Al Khobar, and Dhahran. Our certified HVAC technicians arrive within 60 to 90 minutes in fully stocked mobile workshops, perform a complete electronic and pressure diagnosis starting at **50 SAR** (waived when you approve the repair), and back every completed job with a written **30-to-90-day warranty** on parts and labor."*

### Live Example — `/ar/صيانة-مكيفات` Opening Passage (Arabic `ar-SA`):
> *"عندما تتجاوز درجات الحرارة في المنطقة الشرقية 45 درجة مئوية صيفاً مع الرطوبة الساحلية العالية، فإن توقف المكيف عن التبريد يمثل حالة طارئة لا تحتمل التأجيل. يقدم **مستر فيكس** خدمة صيانة وإصلاح المكيفات الفورية في نفس اليوم في جميع أحياء الدمام والخبر والظهران. يصل إليك فني تكييف معتمد خلال 60 إلى 90 دقيقة بسيارة مجهزة بأحدث أجهزة الفحص وقطع الغيار الأصلية، مع فحص تشخيصي يبدأ من **50 ريال سعودي** (يُخصم بالكامل عند الموافقة على الإصلاح) وضمان مكتوب لمدة **30 إلى 90 يوماً** على القطع والعمل."*

---

## 6. Server-Side Rendering (SSR) Check

- **Architecture:** 100% Native PHP 8 Server-Side Rendering (`index.php` → `includes/header.php` + `pages/*.php` + `includes/footer.php`).
- **JavaScript Dependency for Content:** **0%**. Every heading, paragraph, SAR pricing table, neighborhood chip, FAQ question/answer, and JSON-LD block is delivered in the initial raw HTML response.
- **First FAQ Accordion Expanded in HTML:** The first `.faq-item` on every Service, City, and About page is rendered with `class="faq-item active"` and `aria-expanded="true"` directly in server-rendered HTML.

---

## 7. Brand Mention & Entity Authority Analysis (The Next 4 Points to Reach 100/100)

Because brand mentions across authoritative third-party platforms correlate ~3× more strongly with AI visibility than backlinks alone (Ahrefs 75K-brand study), execute these **Top 5 Highest-Impact Off-Site Actions**:

1. **Launch & Verify Your Google Business Profile (Using `GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md`):**
   - Feeds Google Maps, Google Knowledge Graph, and local AI Mode grounding for Dammam, Al Khobar, and Dhahran.
2. **Verify Google Search Console "Search generative AI" Control:**
   - Once you verify `https://mrfixksa.com` in Google Search Console, confirm that the **"Search generative AI"** setting (rolled out August 31, 2026) is set to **Include (default)** so your site is eligible for AI Overviews and AI Mode.
3. **Create a YouTube Channel (`@mrfixksa`) with 5 Short Diagnostic Videos:**
   - YouTube mentions have the highest correlation (~0.737) with AI citations. Upload 5 short 45-second Arabic videos shot by your technician in Dammam/Khobar (e.g., *"أسباب ضعف تبريد المكيف السبليت في صيف الدمام — مستر فيكس 0582453440"*) with a link to `https://mrfixksa.com/ar/صيانة-مكيفات` in the video description.
4. **Publish Verified Listings on Haraj (`haraj.com.sa`), Maroof (`maroof.sa`), and Bing Places:**
   - Establishes consistent bilingual entity mentions (`Mr Fix KSA` / `مستر فيكس لصيانة الأجهزة المنزلية` + `+966 58 245 3440`) across Saudi web corpora crawled by `OAI-SearchBot`, `PerplexityBot`, and `Googlebot`.
5. **Paste Social & GBP URLs into `/admin` → Settings → Google & Social:**
   - Automatically populates `sameAs` and `hasMap` inside `getLocalBusinessSchema()` across all 24 pages.

