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
 * Get a site setting from database
 */
function getSetting($key, $language = null) {
    global $pdo, $lang;
    static $cache = [];
    
    if (isset($cache[$key])) {
        $row = $cache[$key];
    } else {
        $stmt = $pdo->prepare("SELECT * FROM site_settings WHERE setting_key = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        $cache[$key] = $row;
    }
    
    if (!$row) return '';
    
    $use_lang = $language ?? $lang ?? 'en';
    
    $col = ($use_lang === 'ar') ? 'setting_value_ar' : 'setting_value_en';
    return $row[$col] ?? $row['setting_value_en'] ?? '';
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
    $stmt = $pdo->prepare("SELECT * FROM services WHERE (slug_en = :slug OR slug_ar = :slug) AND is_active = 1 LIMIT 1");
    $stmt->execute([':slug' => $slug]);
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
 * Get service areas
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
 * Get alternate language URL (for hreflang)
 */
function getAlternateUrl($current_page, $alt_lang) {
    global $current_service;
    if (isset($current_service)) {
        $slug_col = 'slug_' . $alt_lang;
        if (isset($current_service[$slug_col])) {
            return url($current_service[$slug_col], $alt_lang);
        }
    }
    return url($current_page, $alt_lang);
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate CSRF token
 */
function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
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
 * Generate structured data JSON-LD for LocalBusiness
 */
function getLocalBusinessSchema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'Ahmad Fix',
        'description' => 'Professional Home Appliance Repair & Maintenance Services in Dammam, Dhahran & Khobar',
        'url' => SITE_URL,
        'telephone' => PHONE,
        'email' => EMAIL,
        'image' => ASSETS_URL . '/images/logo.png',
        'priceRange' => '$$',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Dammam',
            'addressRegion' => 'Eastern Province',
            'addressCountry' => 'SA'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => '26.43311719',
            'longitude' => '50.0850687'
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Dammam'],
            ['@type' => 'City', 'name' => 'Dhahran'],
            ['@type' => 'City', 'name' => 'Al Khobar']
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'opens' => '08:00',
                'closes' => '22:00'
            ],
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'Friday',
                'opens' => '14:00',
                'closes' => '22:00'
            ]
        ],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Repair Services',
            'itemListElement' => [
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'AC Repair & Maintenance']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Refrigerator Repair']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Washing Machine Repair']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Oven & Stove Repair']]
            ]
        ],
        'sameAs' => [
            'https://wa.me/966582453440'
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
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
            'name' => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['a']
            ]
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $items
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
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
