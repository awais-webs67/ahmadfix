<?php
/**
 * Ahmad Fix — Footer Template
 */

$services = getServices();
?>
</main>

<!-- ====== Footer ====== -->
<footer class="footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <!-- About Column -->
            <div class="footer-about">
                <a href="<?php echo url(); ?>" class="logo" aria-label="<?php echo e(SITE_NAME); ?>">
                    <div class="footer-logo-bg">
                        <?php echo logoPicture(); ?>
                    </div>
                </a>
                <p><?php echo __('footer_about'); ?></p>
                <div class="footer-social">
                    <a href="https://wa.me/<?php echo waNumber(); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <a href="tel:<?php echo formatPhone(PHONE); ?>" aria-label="Phone">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </a>
                    <a href="mailto:<?php echo EMAIL; ?>" aria-label="Email">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </a>
                    <?php
                    $social_svgs = [
                        'google'    => ['Google', '<path d="M21.35 11.1H12v2.98h5.35c-.23 1.4-1.66 4.1-5.35 4.1-3.22 0-5.85-2.67-5.85-5.96S8.78 6.26 12 6.26c1.83 0 3.06.78 3.76 1.45l2.57-2.47C16.68 3.7 14.55 2.75 12 2.75 6.89 2.75 2.75 6.89 2.75 12S6.89 21.25 12 21.25c5.34 0 8.88-3.75 8.88-9.04 0-.6-.07-1.06-.15-1.51z" fill="currentColor"/>'],
                        'instagram' => ['Instagram', '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/>'],
                        'snapchat'  => ['Snapchat', '<path d="M12 3c2.9 0 5 2.2 5 5.2v2.2l1.6-.5c.5-.1.9.4.6.8-.4.6-1.3.9-2 1.2.5 1.6 1.8 3 3.3 3.5.4.1.4.7 0 .9-.7.3-1.6.4-2.2.6-.2.5-.2 1.2-.8 1.2-.8 0-1.6-.4-2.6.1-.9.5-1.6 1.3-2.9 1.3s-2-.8-2.9-1.3c-1-.5-1.8-.1-2.6-.1-.6 0-.6-.7-.8-1.2-.6-.2-1.5-.3-2.2-.6-.4-.2-.4-.8 0-.9 1.5-.5 2.8-1.9 3.3-3.5-.7-.3-1.6-.6-2-1.2-.3-.4.1-.9.6-.8l1.6.5V8.2C7 5.2 9.1 3 12 3z" fill="currentColor"/>'],
                        'tiktok'    => ['TikTok', '<path d="M16.5 3c.3 2.2 1.7 3.7 4 3.9v3.1c-1.5.1-2.8-.4-4-1.2v5.9c0 3.4-2.5 5.8-5.7 5.8S5 18.1 5 15c0-3.3 2.8-5.9 6.4-5.5v3.2c-1.6-.4-3.2.6-3.2 2.3 0 1.4 1.1 2.4 2.5 2.4 1.5 0 2.6-1 2.6-2.9V3h3.2z" fill="currentColor"/>'],
                        'facebook'  => ['Facebook', '<path d="M14 8.5V6.8c0-.8.5-1 1-1h2V2.5h-2.8C11.1 2.5 10.5 4.8 10.5 6.4v2.1H8.5V12h2v9.5H14V12h2.6l.4-3.5H14z" fill="currentColor"/>'],
                        'x'         => ['X', '<path d="M17.8 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5.3 21H2.2l7.3-8.3L1.9 3h6.4l4.4 5.9L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z" fill="currentColor"/>'],
                    ];
                    foreach (socialLinks() as $net => $link):
                        if (!isset($social_svgs[$net])) continue; ?>
                    <a href="<?php echo e($link); ?>" target="_blank" rel="noopener" aria-label="<?php echo e($social_svgs[$net][0]); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><?php echo $social_svgs[$net][1]; ?></svg>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Services Column -->
            <div class="footer-column">
                <h4><?php echo __('footer_services'); ?></h4>
                <div class="footer-links">
                    <?php foreach ($services as $svc): ?>
                    <a href="<?php echo url(localized($svc, 'slug')); ?>">
                        <?php echo localized($svc, 'name'); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Quick Links Column -->
            <div class="footer-column">
                <h4><?php echo __('footer_quick_links'); ?></h4>
                <div class="footer-links">
                    <a href="<?php echo url(); ?>"><?php echo __('nav_home'); ?></a>
                    <a href="<?php echo url('about'); ?>"><?php echo __('nav_about'); ?></a>
                    <a href="<?php echo url('contact'); ?>"><?php echo __('nav_contact'); ?></a>
                    <a href="<?php echo url(); ?>#services"><?php echo __('nav_services'); ?></a>
                    <?php foreach (getAreas() as $footer_area): ?>
                    <a href="<?php echo url($footer_area['slug_' . $lang]); ?>"><?php echo e(sprintf(__('area_link_label'), $footer_area['name_' . $lang])); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Contact Column -->
            <div class="footer-column">
                <h4><?php echo __('footer_contact'); ?></h4>
                <div class="footer-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <a href="tel:<?php echo formatPhone(PHONE); ?>" dir="ltr"><?php echo e(PHONE); ?></a>
                </div>
                <div class="footer-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <a href="mailto:<?php echo EMAIL; ?>"><?php echo EMAIL; ?></a>
                </div>
                <div class="footer-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span><?php echo setting('address_en', $lang); ?></span>
                </div>
                <div class="footer-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><?php echo setting('working_hours_en', $lang); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <span><?php echo __('footer_copyright'); ?></span>
            <div class="footer-bottom-links">
                <a href="<?php echo url('privacy-policy'); ?>"><?php echo __('footer_privacy'); ?></a>
                <a href="<?php echo url('terms'); ?>"><?php echo __('footer_terms'); ?></a>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script src="<?php echo ASSETS_URL; ?>/js/main.js?v=<?php echo ASSETS_VERSION; ?>" defer></script>
</body>
</html>
