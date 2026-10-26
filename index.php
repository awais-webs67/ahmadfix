<?php
/**
 * Ahmad Fix — Front Controller / Router
 * All requests are routed through this file
 */

define('FIXORA_APP', true);

// Get the route from the URL
$route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$segments = $route ? explode('/', $route) : [];

// ============================================
// Language Detection from URL
// ============================================
$url_lang = null;
$supported_langs = ['en', 'ar'];

if (!empty($segments[0]) && in_array($segments[0], $supported_langs)) {
    $url_lang = $segments[0];
    array_shift($segments); // Remove language prefix
}

// ============================================
// Load Configuration (sets $lang, $pdo, etc.)
// ============================================
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/mail.php';

// If no language in URL, redirect to default language
if ($url_lang === null && !str_starts_with($route, 'admin') && !str_starts_with($route, 'api')) {
    header('Location: ' . SITE_URL . '/' . $lang);
    exit;
}

// ============================================
// Determine the page to load
// ============================================
$page = !empty($segments) ? implode('/', $segments) : 'home';

// ============================================
// API Routes (for AJAX)
// ============================================
if ($page === 'api/contact-submit' || ($route === 'api/contact-submit')) {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }
    
    // Validate CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRF($token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
        exit;
    }
    
    // Validate required fields
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $service = sanitize($_POST['service'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => __('contact_form_error')]);
        exit;
    }
    
    // Validate phone format (Saudi)
    if (!preg_match('/^(\+966|966|05|5)[0-9]{8,9}$/', preg_replace('/[\s\-]/', '', $phone))) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid Saudi phone number.']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_submissions (customer_name, phone, email, service_requested, message) VALUES (:name, :phone, :email, :service, :message)");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':service' => $service,
            ':message' => $message
        ]);
        
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
if (str_starts_with($route, 'admin') || str_starts_with($page, 'admin')) {
    $admin_page = str_replace('admin/', '', $route);
    $admin_page = str_replace('admin', '', $admin_page);
    $admin_page = $admin_page ?: 'index';
    
    $admin_file = __DIR__ . '/admin/' . basename($admin_page) . '.php';
    if (file_exists($admin_file)) {
        require_once $admin_file;
    } else {
        require_once __DIR__ . '/admin/index.php';
    }
    exit;
}

// ============================================
// Page Routing
// ============================================
$page_map = [
    'home' => 'pages/home.php',
    '' => 'pages/home.php',
    'about' => 'pages/about.php',
    'من-نحن' => 'pages/about.php',
    'contact' => 'pages/contact.php',
    'اتصل-بنا' => 'pages/contact.php',
];

// Check for service pages
$service_slugs = [];
$services_data = getServices();
foreach ($services_data as $svc) {
    $service_slugs[$svc['slug_en']] = $svc;
    $service_slugs[$svc['slug_ar']] = $svc;
}

if (isset($page_map[$page])) {
    // Regular page
    $current_page = $page === '' ? 'home' : $page;
    $page_file = __DIR__ . '/' . $page_map[$page];
} elseif (isset($service_slugs[$page])) {
    // Service page
    $current_page = $page;
    $current_service = $service_slugs[$page];
    $page_file = __DIR__ . '/pages/service-detail.php';
} else {
    // 404
    http_response_code(404);
    $current_page = '404';
    $page_file = __DIR__ . '/pages/404.php';
}

// ============================================
// Load page SEO data
// ============================================
$page_seo_slug = $current_page;
// Map Arabic slugs back to SEO keys
$seo_slug_map = ['من-نحن' => 'about', 'اتصل-بنا' => 'contact'];
if (isset($seo_slug_map[$page_seo_slug])) {
    $page_seo_slug = $seo_slug_map[$page_seo_slug];
}
$page_seo = getPageSEO($page_seo_slug);

// For service pages, use service-specific SEO
if (isset($current_service)) {
    $page_seo = [
        'title_en' => $current_service['meta_title_en'],
        'title_ar' => $current_service['meta_title_ar'],
        'description_en' => $current_service['meta_desc_en'],
        'description_ar' => $current_service['meta_desc_ar'],
        'keywords_en' => $current_service['meta_keywords_en'],
        'keywords_ar' => $current_service['meta_keywords_ar'],
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
