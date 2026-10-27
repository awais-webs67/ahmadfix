<?php
/**
 * Ahmad Fix — Helper Functions
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

/**
 * Translate a key from the loaded language file
 */
function __($key, $default = '') {
    global $translations;
    return $translations[$key] ?? $default ?: $key;
}

/**
 * Escape text for safe HTML output (does not double-escape existing entities)
 */
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Get a site setting from database
 */
function getSetting($key, $language = null) {
    return setting($key, $language);
}

/**
 * Get all settings (cached)
 */
function getAllSettings() {
    global $pdo;
    static $settings = null;

    if ($settings === null) {
        $stmt = $pdo->query("SELECT * FROM site_settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row;
        }
    }

    return $settings;
}

/**
 * Get setting value helper (uses cache)
 */
function setting($key, $language = null) {
    global $lang;
    $all = getAllSettings();
    if (!isset($all[$key])) return '';

    $use_lang = $language ?? $lang ?? 'en';

    $col = ($use_lang === 'ar') ? 'setting_value_ar' : 'setting_value_en';
    return $all[$key][$col] ?? $all[$key]['setting_value_en'] ?? '';
}

/**
 * Get all active services
 */
function getServices() {
    global $pdo;
    static $services = null;

    if ($services === null) {
        $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
        $services = $stmt->fetchAll();
    }

    return $services;
}

/**
 * Get a single service by slug
 */
function getServiceBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM services WHERE (slug_en = :slug_en OR slug_ar = :slug_ar) AND is_active = 1 LIMIT 1");
    $stmt->execute([':slug_en' => $slug, ':slug_ar' => $slug]);
    return $stmt->fetch();
}

/**
 * Get hero content
 */
function getHeroContent() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM hero_content WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 1");
    return $stmt->fetch();
}

/**
 * Get active testimonials
 */
function getTestimonials() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

/**
 * Get service areas (homepage cards, from the database)
 */
function getServiceAreas() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM service_areas WHERE is_active = 1 ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

/**
 * Get page SEO data
 */
function getPageSEO($page_slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pages_seo WHERE page_slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $page_slug]);
    return $stmt->fetch();
}

// ============================================
// City (service area) pages
// ============================================

/**
 * All city pages (from config/areas.php)
 */
function getAreas() {
    static $areas = null;
    if ($areas === null) {
        $areas = require SITE_ROOT . '/config/areas.php';
    }
    return $areas;
}

/**
 * Find a city page by its English or Arabic slug
 */
function getAreaBySlug($slug) {
    foreach (getAreas() as $key => $area) {
        if ($area['slug_en'] === $slug || $area['slug_ar'] === $slug) {
            return $area + ['key' => $key];
        }
    }
    return null;
}

/**
 * Find a city page by the English name used in the service_areas table
 */
function getAreaByName($name_en) {
    foreach (getAreas() as $key => $area) {
        if (strcasecmp($area['match_en'], trim($name_en)) === 0 || strcasecmp($area['name_en'], trim($name_en)) === 0) {
            return $area + ['key' => $key];
        }
    }
    return null;
}

/**
 * Replace {phone} placeholders in area text
 */
function fillPlaceholders($text) {
    return str_replace('{phone}', PHONE, $text);
}

/**
 * Comma list of all city names in the current language: "Dammam, Al Khobar & Dhahran"
 */
function citiesList($language = null) {
    global $lang;
    $language = $language ?? $lang;
    $names = array_map(fn($a) => $a['name_' . $language], array_values(getAreas()));
    if (count($names) < 2) return implode('', $names);
    $last = array_pop($names);
    return implode($language === 'ar' ? '، ' : ', ', $names) . ($language === 'ar' ? ' و' : ' & ') . $last;
}

// ============================================
// URLs
// ============================================

/**
 * Generate URL for a given language
 */
function url($path = '', $language = null) {
    global $lang;
    $language = $language ?? $lang;
    $base = SITE_URL;

    if (empty($path) || $path === '/') {
        return $base . '/' . $language;
    }

    return $base . '/' . $language . '/' . ltrim($path, '/');
}

/**
 * Get alternate language URL (for hreflang + language switcher)
 * Uses $alt_paths set by the router when available.
 */
function getAlternateUrl($current_page, $alt_lang) {
    global $alt_paths, $current_service;
    if (isset($alt_paths[$alt_lang])) {
        return url($alt_paths[$alt_lang], $alt_lang);
    }
    if (isset($current_service)) {
        $slug_col = 'slug_' . $alt_lang;
        if (isset($current_service[$slug_col])) {
            return url($current_service[$slug_col], $alt_lang);
        }
    }
    return url($current_page === 'home' ? '' : $current_page, $alt_lang);
}

/**
 * Percent-encode each path segment (for sitemap / HTTP headers)
 */
