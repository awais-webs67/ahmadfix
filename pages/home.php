<?php
/**
 * Ahmad Fix — Homepage
 */

$hero = getHeroContent();
$services = getServices();
$testimonials = getTestimonials();
$areas = getServiceAreas();
?>

<!-- ====== Hero Section ====== -->
<section class="hero" id="hero">
    <div class="container">
        <div class="hero-inner">
            <!-- Hero Content (Left Side) -->
            <div class="hero-content">
                <div class="hero-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <?php echo __('hero_badge'); ?>
                </div>
                
                <h1 class="hero-title">
                    <?php echo $hero ? localized($hero, 'title') : 'Professional Home Appliance <span class="highlight">Repair & Maintenance</span>'; ?>
                </h1>
                
                <p class="hero-subtitle">
                    <?php echo $hero ? localized($hero, 'subtitle') : __('hero_badge'); ?>
                </p>
                
                <div class="hero-buttons">
                    <a href="tel:<?php echo formatPhone(PHONE); ?>" class="btn btn-primary btn-lg">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <?php echo __('hero_cta_call'); ?>
                    </a>
                    <a href="https://wa.me/<?php echo waNumber(); ?>?text=<?php echo urlencode($lang === 'ar' ? 'مرحباً، أحتاج خدمة صيانة' : 'Hello, I need a repair service'); ?>" class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <?php echo __('hero_cta_whatsapp'); ?>
                    </a>
                </div>
                
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-number counter" data-target="1000">1000+</span>
                        <span class="hero-stat-label"><?php echo __('hero_stats_customers'); ?></span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number counter" data-target="8">8+</span>
                        <span class="hero-stat-label"><?php echo __('hero_stats_experience'); ?></span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">⚡</span>
                        <span class="hero-stat-label"><?php echo __('hero_stats_service'); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Hero Image (Right Side) -->
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <?php echo picture('hero/hero-main.jpg', [
                        'alt' => $lang === 'ar' ? 'فني صيانة أجهزة منزلية محترف في الدمام والخبر والظهران' : 'Professional home appliance repair technician in Dammam, Khobar and Dhahran',
                        'width' => 600, 'height' => 500,
                        'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async',
                        'sizes' => '(max-width: 768px) 100vw, 600px',
                    ]); ?>
                </div>
                <div class="hero-image-decorator"></div>
                <div class="hero-image-badge subtle-float">
                    <div class="hero-image-badge-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <div class="hero-image-badge-text"><?php echo $lang === 'ar' ? 'فنيون معتمدون' : 'Certified Experts'; ?></div>
                        <div class="hero-image-badge-sub"><?php echo $lang === 'ar' ? 'خدمة مضمونة' : 'Guaranteed Service'; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== Services Section ====== -->
