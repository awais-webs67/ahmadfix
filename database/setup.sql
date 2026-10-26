-- ============================================
-- Ahmad Fix Database Setup
-- Database: fixora
-- ============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS fixora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fixora;

-- ============================================
-- 1. Admin Users
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. Site Settings (key-value pairs)
-- ============================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value_en TEXT,
    setting_value_ar TEXT,
    setting_type ENUM('text','textarea','image','url','email','phone') DEFAULT 'text',
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. Services
-- ============================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(150) NOT NULL,
    name_ar VARCHAR(150) NOT NULL,
    slug_en VARCHAR(150) NOT NULL UNIQUE,
    slug_ar VARCHAR(150) NOT NULL,
    short_desc_en TEXT,
    short_desc_ar TEXT,
    full_desc_en LONGTEXT,
    full_desc_ar LONGTEXT,
    icon VARCHAR(50) DEFAULT 'tools',
    image VARCHAR(255),
    meta_title_en VARCHAR(200),
    meta_title_ar VARCHAR(200),
    meta_desc_en VARCHAR(300),
    meta_desc_ar VARCHAR(300),
    meta_keywords_en VARCHAR(500),
    meta_keywords_ar VARCHAR(500),
    faq_en JSON,
    faq_ar JSON,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. Hero Content
-- ============================================
CREATE TABLE IF NOT EXISTS hero_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_en VARCHAR(300) NOT NULL,
    title_ar VARCHAR(300) NOT NULL,
    subtitle_en TEXT,
    subtitle_ar TEXT,
    cta_text_en VARCHAR(100),
    cta_text_ar VARCHAR(100),
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. Testimonials
-- ============================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_location VARCHAR(100),
    review_text_en TEXT NOT NULL,
    review_text_ar TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. Contact Submissions
-- ============================================
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    service_requested VARCHAR(100),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. Pages SEO Meta
-- ============================================
CREATE TABLE IF NOT EXISTS pages_seo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_slug VARCHAR(100) NOT NULL UNIQUE,
    title_en VARCHAR(200),
    title_ar VARCHAR(200),
    description_en VARCHAR(300),
    description_ar VARCHAR(300),
    keywords_en VARCHAR(500),
    keywords_ar VARCHAR(500),
    og_image VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. Service Areas
-- ============================================
CREATE TABLE IF NOT EXISTS service_areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100) NOT NULL,
    description_en TEXT,
    description_ar TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. Gallery
-- ============================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_en VARCHAR(150),
    title_ar VARCHAR(150),
    image VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. Admin login attempts (blocks password guessing)
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SEED DATA
-- ============================================

-- Admin user — default password: ahmadfix2024
-- You MUST choose a new password on first login (the default stops working after that).
INSERT INTO admins (username, password, full_name, email) VALUES
('admin', '$2y$10$8K1p/a0dN1xR0k5v8gqXZuPjY3VkVx0L5M4r7XwKjN2yF9FZ3dHCi', 'Ahmad Fix Admin', 'abubakerjamal573@gmail.com');

-- Site Settings
INSERT INTO site_settings (setting_key, setting_value_en, setting_value_ar, setting_type, setting_group) VALUES
('site_name', 'Ahmad Fix', 'أحمد فيكس السعودية', 'text', 'general'),
('phone', '+966 58 245 3440', '+966 58 245 3440', 'phone', 'contact'),
('whatsapp', '+966582453440', '+966582453440', 'phone', 'contact'),
('email', 'ahmadarian538@gmail.com', 'ahmadarian538@gmail.com', 'email', 'contact'),
('address_en', 'Dammam, Dhahran, Al Khobar - Eastern Province, Saudi Arabia', 'الدمام، الظهران، الخبر - المنطقة الشرقية، المملكة العربية السعودية', 'text', 'contact'),
('ribbon_text', '🔧 Expert Home Appliance Repair Services — Dammam, Dhahran & Khobar | Call Now: +966 58 245 3440 | ✅ Certified Technicians | ⚡ Same-Day Service', '🔧 خدمات صيانة الأجهزة المنزلية الاحترافية — الدمام، الظهران والخبر | اتصل الآن: 3440 245 58 966+ | ✅ فنيون معتمدون | ⚡ خدمة في نفس اليوم', 'textarea', 'general'),
('working_hours_en', 'Saturday - Thursday: 8:00 AM - 10:00 PM | Friday: 2:00 PM - 10:00 PM', 'السبت - الخميس: 8:00 ص - 10:00 م | الجمعة: 2:00 م - 10:00 م', 'text', 'general'),
('google_maps_url', 'https://www.google.com/maps/search/?api=1&query=Dammam%2C+Al+Khobar%2C+Dhahran', 'https://www.google.com/maps/search/?api=1&query=%D8%A7%D9%84%D8%AF%D9%85%D8%A7%D9%85', 'url', 'contact'),
('maps_embed_lat', '26.43311719', '26.43311719', 'text', 'contact'),
('maps_embed_lng', '50.0850687', '50.0850687', 'text', 'contact'),
('schema_version', '2', '2', 'text', 'system');

