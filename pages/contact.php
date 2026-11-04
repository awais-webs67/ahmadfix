<?php
/**
 * Mr Fix KSA — Contact / Booking Page
 * ?service=ac|fridge|washer|oven and ?city=dammam|khobar|dhahran pre-fill the form
 */
$pre_service = in_array($_GET['service'] ?? '', ['ac', 'fridge', 'washer', 'oven', 'other'], true) ? $_GET['service'] : '';
$pre_city = isset(getAreas()[$_GET['city'] ?? '']) ? $_GET['city'] : '';
$min_date = date('Y-m-d');
$max_date = date('Y-m-d', strtotime('+3 months'));
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
                <div class="cf-head">
                    <span class="cf-badge"><span class="cf-dot" aria-hidden="true"></span><?php echo __('cf_badge'); ?></span>
                    <h2 class="cf-title"><?php echo __('contact_form_heading'); ?></h2>
                    <p class="cf-note"><?php echo __('contact_form_note'); ?></p>
                </div>

                <div class="form-message" id="form-message" role="status" aria-live="polite"></div>

                <form id="contact-form" data-msg-required="<?php echo e(__('contact_form_required')); ?>" data-msg-network="<?php echo e(__('contact_form_network')); ?>" method="POST" action="<?php echo SITE_URL; ?>/api/contact-submit" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                    <!-- Spam trap: hidden from people, bots fill it in -->
                    <div class="form-hp" aria-hidden="true">
                        <label for="contact-website"><?php echo __('contact_form_honeypot'); ?></label>
                        <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- 1. Appliance -->
                    <fieldset class="cf-step">
                        <legend class="cf-legend"><span class="cf-num">1</span><?php echo __('cf_step_service'); ?></legend>
                        <div class="cf-services">
                            <?php
                            $cf_icons = [
                                'ac'     => '<path d="M12 2v20M4.9 4.9l14.2 14.2M2 12h20M4.9 19.1L19.1 4.9"/><path d="M9 4l3 2 3-2M9 20l3-2 3 2M4 9l2 3-2 3M20 9l-2 3 2 3"/>',
                                'fridge' => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M5 10h14M9 6v1.5M9 13v3"/>',
                                'washer' => '<rect x="3" y="2" width="18" height="20" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M7 6h.01M10 6h.01"/>',
                                'oven'   => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M7 6h.01M11 6h.01M15 6h2"/><rect x="7" y="12" width="10" height="6" rx="1"/>',
                                'other'  => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
                            ];
                            foreach ($cf_icons as $svc_key => $svc_icon): ?>
                            <label class="cf-service">
                                <input type="radio" name="service" value="<?php echo $svc_key; ?>" <?php echo $pre_service === $svc_key ? 'checked' : ''; ?>>
                                <span class="cf-service-box" title="<?php echo e(__('contact_select_' . $svc_key)); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $svc_icon; ?></svg>
                                    <span><?php echo __('cf_svc_' . $svc_key); ?></span>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <!-- 2. Contact details -->
                    <fieldset class="cf-step">
                        <legend class="cf-legend"><span class="cf-num">2</span><?php echo __('cf_step_details'); ?></legend>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-name"><?php echo __('contact_form_name'); ?> <span class="cf-req" aria-hidden="true">*</span></label>
                                <input type="text" id="contact-name" name="name" class="form-control" autocomplete="name" maxlength="100" placeholder="<?php echo $lang === 'ar' ? 'أدخل اسمك' : 'Enter your name'; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="contact-phone"><?php echo __('contact_form_phone'); ?> <span class="cf-req" aria-hidden="true">*</span></label>
                                <input type="tel" id="contact-phone" name="phone" class="form-control" autocomplete="tel" inputmode="tel" maxlength="20" placeholder="05xxxxxxxx" required dir="ltr">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact-email"><?php echo __('contact_form_email'); ?></label>
                            <input type="email" id="contact-email" name="email" class="form-control" autocomplete="email" inputmode="email" maxlength="100" placeholder="<?php echo $lang === 'ar' ? 'بريدك الإلكتروني' : 'your@email.com'; ?>" dir="ltr">
                        </div>
                    </fieldset>

                    <!-- 3. Visit details -->
                    <fieldset class="cf-step">
                        <legend class="cf-legend"><span class="cf-num">3</span><?php echo __('cf_step_visit'); ?></legend>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-city"><?php echo __('contact_form_city'); ?></label>
                                <select id="contact-city" name="city" class="form-control">
                                    <option value=""><?php echo __('contact_form_city_select'); ?></option>
                                    <?php foreach (getAreas() as $city_key => $city_row): ?>
                                    <option value="<?php echo e($city_key); ?>" <?php echo $pre_city === $city_key ? 'selected' : ''; ?>><?php echo e($city_row['name_' . $lang]); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contact-address"><?php echo __('contact_form_address'); ?></label>
                                <input type="text" id="contact-address" name="address" class="form-control" autocomplete="street-address" maxlength="255" placeholder="<?php echo e(__('contact_form_address_ph')); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact-date"><?php echo __('contact_form_date'); ?></label>
                            <input type="date" id="contact-date" name="preferred_date" class="form-control" min="<?php echo $min_date; ?>" max="<?php echo $max_date; ?>">
                        </div>
                        <div class="form-group">
                            <span class="cf-label" id="cf-time-label"><?php echo __('contact_form_time'); ?></span>
                            <div class="cf-times" role="radiogroup" aria-labelledby="cf-time-label">
                                <?php foreach (['' => 'contact_form_time_any', 'morning' => 'time_morning', 'afternoon' => 'time_afternoon', 'evening' => 'time_evening'] as $t_val => $t_key):
                                    $t_text = __($t_key);
                                    $t_hours = '';
                                    if (preg_match('/^(.*?)\s*\((.*)\)$/u', $t_text, $t_m)) { $t_text = $t_m[1]; $t_hours = $t_m[2]; } ?>
                                <label class="cf-time">
                                    <input type="radio" name="preferred_time" value="<?php echo $t_val; ?>" <?php echo $t_val === '' ? 'checked' : ''; ?>>
                                    <span><strong><?php echo e($t_text); ?></strong><?php if ($t_hours): ?><small dir="auto"><?php echo e($t_hours); ?></small><?php endif; ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact-message"><?php echo __('contact_form_message'); ?></label>
                            <textarea id="contact-message" name="message" class="form-control" rows="4" maxlength="2000" placeholder="<?php echo $lang === 'ar' ? 'اوصف المشكلة التي تواجهها...' : 'Describe the issue you are facing...'; ?>"></textarea>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn btn-primary btn-lg cf-submit">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        <?php echo __('contact_form_submit'); ?>
                    </button>
                    <p class="cf-privacy">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span><?php echo __('cf_privacy'); ?> <a href="<?php echo url('privacy-policy'); ?>"><?php echo __('footer_privacy'); ?></a></span>
                    </p>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="reveal-right contact-aside">
                <div class="contact-info-cards">
                    <!-- Phone -->
                    <a href="tel:<?php echo formatPhone(PHONE); ?>" class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h3><?php echo __('contact_info_phone'); ?></h3>
                            <p dir="ltr"><?php echo e(PHONE); ?></p>
                        </div>
                    </a>
                    
                    <!-- WhatsApp -->
                    <a href="https://wa.me/<?php echo waNumber(); ?>" target="_blank" rel="noopener" class="contact-info-card" style="border-left: 3px solid var(--whatsapp);">
                        <div class="contact-info-icon" style="background: rgba(37, 211, 102, 0.1); color: var(--whatsapp);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h3><?php echo __('contact_info_whatsapp'); ?></h3>
                            <p dir="ltr"><?php echo e(PHONE); ?></p>
                        </div>
                    </a>
                    
                    <!-- Email -->
                    <a href="mailto:<?php echo EMAIL; ?>" class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h3><?php echo __('contact_info_email'); ?></h3>
                            <p><?php echo EMAIL; ?></p>
                        </div>
                    </a>
                    
                    <!-- Address -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h3><?php echo __('contact_info_address'); ?></h3>
                            <p><?php echo setting('address_en', $lang); ?></p>
                        </div>
                    </div>
                    
                    <!-- Working Hours -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="contact-info-text">
                            <h3><?php echo __('contact_info_hours'); ?></h3>
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
