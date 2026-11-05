<?php
/**
 * Mr Fix KSA — One-time database upgrades
 *
 * Runs automatically on the first page load after new files are uploaded.
 * Each step is safe to run more than once and only changes old/default values,
 * so anything you already edited yourself is left alone.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

define('SCHEMA_VERSION', 6);

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

// ============================================
// Arabic text repair (version 4)
// ============================================

/**
 * The website's own tables
 */
function siteTables() {
    return ['admins', 'site_settings', 'services', 'hero_content', 'testimonials', 'contact_submissions',
            'pages_seo', 'service_areas', 'gallery', 'login_attempts', 'email_log'];
}

/**
 * Convert any table stored in a character set that cannot hold Arabic (e.g. latin1) to UTF-8
 */
function fixTableCharsets(PDO $pdo, array &$errors = []) {
    $converted = false;
    try {
        $pdo->exec("ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    } catch (Throwable $e) { /* no permission — tables below still get fixed */ }

    $bad = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
                          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t
                            AND CHARACTER_SET_NAME IS NOT NULL AND CHARACTER_SET_NAME NOT IN ('utf8mb4', 'utf8', 'utf8mb3')");
    $exists = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t");
    foreach (siteTables() as $t) {
        $exists->execute([':t' => $t]);
        if (!(int)$exists->fetchColumn()) continue;
        $bad->execute([':t' => $t]);
        if (!(int)$bad->fetchColumn()) continue;
        try {
            $pdo->exec("ALTER TABLE `{$t}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $converted = true;
        } catch (Throwable $e1) {
            try {
                // Very old MySQL without utf8mb4: utf8 still holds Arabic
                $pdo->exec("ALTER TABLE `{$t}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
                $converted = true;
            } catch (Throwable $e2) {
                $errors[] = "Table {$t} could not be converted: " . $e2->getMessage();
                continue;
            }
        }
        error_log("Mr Fix KSA: table {$t} converted to UTF-8 so Arabic text can be stored.");
    }
    return $converted;
}

/**
 * A new database connection with the same settings as config/bootstrap.php
 */
function freshConnection() {
    return new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ]);
}

/**
 * True when Arabic text was lost and only question marks are left (e.g. "???? ????")
 */
function looksDamaged($v) {
    return is_string($v) && str_contains($v, '??') && !preg_match('/\p{Arabic}/u', $v);
}

/**
 * Read the INSERT rows of database/setup.sql (the original website content)
 * Returns ['table' => [ ['col' => value, ...], ... ]]
 */
function seedRows() {
    static $rows = null;
    if ($rows !== null) return $rows;
    $rows = [];
    $file = SITE_ROOT . '/database/setup.sql';
    if (!is_file($file)) return $rows;
    $sql = file_get_contents($file);
    $len = strlen($sql);
    if (!preg_match_all('/INSERT INTO\s+`?(\w+)`?\s*\(([^)]*)\)\s*VALUES\s*/i', $sql, $m, PREG_OFFSET_CAPTURE)) return $rows;
    foreach ($m[0] as $k => $match) {
        $table = $m[1][$k][0];
        $cols = array_map(fn($c) => trim($c, " `\n\r\t"), explode(',', $m[2][$k][0]));
        $i = $match[1] + strlen($match[0]);
        // Parse ( ... ), ( ... ); tuples
        while ($i < $len) {
            while ($i < $len && ctype_space($sql[$i])) $i++;
            if ($i >= $len || $sql[$i] !== '(') break;
            $i++;
            $vals = [];
            while ($i < $len) {
                while ($i < $len && ctype_space($sql[$i])) $i++;
                if ($sql[$i] === "'") {
                    $i++; $buf = '';
                    while ($i < $len) {
                        $c = $sql[$i];
                        if ($c === '\\' && $i + 1 < $len) {
                            $n = $sql[$i + 1];
                            $buf .= ['n' => "\n", 'r' => "\r", 't' => "\t", '0' => "\0"][$n] ?? $n;
                            $i += 2; continue;
                        }
                        if ($c === "'") {
                            if ($i + 1 < $len && $sql[$i + 1] === "'") { $buf .= "'"; $i += 2; continue; }
                            $i++; break;
                        }
                        $buf .= $c; $i++;
                    }
                    $vals[] = $buf;
                } else {
                    $start = $i;
                    while ($i < $len && $sql[$i] !== ',' && $sql[$i] !== ')') $i++;
                    $raw = trim(substr($sql, $start, $i - $start));
                    $vals[] = strtoupper($raw) === 'NULL' ? null : $raw;
                }
                while ($i < $len && ctype_space($sql[$i])) $i++;
                if ($sql[$i] === ',') { $i++; continue; }
                if ($sql[$i] === ')') { $i++; break; }
            }
            if (count($vals) === count($cols)) {
                $rows[$table][] = array_combine($cols, $vals);
            }
            while ($i < $len && ctype_space($sql[$i])) $i++;
            if ($i < $len && $sql[$i] === ',') { $i++; continue; }
            break;
        }
    }
    $v6_file = SITE_ROOT . '/config/seo-v6-content.php';
    if (is_file($v6_file)) {
        $v6 = require $v6_file;
        foreach ($rows['pages_seo'] ?? [] as $idx => $p) {
            $slug = $p['page_slug'] ?? '';
            if (isset($v6['pages_seo'][$slug])) {
                $rows['pages_seo'][$idx] = array_merge($p, $v6['pages_seo'][$slug]);
            }
        }
        $legacy = legacyServiceSlugs();
        foreach ($rows['services'] ?? [] as $idx => $s) {
            $slug = $legacy[$s['slug_en'] ?? ''] ?? ($s['slug_en'] ?? '');
            if (isset($v6['services'][$slug])) {
                $rows['services'][$idx] = array_merge($s, $v6['services'][$slug], ['slug_en' => $slug]);
            }
        }
    }
    return $rows;
}

/**
 * Put the original Arabic text back wherever only "????" is left
 */
function restoreArabicText(PDO $pdo) {
    $seed = seedRows();
    $fixed = 0;
    $set = function ($table, $col, $value, $where, $params) use ($pdo, &$fixed) {
        $stmt = $pdo->prepare("UPDATE `{$table}` SET `{$col}` = :v WHERE {$where}");
        $stmt->execute([':v' => $value] + $params);
        $fixed += $stmt->rowCount();
    };

    // 1. Settings (business name, address, hours, ribbon, email texts …)
    $schema_ar = [];
    foreach (settingsSchema() as $section) {
        foreach ($section['fields'] as $key => $f) {
            if (!empty($f['bilingual']) && is_array($f['default'] ?? null)) $schema_ar[$key] = $f['default'][1];
        }
    }
    $seed_settings = [];
    foreach ($seed['site_settings'] ?? [] as $r) $seed_settings[$r['setting_key']] = $r['setting_value_ar'];
    foreach ($pdo->query("SELECT setting_key, setting_value_ar FROM site_settings") as $row) {
        if (!looksDamaged($row['setting_value_ar'])) continue;
        $good = null;
        foreach ([$seed_settings[$row['setting_key']] ?? null, $schema_ar[$row['setting_key']] ?? null] as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') { $good = $candidate; break; }
        }
        if ($row['setting_key'] === 'site_name') $good = 'مستر فيكس';
        if ($good !== null && $good !== '') {
            $set('site_settings', 'setting_value_ar', $good, 'setting_key = :k', [':k' => $row['setting_key']]);
        }
    }

    // 2. Services (matched by English slug, old or new)
    $legacy = legacyServiceSlugs();
    foreach ($seed['services'] ?? [] as $r) {
        $slugs = array_unique([$r['slug_en'], $legacy[$r['slug_en']] ?? $r['slug_en']]);
        $new_slug_ar = $legacy[$r['slug_ar']] ?? $r['slug_ar'];
        $in = implode(',', array_fill(0, count($slugs), '?'));
        $q = $pdo->prepare("SELECT * FROM services WHERE slug_en IN ($in)");
        $q->execute(array_values($slugs));
        foreach ($q->fetchAll() as $db) {
            foreach (['name_ar', 'slug_ar', 'short_desc_ar', 'full_desc_ar', 'meta_title_ar', 'meta_desc_ar', 'meta_keywords_ar', 'faq_ar'] as $col) {
                if (!array_key_exists($col, $r) || !looksDamaged($db[$col])) continue;
                $value = $col === 'slug_ar' ? $new_slug_ar : $r[$col];
                $set('services', $col, $value, 'id = :id', [':id' => $db['id']]);
            }
        }
    }

    // 3. Page titles for Google
    foreach ($seed['pages_seo'] ?? [] as $r) {
        $q = $pdo->prepare("SELECT * FROM pages_seo WHERE page_slug = :s");
        $q->execute([':s' => $r['page_slug']]);
        if (!$db = $q->fetch()) continue;
        foreach (['title_ar', 'description_ar', 'keywords_ar'] as $col) {
            if (isset($r[$col]) && looksDamaged($db[$col])) {
                $set('pages_seo', $col, $r[$col], 'id = :id', [':id' => $db['id']]);
            }
        }
    }

    // 4. Homepage hero, reviews, service areas
    $hero = $seed['hero_content'][0] ?? null;
    if ($hero && ($db = $pdo->query("SELECT * FROM hero_content ORDER BY id LIMIT 1")->fetch())) {
        foreach (['title_ar', 'subtitle_ar', 'cta_text_ar'] as $col) {
            if (isset($hero[$col]) && looksDamaged($db[$col])) $set('hero_content', $col, $hero[$col], 'id = :id', [':id' => $db['id']]);
        }
    }
    foreach ($seed['testimonials'] ?? [] as $r) {
        $q = $pdo->prepare("SELECT * FROM testimonials WHERE customer_name = :n");
        $q->execute([':n' => $r['customer_name']]);
        foreach ($q->fetchAll() as $db) {
            if (looksDamaged($db['review_text_ar'])) $set('testimonials', 'review_text_ar', $r['review_text_ar'], 'id = :id', [':id' => $db['id']]);
        }
    }
    foreach ($seed['service_areas'] ?? [] as $r) {
        $q = $pdo->prepare("SELECT * FROM service_areas WHERE name_en = :n");
        $q->execute([':n' => $r['name_en']]);
        foreach ($q->fetchAll() as $db) {
            foreach (['name_ar', 'description_ar'] as $col) {
                if (looksDamaged($db[$col])) $set('service_areas', $col, $r[$col], 'id = :id', [':id' => $db['id']]);
            }
        }
    }

    if ($fixed) error_log("Mr Fix KSA: restored {$fixed} Arabic text value(s) that had turned into question marks.");
    return $fixed;
}

/**
 * Can this database save and read back Arabic? (writes a tiny test setting)
 */
function arabicSelfTest(PDO $pdo) {
    $word = 'اختبار عربي';
    try {
        $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value_en, setting_value_ar, setting_group)
                       VALUES ('_arabic_test', 'test', :a, 'system')
                       ON DUPLICATE KEY UPDATE setting_value_ar = :b")->execute([':a' => $word, ':b' => $word]);
        $back = $pdo->query("SELECT setting_value_ar FROM site_settings WHERE setting_key = '_arabic_test'")->fetchColumn();
        return $back === $word;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * How many Arabic values are still only question marks
 */
function countDamagedArabic(PDO $pdo) {
    $checks = [
        'site_settings' => ['setting_value_ar'],
        'services'      => ['name_ar', 'slug_ar', 'short_desc_ar', 'meta_title_ar', 'meta_desc_ar'],
        'pages_seo'     => ['title_ar', 'description_ar'],
        'testimonials'  => ['review_text_ar'],
        'service_areas' => ['name_ar', 'description_ar'],
        'hero_content'  => ['title_ar', 'subtitle_ar'],
    ];
    $n = 0;
    foreach ($checks as $table => $cols) {
        try {
            foreach ($pdo->query("SELECT " . implode(', ', $cols) . " FROM `{$table}`") as $row) {
                foreach ($row as $v) if (looksDamaged($v)) $n++;
            }
        } catch (Throwable $e) { /* table missing */ }
    }
    return $n;
}

/**
 * Full Arabic repair: convert tables, reconnect, restore text, test.
 * Returns ['ok' => bool, 'restored' => int, 'damaged_left' => int, 'errors' => [...]]
 */
function repairArabic(PDO $pdo) {
    $errors = [];
    try {
        if (fixTableCharsets($pdo, $errors)) {
            $pdo = freshConnection();
            $GLOBALS['pdo'] = $pdo;
        }
    } catch (Throwable $e) {
        $errors[] = 'Checking tables failed: ' . $e->getMessage();
    }
    $restored = 0;
    try {
        $restored = restoreArabicText($pdo);
    } catch (Throwable $e) {
        $errors[] = 'Restoring text failed: ' . $e->getMessage();
    }
    $test_ok = arabicSelfTest($pdo);
    if (!$test_ok) {
        $errors[] = 'The database still cannot save Arabic (test word came back as question marks).';
    }
    $left = countDamagedArabic($pdo);
    // Done when Arabic can be saved and nothing else can be restored (anything left was typed by people, not original content)
    $result = ['ok' => $test_ok && ($left === 0 || $restored === 0), 'restored' => $restored, 'damaged_left' => $left, 'errors' => $errors, 'at' => date('Y-m-d H:i:s')];
    try {
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value_en, setting_value_ar, setting_group) VALUES ('_arabic_repair', :a, :b, 'system')
                       ON DUPLICATE KEY UPDATE setting_value_en = :c, setting_value_ar = :d")->execute([':a' => $json, ':b' => $json, ':c' => $json, ':d' => $json]);
    } catch (Throwable $e) { /* ignore */ }
    return $result;
}

/**
 * Runs on page loads until Arabic is healthy: retries the repair at most every 10 minutes
 */
function arabicHealthCheck(PDO $pdo) {
    try {
        $state = json_decode((string)$pdo->query("SELECT setting_value_en FROM site_settings WHERE setting_key = '_arabic_repair'")->fetchColumn(), true);
    } catch (Throwable $e) {
        return;
    }
    if (!empty($state['ok'])) return;                                         // healthy
    if (!empty($state['at']) && strtotime($state['at']) > time() - 600) return;  // tried recently
    repairArabic($pdo);
}

function runMigrations(PDO $pdo) {
    // Fast path (every page): read the status from the settings the page loads anyway
    $all = getAllSettings();
    $version = (int)($all['schema_version']['setting_value_en'] ?? 1);
    if ($version >= SCHEMA_VERSION) {
        $last = json_decode((string)($all['_migration_log']['setting_value_en'] ?? ''), true);
        if (!empty($last['failed'])) {
            // A step failed last time (e.g. missing permission): retry with a growing pause
            // 10 min, 20, 40 … at most once a day
            $tries = max(1, (int)($last['tries'] ?? 1));
            $wait = min(86400, 600 * (2 ** ($tries - 1)));
            if (empty($last['at']) || strtotime($last['at']) < time() - $wait) {
                runMigrationSteps($pdo, $tries + 1);
                getAllSettings(true);
                return;
            }
        }
        $state = json_decode((string)($all['_arabic_repair']['setting_value_en'] ?? ''), true);
        if (empty($state['ok']) && (empty($state['at']) || strtotime($state['at']) < time() - 600)) {
            repairArabic($pdo);
            getAllSettings(true);
        }
        return;
    }
    runMigrationSteps($pdo, 1);
    getAllSettings(true);
}

/**
 * Run every upgrade step (each one is safe to run again)
 */
function runMigrationSteps(PDO $pdo, $tries = 1) {
    $steps = [
        // ---------------- Version 4 (runs first so later steps see correct Arabic) ----------------
        // Tables that cannot store Arabic → UTF-8, then put lost Arabic text back
        // After a conversion the old connection still writes "?", so everything after
        // this step (and the rest of this page load) uses a fresh connection
        'repair_arabic'  => function (PDO $pdo) {
            $r = repairArabic($pdo);
            return $GLOBALS['pdo'] instanceof PDO ? $GLOBALS['pdo'] : null;   // keep using the fresh connection
        },

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
            $real = settingsSchema()['business']['fields']['email']['default'];
            $stmt->execute([':new_en' => $real, ':new_ar' => $real, ':old' => 'abubakerjamal573@gmail.com']);
        },

        // ---------------- Version 3 ----------------

        // 7. Booking details + language + notes on each lead
        'lead_booking_columns' => function (PDO $pdo) {
            $cols = [
                'city'           => "VARCHAR(60) NULL AFTER service_requested",
                'address'        => "VARCHAR(255) NULL AFTER city",
                'preferred_date' => "DATE NULL AFTER address",
                'preferred_time' => "VARCHAR(30) NULL AFTER preferred_date",
                'lang'           => "CHAR(2) NOT NULL DEFAULT 'en' AFTER message",
                'notes'          => "TEXT NULL AFTER status",
                'ip_address'     => "VARCHAR(45) NULL AFTER notes",
            ];
            foreach ($cols as $col => $def) {
                if (!columnExists($pdo, 'contact_submissions', $col)) {
                    $pdo->exec("ALTER TABLE contact_submissions ADD COLUMN {$col} {$def}");
                }
            }
        },

        // 8. History of every email the website sends
        'email_log_table' => function (PDO $pdo) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS email_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lead_id INT NULL,
                type VARCHAR(30) NOT NULL DEFAULT 'compose',
                to_email VARCHAR(190) NOT NULL,
                to_name VARCHAR(100) NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NULL,
                status VARCHAR(10) NOT NULL DEFAULT 'sent',
                error VARCHAR(500) NULL,
                sent_by VARCHAR(100) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_lead (lead_id),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        },

        // 9. Copy every admin-editable setting into the database (existing values are kept)
        'settings_defaults' => function (PDO $pdo) {
            $schema = settingsSchema();
            $ins = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value_en, setting_value_ar, setting_type, setting_group)
                                  VALUES (:k, :en, :ar, :t, :g)");
            // Values from older config files move into the database
            $from_constants = [
                'ga4_id' => 'GA4_ID', 'google_site_verification' => 'GOOGLE_SITE_VERIFICATION', 'bing_site_verification' => 'BING_SITE_VERIFICATION',
                'google_business_url' => 'GOOGLE_BUSINESS_URL', 'social_instagram' => 'SOCIAL_INSTAGRAM', 'social_snapchat' => 'SOCIAL_SNAPCHAT',
                'social_tiktok' => 'SOCIAL_TIKTOK', 'social_facebook' => 'SOCIAL_FACEBOOK', 'social_x' => 'SOCIAL_X',
                'smtp_host' => 'SMTP_HOST', 'smtp_port' => 'SMTP_PORT', 'smtp_secure' => 'SMTP_SECURE', 'smtp_user' => 'SMTP_USER',
                'smtp_from_name' => 'SMTP_FROM_NAME',
            ];
            $types = ['textarea' => 'textarea', 'url' => 'url', 'email' => 'email'];
            // New-lead alerts go to the business email already saved in the admin panel
            $cur_email = $pdo->query("SELECT setting_value_en FROM site_settings WHERE setting_key = 'email'")->fetchColumn();
            foreach ($schema as $group => $section) {
                foreach ($section['fields'] as $key => $f) {
                    $d = $f['default'] ?? '';
                    [$en, $ar] = is_array($d) ? $d : [$d, $d];
                    if ($key === 'notify_emails' && $cur_email) {
                        $en = $ar = $cur_email;
                    }
                    if (isset($from_constants[$key]) && defined($from_constants[$key]) && (string)constant($from_constants[$key]) !== '') {
                        $en = $ar = (string)constant($from_constants[$key]);
                    }
                    $ins->execute([':k' => $key, ':en' => $en, ':ar' => $ar, ':t' => $types[$f['type']] ?? 'text', ':g' => $group]);
                }
            }
            // A Gmail password saved in an older config file moves in too (encrypted)
            if (defined('SMTP_PASS') && SMTP_PASS !== '') {
                $enc = encryptSecret(SMTP_PASS);
                $pdo->prepare("UPDATE site_settings SET setting_value_en = :v1, setting_value_ar = :v2 WHERE setting_key = 'smtp_pass' AND (setting_value_en = '' OR setting_value_en IS NULL)")
                    ->execute([':v1' => $enc, ':v2' => $enc]);
            }
        },

        // 10. Values the site displayed before (seed data differed)
        'settings_display_fixes' => function (PDO $pdo) {
            $fix = $pdo->prepare("UPDATE site_settings SET setting_value_ar = :new WHERE setting_key = 'site_name' AND setting_value_ar = :old");
            $fix->execute([':new' => 'مستر فيكس', ':old' => 'أحمد فيكس السعودية']);
            // Map pin pointed at a hospital — use the Dammam city centre
            $coords = $pdo->prepare("UPDATE site_settings SET setting_value_en = :v1, setting_value_ar = :v2 WHERE setting_key = :k AND setting_value_en = :old");
            $coords->execute([':v1' => '26.4207', ':v2' => '26.4207', ':k' => 'maps_embed_lat', ':old' => '26.43311719']);
            $coords->execute([':v1' => '50.0888', ':v2' => '50.0888', ':k' => 'maps_embed_lng', ':old' => '50.0850687']);
        },

        // 11. Google titles/descriptions that were too long (only the original texts are changed)
        'seo_lengths' => function (PDO $pdo) {
            $pages = [
                ['home', 'title_en', 'Home Appliance Repair in Dammam, Khobar & Dhahran | Ahmad Fix', 'Appliance Repair in Dammam, Khobar & Dhahran | {site_name}'],
                ['home', 'description_en', 'Ahmad Fix provides expert AC, refrigerator, washing machine & oven repair services in Dammam, Dhahran & Khobar. Certified technicians, same-day service, genuine parts. Call +966 58 245 3440',
                         'AC, fridge, washing machine & oven repair in Dammam, Khobar & Dhahran. Certified technicians, same-day service, genuine parts. Call {phone}'],
                ['about', 'title_en', 'About Ahmad Fix — Trusted Appliance Repair Experts in Eastern Province', 'About {site_name} — Trusted Appliance Repair in Dammam & Khobar'],
                ['contact', 'title_en', 'Contact Ahmad Fix — Book Appliance Repair in Dammam, Dhahran, Khobar', 'Contact {site_name} — Book Appliance Repair in Dammam & Khobar'],
                ['contact', 'description_en', 'Contact Ahmad Fix for fast home appliance repair services. Call +966 58 245 3440 or WhatsApp us. Serving Dammam, Dhahran & Khobar.',
                            'Contact {site_name} for fast home appliance repair services. Call {phone} or WhatsApp us. Serving Dammam, Dhahran & Khobar.'],
            ];
            foreach ($pages as [$slug, $col, $old, $new]) {
                $pdo->prepare(sprintf("UPDATE pages_seo SET %s = :new WHERE page_slug = :slug AND %s = :old", $col, $col))
                    ->execute([':new' => $new, ':slug' => $slug, ':old' => $old]);
            }
            $services = [
                ['meta_title_en', 'AC Repair & Maintenance in Dammam, Dhahran, Khobar | Ahmad Fix', 'AC Repair in Dammam, Khobar & Dhahran | {site_name}'],
                ['meta_desc_en', 'Professional AC repair services in Dammam, Dhahran & Khobar. Split & central AC repair, gas refilling, deep cleaning. Certified technicians, same-day service. Call +966 58 245 3440',
                                 'AC repair in Dammam, Khobar & Dhahran: split & central AC, gas refilling, deep cleaning. Certified technicians, same-day service. Call {phone}'],
                ['meta_desc_en', 'Professional refrigerator repair services in Khobar, Dammam & Dhahran. Fridge not cooling, compressor repair, gas refilling. Same-day service. Call +966 58 245 3440',
                                 'Fridge repair in Khobar, Dammam & Dhahran: not cooling, compressor and gas refilling. Certified technicians, same-day service. Call {phone}'],
                ['meta_title_en', 'Washing Machine Repair in Dhahran, Dammam, Khobar | Ahmad Fix', 'Washing Machine Repair Dammam, Khobar, Dhahran | {site_name}'],
                ['meta_desc_en', 'Expert washing machine repair in Dhahran, Dammam & Khobar. Fix drainage, spinning, electrical issues. All brands serviced. Call +966 58 245 3440',
                                 'Expert washing machine repair in Dhahran, Dammam & Khobar. Fix drainage, spinning, electrical issues. All brands serviced. Call {phone}'],
                ['meta_desc_en', 'Professional oven and stove repair in Dammam, Dhahran & Khobar. Gas & electric oven repair, burner fixes, thermostat calibration. Call +966 58 245 3440',
                                 'Oven & stove repair in Dammam, Khobar & Dhahran: gas & electric ovens, burners, thermostats. Certified, same-day service. Call {phone}'],
            ];
            foreach ($services as [$col, $old, $new]) {
                $pdo->prepare(sprintf("UPDATE services SET %s = :new WHERE %s = :old", $col, $col))->execute([':new' => $new, ':old' => $old]);
            }
            // Arabic: phone number → {phone} so it follows Admin → Settings
            foreach (['pages_seo' => 'description_ar', 'services' => 'meta_desc_ar'] as $table => $col) {
                $pdo->exec("UPDATE {$table} SET {$col} = REPLACE({$col}, '3440 245 58 966+', '{phone}') WHERE {$col} LIKE '%3440 245 58 966+%'");
                $pdo->exec("UPDATE {$table} SET {$col} = REPLACE({$col}, '+966 58 245 3440', '{phone}') WHERE {$col} LIKE '%+966 58 245 3440%'");
            }
        },

        // ---------------- Version 5 ----------------
        // 12. New name: Ahmad Fix → Mr Fix KSA (Arabic: مستر فيكس) in everything the website shows.
        //     Google titles/descriptions get {site_name}, so they follow Admin → Settings from now on.
        //     Leads and the email history are records and stay as they were.
        'rebrand_mr_fix_ksa' => function (PDO $pdo) {
            $en_new = 'Mr Fix KSA';
            $ar_new = 'مستر فيكس';
            $old_en = ['Ahmad Fix', 'Ahmad fix', 'AHMAD FIX', 'ahmad fix', 'AhmadFix', 'Ahmadfix', 'ahmadfix'];
            $old_ar = ['أحمد فيكس السعودية', 'احمد فيكس السعودية', 'أحمد فيكس', 'احمد فيكس'];
            $seo_cols = ['pages_seo' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'keywords_en', 'keywords_ar'],
                         'services'  => ['meta_title_en', 'meta_title_ar', 'meta_desc_en', 'meta_desc_ar', 'meta_keywords_en', 'meta_keywords_ar']];
            $skip = ['setting_key', 'setting_group', 'setting_type', 'slug_en', 'slug_ar', 'page_slug', 'image', 'icon', 'og_image',
                     'username', 'password', 'email', 'category', 'customer_location'];
            $tables = ['site_settings', 'services', 'pages_seo', 'testimonials', 'hero_content', 'service_areas', 'gallery', 'admins'];
            $cols = $pdo->query("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS
                                 WHERE TABLE_SCHEMA = DATABASE() AND DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext')")->fetchAll(PDO::FETCH_NUM);
            foreach ($cols as [$table, $col]) {
                if (!in_array($table, $tables, true) || in_array($col, $skip, true)) continue;
                $is_seo = in_array($col, $seo_cols[$table] ?? [], true);
                $is_ar = str_ends_with($col, '_ar');
                $expr = "`{$col}`";
                $params = [];
                $i = 0;
                foreach ($old_en as $o) {
                    $expr = "REPLACE({$expr}, :o{$i}, :n{$i})";
                    $params[":o{$i}"] = $o;
                    $params[":n{$i}"] = $is_seo ? '{site_name}' : (($is_ar && $table !== 'site_settings') ? $ar_new : $en_new);
                    $i++;
                }
                foreach ($old_ar as $o) {
                    $expr = "REPLACE({$expr}, :o{$i}, :n{$i})";
                    $params[":o{$i}"] = $o;
                    $params[":n{$i}"] = $is_seo ? '{site_name}' : $ar_new;
                    $i++;
                }
                $where = [];
                foreach (array_merge($old_en, $old_ar) as $j => $o) {
                    $where[] = "`{$col}` LIKE :w{$j}";
                    $params[":w{$j}"] = '%' . $o . '%';
                }
                $pdo->prepare("UPDATE `{$table}` SET `{$col}` = {$expr} WHERE " . implode(' OR ', $where))->execute($params);
            }
            // Business name itself
            $pdo->prepare("UPDATE site_settings SET setting_value_en = :en WHERE setting_key = 'site_name' AND (setting_value_en = '' OR setting_value_en IS NULL)")->execute([':en' => $en_new]);
            $pdo->prepare("UPDATE site_settings SET setting_value_ar = :ar WHERE setting_key = 'site_name' AND (setting_value_ar = '' OR setting_value_ar IS NULL OR setting_value_ar = :en)")->execute([':ar' => $ar_new, ':en' => $en_new]);
        },

        // ---------------- Version 6 ----------------
        // 13. Deep SEO, GEO & AEO upgrade:
        //     - Set canonical site_url to https://mrfixksa.com when blank
        //     - Optimize all pages_seo and services titles (45-59 chars) & meta descriptions (135-156 chars)
        //     - Upgrade all 4 service pages to 800+ words (EN & AR) with SAR pricing tables, H2/H3 parity, and 6 FAQs
        'seo_v6_deep_optimization' => function (PDO $pdo) {
            $pdo->prepare("UPDATE site_settings SET setting_value_en = :u, setting_value_ar = :u2 WHERE setting_key = 'site_url' AND (setting_value_en = '' OR setting_value_en IS NULL)")
                ->execute([':u' => 'https://mrfixksa.com', ':u2' => 'https://mrfixksa.com']);

            $payload_file = SITE_ROOT . '/config/seo-v6-content.php';
            if (!is_file($payload_file)) return;
            $payload = require $payload_file;

            // Update pages_seo (home, about, contact)
            $upd_page = $pdo->prepare("UPDATE pages_seo
                SET title_en = :t_en, title_ar = :t_ar, description_en = :d_en, description_ar = :d_ar
                WHERE page_slug = :slug");
            foreach ($payload['pages_seo'] ?? [] as $slug => $row) {
                $upd_page->execute([
                    ':t_en' => $row['title_en'],
                    ':t_ar' => $row['title_ar'],
                    ':d_en' => $row['description_en'],
                    ':d_ar' => $row['description_ar'],
                    ':slug' => $slug,
                ]);
            }

            // Update services (ac-repair, refrigerator-repair, washing-machine-repair, oven-repair)
            $upd_svc = $pdo->prepare("UPDATE services
                SET meta_title_en = :mt_en,
                    meta_title_ar = :mt_ar,
                    meta_desc_en  = :md_en,
                    meta_desc_ar  = :md_ar,
                    short_desc_en = :sd_en,
                    short_desc_ar = :sd_ar,
                    full_desc_en  = :fd_en,
                    full_desc_ar  = :fd_ar,
                    faq_en        = :fq_en,
                    faq_ar        = :fq_ar
                WHERE slug_en = :slug");
            foreach ($payload['services'] ?? [] as $slug_en => $svc) {
                $upd_svc->execute([
                    ':mt_en' => $svc['meta_title_en'],
                    ':mt_ar' => $svc['meta_title_ar'],
                    ':md_en' => $svc['meta_desc_en'],
                    ':md_ar' => $svc['meta_desc_ar'],
                    ':sd_en' => $svc['short_desc_en'],
                    ':sd_ar' => $svc['short_desc_ar'],
                    ':fd_en' => $svc['full_desc_en'],
                    ':fd_ar' => $svc['full_desc_ar'],
                    ':fq_en' => $svc['faq_en'],
                    ':fq_ar' => $svc['faq_ar'],
                    ':slug'  => $slug_en,
                ]);
            }
        },
    ];

    $failed = [];
    foreach ($steps as $name => $step) {
        try {
            $result = $step($pdo);
            if ($result instanceof PDO) {
                // A step asked for a fresh connection: use it from now on (also for the page)
                $pdo = $result;
                $GLOBALS['pdo'] = $result;
            }
        } catch (Throwable $e) {
            error_log("Mr Fix KSA migration '{$name}' failed: " . $e->getMessage());
            $failed[$name] = $e->getMessage();
        }
    }

    // Shown in Admin → Settings → System check
    try {
        $json = json_encode(['at' => date('Y-m-d H:i:s'), 'failed' => $failed, 'tries' => $failed ? $tries : 0], JSON_UNESCAPED_UNICODE);
        $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value_en, setting_value_ar, setting_group) VALUES ('_migration_log', :a, :b, 'system')
                       ON DUPLICATE KEY UPDATE setting_value_en = :c, setting_value_ar = :d")->execute([':a' => $json, ':b' => $json, ':c' => $json, ':d' => $json]);
    } catch (Throwable $e) { /* ignore */ }

    try {
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value_en, setting_value_ar, setting_type, setting_group)
                               VALUES ('schema_version', :v1, :v2, 'text', 'system')
                               ON DUPLICATE KEY UPDATE setting_value_en = :v3, setting_value_ar = :v4");
        $v = (string)SCHEMA_VERSION;
        $stmt->execute([':v1' => $v, ':v2' => $v, ':v3' => $v, ':v4' => $v]);
    } catch (PDOException $e) {
        error_log('Mr Fix KSA: could not save schema_version: ' . $e->getMessage());
    }
}
