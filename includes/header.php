<?php
/**
 * Ahmad Fix — Header Template
 * <head> (SEO, social, structured data) + top ribbon + sticky navigation
 */

// Get current page data
$seo_title = '';
$seo_desc = '';
$seo_keywords = '';

if (isset($page_seo) && $page_seo) {
    $seo_title = localized($page_seo, 'title');
    $seo_desc = localized($page_seo, 'description');
    $seo_keywords = localized($page_seo, 'keywords');
}

if (empty($seo_title)) {
    $seo_title = $lang === 'ar'
        ? 'أحمد فيكس — صيانة الأجهزة المنزلية في الدمام والظهران والخبر'
        : 'Ahmad Fix — Professional Home Appliance Repair in Dammam, Dhahran & Khobar';
}
if (empty($seo_desc)) {
    $seo_desc = __('footer_about');
}

$ribbon_text = setting('ribbon_text', $lang);
$phone = setting('phone', $lang);
$whatsapp = setting('whatsapp', $lang);

// Determine active page for nav
$current_nav = $current_page ?? 'home';
$is_404 = $is_404 ?? false;

// Official address of this page (canonical) and its other-language versions
$has_alternates = !$is_404 && is_array($alt_paths ?? null);
$canonical_url = $has_alternates ? url($alt_paths[$lang], $lang) : null;
$alt_en = $has_alternates ? url($alt_paths['en'], 'en') : url('', 'en');
$alt_ar = $has_alternates ? url($alt_paths['ar'], 'ar') : url('', 'ar');

$og_image = ASSETS_URL . '/images/og-image.jpg';
$fonts_url = $lang === 'ar'
    ? 'https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap'
    : 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang === 'ar' ? 'ar-SA' : 'en'; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title><?php echo e($seo_title); ?></title>
    <meta name="description" content="<?php echo e($seo_desc); ?>">
<?php if ($seo_keywords): ?>
    <meta name="keywords" content="<?php echo e($seo_keywords); ?>">
<?php endif; ?>
    <meta name="robots" content="<?php echo $is_404 ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'; ?>">
    <meta name="author" content="<?php echo e(SITE_NAME); ?>">
    <meta name="theme-color" content="#FF7A00">
    <meta name="format-detection" content="telephone=yes">
    <meta name="geo.region" content="SA-04">
    <meta name="geo.placename" content="<?php echo $lang === 'ar' ? 'الدمام، الخبر، الظهران' : 'Dammam, Al Khobar, Dhahran'; ?>">
    <meta name="geo.position" content="26.4207;50.0888">
    <meta name="ICBM" content="26.4207, 50.0888">
<?php if (GOOGLE_SITE_VERIFICATION): ?>
    <meta name="google-site-verification" content="<?php echo e(GOOGLE_SITE_VERIFICATION); ?>">
<?php endif; ?>
<?php if (BING_SITE_VERIFICATION): ?>
    <meta name="msvalidate.01" content="<?php echo e(BING_SITE_VERIFICATION); ?>">
<?php endif; ?>

<?php if ($canonical_url): ?>
    <!-- Canonical URL (the one official address of this page) -->
    <link rel="canonical" href="<?php echo e($canonical_url); ?>">

    <!-- Hreflang Tags (English / Arabic versions of this page) -->
    <link rel="alternate" hreflang="en" href="<?php echo e($alt_en); ?>">
    <link rel="alternate" hreflang="ar" href="<?php echo e($alt_ar); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo e($alt_en); ?>">
<?php endif; ?>

    <!-- Open Graph (WhatsApp, Facebook, Snapchat link previews) -->
    <meta property="og:title" content="<?php echo e($seo_title); ?>">
    <meta property="og:description" content="<?php echo e($seo_desc); ?>">
    <meta property="og:type" content="website">
<?php if ($canonical_url): ?>
    <meta property="og:url" content="<?php echo e($canonical_url); ?>">
<?php endif; ?>
    <meta property="og:image" content="<?php echo e($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php echo e($lang === 'ar' ? 'أحمد فيكس — صيانة الأجهزة المنزلية' : 'Ahmad Fix — Home Appliance Repair'); ?>">
    <meta property="og:locale" content="<?php echo $lang === 'ar' ? 'ar_SA' : 'en_US'; ?>">
    <meta property="og:locale:alternate" content="<?php echo $lang === 'ar' ? 'en_US' : 'ar_SA'; ?>">
    <meta property="og:site_name" content="<?php echo e($lang === 'ar' ? SITE_NAME_AR : SITE_NAME); ?>">

    <!-- Twitter / X Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($seo_title); ?>">
    <meta name="twitter:description" content="<?php echo e($seo_desc); ?>">
    <meta name="twitter:image" content="<?php echo e($og_image); ?>">

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="<?php echo ASSETS_URL; ?>/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="64x64" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    <link rel="shortcut icon" href="<?php echo ASSETS_URL; ?>/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo ASSETS_URL; ?>/images/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo SITE_URL; ?>/site.webmanifest">

    <!-- Fonts (only the font this language needs) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Loaded without blocking the first paint; text shows in a system font for a moment -->
    <link rel="preload" as="style" href="<?php echo $fonts_url; ?>">
    <link rel="stylesheet" href="<?php echo $fonts_url; ?>" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="<?php echo $fonts_url; ?>"></noscript>

