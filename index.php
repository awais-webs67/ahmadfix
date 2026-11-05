<?php
/**
 * Mr Fix KSA — Front Controller / Router
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
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/config/mail.php';

// One official address (https / www) — set in Admin → Settings → Website address
enforceCanonicalUrl();

// ============================================
// Sitemap, robots.txt, web manifest (generated, always use the current domain)
// ============================================
if ($route === 'sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    exit;
}
if ($route === 'robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "# " . SITE_NAME . " (" . SITE_NAME_AR . ") — robots.txt\n";
    echo "# Official Domain: " . SITE_URL . "\n\n";
    echo "User-agent: *\nAllow: /\n";
    echo "Content-Signal: search=yes, ai-input=yes, ai-train=yes\n\n";
    echo "# Private / technical areas\nDisallow: /admin\nDisallow: /api/\nDisallow: /config/\nDisallow: /database/\nDisallow: /includes/\nDisallow: /lang/\n\n";
    echo "# Search & AI Answer Engine Crawlers — Explicitly Allowed\n";
    foreach (['Googlebot', 'Bingbot', 'OAI-SearchBot', 'ChatGPT-User', 'GPTBot', 'Claude-SearchBot', 'ClaudeBot', 'PerplexityBot', 'Google-Extended', 'Applebot-Extended', 'DuckAssistBot', 'YouBot'] as $bot) {
        echo "User-agent: {$bot}\nAllow: /\nDisallow: /admin\nDisallow: /api/\n\n";
    }
    echo "Sitemap: " . SITE_URL . "/sitemap.xml\n";
    echo "# AI / LLM Knowledge Base: " . SITE_URL . "/llms.txt\n";
    exit;
}
if ($route === 'llms.txt' || $route === 'llms-full.txt') {
    header('Content-Type: text/markdown; charset=utf-8');
    $is_full = ($route === 'llms-full.txt');
    echo "# " . SITE_NAME . " (" . SITE_NAME_AR . ") — Certified Home Appliance Repair in Eastern Province, Saudi Arabia\n\n";
    echo "> " . SITE_NAME . " (" . SITE_NAME_AR . ") is a certified home appliance repair and maintenance company headquartered in Dammam, Saudi Arabia, providing same-day on-site repair for air conditioners (AC), refrigerators, washing machines, and gas/electric ovens across Dammam (الدمام), Al Khobar (الخبر), and Dhahran (الظهران).\n\n";
    echo "## Official Entity Facts\n";
    echo "- **Legal / Brand Name (EN)**: " . SITE_NAME . " (MrFixKSA)\n";
    echo "- **Brand Name (AR)**: " . SITE_NAME_AR . " (مركز مستر فيكس لصيانة الأجهزة المنزلية)\n";
    echo "- **Primary Website**: " . SITE_URL . "\n";
    echo "- **Phone & Emergency Dispatch**: " . PHONE . " (`tel:" . formatPhone(PHONE) . "`)\n";
    echo "- **WhatsApp Instant Booking**: https://wa.me/" . waNumber() . "\n";
    echo "- **Email**: " . EMAIL . "\n";
    echo "- **Headquarters**: King Fahd Road, Al Faisaliyah District, Dammam 32272, Eastern Province, Saudi Arabia (26.4207° N, 50.0888° E)\n";
    echo "- **Operating Hours**: Saturday–Thursday 08:00–22:00, Friday 14:00–22:00 (Asia/Riyadh, UTC+3)\n";
    echo "- **Inspection & Diagnostic Fee**: 50 SAR (waived/credited toward the repair upon approval)\n";
    echo "- **Typical Repair Price Range**: 80 SAR – 450 SAR (upfront fixed quote before work begins)\n";
    echo "- **Service Warranty**: Written 30-to-90-day warranty on all genuine OEM spare parts and labor\n";
    echo "- **Payment Methods**: Cash, Mada, STC Pay, Apple Pay, Bank Transfer\n";
    echo "- **Supported Brands**: " . brandsList() . "\n\n";
    echo "## Core Services & Canonical URLs\n";
    foreach (getServices() as $svc) {
        echo "- **" . $svc['name_en'] . " (" . $svc['name_ar'] . ")**\n";
        echo "  - English URL: " . url($svc['slug_en'], 'en') . "\n";
        echo "  - Arabic URL: " . url($svc['slug_ar'], 'ar') . "\n";
        echo "  - Summary (EN): " . $svc['short_desc_en'] . "\n";
        echo "  - Summary (AR): " . $svc['short_desc_ar'] . "\n";
    }
    echo "\n## Transparent Price Guide (SAR)\n";
    echo "| Service Category | Diagnostic Visit | Common Repair / Maintenance Range | Warranty |\n";
    echo "|---|---|---|---|\n";
    echo "| Air Conditioner (Split, Central, Window) | 50 SAR (waived on repair) | 120 – 450 SAR (Cleaning, R410A/R22 Freon, Capacitor, PCB, Compressor) | 30–90 Days |\n";
    echo "| Refrigerator & Freezer (Inverter, Side-by-Side) | 50 SAR (waived on repair) | 120 – 450 SAR (Gas Recharge, Thermostat, Defrost Heater, Fan, Compressor) | 30–90 Days |\n";
    echo "| Washing Machine (Front-Load, Top-Load, Dryer) | 50 SAR (waived on repair) | 100 – 380 SAR (Drain Pump, Door Lock, Bearings, Shock Absorbers, PCB) | 30–90 Days |\n";
    echo "| Gas & Electric Oven / Range / Cooker | 50 SAR (waived on repair) | 100 – 350 SAR (Igniter, Thermocouple, Heating Element, Thermostat, Hinges) | 30–90 Days |\n\n";
    echo "## Cities & Neighborhoods Served\n";
    foreach (getAreas() as $area) {
        echo "### " . $area['name_en'] . " (" . $area['name_ar'] . ") — " . $area['lat'] . ", " . $area['lng'] . "\n";
        echo "- English Page: " . url($area['slug_en'], 'en') . "\n";
        echo "- Arabic Page: " . url($area['slug_ar'], 'ar') . "\n";
        echo "- Districts Covered (EN): " . implode(', ', $area['districts_en']) . "\n";
        echo "- Districts Covered (AR): " . implode('، ', $area['districts_ar']) . "\n\n";
    }
    if ($is_full) {
        echo "## Detailed Technical Service Documentation\n\n";
        foreach (getServices() as $svc) {
            echo "### " . $svc['name_en'] . " / " . $svc['name_ar'] . "\n";
            echo strip_tags(str_replace(['</h2>', '</h3>', '</p>', '</li>', '</tr>'], "\n\n", $svc['full_desc_en'])) . "\n\n";
            echo strip_tags(str_replace(['</h2>', '</h3>', '</p>', '</li>', '</tr>'], "\n\n", $svc['full_desc_ar'])) . "\n\n";
        }
    }
    echo "## Key Informational & Booking Links\n";
    echo "- English Home: " . url('', 'en') . "\n";
    echo "- Arabic Home: " . url('', 'ar') . "\n";
    echo "- About Us: " . url('about', 'en') . " | " . url('about', 'ar') . "\n";
    echo "- Online Booking & Contact: " . url('contact', 'en') . " | " . url('contact', 'ar') . "\n";
    echo "- Privacy Policy: " . url('privacy-policy', 'en') . " | " . url('privacy-policy', 'ar') . "\n";
    echo "- Terms of Service: " . url('terms', 'en') . " | " . url('terms', 'ar') . "\n";
    echo "- Full LLM Context: " . SITE_URL . "/llms-full.txt\n";
    exit;
}
if ($route === '.well-known/ai-catalog.json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'schema_version' => '1.0',
        'name_for_human' => SITE_NAME . ' (' . SITE_NAME_AR . ')',
        'name_for_model' => 'mrfixksa_appliance_repair',
        'description_for_human' => 'Same-day AC, refrigerator, washing machine, and oven repair in Dammam, Al Khobar, and Dhahran.',
        'description_for_model' => 'Official AI discovery catalog for Mr Fix KSA (مستر فيكس), providing certified home appliance repair (AC, refrigerator, washing machine, gas/electric oven) across Dammam, Al Khobar, and Dhahran in Saudi Arabia. Inspection fee is 50 SAR (waived on repair), with a 30-90 day warranty.',
        'website' => SITE_URL,
        'llms_txt' => SITE_URL . '/llms.txt',
        'llms_full_txt' => SITE_URL . '/llms-full.txt',
        'sitemap' => SITE_URL . '/sitemap.xml',
        'contact' => [
            'phone' => formatPhone(PHONE),
            'whatsapp' => 'https://wa.me/' . waNumber(),
            'email' => EMAIL,
            'booking_url_en' => url('contact', 'en'),
            'booking_url_ar' => url('contact', 'ar'),
        ],
        'webmcp_tools' => [
            [
                'name' => 'book_appliance_repair',
                'description' => 'Book a certified appliance repair technician in Dammam, Al Khobar, or Dhahran',
                'page_url' => url('contact', 'en'),
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
if ($route === 'indexnow-key.txt' || $route === indexNowKey() . '.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo indexNowKey();
    exit;
}
if ($route === 'site.webmanifest') {
    header('Content-Type: application/manifest+json; charset=utf-8');
    $base = parse_url(SITE_URL, PHP_URL_PATH) ?: '';
    echo json_encode([
        'name' => SITE_NAME . ' — ' . __('site_tagline'),
        'short_name' => SITE_NAME,
        'description' => trim(setting('business_description', $lang)),
        'start_url' => $base . '/' . $lang,
        'display' => 'browser',
        'background_color' => '#FFFFFF',
        'theme_color' => '#FF7A00',
        'icons' => [
            ['src' => $base . '/assets/images/favicon.png', 'sizes' => '64x64', 'type' => 'image/png'],
            ['src' => $base . '/assets/images/apple-touch-icon.png', 'sizes' => '180x180', 'type' => 'image/png'],
            ['src' => $base . '/assets/images/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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

    // Also limit by internet address (stops bots that drop cookies): max 10 per hour
    try {
        $ip_check = $pdo->prepare("SELECT COUNT(*) FROM contact_submissions WHERE ip_address = :ip AND submitted_at > (NOW() - INTERVAL 1 HOUR)");
        $ip_check->execute([':ip' => substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45)]);
        if ((int)$ip_check->fetchColumn() >= 10) {
            echo json_encode(['success' => false, 'message' => __('contact_form_too_many')]);
            exit;
        }
    } catch (PDOException $e) {
        error_log('Contact rate check failed: ' . $e->getMessage());
    }

    // Validate required fields
    $name = sanitize(mb_substr($_POST['name'] ?? '', 0, 100));
    $phone = sanitize(mb_substr($_POST['phone'] ?? '', 0, 20));
    $email = sanitize(mb_substr($_POST['email'] ?? '', 0, 100));
    $service = sanitize($_POST['service'] ?? '');
    $message = sanitize(mb_substr($_POST['message'] ?? '', 0, 2000));
    $address = sanitize(mb_substr($_POST['address'] ?? '', 0, 255));

    if (!in_array($service, ['', 'ac', 'fridge', 'washer', 'oven', 'other'], true)) {
        $service = 'other';
    }

    // City must be one of the cities we serve (or empty)
    $city = '';
    $city_key = $_POST['city'] ?? '';
    if ($city_key !== '' && isset(getAreas()[$city_key])) {
        $city = getAreas()[$city_key]['name_en'];
    }

    // Preferred date: today or later (within 3 months)
    $preferred_date = null;
    $date_in = $_POST['preferred_date'] ?? '';
    if ($date_in !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_in)) {
        $d = DateTime::createFromFormat('Y-m-d', $date_in);
        $today = new DateTime('today');
        if ($d && $d >= $today && $d <= (clone $today)->modify('+3 months')) {
            $preferred_date = $d->format('Y-m-d');
        }
    }
    $preferred_time = in_array($_POST['preferred_time'] ?? '', ['morning', 'afternoon', 'evening'], true) ? $_POST['preferred_time'] : '';

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
        $stmt = $pdo->prepare("INSERT INTO contact_submissions
            (customer_name, phone, email, service_requested, city, address, preferred_date, preferred_time, message, lang, ip_address)
            VALUES (:name, :phone, :email, :service, :city, :address, :pdate, :ptime, :message, :lang, :ip)");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':service' => $service,
            ':city' => $city,
            ':address' => $address,
            ':pdate' => $preferred_date,
            ':ptime' => $preferred_time,
            ':message' => $message,
            ':lang' => $lang,
            ':ip' => substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
        ]);
        $lead_id = (int)$pdo->lastInsertId();

        $_SESSION['contact_times'][] = $now;

        // Answer the visitor right away, then send the emails in the background
        respondAndContinue(json_encode(['success' => true, 'message' => __('contact_form_success')]));

        // Email: alert to you + "Thank you" to the customer (never blocks the form if email fails)
        try {
            $lead = $pdo->query("SELECT * FROM contact_submissions WHERE id = " . $lead_id)->fetch();
            if ($lead) {
                sendContactEmails($lead);
            }
        } catch (Throwable $mail_error) {
            error_log('Contact email error: ' . $mail_error->getMessage());
        }
        exit;
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
    if ($admin_page !== 'auth' && !str_starts_with(basename($admin_page), '_') && file_exists($admin_file)) {
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
        // Homepage: send visitors to their language (Admin → Settings → Default website language)
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

    // Title/description from Admin → Settings → Google Titles; empty fields use the built-in text
    $page_seo = getPageSEO($page) ?: [];
    foreach (['title' => 'seo_title_', 'description' => 'seo_desc_'] as $field => $prefix) {
        if (trim($page_seo[$field . '_' . $lang] ?? '') === '') {
            $fallback = __($prefix . $page, '');
            $page_seo[$field . '_' . $lang] = $fallback === $prefix . $page ? '' : $fallback;
        }
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
// Speed: let the hosting (LiteSpeed Cache) keep a copy of normal pages for 5 minutes.
// Not cached: the contact page (security token), 404s, admin, forms.
// Saving anything in the admin panel clears this cache.
// ============================================
if (!NEEDS_SESSION && !$is_404 && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    header('X-LiteSpeed-Cache-Control: public,max-age=300');
    header('Cache-Control: public, max-age=0, must-revalidate');
} else {
    header('X-LiteSpeed-Cache-Control: no-cache');
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
