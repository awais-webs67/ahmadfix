<?php
/**
 * Ahmad Fix — About Page
 */

$services = getServices();
$is_ar = ($lang === 'ar');
$arrow = $is_ar ? '←' : '→';

$service_images = [
    'snowflake' => 'services/ac-repair.jpg',
    'thermometer' => 'services/fridge-repair.jpg',
    'rotate-cw' => 'services/washing-machine-repair.jpg',
    'flame' => 'services/oven-repair.jpg',
];

$about_faqs = [];
for ($i = 1; $i <= 4; $i++) {
    $about_faqs[] = ['q' => __('about_faq_q' . $i), 'a' => __('about_faq_a' . $i)];
}

// Small inline icon set (stroke icons, same style as the rest of the site)
$icon = function ($name, $size = 24) {
    $paths = [
        'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
        'tool'    => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.9 6.91a2.12 2.12 0 0 1-3-3l6.91-6.9a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'heart'   => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        'check'   => '<circle cx="12" cy="12" r="10"/><polyline points="8 12 11 15 16 9"/>',
        'clock'   => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'phone'   => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'search'  => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'award'   => '<circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>',
        'pin'     => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'target'  => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
    ];
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
};
?>

<!-- Structured data: this is the About page of the business, plus its FAQ -->
<?php echo jsonLd([
    '@context' => 'https://schema.org',
    '@type' => 'AboutPage',
    'name' => __('about_title'),
    'url' => url('about'),
    'inLanguage' => $is_ar ? 'ar-SA' : 'en-SA',
    'mainEntity' => ['@id' => SITE_URL . '/#business'],
]); ?>
<?php echo getFAQSchema($about_faqs); ?>

<!-- ====== About Hero ====== -->
<section class="about-hero-v2">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo url(); ?>"><?php echo __('nav_home'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span aria-current="page"><?php echo __('nav_about'); ?></span>
        </nav>
        <span class="about-eyebrow"><?php echo $icon('award', 16); ?> <?php echo __('about_hero_badge'); ?></span>
        <h1><?php echo __('about_title'); ?></h1>
        <p class="about-hero-lead"><?php echo __('about_hero_lead'); ?></p>
        <div class="about-hero-actions">
            <a href="tel:<?php echo formatPhone(PHONE); ?>" class="btn btn-primary btn-lg">
                <?php echo $icon('phone', 18); ?> <?php echo __('hero_cta_call'); ?>
            </a>
            <a href="<?php echo url('contact'); ?>" class="btn btn-ghost-light btn-lg"><?php echo __('nav_contact'); ?></a>
        </div>
        <ul class="about-trust">
            <li><?php echo $icon('shield', 18); ?> <?php echo __('why_certified'); ?></li>
            <li><?php echo $icon('clock', 18); ?> <?php echo __('why_sameday'); ?></li>
            <li><?php echo $icon('award', 18); ?> <?php echo __('why_warranty'); ?></li>
        </ul>
    </div>
</section>

