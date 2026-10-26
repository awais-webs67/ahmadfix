<?php
/**
 * Ahmad Fix — Service Detail Page
 * Displays individual service with full content, FAQ, and sidebar
 */

if (!isset($current_service)) {
    header('Location: ' . url());
    exit;
}

$service = $current_service;
$all_services = getServices();

// Parse FAQ JSON
$faq_key = 'faq_' . $lang;
$faqs = [];
if (!empty($service[$faq_key])) {
    $faqs = json_decode($service[$faq_key], true) ?? [];
}

// Every service is offered in every city — add a FAQ that says so (also shown to Google)
$service_name = localized($service, 'name');
$cities_text = citiesList();
$faqs[] = [
    'q' => sprintf(__('service_faq_cities_q'), $service_name, $cities_text),
    'a' => sprintf(__('service_faq_cities_a'), $service_name, $cities_text),
];

// Service photo
$service_images = [
    'snowflake' => 'services/ac-repair.jpg',
    'thermometer' => 'services/fridge-repair.jpg',
    'rotate-cw' => 'services/washing-machine-repair.jpg',
    'flame' => 'services/oven-repair.jpg',
];
$service_image = $service_images[$service['icon']] ?? null;

// Get other services for sidebar
$other_services = array_filter($all_services, function($s) use ($service) {
    return $s['id'] !== $service['id'];
});
?>

<!-- Structured data: this service + its FAQ -->
<?php echo getServiceSchema($service); ?>
<?php if (!empty($faqs)): ?>
    <?php echo getFAQSchema($faqs); ?>
<?php endif; ?>

<!-- ====== Service Hero ====== -->
<section class="service-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo url(); ?>"><?php echo __('nav_home'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?php echo url(); ?>#services"><?php echo __('nav_services'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span aria-current="page"><?php echo e($service_name); ?></span>
        </nav>
        <h1><?php echo e($service_name); ?> <span class="h1-cities"><?php echo e(sprintf(__('service_h1_suffix'), $cities_text)); ?></span></h1>
        <p><?php echo localized($service, 'short_desc'); ?></p>
    </div>
</section>

<!-- ====== Service Detail Content ====== -->
<section class="service-detail-content">
    <div class="container">
        <div class="service-detail-grid">
            <!-- Main Content -->
            <div class="service-main-content reveal">
                <?php if ($service_image): ?>
                <div class="service-detail-image">
                    <?php echo picture($service_image, [
                        'alt' => sprintf(__('service_card_alt'), $service_name, $cities_text),
                        'width' => 1024, 'height' => 1024,
                        'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async',
                        'sizes' => '(max-width: 1024px) 100vw, 700px',
                    ]); ?>
                </div>
                <?php endif; ?>

                <?php echo localized($service, 'full_desc'); ?>

                <!-- Available in every city -->
                <h2><?php echo e(sprintf(__('service_cities_title'), $service_name, $cities_text)); ?></h2>
                <p><?php echo e(sprintf(__('service_cities_text'), $service_name, $cities_text)); ?></p>
                <div class="service-city-grid">
                    <?php foreach (getAreas() as $city): ?>
                    <a class="service-city-card" href="<?php echo url($city['slug_' . $lang]); ?>">
                        <span class="service-city-name">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?php echo e($city['name_' . $lang]); ?>
                        </span>
                        <span class="service-city-districts"><?php echo e(implode($lang === 'ar' ? '، ' : ', ', array_slice($city['districts_' . $lang], 0, 4))); ?>…</span>
                        <span class="service-link"><?php echo e(sprintf(__('area_link_label'), $city['name_' . $lang])); ?> <span aria-hidden="true"><?php echo $lang === 'ar' ? '←' : '→'; ?></span></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- FAQ Section -->
                <?php if (!empty($faqs)): ?>
                <h2><?php echo __('faq_title'); ?></h2>
                <div class="faq-list">
                    <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo sanitize($faq['q']); ?></span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="faq-answer" role="region">
                            <div class="faq-answer-inner">
                                <?php echo sanitize($faq['a']); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="service-sidebar reveal-right">
                <!-- CTA Card -->
                <div class="sidebar-cta">
                    <h3><?php echo __('service_book'); ?></h3>
                    <p><?php echo $lang === 'ar' ? 'اتصل بنا الآن للحصول على خدمة سريعة واحترافية' : 'Contact us now for fast and professional service'; ?></p>
                    <a href="tel:<?php echo formatPhone(PHONE); ?>" class="btn btn-white" style="width: 100%; margin-bottom: 10px;">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <?php echo __('hero_cta_call'); ?>
                    </a>
                    <a href="https://wa.me/<?php echo waNumber(); ?>" class="btn btn-whatsapp" style="width: 100%;" target="_blank" rel="noopener">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <?php echo __('hero_cta_whatsapp'); ?>
                    </a>
                </div>
                
                <!-- Other Services -->
                <div class="sidebar-card">
                    <h3><?php echo __('related_services'); ?></h3>
                    <div class="footer-links">
                        <?php foreach ($other_services as $s): ?>
                        <a href="<?php echo url(localized($s, 'slug')); ?>" style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                            <?php echo localized($s, 'name'); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Brands -->
                <div class="sidebar-card">
                    <h3><?php echo __('brands_we_service'); ?></h3>
                    <p style="font-size: var(--text-sm); color: var(--gray-600); line-height: 1.8;">
                        Samsung, LG, Carrier, Daikin, Gree, Midea, Haier, Hitachi, Toshiba, Whirlpool, Bosch, Siemens, Panasonic, Sharp, General Electric
                    </p>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- ====== CTA Section ====== -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content reveal">
            <h2><?php echo __('cta_title'); ?></h2>
            <p><?php echo __('cta_subtitle'); ?></p>
            <div class="cta-buttons">
                <a href="tel:<?php echo formatPhone(PHONE); ?>" class="btn btn-white btn-lg">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <?php echo __('cta_call'); ?>
                </a>
                <a href="https://wa.me/<?php echo waNumber(); ?>" class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener">
                    <?php echo __('cta_whatsapp'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