-- Hero Content
INSERT INTO hero_content (title_en, title_ar, subtitle_en, subtitle_ar, cta_text_en, cta_text_ar, is_active) VALUES
('Professional Home Appliance <span class="highlight">Repair & Maintenance</span>', 'صيانة وإصلاح الأجهزة المنزلية <span class="highlight">بأيدي خبراء محترفين</span>', 'Trusted AC, Fridge, Washing Machine & Oven repair services in Dammam, Dhahran & Khobar. Certified technicians, same-day service, and genuine spare parts.', 'خدمات صيانة مكيفات، ثلاجات، غسالات وأفران موثوقة في الدمام، الظهران والخبر. فنيون معتمدون، خدمة في نفس اليوم، وقطع غيار أصلية.', 'Book a Service', 'احجز خدمة الآن', 1);

-- Services
INSERT INTO services (name_en, name_ar, slug_en, slug_ar, short_desc_en, short_desc_ar, full_desc_en, full_desc_ar, icon, meta_title_en, meta_title_ar, meta_desc_en, meta_desc_ar, meta_keywords_en, meta_keywords_ar, faq_en, faq_ar, sort_order) VALUES
(
    'AC Repair & Maintenance',
    'صيانة وإصلاح المكيفات',
    'ac-repair',
    'صيانة-مكيفات',
    'Expert split & central AC repair, gas refilling, deep cleaning, and annual maintenance contracts in Dammam, Dhahran & Khobar.',
    'إصلاح مكيفات سبليت ومركزية، تعبئة فريون، تنظيف عميق، وعقود صيانة سنوية في الدمام والظهران والخبر.',
    '<h2>Professional AC Repair Services in Dammam, Dhahran & Khobar</h2>\n<p>Is your AC not cooling properly? Leaking water? Making unusual noises? Ahmad Fix provides comprehensive AC repair and maintenance services across the Eastern Province. Our certified technicians handle all brands and models of split and central air conditioning systems.</p>\n<h3>Our AC Services Include:</h3>\n<ul>\n<li><strong>AC Not Cooling Repair</strong> — Diagnosis and fix for all cooling issues</li>\n<li><strong>Freon/Gas Refilling</strong> — Professional refrigerant recharging</li>\n<li><strong>Deep Cleaning Service</strong> — Complete internal and external AC cleaning</li>\n<li><strong>Compressor Repair & Replacement</strong> — Expert compressor services</li>\n<li><strong>Thermostat & Control Board Repair</strong> — Electronic component fixes</li>\n<li><strong>Annual Maintenance Contracts (AMC)</strong> — Preventive maintenance plans</li>\n<li><strong>New AC Installation</strong> — Professional setup for all AC types</li>\n</ul>\n<h3>Why Choose Ahmad Fix for AC Repair?</h3>\n<p>With temperatures soaring above 45°C in the Eastern Province, a functioning AC is not a luxury — it''s a necessity. Our same-day service ensures you''re never left in the heat. We use genuine spare parts and offer warranty on all repairs.</p>',
    '<h2>خدمات صيانة المكيفات الاحترافية في الدمام والظهران والخبر</h2>\n<p>هل مكيفك لا يبرد بشكل صحيح؟ يسرب ماء؟ يصدر أصوات غريبة؟ أحمد فيكس تقدم خدمات صيانة وإصلاح المكيفات الشاملة في جميع أنحاء المنطقة الشرقية. فنيونا المعتمدون يتعاملون مع جميع الماركات والموديلات.</p>\n<h3>خدمات المكيفات لدينا تشمل:</h3>\n<ul>\n<li><strong>إصلاح عدم التبريد</strong> — تشخيص وإصلاح جميع مشاكل التبريد</li>\n<li><strong>تعبئة فريون</strong> — إعادة شحن غاز التبريد بشكل احترافي</li>\n<li><strong>خدمة التنظيف العميق</strong> — تنظيف داخلي وخارجي شامل</li>\n<li><strong>إصلاح واستبدال الكمبروسر</strong> — خدمات ضاغط متخصصة</li>\n<li><strong>إصلاح الثرموستات ولوحة التحكم</strong> — إصلاح المكونات الإلكترونية</li>\n<li><strong>عقود الصيانة السنوية</strong> — خطط صيانة وقائية</li>\n<li><strong>تركيب مكيفات جديدة</strong> — تركيب احترافي لجميع أنواع المكيفات</li>\n</ul>',
    'snowflake',
    'AC Repair & Maintenance in Dammam, Dhahran, Khobar | Ahmad Fix',
    'صيانة وإصلاح المكيفات في الدمام والظهران والخبر | أحمد فيكس',
    'Professional AC repair services in Dammam, Dhahran & Khobar. Split & central AC repair, gas refilling, deep cleaning. Certified technicians, same-day service. Call +966 58 245 3440',
    'خدمات صيانة المكيفات الاحترافية في الدمام والظهران والخبر. إصلاح مكيفات سبليت ومركزية، تعبئة فريون، تنظيف عميق. فنيون معتمدون. اتصل 3440 245 58 966+',
    'AC repair Dammam, AC maintenance Khobar, air conditioner repair Dhahran, split AC repair, central AC service, AC gas refilling, AC cleaning Dammam, AC not cooling repair, emergency AC repair Eastern Province',
    'صيانة مكيفات الدمام, تصليح مكيفات الخبر, إصلاح مكيفات الظهران, تعبئة فريون, تنظيف مكيفات, صيانة مكيفات سبليت, مكيف لا يبرد, صيانة مكيفات مركزية',
    '[{"q":"How much does AC repair cost in Dammam?","a":"AC repair costs vary depending on the issue. Basic repairs start from SAR 150, while major repairs like compressor replacement may cost more. Contact us for a free diagnosis and quote."},{"q":"Do you offer same-day AC repair service?","a":"Yes! We offer same-day service across Dammam, Dhahran, and Khobar. Call us before 2 PM for same-day dispatch."},{"q":"How often should I service my AC?","a":"We recommend servicing your AC at least twice a year — once before summer and once after. Regular maintenance extends your AC lifespan and improves efficiency."},{"q":"Do you repair all AC brands?","a":"Yes, our technicians are trained to repair all major brands including Samsung, LG, Carrier, Daikin, Gree, Midea, and more."}]',
    '[{"q":"كم تكلفة إصلاح المكيف في الدمام؟","a":"تختلف تكلفة إصلاح المكيفات حسب المشكلة. تبدأ الإصلاحات الأساسية من 150 ريال. اتصل بنا للحصول على تشخيص مجاني."},{"q":"هل تقدمون خدمة إصلاح في نفس اليوم؟","a":"نعم! نقدم خدمة في نفس اليوم في الدمام والظهران والخبر. اتصل قبل الساعة 2 ظهراً."},{"q":"كم مرة يجب صيانة المكيف؟","a":"ننصح بصيانة المكيف مرتين في السنة على الأقل — مرة قبل الصيف ومرة بعده."},{"q":"هل تصلحون جميع ماركات المكيفات؟","a":"نعم، فنيونا مدربون على إصلاح جميع الماركات الكبرى مثل سامسونج، إل جي، كارير، دايكن، جري، وغيرها."}]',
    1
),
(
    'Refrigerator Repair',
    'صيانة وإصلاح الثلاجات',
    'refrigerator-repair',
    'صيانة-ثلاجات',
    'Complete fridge and freezer repair in Dammam, Khobar & Dhahran. We fix cooling issues, compressor problems, ice makers, and thermostats.',
    'إصلاح ثلاجات وفريزرات في الدمام والخبر والظهران. نصلح مشاكل التبريد، أعطال الكمبروسر، وصانع الثلج.',
    '<h2>Expert Refrigerator Repair in Dammam, Dhahran & Khobar</h2>\n<p>A malfunctioning refrigerator can lead to food spoilage and costly replacements. At Ahmad Fix, we provide fast and reliable refrigerator repair services to keep your food fresh and safe. Our experienced technicians diagnose and fix all types of fridge problems.</p>\n<h3>Our Refrigerator Services Include:</h3>\n<ul>\n<li><strong>Not Cooling / Overcooling Fix</strong> — Temperature regulation repair</li>\n<li><strong>Compressor Repair & Replacement</strong> — Heart of your fridge</li>\n<li><strong>Gas Leakage Detection & Repair</strong> — Refrigerant leak fixes</li>\n<li><strong>Ice Maker Repair</strong> — Ice production issues resolved</li>\n<li><strong>Thermostat Replacement</strong> — Accurate temperature control</li>\n<li><strong>Door Seal / Gasket Replacement</strong> — Prevent cold air leakage</li>\n<li><strong>Strange Noise Diagnosis</strong> — Quiet operation restored</li>\n</ul>',
    '<h2>إصلاح الثلاجات المتخصص في الدمام والظهران والخبر</h2>\n<p>الثلاجة المعطلة قد تؤدي إلى فساد الطعام. في أحمد فيكس، نقدم خدمات إصلاح ثلاجات سريعة وموثوقة للحفاظ على طعامك طازجاً وآمناً.</p>\n<h3>خدمات الثلاجات لدينا تشمل:</h3>\n<ul>\n<li><strong>إصلاح عدم التبريد</strong> — إصلاح تنظيم درجة الحرارة</li>\n<li><strong>إصلاح واستبدال الكمبروسر</strong> — قلب ثلاجتك</li>\n<li><strong>كشف وإصلاح تسريب الغاز</strong> — إصلاح تسريبات المبرد</li>\n<li><strong>إصلاح صانع الثلج</strong> — حل مشاكل إنتاج الثلج</li>\n<li><strong>استبدال الثرموستات</strong> — تحكم دقيق بدرجة الحرارة</li>\n<li><strong>استبدال جوان الباب</strong> — منع تسرب الهواء البارد</li>\n</ul>',
    'thermometer',
    'Refrigerator Repair in Khobar, Dammam, Dhahran | Ahmad Fix',
    'صيانة وإصلاح الثلاجات في الخبر والدمام والظهران | أحمد فيكس',
    'Professional refrigerator repair services in Khobar, Dammam & Dhahran. Fridge not cooling, compressor repair, gas refilling. Same-day service. Call +966 58 245 3440',
    'خدمات إصلاح الثلاجات في الخبر والدمام والظهران. ثلاجة لا تبرد، إصلاح كمبروسر، تعبئة غاز. خدمة نفس اليوم. اتصل 3440 245 58 966+',
    'fridge repair Khobar, refrigerator repair Dammam, fridge not cooling, freezer repair Dhahran, fridge compressor repair, refrigerator maintenance Eastern Province',
    'صيانة ثلاجات الخبر, تصليح ثلاجات الدمام, ثلاجة لا تبرد, إصلاح فريزر الظهران, صيانة كمبروسر ثلاجة, صيانة ثلاجات المنطقة الشرقية',
    '[{"q":"My fridge is not cooling, what should I do?","a":"First, check if the thermostat is set correctly and the condenser coils are clean. If the problem persists, call Ahmad Fix for a professional diagnosis. It could be a compressor or gas issue."},{"q":"How long does a fridge repair take?","a":"Most repairs are completed within 1-2 hours. Complex issues like compressor replacement may take longer if parts need to be ordered."},{"q":"Do you repair all refrigerator brands?","a":"Yes, we service all major brands including Samsung, LG, Haier, Hitachi, Toshiba, Whirlpool, and more."}]',
    '[{"q":"ثلاجتي لا تبرد، ماذا أفعل؟","a":"أولاً، تحقق من إعداد الثرموستات ونظافة ملفات المكثف. إذا استمرت المشكلة، اتصل بأحمد فيكس للتشخيص المهني."},{"q":"كم يستغرق إصلاح الثلاجة؟","a":"معظم الإصلاحات تتم خلال 1-2 ساعة. المشاكل المعقدة قد تستغرق وقتاً أطول."},{"q":"هل تصلحون جميع ماركات الثلاجات؟","a":"نعم، نخدم جميع الماركات الكبرى مثل سامسونج، إل جي، هاير، هيتاشي، توشيبا، ويرلبول وغيرها."}]',
    2
),
(
    'Washing Machine Repair',
    'صيانة وإصلاح الغسالات',
    'washing-machine-repair',
    'صيانة-غسالات',
    'Washing machine repair in Dammam, Khobar & Dhahran for all types — automatic, semi-automatic and front-load. Drainage, spinning and electrical fixes.',
    'إصلاح غسالات في الدمام والخبر والظهران لجميع الأنواع — أوتوماتيك، نصف أوتوماتيك، وتحميل أمامي. نصلح الصرف والدوران.',
    '<h2>Washing Machine Repair Services in Dammam, Dhahran & Khobar</h2>\n<p>A broken washing machine disrupts your entire household routine. Ahmad Fix offers prompt and professional washing machine repair services for all types and brands. Whether it''s a drainage problem, spinning issue, or electrical fault — our technicians have you covered.</p>\n<h3>Our Washing Machine Services Include:</h3>\n<ul>\n<li><strong>Drainage & Water Pump Repair</strong> — Fix water not draining issues</li>\n<li><strong>Drum / Spin Motor Repair</strong> — Resolve spinning and vibration problems</li>\n<li><strong>Door Lock & Seal Repair</strong> — Fix leaking door issues</li>\n<li><strong>Control Board Repair</strong> — Electronic panel diagnostics</li>\n<li><strong>Belt Replacement</strong> — Drive belt wear and tear</li>\n<li><strong>Water Inlet Valve Repair</strong> — Water filling problems</li>\n<li><strong>Error Code Diagnosis</strong> — Decode and fix digital error codes</li>\n</ul>',
    '<h2>خدمات إصلاح الغسالات في الدمام والظهران والخبر</h2>\n<p>الغسالة المعطلة تعطل روتين منزلك بالكامل. أحمد فيكس تقدم خدمات إصلاح غسالات سريعة واحترافية لجميع الأنواع والماركات.</p>\n<h3>خدمات الغسالات لدينا تشمل:</h3>\n<ul>\n<li><strong>إصلاح الصرف ومضخة المياه</strong> — حل مشاكل عدم صرف المياه</li>\n<li><strong>إصلاح حلة الغسيل والموتور</strong> — حل مشاكل الدوران والاهتزاز</li>\n<li><strong>إصلاح قفل الباب والجوان</strong> — إصلاح تسريب الباب</li>\n<li><strong>إصلاح لوحة التحكم</strong> — تشخيص اللوحة الإلكترونية</li>\n<li><strong>استبدال السير</strong> — تآكل سير الإدارة</li>\n<li><strong>إصلاح صمام دخول المياه</strong> — مشاكل ملء المياه</li>\n</ul>',
    'rotate-cw',
    'Washing Machine Repair in Dhahran, Dammam, Khobar | Ahmad Fix',
    'صيانة وإصلاح الغسالات في الظهران والدمام والخبر | أحمد فيكس',
    'Expert washing machine repair in Dhahran, Dammam & Khobar. Fix drainage, spinning, electrical issues. All brands serviced. Call +966 58 245 3440',
    'إصلاح غسالات احترافي في الظهران والدمام والخبر. إصلاح مشاكل الصرف والدوران. جميع الماركات. اتصل 3440 245 58 966+',
    'washing machine repair Dhahran, washer repair Dammam, washing machine maintenance Khobar, automatic washer repair, washing machine not spinning, washing machine not draining',
    'صيانة غسالات الظهران, تصليح غسالات الدمام, إصلاح غسالات الخبر, غسالة لا تدور, غسالة لا تصرف الماء, صيانة غسالات أوتوماتيك',
    '[{"q":"Why is my washing machine not spinning?","a":"Common causes include a broken drive belt, worn motor coupling, faulty lid switch, or an unbalanced load. Our technicians can diagnose and fix the issue quickly."},{"q":"My washing machine is leaking water, can you fix it?","a":"Yes! Water leaks are usually caused by worn door seals, loose hose connections, or a faulty water pump. We repair all types of leaks."},{"q":"Do you service both top-load and front-load machines?","a":"Absolutely! We repair all types including top-load, front-load, automatic, and semi-automatic washing machines from all brands."}]',
    '[{"q":"لماذا غسالتي لا تدور؟","a":"الأسباب الشائعة تشمل كسر سير الإدارة، تآكل وصلة الموتور، أو عطل مفتاح الغطاء. فنيونا يشخصون ويصلحون المشكلة بسرعة."},{"q":"غسالتي تسرب ماء، هل تصلحونها؟","a":"نعم! تسريبات المياه عادة بسبب تآكل جوان الباب أو وصلات الخرطوم. نصلح جميع أنواع التسريبات."},{"q":"هل تصلحون الغسالات الأمامية والعلوية؟","a":"بالتأكيد! نصلح جميع الأنواع من جميع الماركات."}]',
    3
),
(
    'Oven & Stove Repair',
    'صيانة وإصلاح الأفران والبوتاجاز',
    'oven-repair',
    'صيانة-افران',
    'Gas and electric oven repair in Dammam, Khobar & Dhahran — burners, thermostats and ignition systems. Safe, certified service.',
    'إصلاح أفران الغاز والكهرباء في الدمام والخبر والظهران — الشعلات، الثرموستات، ونظام الإشعال. خدمة آمنة ومعتمدة.',
    '<h2>Oven & Stove Repair Services in Dammam, Dhahran & Khobar</h2>\n<p>A faulty oven or stove can be both inconvenient and dangerous. Ahmad Fix provides safe and professional oven repair services for gas and electric models. Our certified technicians ensure your cooking appliances work efficiently and safely.</p>\n<h3>Our Oven & Stove Services Include:</h3>\n<ul>\n<li><strong>Gas Oven Repair</strong> — Burner, ignition, and gas valve fixes</li>\n<li><strong>Electric Oven Repair</strong> — Heating element and control panel repair</li>\n<li><strong>Thermostat Calibration</strong> — Accurate temperature cooking</li>\n<li><strong>Ignition System Repair</strong> — Fix oven not lighting issues</li>\n<li><strong>Gas Leak Detection</strong> — Safety inspection and repair</li>\n<li><strong>Door Hinge & Seal Repair</strong> — Proper heat retention</li>\n<li><strong>Stove Burner Repair</strong> — Fix uneven flames and blockages</li>\n</ul>',
    '<h2>خدمات إصلاح الأفران والبوتاجاز في الدمام والظهران والخبر</h2>\n<p>الفرن أو البوتاجاز المعطل قد يكون مزعجاً وخطيراً. أحمد فيكس تقدم خدمات إصلاح أفران آمنة واحترافية لأفران الغاز والكهرباء.</p>\n<h3>خدمات الأفران لدينا تشمل:</h3>\n<ul>\n<li><strong>إصلاح أفران الغاز</strong> — إصلاح الشعلات والإشعال وصمام الغاز</li>\n<li><strong>إصلاح أفران الكهرباء</strong> — إصلاح عناصر التسخين ولوحة التحكم</li>\n<li><strong>معايرة الثرموستات</strong> — طهي بدرجة حرارة دقيقة</li>\n<li><strong>إصلاح نظام الإشعال</strong> — حل مشاكل عدم اشتعال الفرن</li>\n<li><strong>كشف تسريب الغاز</strong> — فحص السلامة والإصلاح</li>\n<li><strong>إصلاح مفصلات وجوان الباب</strong> — احتفاظ صحيح بالحرارة</li>\n</ul>',
    'flame',
    'Oven & Stove Repair in Dammam, Khobar & Dhahran | Ahmad Fix',
    'صيانة أفران وبوتاجاز في الدمام والخبر والظهران | أحمد فيكس',
    'Professional oven and stove repair in Dammam, Dhahran & Khobar. Gas & electric oven repair, burner fixes, thermostat calibration. Call +966 58 245 3440',
    'إصلاح أفران وبوتاجاز في الدمام والظهران والخبر. إصلاح أفران غاز وكهرباء، إصلاح شعلات. اتصل 3440 245 58 966+',
    'oven repair Dammam, stove repair Khobar, gas oven repair Dhahran, electric oven repair, oven not heating, stove burner repair Eastern Province',
    'صيانة أفران الدمام, تصليح بوتاجاز الخبر, إصلاح فرن غاز الظهران, فرن لا يسخن, إصلاح شعلات البوتاجاز, صيانة أفران كهربائية',
    '[{"q":"Is it safe to repair a gas oven at home?","a":"Yes, when done by certified professionals. Our technicians are trained in gas safety protocols and carry proper equipment for safe repairs."},{"q":"My oven is not heating evenly, what could be wrong?","a":"Uneven heating is usually caused by a faulty heating element, broken fan (in convection ovens), or thermostat issues. We can diagnose and fix it."},{"q":"Do you repair built-in ovens?","a":"Yes, we repair all types including built-in, freestanding, and countertop ovens from all major brands."}]',
    '[{"q":"هل إصلاح فرن الغاز في المنزل آمن؟","a":"نعم، عندما يتم بواسطة محترفين معتمدين. فنيونا مدربون على بروتوكولات سلامة الغاز."},{"q":"فرني لا يسخن بالتساوي، ما المشكلة؟","a":"التسخين غير المتساوي عادة بسبب عنصر تسخين معطل أو مشاكل في الثرموستات. يمكننا تشخيصه وإصلاحه."},{"q":"هل تصلحون الأفران المدمجة؟","a":"نعم، نصلح جميع الأنواع من جميع الماركات الكبرى."}]',
    4
);

