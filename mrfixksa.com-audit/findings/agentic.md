# Agentic Readiness (AEO / WebMCP / ARD 1.0) Findings — `mrfixksa.com` (Updated Live Audit — 2026-11-11)

**Agentic Readiness Score: 100 / 100 (`11 / 11` Applicable Automated Checks Passed)**

---

## Automated Live Verification (`agentic_check.py https://mrfixksa.com/en`)

Executed on **2026-11-11** against live production (`https://mrfixksa.com/en`):

| Check ID | Priority | Result | Live Verification Detail |
|---|---|---|
| `server-rendered` | P0 | **PASS** | `8,274` visible SSR characters rendered without JS (`<h1>` present, zero CSR shell) |
| `robots-reachable` | P0 | **PASS** | `https://mrfixksa.com/robots.txt` returns `200 text/plain` (`1,012 B`) |
| `robots-ai-groups` | P0 | **PASS** | Explicit `Allow: /` rules for Search, AI-Input, and AI-Train crawler classes |
| `robots-user-agents` | P1 | **PASS** | Named rules for `OAI-SearchBot`, `ChatGPT-User`, `GPTBot`, `Claude-SearchBot`, `ClaudeBot`, `PerplexityBot`, `Google-Extended`, `Applebot-Extended` |
| `content-signal` | P1 | **PASS** | `Content-Signal: search=yes, ai-input=yes, ai-train=yes` present in `/robots.txt` |
| `llms-txt` | P1 | **PASS** | `/llms.txt` (`200 text/markdown`) + `/llms-full.txt` (`61 KB`) with valid Markdown links |
| `markdown-delivery` | P1 | **PASS** | `<link rel="alternate" type="text/markdown" href="https://mrfixksa.com/llms.txt">` in `<head>` + `Accept: text/markdown` support |
| `http-404` | P1 | **PASS** | Non-existent routes return genuine HTTP `404` (no soft-404 `200` traps) |
| `webmcp-tools` | P2 | **PASS** | Imperative `navigator.modelContext.registerTool('book_appliance_repair')` + declarative `toolname="book_appliance_repair"` on `#contact-form` |
| `webmcp-entry-point` | P2 | **PASS** | `document.modelContext` + `/.well-known/ai-catalog.json` entry point wired |
| `ard-catalog` | P3 | **PASS** | `/.well-known/ai-catalog.json` conforms to Lighthouse 13.5 ARD `specVersion: "1.0"` with `urn:air:*` entries |

---

## Note on Hostinger CDN (`hcdn`) Bot Verification (`Info`)
- Requests spoofing `User-Agent: Googlebot` from non-Google IPs receive `HTTP 403` from Hostinger's reverse-DNS anti-spoofing WAF, while genuine Googlebot IPs and AI crawler user agents (`OAI-SearchBot`, `PerplexityBot`, `ClaudeBot`, standard browsers) receive `200 OK`.
