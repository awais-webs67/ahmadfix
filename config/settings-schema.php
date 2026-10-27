<?php
/**
 * Ahmad Fix — Every setting you can change from Admin → Settings
 *
 * Each field is stored in the site_settings table. "bilingual" fields have an
 * English and an Arabic value. The defaults are only used the first time
 * (the database upgrade copies them in); after that the admin panel is the source of truth.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

return [
    'business' => [
        'title' => 'Business Information',
        'icon'  => '🏢',
        'fields' => [
            'site_name'     => ['label' => 'Business name', 'type' => 'text', 'bilingual' => true, 'default' => ['Ahmad Fix', 'أحمد فيكس']],
            'site_url'      => ['label' => 'Website address', 'type' => 'url', 'default' => '',
                                'help' => 'Your live address, e.g. https://ahmadfix.com — Google, the sitemap and emails use it. Visitors on http:// or www. are redirected to it. Leave empty to use whatever address the visitor typed.'],
            'phone'         => ['label' => 'Phone number (shown on site)', 'type' => 'text', 'default' => '+966 58 245 3440'],
            'whatsapp'      => ['label' => 'WhatsApp number', 'type' => 'text', 'default' => '+966582453440', 'help' => 'With country code, e.g. +966582453440'],
            'email'         => ['label' => 'Business email (shown on site)', 'type' => 'email', 'default' => 'ahmadarian538@gmail.com'],
            'address_en'    => ['label' => 'Address / service area', 'type' => 'text', 'bilingual' => true,
                                'default' => ['Dammam, Dhahran, Al Khobar - Eastern Province, Saudi Arabia', 'الدمام، الظهران، الخبر - المنطقة الشرقية، المملكة العربية السعودية']],
            'business_description' => ['label' => 'Short business description (for Google)', 'type' => 'textarea', 'bilingual' => true,
                                'default' => ['Professional AC, refrigerator, washing machine and oven repair in Dammam, Al Khobar and Dhahran. Certified technicians and same-day service.',
                                              'صيانة وإصلاح المكيفات والثلاجات والغسالات والأفران في الدمام والخبر والظهران. فنيون معتمدون وخدمة في نفس اليوم.']],
            'ribbon_text'   => ['label' => 'Top scrolling announcement', 'type' => 'textarea', 'bilingual' => true, 'default' => ['', '']],
            'brands'        => ['label' => 'Brands you repair (comma separated)', 'type' => 'textarea',
                                'default' => 'Samsung, LG, Carrier, Daikin, Gree, Midea, Haier, Hitachi, Toshiba, Whirlpool, Bosch, Siemens, Panasonic, Sharp, General Electric'],
            'price_range'   => ['label' => 'Price range (for Google)', 'type' => 'select', 'options' => ['$' => '$ (cheap)', '$$' => '$$ (moderate)', '$$$' => '$$$ (premium)'], 'default' => '$$'],
        ],
    ],

    'hours' => [
        'title' => 'Working Hours & Location',
        'icon'  => '🕒',
        'fields' => [
            'working_hours_en'     => ['label' => 'Working hours (text shown on site)', 'type' => 'text', 'bilingual' => true,
                                       'default' => ['Saturday - Thursday: 8:00 AM - 10:00 PM | Friday: 2:00 PM - 10:00 PM', 'السبت - الخميس: 8:00 ص - 10:00 م | الجمعة: 2:00 م - 10:00 م']],
            'hours_week_open'      => ['label' => 'Saturday–Thursday opens', 'type' => 'time', 'default' => '08:00'],
            'hours_week_close'     => ['label' => 'Saturday–Thursday closes', 'type' => 'time', 'default' => '22:00'],
            'hours_friday_open'    => ['label' => 'Friday opens (empty = closed)', 'type' => 'time', 'default' => '14:00'],
            'hours_friday_close'   => ['label' => 'Friday closes', 'type' => 'time', 'default' => '22:00'],
            'maps_embed_lat'       => ['label' => 'Map latitude (business location)', 'type' => 'text', 'default' => '26.4207', 'help' => 'Right-click your location in Google Maps to copy the numbers.'],
            'maps_embed_lng'       => ['label' => 'Map longitude', 'type' => 'text', 'default' => '50.0888'],
            'google_maps_url'      => ['label' => 'Google Maps link', 'type' => 'url', 'default' => 'https://www.google.com/maps/search/?api=1&query=Dammam%2C+Al+Khobar%2C+Dhahran'],
        ],
    ],

    'seo' => [
        'title' => 'Google & Social',
        'icon'  => '📈',
        'fields' => [
            'ga4_id'                   => ['label' => 'Google Analytics 4 ID', 'type' => 'text', 'default' => '', 'placeholder' => 'G-XXXXXXXXXX'],
            'google_site_verification' => ['label' => 'Google Search Console code', 'type' => 'text', 'default' => '', 'help' => 'Only the content="..." value from the HTML-tag method.'],
            'bing_site_verification'   => ['label' => 'Bing Webmaster code', 'type' => 'text', 'default' => ''],
            'google_business_url'      => ['label' => 'Google Business Profile link', 'type' => 'url', 'default' => '', 'placeholder' => 'https://maps.app.goo.gl/...'],
            'google_review_url'        => ['label' => '"Write a review" link (Google)', 'type' => 'url', 'default' => '', 'help' => 'From Google Business Profile → "Ask for reviews". Shown in emails after a repair.'],
            'social_instagram'         => ['label' => 'Instagram', 'type' => 'url', 'default' => ''],
            'social_snapchat'          => ['label' => 'Snapchat', 'type' => 'url', 'default' => ''],
            'social_tiktok'            => ['label' => 'TikTok', 'type' => 'url', 'default' => ''],
            'social_facebook'          => ['label' => 'Facebook', 'type' => 'url', 'default' => ''],
            'social_x'                 => ['label' => 'X (Twitter)', 'type' => 'url', 'default' => ''],
            'social_youtube'           => ['label' => 'YouTube', 'type' => 'url', 'default' => ''],
        ],
    ],

    'email' => [
        'title' => 'Email (SMTP)',
        'icon'  => '✉️',
        'fields' => [
            'smtp_host'       => ['label' => 'SMTP server', 'type' => 'text', 'default' => 'smtp.gmail.com', 'help' => 'Gmail: smtp.gmail.com · Outlook: smtp.office365.com · Your hosting: usually mail.yourdomain.com'],
            'smtp_port'       => ['label' => 'Port', 'type' => 'number', 'default' => '587'],
            'smtp_secure'     => ['label' => 'Security', 'type' => 'select', 'options' => ['tls' => 'TLS (port 587)', 'ssl' => 'SSL (port 465)', 'none' => 'None (not recommended)'], 'default' => 'tls'],
            'smtp_user'       => ['label' => 'SMTP username (email address)', 'type' => 'text', 'default' => ''],
            'smtp_pass'       => ['label' => 'SMTP password / Gmail app password', 'type' => 'password', 'default' => '',
                                  'help' => 'Gmail: Google Account → Security → 2-Step Verification → App passwords. Stored encrypted.'],
            'smtp_from_email' => ['label' => 'Send emails from (address)', 'type' => 'email', 'default' => '', 'help' => 'Empty = same as SMTP username. Gmail only allows your own address.'],
            'smtp_from_name'  => ['label' => 'Send emails from (name)', 'type' => 'text', 'default' => 'Ahmad Fix'],
            'notify_emails'   => ['label' => 'Send new-lead alerts to', 'type' => 'text', 'default' => 'ahmadarian538@gmail.com', 'help' => 'One or more addresses, separated by commas.'],
            'auto_reply_enabled' => ['label' => 'Send automatic "Thank you" email to customers', 'type' => 'toggle', 'default' => '1'],
        ],
    ],

    'templates' => [
        'title' => 'Email Texts',
        'icon'  => '📝',
        'help'  => 'You can use these placeholders: {name} {service} {city} {date} {time} {phone} {whatsapp} {site_name} {email}',
        'fields' => [
            'auto_reply_subject' => ['label' => 'Automatic reply — subject', 'type' => 'text', 'bilingual' => true,
                'default' => ['We received your request — {site_name}', 'تم استلام طلبك — {site_name}']],
            'auto_reply_message' => ['label' => 'Automatic reply — message', 'type' => 'textarea', 'rows' => 7, 'bilingual' => true,
                'default' => [
                    "Hello {name},\n\nThank you for contacting {site_name}! We have received your request for {service} and one of our team will call you shortly to confirm your appointment.\n\nNeed help right away? Call or WhatsApp us on {phone}.",
                    "مرحباً {name}،\n\nشكراً لتواصلك مع {site_name}! لقد استلمنا طلبك لخدمة {service} وسيتصل بك أحد أعضاء فريقنا قريباً لتأكيد الموعد.\n\nتحتاج مساعدة فورية؟ اتصل بنا أو راسلنا واتساب على {phone}.",
                ]],
            'email_signature' => ['label' => 'Signature (added to every email you send)', 'type' => 'textarea', 'rows' => 4, 'bilingual' => true,
                'default' => ["Best regards,\n{site_name} Team\n{phone}", "مع أطيب التحيات،\nفريق {site_name}\n{phone}"]],
            'reply_tpl_confirm' => ['label' => 'Quick reply: confirm visit', 'type' => 'textarea', 'rows' => 4, 'bilingual' => true,
                'default' => ["Hello {name},\n\nYour {service} visit is confirmed for {date} ({time}). Our technician will call you before arriving.",
                              "مرحباً {name}،\n\nتم تأكيد موعد زيارة {service} يوم {date} ({time}). سيتصل بك الفني قبل الوصول."]],
            'reply_tpl_details' => ['label' => 'Quick reply: need more details', 'type' => 'textarea', 'rows' => 4, 'bilingual' => true,
                'default' => ["Hello {name},\n\nThank you for your request. To send the right technician, could you tell us the brand and model of your appliance and describe the problem?",
                              "مرحباً {name}،\n\nشكراً لطلبك. لإرسال الفني المناسب، هل يمكنك إخبارنا بماركة الجهاز وموديله ووصف المشكلة؟"]],
            'reply_tpl_thanks' => ['label' => 'Quick reply: thank you after repair', 'type' => 'textarea', 'rows' => 4, 'bilingual' => true,
                'default' => ["Hello {name},\n\nThank you for choosing {site_name}! We hope your appliance is working perfectly. If you have a minute, a Google review would help us a lot.",
                              "مرحباً {name}،\n\nشكراً لاختيارك {site_name}! نتمنى أن يعمل جهازك بشكل ممتاز. إذا كان لديك دقيقة، فإن تقييمك على جوجل يساعدنا كثيراً."]],
        ],
    ],
];