-- Testimonials
INSERT INTO testimonials (customer_name, customer_location, review_text_en, review_text_ar, rating, sort_order) VALUES
('Ahmed Al-Rashid', 'Dammam', 'Ahmad Fix fixed my AC in just 2 hours! The technician was professional, punctual, and explained everything clearly. Highly recommended for anyone in Dammam.', 'أحمد فيكس أصلحت مكيفي في ساعتين فقط! الفني كان محترفاً ودقيقاً في الموعد وشرح كل شيء بوضوح. أنصح بهم بشدة.', 5, 1),
('Fatima Hassan', 'Al Khobar', 'My washing machine stopped working on a weekend, and Ahmad Fix sent a technician the same day. Great service and fair pricing. Thank you!', 'غسالتي توقفت عن العمل في عطلة نهاية الأسبوع، وأحمد فيكس أرسلت فني في نفس اليوم. خدمة ممتازة وأسعار عادلة. شكراً!', 5, 2),
('Mohammed Al-Qahtani', 'Dhahran', 'The best refrigerator repair service in the Eastern Province. They used original spare parts and gave me a warranty. My fridge works like new!', 'أفضل خدمة إصلاح ثلاجات في المنطقة الشرقية. استخدموا قطع غيار أصلية وأعطوني ضمان. ثلاجتي تعمل كالجديدة!', 5, 3),
('Sara Abdullah', 'Dammam', 'Professional and trustworthy. They repaired my oven quickly and the price was very reasonable. I will definitely use Ahmad Fix again.', 'محترفون وموثوقون. أصلحوا فرني بسرعة والسعر كان معقولاً جداً. سأستخدم أحمد فيكس مرة أخرى بالتأكيد.', 5, 4),
('Khalid Ibrahim', 'Al Khobar', 'I have an annual maintenance contract with Ahmad Fix for all my home appliances. Their regular service keeps everything running smoothly. Best in Khobar!', 'لدي عقد صيانة سنوي مع أحمد فيكس لجميع أجهزتي المنزلية. صيانتهم الدورية تبقي كل شيء يعمل بسلاسة. الأفضل في الخبر!', 5, 5);

