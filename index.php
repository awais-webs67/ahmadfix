<?php
/**
 * Ahmad Fix — Front Controller / Router
 * All requests are routed through this file
 */

define('FIXORA_APP', true);

// Get the route from the URL
$route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$segments = $route !== '' ? explode('/', $route) : [];

// ============================================
// Language Detection from URL
// ============================================
$url_lang = null;
$supported_langs = ['en', 'ar'];

if (!empty($segments[0]) && in_array($segments[0], $supported_langs, true)) {
    $url_lang = $segments[0];
    array_shift($segments); // Remove language prefix
}

// ============================================
// Load Configuration (sets $lang, $pdo, etc.)
// ============================================
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/mail.php';

// ============================================
// Sitemap (generated from the database, always up to date)
// ============================================
if ($route === 'sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    exit;
}

// ============================================
// API Routes (for AJAX)
// ============================================
if ($route === 'api/contact-submit') {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Robots-Tag: noindex');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    // Use the language of the page the form was sent from
    if (isset($_POST['lang']) && in_array($_POST['lang'], $supported_langs, true) && $_POST['lang'] !== $lang) {
        $lang = $_POST['lang'];
        $translations = require SITE_ROOT . '/lang/' . $lang . '.php';
    }

    // Validate CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRF($token)) {
        echo json_encode(['success' => false, 'message' => __('contact_form_token')]);
        exit;
    }

    // Spam trap: real visitors never see or fill the hidden "website" field
    if (!empty($_POST['website'])) {
        echo json_encode(['success' => true, 'message' => __('contact_form_success')]);
        exit;
    }

    // Simple flood protection: max 5 messages per 10 minutes per visitor
    $now = time();
    $_SESSION['contact_times'] = array_values(array_filter($_SESSION['contact_times'] ?? [], fn($t) => $t > $now - 600));
    if (count($_SESSION['contact_times']) >= 5) {
        echo json_encode(['success' => false, 'message' => __('contact_form_too_many')]);
        exit;
    }

    // Validate required fields
    $name = sanitize(mb_substr($_POST['name'] ?? '', 0, 100));
    $phone = sanitize(mb_substr($_POST['phone'] ?? '', 0, 20));
    $email = sanitize(mb_substr($_POST['email'] ?? '', 0, 100));
    $service = sanitize($_POST['service'] ?? '');
    $message = sanitize(mb_substr($_POST['message'] ?? '', 0, 2000));

    if (!in_array($service, ['', 'ac', 'fridge', 'washer', 'oven', 'other'], true)) {
        $service = 'other';
    }

    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => __('contact_form_required')]);
        exit;
    }

    if ($email !== '' && !filter_var(html_entity_decode($email, ENT_QUOTES, 'UTF-8'), FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => __('contact_form_bad_email')]);
        exit;
    }

    // Validate phone format (Saudi) — also accepts Arabic-Indic digits
    $phone_digits = strtr($phone, ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);
    $phone_digits = preg_replace('/[\s\-\(\)]/', '', $phone_digits);
    if (!preg_match('/^(\+966|00966|966|05|5)[0-9]{8,9}$/', $phone_digits)) {
        echo json_encode(['success' => false, 'message' => __('contact_form_bad_phone')]);
        exit;
    }
    $phone = $phone_digits;

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_submissions (customer_name, phone, email, service_requested, message) VALUES (:name, :phone, :email, :service, :message)");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':service' => $service,
            ':message' => $message
        ]);

        $_SESSION['contact_times'][] = $now;

        // Send email notifications via SMTP (handled gracefully inside the helper)
        sendContactEmails($name, $phone, $email, $service, $message, $lang);

        echo json_encode(['success' => true, 'message' => __('contact_form_success')]);
    } catch (PDOException $e) {
        error_log("Contact form error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => __('contact_form_error')]);
    }
    exit;
}

// ============================================
// Admin Routes
// ============================================
if ($route === 'admin' || str_starts_with($route, 'admin/')) {
    header('X-Robots-Tag: noindex, nofollow');
    $admin_page = trim(substr($route, 5), '/');
    $admin_page = $admin_page ?: 'index';

    $admin_file = __DIR__ . '/admin/' . basename($admin_page) . '.php';
    if ($admin_page !== 'auth' && file_exists($admin_file)) {
        require_once $admin_file;
    } else {
        require_once __DIR__ . '/admin/index.php';
    }
    exit;
}

// ============================================
// No language in the URL
// ============================================
if ($url_lang === null) {
    if ($route === '') {
        // Homepage: send visitors to their language (Google sees x-default = English)
        header('Vary: Accept-Language');
        header('Location: ' . SITE_URL . '/' . $lang, true, 302);
        exit;
    }
    // Old/shared links without /en or /ar: keep the path, add the language (permanent)
    $guess = preg_match('/\p{Arabic}/u', $route) ? 'ar' : 'en';
    redirect301(url($route, $guess));
}

