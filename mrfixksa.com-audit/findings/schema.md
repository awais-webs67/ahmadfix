# Schema & Structured Data Findings — mrfixksa.com

**Schema Score: 74 / 100**

---

## Executive Summary

- **Strong Baseline Implementation**: [`config/functions.php`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L523-L712) outputs well-formed JSON-LD across all 24 pages (`HomeAndConstructionBusiness` + `HVACBusiness`, `WebSite`, `BreadcrumbList`, `Service`, `AboutPage`, and `FAQPage`) with zero JSON syntax errors.
- **Missing `streetAddress` and `postalCode` on `LocalBusiness` (High)**: [`getLocalBusinessSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L557-L562) only populates `addressLocality` (`Dammam`), `addressRegion` (`Eastern Province`), and `addressCountry` (`SA`). Google's Rich Results validator flags `LocalBusiness` entities missing `streetAddress` and `postalCode`.
- **Dammam Coordinates Hardcoded on Al Khobar and Dhahran Pages (Medium)**: Every page on the site outputs Dammam coordinates (`26.4207, 50.0888`) and `addressLocality: Dammam`, and city pages (`/en/appliance-repair-khobar`, `/en/appliance-repair-dhahran`) lack a city-specific `Service` schema block targeting the respective `City` entity.
- **Empty `sameAs` Array by Default (Medium)**: Unless `google_business_url` or social profile URLs are entered in the admin database, `sameAs` is omitted entirely from `HomeAndConstructionBusiness`, weakening entity reconciliation across Google Knowledge Graph and AI answer engines.
- **`FAQPage` Schema Retirement Notice (Info)**: Present on 16 pages (8 service pages, 6 city pages, 2 about pages). Google retired FAQ rich results for all commercial websites on May 7, 2026. Keep the existing `FAQPage` markup (do not remove, as non-Google answer engines and LLMs still parse it), flagged at **Info** severity only.

---

## Inventory & Validation of Existing JSON-LD

| Schema Type | Source Function | Pages Present | Validation Status | Findings |
|---|---|---|---|---|
| `["HomeAndConstructionBusiness", "HVACBusiness"]` | [`getLocalBusinessSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L523-L598) | All 24 pages | Valid JSON / Partial Rich Result fields | Missing `address.streetAddress`, `address.postalCode`, `email`; `sameAs` omitted when social settings are blank; `url` changes per language (`/en` vs `/ar`) while `@id` stays `https://mrfixksa.com/#business`. |
| `WebSite` | [`getWebSiteSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L604-L621) | All 24 pages | Valid | Uses `@id: https://mrfixksa.com/#website`, references `publisher: {"@id": "https://mrfixksa.com/#business"}`, declares `inLanguage` (`en-SA` / `ar-SA`). |
| `BreadcrumbList` | [`getBreadcrumbSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L679-L702) | 22 inner pages | Valid | Properly ordered `ListItem` elements with absolute `item` URLs. |
| `Service` | [`getServiceSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L627-L654) | 8 service pages | Valid / Expandable | Links `provider` to `#business` and lists 3 `City` entities in `areaServed`; missing `offers` (`PriceSpecification` for diagnostic fee) and missing on the 6 city landing pages. |
| `AboutPage` | [`getAboutPageSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L660-L673) | 2 about pages | Valid | Properly references `about: {"@id": "https://mrfixksa.com/#business"}`. |
| `FAQPage` | [`getFaqSchema()`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L708-L735) | 16 pages (services, areas, about) | **Info** (Retired by Google May 7, 2026) | Valid syntax with 4–5 Q&A pairs per page. Do not remove; keep for Bing/LLM extraction. |

---

## Recommended JSON-LD Enhancements

### 1. Enrich `getLocalBusinessSchema()` in [`config/functions.php`](file:///c:/xampp/htdocs/FixoraKSA/config/functions.php#L557-L562)
Add `streetAddress`, `postalCode`, and `email` (using a domain address `info@mrfixksa.com`), and use canonical root `SITE_URL . '/en'` consistently or include both language URLs:

```json
{
  "@context": "https://schema.org",
  "@type": ["HomeAndConstructionBusiness", "HVACBusiness"],
  "@id": "https://mrfixksa.com/#business",
  "name": "Mr Fix KSA",
  "alternateName": "مستر فيكس لصيانة الأجهزة المنزلية",
  "url": "https://mrfixksa.com/en",
  "telephone": "+966575064911",
  "email": "info@mrfixksa.com",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "King Fahd Road, Al Faisaliyah District",
    "addressLocality": "Dammam",
    "addressRegion": "Eastern Province",
    "postalCode": "32272",
    "addressCountry": "SA"
  }
}
```
*(Note: Replace `"streetAddress"` and `"postalCode"` with the exact commercial registration / workshop address in Dammam).*

### 2. Add City-Scoped `Service` Schema on City Pages ([`pages/area.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/area.php#L10-L13))
Currently `pages/area.php` only outputs `BreadcrumbList` and `FAQPage`. Add a city-targeted `Service` schema with the city's specific coordinates (`Dammam: 26.4207, 50.0888`; `Al Khobar: 26.2172, 50.1971`; `Dhahran: 26.2361, 50.0393`) and `hasOfferCatalog` listing the 4 appliance repair services in that city:

```json
{
  "@context": "https://schema.org",
  "@type": "Service",
  "@id": "https://mrfixksa.com/en/appliance-repair-khobar#service",
  "name": "Home Appliance Repair in Al Khobar",
  "serviceType": "Home Appliance Repair",
  "provider": {
    "@id": "https://mrfixksa.com/#business"
  },
  "areaServed": {
    "@type": "City",
    "name": "Al Khobar",
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 26.2172,
      "longitude": 50.1971
    }
  }
}
```

### 3. Add `ContactPage` Schema on `/en/contact` and `/ar/contact` ([`pages/contact.php`](file:///c:/xampp/htdocs/FixoraKSA/pages/contact.php#L5))
Match the pattern used in `getAboutPageSchema()` by outputting a `ContactPage` JSON-LD block referencing `mainEntity: {"@id": "https://mrfixksa.com/#business"}`.