function encodeUrl($url) {
    $parts = parse_url($url);
    $path = implode('/', array_map('rawurlencode', explode('/', $parts['path'] ?? '')));
    return ($parts['scheme'] ?? 'https') . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '') . $path;
}

/**
 * Permanent (301) redirect and stop
 */
function redirect301($url) {
    header('Location: ' . encodeUrl($url), true, 301);
    exit;
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate CSRF token
 */
function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get localized field from a row
 */
function localized($row, $field) {
    global $lang;
    $key = $field . '_' . $lang;
    $fallback = $field . '_en';
    return $row[$key] ?? $row[$fallback] ?? '';
}

/**
 * Format phone number for tel: link
 */
function formatPhone($phone) {
    return preg_replace('/[^+0-9]/', '', $phone);
}

/**
 * WhatsApp number in the format wa.me expects (digits only)
 */
function waNumber() {
    return ltrim(formatPhone(WHATSAPP), '+');
}

// ============================================
// Images
// ============================================

/**
 * Responsive <picture> with WebP versions + original JPG fallback.
 * Looks for {name}-400/-640/-800/-1024.webp next to the original.
 *
 * @param string $src   path relative to /assets/images, e.g. 'services/ac-repair.jpg'
 * @param array  $attrs extra <img> attributes (alt, width, height, loading, class, sizes...)
 */
function picture($src, array $attrs = []) {
    $base = preg_replace('/\.(jpe?g|png)$/i', '', $src);
    $dir = SITE_ROOT . '/assets/images/';
    $sizes = $attrs['sizes'] ?? '(max-width: 768px) 100vw, 600px';
    unset($attrs['sizes']);

    $srcset = [];
    foreach ([400, 640, 800, 1024] as $w) {
        if (file_exists($dir . $base . '-' . $w . '.webp')) {
            $srcset[] = ASSETS_URL . '/images/' . $base . '-' . $w . '.webp?v=' . ASSETS_VERSION . ' ' . $w . 'w';
        }
    }

    $img_attrs = '';
    foreach ($attrs as $k => $v) {
        if ($v === null || $v === false) continue;
        $img_attrs .= ' ' . $k . '="' . e($v) . '"';
    }
    $img = '<img src="' . ASSETS_URL . '/images/' . e($src) . '"' . $img_attrs . '>';

    if (!$srcset) {
        return $img;
    }
    return '<picture><source type="image/webp" srcset="' . e(implode(', ', $srcset)) . '" sizes="' . e($sizes) . '">' . $img . '</picture>';
}

/**
 * Header/footer logo (small WebP + PNG fallback instead of the 100 KB original)
 */
function logoPicture($class = 'logo-img', $style = '') {
    $alt = SITE_NAME;
    $style_attr = $style ? ' style="' . e($style) . '"' : '';
    if (file_exists(SITE_ROOT . '/assets/images/logo-header.webp')) {
        return '<picture><source type="image/webp" srcset="' . ASSETS_URL . '/images/logo-header.webp?v=' . ASSETS_VERSION . '">'
             . '<img src="' . ASSETS_URL . '/images/logo-header.png?v=' . ASSETS_VERSION . '" alt="' . e($alt) . '" class="' . e($class) . '" width="360" height="201"' . $style_attr . '></picture>';
    }
    return '<img src="' . ASSETS_URL . '/images/logo.png" alt="' . e($alt) . '" class="' . e($class) . '"' . $style_attr . '>';
}

// ============================================
// Structured data (JSON-LD) for Google
// ============================================

function jsonLd(array $schema) {
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . '</script>' . "\n";
}

/**
 * Social / profile links that exist (used in schema "sameAs" and the footer)
 */
function socialLinks() {
    $links = [
        'google'    => GOOGLE_BUSINESS_URL,
        'instagram' => SOCIAL_INSTAGRAM,
        'snapchat'  => SOCIAL_SNAPCHAT,
        'tiktok'    => SOCIAL_TIKTOK,
        'facebook'  => SOCIAL_FACEBOOK,
        'x'         => SOCIAL_X,
    ];
    return array_filter($links, fn($u) => is_string($u) && $u !== '');
}

/**
 * Main business schema — the same @id is referenced by every page
 */
function getLocalBusinessSchema() {
    global $lang;
    $is_ar = ($lang === 'ar');

    $services = [];
    foreach (getServices() as $svc) {
        $services[] = [
            '@type' => 'Offer',
            'itemOffered' => [
                '@type' => 'Service',
                'name' => localized($svc, 'name'),
                'url' => url(localized($svc, 'slug')),
            ],
        ];
    }

    $areas = [];
    foreach (getAreas() as $area) {
        $areas[] = [
            '@type' => 'City',
            'name' => $area['name_' . $lang],
            'containedInPlace' => ['@type' => 'AdministrativeArea', 'name' => $is_ar ? 'المنطقة الشرقية' : 'Eastern Province'],
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => ['HomeAndConstructionBusiness', 'HVACBusiness'],
        '@id' => SITE_URL . '/#business',
        'name' => $is_ar ? SITE_NAME_AR : SITE_NAME,
        'alternateName' => $is_ar ? SITE_NAME : SITE_NAME_AR,
        'description' => $is_ar
            ? 'صيانة وإصلاح المكيفات والثلاجات والغسالات والأفران في الدمام والخبر والظهران. فنيون معتمدون وخدمة في نفس اليوم.'
            : 'Professional AC, refrigerator, washing machine and oven repair in Dammam, Al Khobar and Dhahran. Certified technicians and same-day service.',
        'url' => url(''),
        'logo' => ASSETS_URL . '/images/logo.png',
        'image' => [ASSETS_URL . '/images/og-image.jpg', ASSETS_URL . '/images/hero/hero-main.jpg'],
        'telephone' => formatPhone(PHONE),
        'email' => EMAIL,
        'priceRange' => '$$',
        'currenciesAccepted' => 'SAR',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $is_ar ? 'الدمام' : 'Dammam',
            'addressRegion' => $is_ar ? 'المنطقة الشرقية' : 'Eastern Province',
            'addressCountry' => 'SA',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 26.4207,
            'longitude' => 50.0888,
        ],
        'areaServed' => $areas,
        'knowsLanguage' => ['ar', 'en'],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => formatPhone(PHONE),
            'contactType' => 'customer service',
            'areaServed' => 'SA',
            'availableLanguage' => ['Arabic', 'English'],
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'opens' => '08:00',
                'closes' => '22:00',
            ],
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'Friday',
                'opens' => '14:00',
                'closes' => '22:00',
            ],
        ],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => $is_ar ? 'خدمات الصيانة' : 'Repair Services',
            'itemListElement' => $services,
        ],
    ];

    if ($links = socialLinks()) {
        $schema['sameAs'] = array_values($links);
    }
    if (GOOGLE_BUSINESS_URL) {
        $schema['hasMap'] = GOOGLE_BUSINESS_URL;
    }

    return jsonLd($schema);
}

