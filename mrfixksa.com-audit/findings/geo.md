# Specialist Findings: AI Search / GEO (`findings/geo.md`)

- **Audit Date:** November 15, 2026 (`Schema v9` On-Site GEO Upgrade Deployed)
- **GEO Readiness Score:** **96 / 100 (Grade A+)** *(Heuristic score across 5 weighted criteria — up from `93 / 100` in v8 and `55 / 100` initial baseline)*
- **Full Report:** [`GEO-ANALYSIS.md`](../GEO-ANALYSIS.md)

## Key Live & `Schema v9` Findings (`2026-11-15`)

1. **Google Search & Vertex AI Grounding Confirmed Live (`7+` URLs Indexed):**  
   Up from `0` indexed URLs on Nov 6, live queries in both English (`"Mr Fix KSA" Dammam Khobar`, `site:mrfixksa.com`) and Arabic (`"مستر فيكس" "0582453440" الدمام الخبر`) retrieve and cite `mrfixksa.com` service pages, working hours, neighborhoods (`Al Doha`, `Al Qusur`, `Al Dana`), and `+966 58 245 3440`.
2. **Citability (`100/100`) & Structural Readability (`100/100` — `Schema v9`):**  
   - Front-loaded Answer-First lead blocks + `.seo-pricing-table` SAR tables now cover **all 14 money pages** (all 8 EN/AR service pages + new 6-row City Dispatch, Coverage & SAR Price Summary Tables on all 6 EN/AR city pages).
   - Converted 32 section `H2`s across EN & AR service pages into natural query-aligned questions (`Why Is My AC Not Cooling...?` / `كم تكلفة صيانة المكيفات...؟`) while preserving 100% `H1:1 / H2:8 / H3:7` structural parity, auto-synced via `SCHEMA_VERSION = 9`.
3. **E-E-A-T Reviewer Attribution & Genuine Freshness (`89/100` Authority):**  
   Added visible `.eeat-meta-bar` (`<time datetime="YYYY-MM-DD">`) and `WebPage` JSON-LD (`dateModified`, `reviewedBy`, `specialty`) across all 14 service & city pages, synced 100% with `/sitemap.xml` `<lastmod>`.
4. **Separated AI Crawler Access (`99/100` Technical Accessibility):**  
   - **22 Explicit Search & AI Bots Allowed in `/robots.txt`:** `Googlebot`, `OAI-SearchBot`, `Claude-SearchBot`, `PerplexityBot`, `Bingbot`, `Applebot`, `DuckAssistBot`, `YouBot`, `ChatGPT-User`, `Claude-User`, `Perplexity-User`, `Google-Agent`, `Google-GeminiNotebook`, plus training/agent signals (`GPTBot`, `ClaudeBot`, `Google-Extended`, `Google-CloudVertexBot`, `Applebot-Extended`, `CCBot`, `Bytespider`, `cohere-ai`).
   - **`/llms.txt` & `/llms-full.txt` Present (`0%` Score Weight):** Maintained for non-Google tools; Google Search ignores `llms.txt` per Google's AI Optimization Guide (`2026-06-15` / `2026-07-10`).
5. **Remaining Off-Site Actions for Owner (`+3–4 pts` to `99–100/100`):**  
   - Launch GBP via [`GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md`](../GOOGLE-BUSINESS-PROFILE-MUSE-AI-BRIEF.md) & populate `hasMap` + `sameAs` in `/admin`.
   - Publish 4–5 short technician diagnostic videos on YouTube (`@mrfixksa` — strongest ~`0.737` AI citation correlation) and embed with `VideoObject` schema.
