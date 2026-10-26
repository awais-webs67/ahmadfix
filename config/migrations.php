<?php
/**
 * Ahmad Fix — One-time database upgrades
 *
 * Runs automatically on the first page load after new files are uploaded.
 * Each step is safe to run more than once and only changes old/default values,
 * so anything you already edited yourself is left alone.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

define('SCHEMA_VERSION', 2);

/**
 * Old service URLs → new city-neutral URLs (every service is offered in every city).
 * Also used by the router to send 301 redirects from the old addresses.
 */
function legacyServiceSlugs() {
    return [
        // old slug                         => new slug
        'ac-repair-dammam'                  => 'ac-repair',
        'fridge-repair-khobar'              => 'refrigerator-repair',
        'washing-machine-repair-dhahran'    => 'washing-machine-repair',
        'oven-repair-eastern-province'      => 'oven-repair',
        'صيانة-مكيفات-الدمام'               => 'صيانة-مكيفات',
        'صيانة-ثلاجات-الخبر'                => 'صيانة-ثلاجات',
        'صيانة-غسالات-الظهران'              => 'صيانة-غسالات',
        'صيانة-افران-المنطقة-الشرقية'       => 'صيانة-افران',
    ];
}

function getSchemaVersion(PDO $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT setting_value_en FROM site_settings WHERE setting_key = 'schema_version' LIMIT 1");
        $stmt->execute();
        return (int)($stmt->fetchColumn() ?: 1);
    } catch (PDOException $e) {
        return 1;
    }
}

