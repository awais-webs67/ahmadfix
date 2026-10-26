<?php
/**
 * Ahmad Fix — Contact Page
 */
?>

<!-- ====== Page Header ====== -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo url(); ?>"><?php echo __('nav_home'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span aria-current="page"><?php echo __('nav_contact'); ?></span>
        </nav>
        <h1><?php echo __('contact_title'); ?></h1>
        <p><?php echo __('contact_subtitle'); ?></p>
    </div>
</section>

<!-- ====== Contact Content ====== -->
<section class="section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form reveal-left" id="contact-section">
                <h2 style="font-size: var(--text-2xl); font-weight: 800; margin-bottom: var(--space-6);">
                    <?php echo $lang === 'ar' ? 'أرسل لنا رسالة' : 'Send Us a Message'; ?>
                </h2>
                
                <div class="form-message" id="form-message"></div>
                
                <form id="contact-form" data-msg-required="<?php echo e(__('contact_form_required')); ?>" data-msg-network="<?php echo e(__('contact_form_network')); ?>" method="POST" action="<?php echo SITE_URL; ?>/api/contact-submit" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                    <!-- Spam trap: hidden from people, bots fill it in -->
                    <div class="form-hp" aria-hidden="true">
                        <label for="contact-website"><?php echo __('contact_form_honeypot'); ?></label>
                        <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-name"><?php echo __('contact_form_name'); ?> *</label>
                        <input type="text" id="contact-name" name="name" class="form-control" autocomplete="name" maxlength="100" placeholder="<?php echo $lang === 'ar' ? 'أدخل اسمك' : 'Enter your name'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-phone"><?php echo __('contact_form_phone'); ?> *</label>
                        <input type="tel" id="contact-phone" name="phone" class="form-control" autocomplete="tel" inputmode="tel" maxlength="20" placeholder="<?php echo $lang === 'ar' ? '05xxxxxxxx' : '05xxxxxxxx'; ?>" required dir="ltr">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-email"><?php echo __('contact_form_email'); ?></label>
                        <input type="email" id="contact-email" name="email" class="form-control" autocomplete="email" maxlength="100" placeholder="<?php echo $lang === 'ar' ? 'بريدك الإلكتروني' : 'your@email.com'; ?>" dir="ltr">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-service"><?php echo __('contact_form_service'); ?></label>
                        <select id="contact-service" name="service" class="form-control">
                            <option value=""><?php echo $lang === 'ar' ? '— اختر الخدمة —' : '— Select a Service —'; ?></option>
                            <option value="ac"><?php echo __('contact_select_ac'); ?></option>
                            <option value="fridge"><?php echo __('contact_select_fridge'); ?></option>
                            <option value="washer"><?php echo __('contact_select_washer'); ?></option>
                            <option value="oven"><?php echo __('contact_select_oven'); ?></option>
                            <option value="other"><?php echo __('contact_select_other'); ?></option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-message"><?php echo __('contact_form_message'); ?></label>
                        <textarea id="contact-message" name="message" class="form-control" maxlength="2000" placeholder="<?php echo $lang === 'ar' ? 'اوصف المشكلة التي تواجهها...' : 'Describe the issue you are facing...'; ?>"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        <?php echo __('contact_form_submit'); ?>
                    </button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="reveal-right">
                <div class="contact-info-cards">
                    <!-- Phone -->
                    <a href="tel:<?php echo formatPhone(PHONE); ?>" class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo __('contact_info_phone'); ?></h4>
                            <p dir="ltr"><?php echo e(PHONE); ?></p>
                        </div>
                    </a>
                    
                    <!-- WhatsApp -->
                    <a href="https://wa.me/<?php echo waNumber(); ?>" target="_blank" rel="noopener" class="contact-info-card" style="border-left: 3px solid var(--whatsapp);">
                        <div class="contact-info-icon" style="background: rgba(37, 211, 102, 0.1); color: var(--whatsapp);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo __('contact_info_whatsapp'); ?></h4>
                            <p dir="ltr"><?php echo e(PHONE); ?></p>
                        </div>
                    </a>
                    
                    <!-- Email -->
                    <a href="mailto:<?php echo EMAIL; ?>" class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo __('contact_info_email'); ?></h4>
                            <p><?php echo EMAIL; ?></p>
                        </div>
                    </a>
                    
                    <!-- Address -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo __('contact_info_address'); ?></h4>
                            <p><?php echo setting('address_en', $lang); ?></p>
                        </div>
                    </div>
                    
                    <!-- Working Hours -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h4><?php echo __('contact_info_hours'); ?></h4>
                            <p><?php echo setting('working_hours_en', $lang); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Google Maps -->
                <div class="contact-map">
                    <iframe 
                        src="https://maps.google.com/maps?q=<?php echo rawurlencode('Dammam, Al Khobar, Dhahran, Saudi Arabia'); ?>&amp;ll=26.33,50.12&amp;z=10&amp;hl=<?php echo $lang; ?>&amp;output=embed" 
                        width="100%" 
                        height="300" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="<?php echo $lang === 'ar' ? 'موقعنا على الخريطة' : 'Our Location on Map'; ?>">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>
