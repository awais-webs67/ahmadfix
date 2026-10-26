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

// Get other services for sidebar
$other_services = array_filter($all_services, function($s) use ($service) {
    return $s['id'] !== $service['id'];
});
?>

<!-- FAQ Schema -->
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
            <span><?php echo localized($service, 'name'); ?></span>
        </nav>
        <h1><?php echo localized($service, 'name'); ?></h1>
        <p><?php echo localized($service, 'short_desc'); ?></p>
    </div>
</section>

<!-- ====== Service Detail Content ====== -->
<section class="service-detail-content">
    <div class="container">
        <div class="service-detail-grid">
            <!-- Main Content -->
            <div class="service-main-content reveal">
                <?php echo localized($service, 'full_desc'); ?>
                
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
                    <a href="https://wa.me/<?php echo formatPhone(WHATSAPP); ?>" class="btn btn-whatsapp" style="width: 100%;" target="_blank" rel="noopener">
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
                <a href="https://wa.me/<?php echo formatPhone(WHATSAPP); ?>" class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener">
                    <?php echo __('cta_whatsapp'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
