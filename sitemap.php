<?php
/**
 * Ahmad Fix — XML Sitemap (served at /sitemap.xml)
 * Built from the database + city list, so it is always up to date.
 * Every page is listed in English and Arabic with hreflang links between them.
 */

if (!defined('FIXORA_APP')) {
    // Opened directly as /sitemap.php — send to the official address
    header('Location: /sitemap.xml', true, 301);
    exit;
}

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

$entries = [];

// Last change date of a template file (used for static pages)
$file_date = fn($file) => date('Y-m-d', filemtime(SITE_ROOT . '/' . $file) ?: time());

// Static pages (same path in both languages)
$static = [
    ''               => 'pages/home.php',
    'about'          => 'pages/about.php',
    'contact'        => 'pages/contact.php',
    'privacy-policy' => 'pages/privacy.php',
    'terms'          => 'pages/terms.php',
];
foreach ($static as $path => $file) {
    $entries[] = [
        'paths'   => ['en' => $path, 'ar' => $path],
        'lastmod' => $file_date($file),
        'images'  => $path === '' ? [ASSETS_URL . '/images/hero/hero-main.jpg'] : ($path === 'about' ? [ASSETS_URL . '/images/about/about-team.jpg'] : []),
    ];
}

// Service pages
$service_images = [
    'snowflake' => 'ac-repair.jpg',
    'thermometer' => 'fridge-repair.jpg',
    'rotate-cw' => 'washing-machine-repair.jpg',
    'flame' => 'oven-repair.jpg',
];
foreach (getServices() as $svc) {
    $updated = !empty($svc['updated_at']) ? strtotime($svc['updated_at']) : time();
    $updated = max($updated, filemtime(SITE_ROOT . '/pages/service-detail.php'));
    $entries[] = [
        'paths'   => ['en' => $svc['slug_en'], 'ar' => $svc['slug_ar']],
        'lastmod' => date('Y-m-d', $updated),
        'images'  => [ASSETS_URL . '/images/services/' . ($service_images[$svc['icon']] ?? 'ac-repair.jpg')],
    ];
}

// City pages
$areas_date = max(filemtime(SITE_ROOT . '/config/areas.php'), filemtime(SITE_ROOT . '/pages/area.php'));
foreach (getAreas() as $area) {
    $entries[] = [
        'paths'   => ['en' => $area['slug_en'], 'ar' => $area['slug_ar']],
        'lastmod' => date('Y-m-d', $areas_date),
        'images'  => [],
    ];
}

$x = fn($s) => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
foreach ($entries as $entry) {
    $en = encodeUrl(url($entry['paths']['en'], 'en'));
    $ar = encodeUrl(url($entry['paths']['ar'], 'ar'));
    foreach (['en' => $en, 'ar' => $ar] as $loc) {
        echo "  <url>\n";
        echo "    <loc>" . $x($loc) . "</loc>\n";
        echo "    <lastmod>" . $entry['lastmod'] . "</lastmod>\n";
        echo '    <xhtml:link rel="alternate" hreflang="en" href="' . $x($en) . '"/>' . "\n";
        echo '    <xhtml:link rel="alternate" hreflang="ar" href="' . $x($ar) . '"/>' . "\n";
        echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . $x($en) . '"/>' . "\n";
        foreach ($entry['images'] as $img) {
            echo "    <image:image><image:loc>" . $x($img) . "</image:loc></image:image>\n";
        }
        echo "  </url>\n";
    }
}
echo "</urlset>\n";