<?php if ($current_nav === 'home' && file_exists(SITE_ROOT . '/assets/images/hero/hero-main-1024.webp')): ?>
    <!-- Load the big homepage photo first (faster first paint) -->
    <link rel="preload" as="image" type="image/webp" imagesrcset="<?php foreach ([400, 640, 800, 1024] as $i => $w) { echo ($i ? ', ' : '') . ASSETS_URL . '/images/hero/hero-main-' . $w . '.webp?v=' . ASSETS_VERSION . ' ' . $w . 'w'; } ?>" imagesizes="(max-width: 768px) 100vw, 600px" fetchpriority="high">
<?php endif; ?>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css?v=<?php echo ASSETS_VERSION; ?>">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/animations.css?v=<?php echo ASSETS_VERSION; ?>">

    <!-- Structured Data (helps Google understand the business) -->
    <?php echo getLocalBusinessSchema(); ?>
    <?php echo getWebsiteSchema(); ?>
<?php if (!$is_404 && !empty($breadcrumbs)): ?>
    <?php echo getBreadcrumbSchema($breadcrumbs); ?>
<?php endif; ?>

<?php if (GA4_ID): ?>
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(GA4_ID); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo e(GA4_ID); ?>');
    </script>
<?php endif; ?>
    <!-- If JavaScript is off, show content that normally animates in -->
    <noscript><style>.reveal,.reveal-left,.reveal-right,.reveal-scale,.stagger-children>*{opacity:1!important;transform:none!important}</style></noscript>
</head>
<body class="page-transition">
<a class="skip-link" href="#main"><?php echo $lang === 'ar' ? 'انتقل إلى المحتوى' : 'Skip to content'; ?></a>

<!-- ====== Top Ribbon (Scrolling Ticker) ====== -->
<div class="top-ribbon" role="region" aria-label="<?php echo $lang === 'ar' ? 'شريط الإعلانات' : 'Announcement Bar'; ?>">
    <div class="ribbon-track">
        <span class="ribbon-text">
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
        </span>
        <span class="ribbon-text" aria-hidden="true">
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
            <?php echo $ribbon_text; ?>
            <span class="ribbon-separator">★</span>
        </span>
    </div>
</div>

<!-- ====== Header / Navigation ====== -->
<header class="header" id="main-header">
    <div class="header-inner">
        <!-- Logo -->
        <a href="<?php echo url(); ?>" class="logo" aria-label="Ahmad Fix Home">
            <?php echo logoPicture(); ?>
        </a>

        <!-- Desktop Navigation -->
        <nav class="desktop-nav">
            <a href="<?php echo url(); ?>" class="nav-link <?php echo $current_nav === 'home' ? 'active' : ''; ?>"><?php echo __('nav_home'); ?></a>
            <a href="<?php echo url(); ?>#services" class="nav-link"><?php echo __('nav_services'); ?></a>
            <a href="<?php echo url('about'); ?>" class="nav-link <?php echo in_array($current_nav, ['about', 'من-نحن']) ? 'active' : ''; ?>"><?php echo __('nav_about'); ?></a>
            <a href="<?php echo url('contact'); ?>" class="nav-link <?php echo in_array($current_nav, ['contact', 'اتصل-بنا']) ? 'active' : ''; ?>"><?php echo __('nav_contact'); ?></a>
        </nav>

        <!-- Header Actions -->
        <div class="header-actions">
            <!-- Language Switcher -->
            <div class="lang-switch">
                <a href="<?php echo e($alt_en); ?>" hreflang="en" lang="en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
                <a href="<?php echo e($alt_ar); ?>" hreflang="ar" lang="ar" class="lang-btn <?php echo $lang === 'ar' ? 'active' : ''; ?>">عربي</a>
            </div>

            <!-- CTA Button -->
            <a href="tel:<?php echo formatPhone(PHONE); ?>" class="header-cta">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <?php echo __('nav_book_now'); ?>
            </a>
            
            <!-- Mobile Menu Toggle (Mobile Only) -->
            <button class="menu-toggle" id="menu-toggle" aria-label="<?php echo $lang === 'ar' ? 'القائمة' : 'Menu'; ?>" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Navigation Backdrop -->
<div class="mobile-nav-backdrop" id="mobile-nav-backdrop"></div>

<!-- Mobile Navigation Drawer -->
<nav class="nav-menu" id="nav-menu" role="navigation" aria-label="<?php echo $lang === 'ar' ? 'القائمة الرئيسية' : 'Main Navigation'; ?>">
    <div class="mobile-nav-header">
        <div class="mobile-nav-brand">
            <?php echo logoPicture('', 'height: 38px; width: auto;'); ?>
        </div>
        <button class="mobile-nav-close" id="mobile-nav-close" aria-label="Close Menu">&times;</button>
    </div>

    <div class="mobile-nav-links">
        <a href="<?php echo url(); ?>" class="nav-link <?php echo $current_nav === 'home' ? 'active' : ''; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            <?php echo __('nav_home'); ?>
        </a>
        <a href="<?php echo url(); ?>#services" class="nav-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.9 6.91a2.12 2.12 0 0 1-3-3l6.91-6.9a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            <?php echo __('nav_services'); ?>
        </a>
        <a href="<?php echo url('about'); ?>" class="nav-link <?php echo in_array($current_nav, ['about', 'من-نحن']) ? 'active' : ''; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <?php echo __('nav_about'); ?>
        </a>
        <a href="<?php echo url('contact'); ?>" class="nav-link <?php echo in_array($current_nav, ['contact', 'اتصل-بنا']) ? 'active' : ''; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <?php echo __('nav_contact'); ?>
        </a>
    </div>

    <div class="mobile-nav-footer">
        <a href="tel:<?php echo formatPhone(PHONE); ?>" class="btn btn-primary btn-lg" style="width:100%;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <?php echo __('hero_cta_call'); ?>
        </a>
    </div>
</nav>

<main id="main" tabindex="-1">