/**
 * WebSite schema (site name in Google results)
 */
function getWebsiteSchema() {
    global $lang;
    return jsonLd([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => SITE_URL . '/#website',
        'name' => $lang === 'ar' ? SITE_NAME_AR : SITE_NAME,
        'alternateName' => [$lang === 'ar' ? SITE_NAME : SITE_NAME_AR, 'AhmadFix'],
        'url' => url(''),
        'inLanguage' => $lang === 'ar' ? 'ar-SA' : 'en-SA',
        'publisher' => ['@id' => SITE_URL . '/#business'],
    ]);
}

/**
 * Breadcrumb schema: [['name' => 'Home', 'url' => '...'], ...]
 */
function getBreadcrumbSchema(array $crumbs) {
    if (count($crumbs) < 2) return '';
    $items = [];
    foreach (array_values($crumbs) as $i => $c) {
        $item = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name']];
        if (!empty($c['url'])) $item['item'] = $c['url'];
        $items[] = $item;
    }
    return jsonLd([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ]);
}

/**
 * Service schema for a service page
 */
function getServiceSchema(array $service) {
    global $lang;
    $areas = array_map(fn($a) => ['@type' => 'City', 'name' => $a['name_' . $lang]], array_values(getAreas()));
    $image_map = [
        'snowflake' => 'ac-repair.jpg',
        'thermometer' => 'fridge-repair.jpg',
        'rotate-cw' => 'washing-machine-repair.jpg',
        'flame' => 'oven-repair.jpg',
    ];
    return jsonLd([
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => localized($service, 'name'),
        'serviceType' => localized($service, 'name'),
        'description' => strip_tags(localized($service, 'short_desc')),
        'url' => url(localized($service, 'slug')),
        'image' => ASSETS_URL . '/images/services/' . ($image_map[$service['icon']] ?? 'ac-repair.jpg'),
        'provider' => ['@id' => SITE_URL . '/#business'],
        'areaServed' => $areas,
        'availableChannel' => [
            '@type' => 'ServiceChannel',
            'servicePhone' => ['@type' => 'ContactPoint', 'telephone' => formatPhone(PHONE)],
            'serviceUrl' => url('contact'),
        ],
    ]);
}

/**
 * Generate FAQ structured data
 */
function getFAQSchema($faqs) {
    if (empty($faqs)) return '';

    $items = [];
    foreach ($faqs as $faq) {
        $items[] = [
            '@type' => 'Question',
            'name' => html_entity_decode($faq['q'], ENT_QUOTES, 'UTF-8'),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => html_entity_decode($faq['a'], ENT_QUOTES, 'UTF-8'),
            ],
        ];
    }

    return jsonLd([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $items,
    ]);
}

/**
 * Count unread contact submissions
 */
function getUnreadSubmissions() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_submissions WHERE is_read = 0");
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}