<section class="section section-gray" id="services">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('services_title'); ?></h2>
            <p><?php echo __('services_subtitle'); ?></p>
        </div>
        
        <div class="services-grid stagger-children">
            <?php 
            $service_icons = [
                'snowflake' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"/><path d="M20 16l-4-4 4-4"/><path d="M4 8l4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/></svg>',
                'thermometer' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>',
                'rotate-cw' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>',
                'flame' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>',
                'tools' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>'
            ];
            
            $service_images = [
                'snowflake' => 'ac-repair.jpg',
                'thermometer' => 'fridge-repair.jpg',
                'rotate-cw' => 'washing-machine-repair.jpg',
                'flame' => 'oven-repair.jpg',
            ];
            
            foreach ($services as $svc): 
                $icon = $service_icons[$svc['icon']] ?? $service_icons['tools'];
                $img_file = $service_images[$svc['icon']] ?? 'ac-repair.jpg';
            ?>
            <article class="service-card" id="service-<?php echo $svc['id']; ?>">
                <div class="service-card-image">
                    <?php echo picture('services/' . $img_file, [
                        'alt' => sprintf(__('service_card_alt'), localized($svc, 'name'), citiesList()),
                        'width' => 300, 'height' => 180,
                        'loading' => 'lazy', 'decoding' => 'async',
                        'sizes' => '(max-width: 768px) 100vw, 300px',
                    ]); ?>
                    <div class="service-icon-overlay"><?php echo $icon; ?></div>
                </div>
                <div class="service-card-body">
                    <h3><?php echo localized($svc, 'name'); ?></h3>
                    <p><?php echo localized($svc, 'short_desc'); ?></p>
                    <a href="<?php echo url(localized($svc, 'slug')); ?>" class="service-link">
                        <?php echo __('service_learn_more'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== Why Choose Us ====== -->
<section class="section" id="why-us">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('why_title'); ?></h2>
            <p><?php echo __('why_subtitle'); ?></p>
        </div>
        
        <div class="why-grid stagger-children">
            <div class="why-card">
                <div class="why-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3><?php echo __('why_certified'); ?></h3>
                <p><?php echo __('why_certified_desc'); ?></p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3><?php echo __('why_sameday'); ?></h3>
                <p><?php echo __('why_sameday_desc'); ?></p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3><?php echo __('why_parts'); ?></h3>
                <p><?php echo __('why_parts_desc'); ?></p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
                <h3><?php echo __('why_warranty'); ?></h3>
                <p><?php echo __('why_warranty_desc'); ?></p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3><?php echo __('why_price'); ?></h3>
                <p><?php echo __('why_price_desc'); ?></p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <h3><?php echo __('why_support'); ?></h3>
                <p><?php echo __('why_support_desc'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ====== How It Works ====== -->
<section class="section section-gray" id="how-it-works">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('how_title'); ?></h2>
            <p><?php echo __('how_subtitle'); ?></p>
        </div>
        
        <div class="steps-grid reveal">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3><?php echo __('how_step1_title'); ?></h3>
                <p><?php echo __('how_step1_desc'); ?></p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3><?php echo __('how_step2_title'); ?></h3>
                <p><?php echo __('how_step2_desc'); ?></p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3><?php echo __('how_step3_title'); ?></h3>
                <p><?php echo __('how_step3_desc'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ====== Service Areas ====== -->
<section class="section section-dark" id="areas">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('areas_title'); ?></h2>
            <p><?php echo __('areas_subtitle'); ?></p>
        </div>
        
        <div class="areas-grid stagger-children">
            <?php foreach ($areas as $area): ?>
            <div class="area-card">
                <div class="area-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <h3><?php echo localized($area, 'name'); ?></h3>
                <p><?php echo localized($area, 'description'); ?></p>
                <?php $area_page = getAreaByName($area['name_en']); ?>
                <?php if ($area_page): ?>
                <a href="<?php echo url($area_page['slug_' . $lang]); ?>" class="btn btn-outline btn-sm">
                    <?php echo e(sprintf(__('areas_view_page'), localized($area, 'name'))); ?>
                </a>
                <?php else: ?>
                <a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode($area['name_en'] . ', Saudi Arabia'); ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                    <?php echo __('areas_view_map'); ?>
                </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ====== Testimonials ====== -->
<?php if (!empty($testimonials)): ?>
<section class="section" id="testimonials">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('testimonials_title'); ?></h2>
            <p><?php echo __('testimonials_subtitle'); ?></p>
        </div>
        
        <div class="testimonials-slider reveal" id="testimonials-slider">
            <div class="testimonials-track" id="testimonials-track">
                <?php foreach ($testimonials as $review): ?>
                <div class="testimonial-card">
                    <div class="testimonial-inner">
                        <div class="testimonial-stars">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                            <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text"><?php echo localized($review, 'review_text'); ?></p>
                        <div class="testimonial-author"><?php echo sanitize($review['customer_name']); ?></div>
                        <div class="testimonial-location"><?php echo sanitize($review['customer_location']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="testimonial-dots" id="testimonial-dots">
                <?php foreach ($testimonials as $index => $review): ?>
                <button class="testimonial-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" aria-label="Testimonial <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ====== CTA Section ====== -->
<section class="cta-section" id="cta">
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
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <?php echo __('cta_whatsapp'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
