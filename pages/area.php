<?php
/**
 * Ahmad Fix — City (Service Area) Page
 * One page per city, listing every service. Content lives in config/areas.php
 */

if (!isset($current_area)) {
    header('Location: ' . url());
    exit;
}

$area = $current_area;
$city = $area['name_' . $lang];
$services = getServices();
$faqs = $area['faq_' . $lang] ?? [];
$sep = $lang === 'ar' ? '، ' : ', ';
$arrow = $lang === 'ar' ? '←' : '→';

$service_images = [
    'snowflake' => 'services/ac-repair.jpg',
    'thermometer' => 'services/fridge-repair.jpg',
    'rotate-cw' => 'services/washing-machine-repair.jpg',
    'flame' => 'services/oven-repair.jpg',
];

$why_keys = ['why_certified', 'why_sameday', 'why_parts', 'why_warranty', 'why_price'];
?>

<!-- Structured data: city FAQ -->
<?php if (!empty($faqs)): ?>
    <?php echo getFAQSchema($faqs); ?>
<?php endif; ?>

<!-- ====== Area Hero ====== -->
<section class="service-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo url(); ?>"><?php echo __('nav_home'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?php echo url(); ?>#areas"><?php echo __('areas_title'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span aria-current="page"><?php echo e($city); ?></span>
        </nav>
        <h1><?php echo e($area['h1_' . $lang]); ?></h1>
        <p><?php echo e($area['lead_' . $lang]); ?></p>
    </div>
</section>

<!-- ====== Area Content ====== -->
<section class="service-detail-content">
    <div class="container">
        <div class="service-detail-grid">
            <!-- Main Content -->
            <div class="service-main-content reveal">
                <?php foreach ($area['intro_' . $lang] as $paragraph): ?>
                <p><?php echo e($paragraph); ?></p>
                <?php endforeach; ?>

                <!-- All services in this city -->
                <h2><?php echo e(sprintf(__('area_services_title'), $city)); ?></h2>
                <p><?php echo e(sprintf(__('area_services_text'), $city)); ?></p>
                <div class="area-service-grid">
                    <?php foreach ($services as $svc):
                        $svc_name = localized($svc, 'name');
                        $img = $service_images[$svc['icon']] ?? 'services/ac-repair.jpg';
                    ?>
                    <a class="area-service-card" href="<?php echo url(localized($svc, 'slug')); ?>">
                        <?php echo picture($img, [
                            'alt' => sprintf(__('service_card_alt'), $svc_name, $city),
                            'width' => 300, 'height' => 180, 'loading' => 'lazy', 'decoding' => 'async',
                            'sizes' => '(max-width: 768px) 100vw, 340px',
                        ]); ?>
                        <span class="area-service-body">
                            <strong><?php echo e(sprintf(__('area_service_link'), $svc_name, $city)); ?></strong>
                            <span><?php echo e(strip_tags(localized($svc, 'short_desc'))); ?></span>
                            <span class="service-link"><?php echo __('service_learn_more'); ?> <span aria-hidden="true"><?php echo $arrow; ?></span></span>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Districts -->
                <h2><?php echo e(sprintf(__('area_districts_title'), $city)); ?></h2>
                <ul class="area-chips">
                    <?php foreach ($area['districts_' . $lang] as $district): ?>
                    <li><?php echo e($district); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p class="area-note"><?php echo e(sprintf(__('area_districts_note'), $city)); ?></p>

                <!-- Why us -->
                <h2><?php echo e(sprintf(__('area_why_title'), $city)); ?></h2>
                <ul>
                    <?php foreach ($why_keys as $k): ?>
                    <li><strong><?php echo __($k); ?></strong> — <?php echo __($k . '_desc'); ?></li>
                    <?php endforeach; ?>
                </ul>

                <!-- Map of the city -->
                <div class="contact-map area-map">
                    <iframe
                        src="https://maps.google.com/maps?q=<?php echo rawurlencode($area['name_en'] . ', Saudi Arabia'); ?>&amp;ll=<?php echo $area['lat']; ?>,<?php echo $area['lng']; ?>&amp;z=12&amp;hl=<?php echo $lang; ?>&amp;output=embed"
                        width="100%"
                        height="300"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="<?php echo e(sprintf(__('area_map_title'), $city)); ?>">
                    </iframe>
                </div>

                <!-- FAQ -->
                <?php if (!empty($faqs)): ?>
                <h2><?php echo __('faq_title'); ?></h2>
                <div class="faq-list">
                    <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo e($faq['q']); ?></span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="faq-answer" role="region">
                            <div class="faq-answer-inner">
                                <?php echo e($faq['a']); ?>
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
                    <p><?php echo e(sprintf(__('area_book_text'), $city)); ?></p>
                    <a href="tel:<?php echo formatPhone(PHONE); ?>" class="btn btn-white" style="width: 100%; margin-bottom: 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <?php echo __('hero_cta_call'); ?>
                    </a>
                    <a href="https://wa.me/<?php echo waNumber(); ?>?text=<?php echo rawurlencode($lang === 'ar' ? 'مرحباً، أحتاج خدمة صيانة في ' . $city : 'Hello, I need a repair service in ' . $city); ?>" class="btn btn-whatsapp" style="width: 100%;" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <?php echo __('hero_cta_whatsapp'); ?>
                    </a>
                    <a href="<?php echo url('contact'); ?>?city=<?php echo e($area['key']); ?>#contact-section" class="btn btn-ghost-light" style="width: 100%; margin-top: 10px;">
                        <?php echo __('book_online'); ?>
                    </a>
                </div>

                <!-- Services list -->
                <div class="sidebar-card">
                    <h3><?php echo __('footer_services'); ?></h3>
                    <div class="footer-links">
                        <?php foreach ($services as $s): ?>
                        <a href="<?php echo url(localized($s, 'slug')); ?>" style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                            <?php echo e(localized($s, 'name')); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Other cities -->
                <div class="sidebar-card">
                    <h3><?php echo __('area_other_cities'); ?></h3>
                    <div class="footer-links">
                        <?php foreach (getAreas() as $other):
                            if ($other['slug_en'] === $area['slug_en']) continue; ?>
                        <a href="<?php echo url($other['slug_' . $lang]); ?>" style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                            <?php echo e(sprintf(__('area_link_label'), $other['name_' . $lang])); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Brands -->
                <div class="sidebar-card">
                    <h3><?php echo __('brands_we_service'); ?></h3>
                    <p style="font-size: var(--text-sm); color: var(--gray-600); line-height: 1.8;">
                        <?php echo e(brandsList()); ?>
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
                    <?php echo __('cta_call'); ?>
                </a>
                <a href="https://wa.me/<?php echo waNumber(); ?>" class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener">
                    <?php echo __('cta_whatsapp'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