function columnExists(PDO $pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c");
    $stmt->execute([':t' => $table, ':c' => $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function runMigrations(PDO $pdo) {
    if (getSchemaVersion($pdo) >= SCHEMA_VERSION) {
        return;
    }

    $steps = [
        // 1. Lead status column used by the admin dashboard
        'lead_status_column' => function (PDO $pdo) {
            if (!columnExists($pdo, 'contact_submissions', 'status')) {
                $pdo->exec("ALTER TABLE contact_submissions ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'new' AFTER is_read");
                $pdo->exec("UPDATE contact_submissions SET status = IF(is_read = 1, 'contacted', 'new')");
            }
        },

        // 2. Login attempts table (blocks password guessing on the admin login)
        'login_attempts_table' => function (PDO $pdo) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ip_time (ip_address, attempted_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        },

        // 3. City-neutral service URLs (old URLs keep working through 301 redirects)
        'service_slugs' => function (PDO $pdo) {
            $map = [
                ['ac-repair-dammam', 'ac-repair', 'صيانة-مكيفات'],
                ['fridge-repair-khobar', 'refrigerator-repair', 'صيانة-ثلاجات'],
                ['washing-machine-repair-dhahran', 'washing-machine-repair', 'صيانة-غسالات'],
                ['oven-repair-eastern-province', 'oven-repair', 'صيانة-افران'],
            ];
            $stmt = $pdo->prepare("UPDATE services SET slug_en = :new_en, slug_ar = :new_ar WHERE slug_en = :old");
            foreach ($map as [$old, $new_en, $new_ar]) {
                $stmt->execute([':new_en' => $new_en, ':new_ar' => $new_ar, ':old' => $old]);
            }
        },

        // 4. Service titles/descriptions that named only one city → all three cities
        'service_meta_all_cities' => function (PDO $pdo) {
            $updates = [
                ['meta_title_en', 'Oven & Stove Repair in Eastern Province - Dammam, Khobar | Ahmad Fix', 'Oven & Stove Repair in Dammam, Khobar & Dhahran | Ahmad Fix'],
                ['meta_title_ar', 'صيانة وإصلاح الأفران والبوتاجاز في المنطقة الشرقية | أحمد فيكس', 'صيانة أفران وبوتاجاز في الدمام والخبر والظهران | أحمد فيكس'],
                ['short_desc_en', 'Complete fridge and freezer repair services. We fix cooling issues, compressor problems, ice maker repairs, and thermostat replacements.', 'Complete fridge and freezer repair in Dammam, Khobar & Dhahran. We fix cooling issues, compressor problems, ice makers, and thermostats.'],
                ['short_desc_ar', 'خدمات إصلاح الثلاجات والفريزر الشاملة. نصلح مشاكل التبريد، أعطال الكمبروسر، وإصلاح صانع الثلج.', 'إصلاح ثلاجات وفريزرات في الدمام والخبر والظهران. نصلح مشاكل التبريد، أعطال الكمبروسر، وصانع الثلج.'],
                ['short_desc_en', 'Professional washing machine repair for all types — automatic, semi-automatic, and front-load. We fix drainage, spinning, and electrical issues.', 'Washing machine repair in Dammam, Khobar & Dhahran for all types — automatic, semi-automatic and front-load. Drainage, spinning and electrical fixes.'],
                ['short_desc_ar', 'إصلاح غسالات احترافي لجميع الأنواع — أوتوماتيك، نصف أوتوماتيك، وتحميل أمامي. نصلح مشاكل الصرف والدوران.', 'إصلاح غسالات في الدمام والخبر والظهران لجميع الأنواع — أوتوماتيك، نصف أوتوماتيك، وتحميل أمامي. نصلح الصرف والدوران.'],
                ['short_desc_en', 'Gas and electric oven repair, stove burner fixes, thermostat calibration, and ignition system repair. Safe and certified service.', 'Gas and electric oven repair in Dammam, Khobar & Dhahran — burners, thermostats and ignition systems. Safe, certified service.'],
                ['short_desc_ar', 'إصلاح أفران الغاز والكهرباء، إصلاح شعلات البوتاجاز، معايرة الثرموستات، وإصلاح نظام الإشعال.', 'إصلاح أفران الغاز والكهرباء في الدمام والخبر والظهران — الشعلات، الثرموستات، ونظام الإشعال. خدمة آمنة ومعتمدة.'],
            ];
            foreach ($updates as [$col, $old, $new]) {
                // Column names come from the fixed list above, never from user input
                $stmt = $pdo->prepare("UPDATE services SET {$col} = :new WHERE {$col} = :old");
                $stmt->execute([':new' => $new, ':old' => $old]);
            }
        },

        // 4b. Homepage title: keywords first and short enough to show fully in Google (~60 chars)
        'home_title' => function (PDO $pdo) {
            $updates = [
                ['title_en', 'Ahmad Fix — Professional Home Appliance Repair in Dammam, Dhahran & Khobar', 'Home Appliance Repair in Dammam, Khobar & Dhahran | Ahmad Fix'],
                ['title_ar', 'أحمد فيكس — صيانة الأجهزة المنزلية الاحترافية في الدمام والظهران والخبر', 'صيانة أجهزة منزلية في الدمام والخبر والظهران | أحمد فيكس'],
            ];
            foreach ($updates as [$col, $old, $new]) {
                $stmt = $pdo->prepare("UPDATE pages_seo SET {$col} = :new WHERE page_slug = 'home' AND {$col} = :old");
                $stmt->execute([':new' => $new, ':old' => $old]);
            }
        },

        // 5. Map link pointed at a hospital search — point it to the service area instead
        'maps_url' => function (PDO $pdo) {
            $stmt = $pdo->prepare("UPDATE site_settings SET setting_value_en = :en, setting_value_ar = :ar
                                   WHERE setting_key = 'google_maps_url' AND (setting_value_en LIKE :bad OR setting_value_en LIKE :bad2)");
            $stmt->execute([
                ':en'   => 'https://www.google.com/maps/search/?api=1&query=Dammam%2C+Al+Khobar%2C+Dhahran',
                ':ar'   => 'https://www.google.com/maps/search/?api=1&query=%D8%A7%D9%84%D8%AF%D9%85%D8%A7%D9%85',
                ':bad'  => '%مستشفى%',
                ':bad2' => '%26.43311719,50.0850687%',
            ]);
        },

        // 6. Email setting: the seed value was never shown on the site — keep showing the real one
        'email_setting' => function (PDO $pdo) {
            $stmt = $pdo->prepare("UPDATE site_settings SET setting_value_en = :new_en, setting_value_ar = :new_ar WHERE setting_key = 'email' AND setting_value_en = :old");
            $stmt->execute([':new_en' => DEFAULT_EMAIL, ':new_ar' => DEFAULT_EMAIL, ':old' => 'abubakerjamal573@gmail.com']);
        },
    ];

    foreach ($steps as $name => $step) {
        try {
            $step($pdo);
        } catch (Throwable $e) {
            error_log("Ahmad Fix migration '{$name}' failed: " . $e->getMessage());
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value_en, setting_value_ar, setting_type, setting_group)
                               VALUES ('schema_version', :v1, :v2, 'text', 'system')
                               ON DUPLICATE KEY UPDATE setting_value_en = :v3, setting_value_ar = :v4");
        $v = (string)SCHEMA_VERSION;
        $stmt->execute([':v1' => $v, ':v2' => $v, ':v3' => $v, ':v4' => $v]);
    } catch (PDOException $e) {
        error_log('Ahmad Fix: could not save schema_version: ' . $e->getMessage());
    }
}