-- Service Areas
INSERT INTO service_areas (name_en, name_ar, description_en, description_ar, latitude, longitude, sort_order) VALUES
('Dammam', 'الدمام', 'Full coverage across all districts in Dammam including Al Faisaliyah, Al Shati, Al Mazruiyah, and more.', 'تغطية شاملة في جميع أحياء الدمام بما في ذلك الفيصلية، الشاطئ، المزروعية وغيرها.', 26.43311719, 50.0850687, 1),
('Al Khobar', 'الخبر', 'Serving all areas in Al Khobar including Al Corniche, Al Thuqbah, Al Aqrabiyah, and surrounding areas.', 'خدمة جميع مناطق الخبر بما في ذلك الكورنيش، الثقبة، العقربية والمناطق المحيطة.', 26.2794, 50.2083, 2),
('Dhahran', 'الظهران', 'Complete service coverage in Dhahran including all residential and commercial areas.', 'تغطية خدمة كاملة في الظهران بما في ذلك جميع المناطق السكنية والتجارية.', 26.2361, 50.0393, 3);

-- Pages SEO
INSERT INTO pages_seo (page_slug, title_en, title_ar, description_en, description_ar, keywords_en, keywords_ar) VALUES
('home', 'Home Appliance Repair in Dammam, Khobar & Dhahran | Ahmad Fix', 'صيانة أجهزة منزلية في الدمام والخبر والظهران | أحمد فيكس', 'Ahmad Fix provides expert AC, refrigerator, washing machine & oven repair services in Dammam, Dhahran & Khobar. Certified technicians, same-day service, genuine parts. Call +966 58 245 3440', 'أحمد فيكس تقدم خدمات صيانة مكيفات، ثلاجات، غسالات وأفران احترافية في الدمام والظهران والخبر. فنيون معتمدون، خدمة نفس اليوم. اتصل 3440 245 58 966+', 'home appliance repair Dammam, AC repair Khobar, fridge repair Dhahran, washing machine maintenance, oven repair Eastern Province, Ahmad Fix', 'صيانة أجهزة منزلية الدمام, صيانة مكيفات الخبر, تصليح ثلاجات الظهران, صيانة غسالات, إصلاح أفران المنطقة الشرقية, أحمد فيكس'),
('about', 'About Ahmad Fix — Trusted Appliance Repair Experts in Eastern Province', 'عن أحمد فيكس — خبراء صيانة الأجهزة الموثوقون في المنطقة الشرقية', 'Learn about Ahmad Fix, your trusted home appliance repair partner in Dammam, Dhahran & Khobar. Certified technicians with years of experience.', 'تعرف على أحمد فيكس، شريكك الموثوق في صيانة الأجهزة المنزلية في الدمام والظهران والخبر.', 'about Ahmad Fix, appliance repair company Dammam, maintenance service Eastern Province', 'عن أحمد فيكس, شركة صيانة أجهزة الدمام, خدمة صيانة المنطقة الشرقية'),
('contact', 'Contact Ahmad Fix — Book Appliance Repair in Dammam, Dhahran, Khobar', 'اتصل بأحمد فيكس — احجز صيانة أجهزة في الدمام والظهران والخبر', 'Contact Ahmad Fix for fast home appliance repair services. Call +966 58 245 3440 or WhatsApp us. Serving Dammam, Dhahran & Khobar.', 'اتصل بأحمد فيكس لخدمات صيانة أجهزة منزلية سريعة. اتصل 3440 245 58 966+ أو واتساب. خدمة الدمام والظهران والخبر.', 'contact Ahmad Fix, book repair service Dammam, appliance repair phone Khobar', 'اتصل أحمد فيكس, حجز صيانة الدمام, هاتف صيانة أجهزة الخبر');