<!-- ====== Our Story ====== -->
<section class="section">
    <div class="container">
        <div class="about-story">
            <div class="about-story-media reveal-left">
                <?php echo picture('about/about-team.jpg', [
                    'alt' => $is_ar ? 'فريق أحمد فيكس - فنيون معتمدون في الدمام والخبر والظهران' : 'Ahmad Fix team - certified appliance repair technicians in Dammam, Khobar and Dhahran',
                    'width' => 1024, 'height' => 1024,
                    'loading' => 'lazy', 'decoding' => 'async',
                    'sizes' => '(max-width: 768px) 100vw, 560px',
                ]); ?>
                <div class="about-years-badge">
                    <strong>8+</strong>
                    <span><?php echo __('about_years_badge'); ?></span>
                </div>
            </div>
            <div class="about-story-content reveal-right">
                <span class="section-eyebrow"><?php echo __('about_story_title'); ?></span>
                <h2><?php echo __('about_story_heading'); ?></h2>
                <p><?php echo __('about_story_text'); ?></p>
                <p><?php echo __('about_story_text_2'); ?></p>
                <ul class="about-checklist">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <li><?php echo $icon('check', 20); ?> <?php echo __('about_check_' . $i); ?></li>
                    <?php endfor; ?>
                </ul>
                <div class="about-mission">
                    <div class="about-mission-icon"><?php echo $icon('target', 22); ?></div>
                    <div>
                        <h3><?php echo __('about_mission_title'); ?></h3>
                        <p><?php echo __('about_mission_text'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== Numbers ====== -->
<section class="about-stats" aria-label="<?php echo e(__('about_stats_label')); ?>">
    <div class="container">
        <div class="about-stats-grid">
            <div class="about-stat">
                <span class="about-stat-number counter" data-target="1000">1000+</span>
                <span class="about-stat-label"><?php echo __('hero_stats_customers'); ?></span>
            </div>
            <div class="about-stat">
                <span class="about-stat-number counter" data-target="8">8+</span>
                <span class="about-stat-label"><?php echo __('hero_stats_experience'); ?></span>
            </div>
            <div class="about-stat">
                <span class="about-stat-number"><?php echo count(getAreas()); ?></span>
                <span class="about-stat-label"><?php echo __('about_stat_cities'); ?></span>
            </div>
            <div class="about-stat">
                <span class="about-stat-number counter" data-target="15">15+</span>
                <span class="about-stat-label"><?php echo __('about_stat_brands'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- ====== Our Values ====== -->
<section class="section section-gray">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('about_values_title'); ?></h2>
            <p><?php echo __('about_values_subtitle'); ?></p>
        </div>
        <div class="about-values stagger-children">
            <?php foreach ([1 => 'shield', 2 => 'tool', 3 => 'heart', 4 => 'check'] as $i => $ic): ?>
            <div class="about-value-card">
                <div class="why-icon"><?php echo $icon($ic); ?></div>
                <h3><?php echo __('about_value_' . $i); ?></h3>
                <p><?php echo __('about_value_' . $i . '_desc'); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== How We Work ====== -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('about_process_title'); ?></h2>
            <p><?php echo __('about_process_subtitle'); ?></p>
        </div>
        <ol class="about-process stagger-children">
            <?php foreach ([1 => 'phone', 2 => 'search', 3 => 'tool', 4 => 'shield'] as $i => $ic): ?>
            <li class="about-process-step">
                <span class="about-process-num"><?php echo $i; ?></span>
                <div class="about-process-icon"><?php echo $icon($ic, 22); ?></div>
                <h3><?php echo __('about_step' . $i . '_title'); ?></h3>
                <p><?php echo __('about_step' . $i . '_desc'); ?></p>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- ====== Areas We Serve ====== -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('areas_title'); ?></h2>
            <p><?php echo __('about_areas_subtitle'); ?></p>
        </div>
        <div class="areas-grid stagger-children">
            <?php foreach (getAreas() as $city): ?>
            <a class="area-card about-area-card" href="<?php echo url($city['slug_' . $lang]); ?>">
                <div class="area-icon"><?php echo $icon('pin'); ?></div>
                <h3><?php echo e($city['name_' . $lang]); ?></h3>
                <p><?php echo e(implode($is_ar ? '، ' : ', ', array_slice($city['districts_' . $lang], 0, 4))); ?>…</p>
                <span class="btn btn-outline btn-sm"><?php echo e(sprintf(__('areas_view_page'), $city['name_' . $lang])); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== Services Overview ====== -->
<section class="section section-gray">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('services_title'); ?></h2>
            <p><?php echo __('services_subtitle'); ?></p>
        </div>
        <div class="services-grid stagger-children">
            <?php foreach ($services as $svc):
                $img = $service_images[$svc['icon']] ?? 'services/ac-repair.jpg'; ?>
            <article class="service-card">
                <div class="service-card-image">
                    <?php echo picture($img, [
                        'alt' => sprintf(__('service_card_alt'), localized($svc, 'name'), citiesList()),
                        'width' => 300, 'height' => 180, 'loading' => 'lazy', 'decoding' => 'async',
                        'sizes' => '(max-width: 768px) 100vw, 300px',
                    ]); ?>
                </div>
                <div class="service-card-body">
                    <h3><?php echo e(localized($svc, 'name')); ?></h3>
                    <p><?php echo e(strip_tags(localized($svc, 'short_desc'))); ?></p>
                    <a href="<?php echo url(localized($svc, 'slug')); ?>" class="service-link">
                        <?php echo __('service_learn_more'); ?><span class="sr-only">: <?php echo e(localized($svc, 'name')); ?></span> <span aria-hidden="true"><?php echo $arrow; ?></span>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== About FAQ ====== -->
<section class="section">
    <div class="container about-faq">
        <div class="section-header reveal">
            <h2><?php echo __('faq_title'); ?></h2>
        </div>
        <div class="faq-list">
            <?php foreach ($about_faqs as $faq): ?>
            <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                    <span><?php echo e($faq['q']); ?></span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer" role="region">
                    <div class="faq-answer-inner"><?php echo e($faq['a']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== CTA ====== -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content reveal">
            <h2><?php echo __('cta_title'); ?></h2>
            <p><?php echo __('cta_subtitle'); ?></p>
            <div class="cta-buttons">
                <a href="tel:<?php echo formatPhone(PHONE); ?>" class="btn btn-white btn-lg">
                    <?php echo $icon('phone', 20); ?>
                    <?php echo __('cta_call'); ?>
                </a>
                <a href="https://wa.me/<?php echo waNumber(); ?>" class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener">
                    <?php echo __('cta_whatsapp'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
