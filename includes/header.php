<?php
/**
 * Ahmad Fix — Header Template
 * Top ribbon + sticky navigation
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
    $seo_title = 'Ahmad Fix — Professional Home Appliance Repair in Dammam, Dhahran & Khobar';
}

$ribbon_text = setting('ribbon_text', $lang);
$phone = setting('phone', $lang);
$whatsapp = setting('whatsapp', $lang);

// Determine active page for nav
$current_nav = $current_page ?? 'home';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo sanitize($seo_title); ?></title>
    <meta name="description" content="<?php echo sanitize($seo_desc); ?>">
    <meta name="keywords" content="<?php echo sanitize($seo_keywords); ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Ahmad Fix">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo sanitize($seo_title); ?>">
    <meta property="og:description" content="<?php echo sanitize($seo_desc); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . '/' . $lang . '/' . ($current_page ?? ''); ?>">
    <meta property="og:image" content="<?php echo ASSETS_URL; ?>/images/logo.png">
    <meta property="og:locale" content="<?php echo $lang === 'ar' ? 'ar_SA' : 'en_US'; ?>">
    <meta property="og:site_name" content="Ahmad Fix">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo sanitize($seo_title); ?>">
    <meta name="twitter:description" content="<?php echo sanitize($seo_desc); ?>">
    
    <!-- Hreflang Tags -->
    <link rel="alternate" hreflang="en" href="<?php echo getAlternateUrl($current_nav === 'home' ? '' : $current_nav, 'en'); ?>">
    <link rel="alternate" hreflang="ar" href="<?php echo getAlternateUrl($current_nav === 'home' ? '' : $current_nav, 'ar'); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo getAlternateUrl($current_nav === 'home' ? '' : $current_nav, 'en'); ?>">
    
    <!-- Canonical URL -->
    <?php 
    $canonical_slug = ($current_nav === 'home' ? '' : $current_nav);
    if (isset($current_service)) {
        $canonical_slug = localized($current_service, 'slug');
    }
    ?>
    <link rel="canonical" href="<?php echo SITE_URL . '/' . $lang . '/' . $canonical_slug; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    <link rel="shortcut icon" href="<?php echo ASSETS_URL; ?>/images/favicon.ico">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/animations.css">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Structured Data -->
    <?php echo getLocalBusinessSchema(); ?>
</head>
<body class="page-transition">

<!-- ====== Top Ribbon (Scrolling Ticker) ====== -->
<div class="top-ribbon" role="banner" aria-label="<?php echo $lang === 'ar' ? 'شريط الإعلانات' : 'Announcement Bar'; ?>">
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
    </div>
</div>

<!-- ====== Header / Navigation ====== -->
<header class="header" id="main-header">
    <div class="header-inner">
        <!-- Logo -->
        <a href="<?php echo url(); ?>" class="logo" aria-label="Ahmad Fix Home">
            <img src="<?php echo ASSETS_URL; ?>/images/logo.png" alt="Ahmad Fix" class="logo-img">
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
                <a href="<?php echo getAlternateUrl($current_nav === 'home' ? '' : $current_nav, 'en'); ?>" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
                <a href="<?php echo getAlternateUrl($current_nav === 'home' ? '' : $current_nav, 'ar'); ?>" class="lang-btn <?php echo $lang === 'ar' ? 'active' : ''; ?>">عربي</a>
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
            <img src="<?php echo ASSETS_URL; ?>/images/logo.png" alt="Ahmad Fix" style="height: 38px;">
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

<main>
