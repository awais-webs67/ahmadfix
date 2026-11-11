# Visual & Responsive UX Findings — mrfixksa.com

**Visual & Mobile UX Score: 84 / 100** *(Evaluated via Static DOM + CSS Architecture Inspection; live Playwright screenshot capture blocked by `mrfixksa.com:443` TCP connect timeout)*

---

## Executive Summary

- **Bilingual RTL/LTR Design Parity**: [`assets/css/style.css`](file:///c:/xampp/htdocs/FixoraKSA/assets/css/style.css) and [`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L51) implement proper bidirectional layout (`dir="rtl" lang="ar-SA"` vs `dir="ltr" lang="en"`), custom typography (`Cairo` for Arabic, `Plus Jakarta Sans` for English), and high-contrast brand tokens (`--primary: #0B2545`, `--accent: #FF6B2B`).
- **Above-the-Fold Conversion Clarity**:
  - Homepage hero immediately presents the primary value proposition, trust badge, two high-contrast CTAs (`Call Now` + `WhatsApp Us`), 4 trust pills (`Same-Day Service`, `Certified Technicians`, `Service Warranty`, `Transparent Pricing`), and the hero technician illustration.
  - Persistent top bar displays phone, working hours, email, and language switcher (`العربية` / `English`).
- **Visual & Layout Issues Identified**:
  1. **Header Logo Aspect Ratio Distortion Risk ([`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L116), [`includes/footer.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/footer.php#L22))**: `logo-header.png`/`.webp` has native dimensions `360x205` (`1.76:1`), while the HTML attributes specify `width="120" height="40"` (`3:1`) and `width="150" height="48"` (`3.125:1`). Unless CSS forces `width: auto; height: 40px;` (or vice versa), the browser's intrinsic aspect-ratio mapping reserves a 3:1 box that disagrees with the 1.76:1 image.
  2. **Hidden FAQ Answers Require JS Click Interaction**: All FAQ answers (`.faq-answer`) are collapsed on load (`aria-expanded="false"`). Users scanning on mobile cannot see key answers (like the SAR 150 diagnostic fee or warranty policy) without tapping each accordion row.
  3. **Stock/AI Illustrations Instead of Real Local Visual Proof**: All 6 visual assets (`hero-main`, `about-team`, `ac-repair`, `fridge-repair`, `oven-repair`, `washing-machine-repair`) are square (`1024x1024`) stylized illustrations rather than real photos of Mr Fix KSA technicians, branded service vans, or appliance repairs in Dammam/Khobar.
  4. **Service Detail Page Unnecessary `<link rel="preload">` on `/about` ([`includes/header.php`](file:///c:/xampp/htdocs/FixoraKSA/includes/header.php#L102-L106))**: On `/en/about` and `/ar/about`, `about-team-640.webp` is preloaded as an LCP candidate even though it sits below the `.page-hero` banner and has `loading="lazy"` in [`pages/about.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/about.php#L59)! Combining `<link rel="preload" as="image">` with `<img loading="lazy">` is an anti-pattern that triggers browser console warnings and wastes early bandwidth.

