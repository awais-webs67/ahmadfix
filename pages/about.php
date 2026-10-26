<?php
/**
 * Ahmad Fix — About Page
 */

$services = getServices();
?>

<!-- ====== About Hero ====== -->
<section class="about-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom: var(--space-6);">
            <a href="<?php echo url(); ?>" style="color: var(--gray-500);"><?php echo __('nav_home'); ?></a>
            <span class="breadcrumb-separator" style="color: var(--gray-400);">/</span>
            <span style="color: var(--primary);"><?php echo __('nav_about'); ?></span>
        </nav>
        <h1><?php echo __('about_title'); ?></h1>
        <p><?php echo __('about_subtitle'); ?></p>
    </div>
</section>

<!-- ====== Our Story ====== -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-content reveal-left">
                <h2><?php echo __('about_story_title'); ?></h2>
                <p><?php echo __('about_story_text'); ?></p>
                
                <h2 style="margin-top: var(--space-10);"><?php echo __('about_mission_title'); ?></h2>
                <p><?php echo __('about_mission_text'); ?></p>
            </div>
            <div class="reveal-right">
                <img src="<?php echo ASSETS_URL; ?>/images/about/about-team.jpg" 
                     alt="<?php echo $lang === 'ar' ? 'فريق أحمد فيكس - فنيون معتمدون' : 'Ahmad Fix Team - Certified Technicians'; ?>" 
                     style="border-radius: var(--radius-xl); box-shadow: var(--shadow-xl); width: 100%; height: 400px; object-fit: cover;"
                     loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- ====== Our Values ====== -->
<section class="section section-gray">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('about_values_title'); ?></h2>
        </div>
        <div class="values-grid stagger-children" style="max-width: 800px; margin: 0 auto;">
            <div class="value-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <?php echo __('about_value_1'); ?>
            </div>
            <div class="value-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                <?php echo __('about_value_2'); ?>
            </div>
            <div class="value-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                <?php echo __('about_value_3'); ?>
            </div>
            <div class="value-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <?php echo __('about_value_4'); ?>
            </div>
        </div>
    </div>
</section>

<!-- ====== Services Overview ====== -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <h2><?php echo __('services_title'); ?></h2>
            <p><?php echo __('services_subtitle'); ?></p>
        </div>
        
        <div class="services-grid stagger-children">
            <?php 
            $service_icons = [
                'snowflake' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"/><path d="M20 16l-4-4 4-4"/><path d="M4 8l4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/></svg>',
                'thermometer' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>',
                'rotate-cw' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>',
                'flame' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>',
                'tools' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>'
            ];
            
            foreach ($services as $svc): 
                $icon = $service_icons[$svc['icon']] ?? $service_icons['tools'];
            ?>
            <article class="service-card">
                <div class="service-icon"><?php echo $icon; ?></div>
                <h3><?php echo localized($svc, 'name'); ?></h3>
                <p><?php echo localized($svc, 'short_desc'); ?></p>
                <a href="<?php echo url(localized($svc, 'slug')); ?>" class="service-link">
                    <?php echo __('service_learn_more'); ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </article>
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