// ============================================
// Determine the page to load
// ============================================
$page = !empty($segments) ? implode('/', $segments) : 'home';

$static_pages = [
    'home'           => 'pages/home.php',
    'about'          => 'pages/about.php',
    'contact'        => 'pages/contact.php',
    'privacy-policy' => 'pages/privacy.php',
    'terms'          => 'pages/terms.php',
];

// Duplicate addresses → one official address per page (avoids duplicate content)
$page_aliases = [
    'home'      => '',
    'من-نحن'    => 'about',
    'اتصل-بنا'  => 'contact',
    'privacy'   => 'privacy-policy',
    'terms-of-service' => 'terms',
];
if (!empty($segments) && isset($page_aliases[$page])) {
    redirect301(url($page_aliases[$page]));
}

// Old service URLs (city in the slug) → new city-neutral URLs
$legacy = legacyServiceSlugs();
if (isset($legacy[$page])) {
    $svc = getServiceBySlug($legacy[$page]);
    redirect301(url($svc ? localized($svc, 'slug') : $legacy[$page]));
}

// Defaults used by the header template
$alt_paths = null;
$breadcrumbs = [['name' => __('nav_home'), 'url' => url('')]];
$page_schema = '';
$is_404 = false;

if (isset($static_pages[$page])) {
    // ---- Regular page ----
    $current_page = $page;
    $page_file = __DIR__ . '/' . $static_pages[$page];
    $path = $page === 'home' ? '' : $page;
    $alt_paths = ['en' => $path, 'ar' => $path];

    $page_seo = getPageSEO($page);
    if (!$page_seo) {
        // Pages without a database row get their title/description from the language files
        $page_seo = [
            'title_' . $lang => __('seo_title_' . $page, ''),
            'description_' . $lang => __('seo_desc_' . $page, ''),
        ];
    }

    $crumb_names = ['about' => __('nav_about'), 'contact' => __('nav_contact'), 'privacy-policy' => __('footer_privacy'), 'terms' => __('footer_terms')];
    if (isset($crumb_names[$page])) {
        $breadcrumbs[] = ['name' => $crumb_names[$page], 'url' => url($page)];
    }
} elseif ($svc = getServiceBySlug($page)) {
    // ---- Service page ----
    if ($page !== $svc['slug_' . $lang]) {
        // e.g. /en/<arabic-slug> → /en/<english-slug>
        redirect301(url($svc['slug_' . $lang]));
    }
    $current_page = 'service';
    $current_service = $svc;
    $page_file = __DIR__ . '/pages/service-detail.php';
    $alt_paths = ['en' => $svc['slug_en'], 'ar' => $svc['slug_ar']];
    $page_seo = [
        'title_en' => $svc['meta_title_en'],
        'title_ar' => $svc['meta_title_ar'],
        'description_en' => $svc['meta_desc_en'],
        'description_ar' => $svc['meta_desc_ar'],
        'keywords_en' => $svc['meta_keywords_en'],
        'keywords_ar' => $svc['meta_keywords_ar'],
    ];
    $breadcrumbs[] = ['name' => __('nav_services'), 'url' => url('') . '#services'];
    $breadcrumbs[] = ['name' => localized($svc, 'name'), 'url' => url($svc['slug_' . $lang])];
} elseif ($area = getAreaBySlug($page)) {
    // ---- City (service area) page ----
    if ($page !== $area['slug_' . $lang]) {
        redirect301(url($area['slug_' . $lang]));
    }
    $current_page = 'area';
    $current_area = $area;
    $page_file = __DIR__ . '/pages/area.php';
    $alt_paths = ['en' => $area['slug_en'], 'ar' => $area['slug_ar']];
    $page_seo = [
        'title_' . $lang => $area['title_' . $lang],
        'description_' . $lang => fillPlaceholders($area['desc_' . $lang]),
    ];
    $breadcrumbs[] = ['name' => __('areas_title'), 'url' => url('') . '#areas'];
    $breadcrumbs[] = ['name' => $area['name_' . $lang], 'url' => url($area['slug_' . $lang])];
} else {
    // ---- 404 ----
    http_response_code(404);
    $is_404 = true;
    $current_page = '404';
    $page_file = __DIR__ . '/pages/404.php';
    $page_seo = [
        'title_' . $lang => __('seo_title_404'),
        'description_' . $lang => '',
    ];
}

// ============================================
// Render the page
// ============================================
require_once __DIR__ . '/includes/header.php';

if (file_exists($page_file)) {
    require_once $page_file;
} else {
    echo '<main class="container"><h1>Page Not Found</h1><p>The page you are looking for does not exist.</p></main>';
}

require_once __DIR__ . '/includes/floating-buttons.php';
require_once __DIR__ . '/includes/footer.php';
