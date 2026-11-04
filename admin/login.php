<?php
/**
 * Ahmad Fix — Admin Login
 */

if (!defined('FIXORA_APP')) {
    define('FIXORA_APP', true);
}

require_once dirname(__DIR__) . '/config/bootstrap.php';

$error = '';

// The placeholder password hash from database/setup.sql. It only works with the
// default password, and you are forced to choose a new password right after login.
const ADMIN_SEED_HASH = '$2y$10$8K1p/a0dN1xR0k5v8gqXZuPjY3VkVx0L5M4r7XwKjN2yF9FZ3dHCi';
const ADMIN_SEED_PASSWORD = 'ahmadfix2024';
const LOGIN_MAX_ATTEMPTS = 5;      // failed tries allowed...
const LOGIN_WINDOW_MINUTES = 15;   // ...within this many minutes

header('X-Robots-Tag: noindex, nofollow');

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

$ip = substr($_SERVER['REMOTE_ADDR'] ?? 'unknown', 0, 45);

function tooManyLoginAttempts(PDO $pdo, $ip) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = :ip AND attempted_at > (NOW() - INTERVAL " . LOGIN_WINDOW_MINUTES . " MINUTE)");
        $stmt->execute([':ip' => $ip]);
        return (int)$stmt->fetchColumn() >= LOGIN_MAX_ATTEMPTS;
    } catch (PDOException $e) {
        return false;
    }
}

function recordFailedLogin(PDO $pdo, $ip) {
    try {
        $pdo->prepare("INSERT INTO login_attempts (ip_address) VALUES (:ip)")->execute([':ip' => $ip]);
        // Housekeeping: forget attempts older than a day
        $pdo->exec("DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)");
    } catch (PDOException $e) {
        error_log('Login attempt logging failed: ' . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Your session expired. Please try again.';
    } elseif (tooManyLoginAttempts($pdo, $ip)) {
        $error = 'Too many failed attempts. Please wait ' . LOGIN_WINDOW_MINUTES . ' minutes and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            $ok = false;
            $must_change = false;
            if ($admin) {
                if (password_verify($password, $admin['password'])) {
                    $ok = true;
                } elseif (hash_equals(ADMIN_SEED_HASH, $admin['password']) && hash_equals(ADMIN_SEED_PASSWORD, $password)) {
                    // First login with the default password → must set a new one
                    $ok = true;
                    $must_change = true;
                }
            }

            if ($ok) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'] ?: $admin['username'];
                $_SESSION['must_change_password'] = $must_change;

                $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id")->execute([':id' => $admin['id']]);
                $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = :ip")->execute([':ip' => $ip]);

                header('Location: ' . SITE_URL . '/admin/index');
                exit;
            }

            recordFailedLogin($pdo, $ip);
            $error = 'Invalid username or password.';
        } catch (PDOException $e) {
            error_log('Admin login error: ' . $e->getMessage());
            $error = 'Login is temporarily unavailable. Please try again later.';
        }
    }
}
?>
<?php
require_once __DIR__ . '/_layout.php';
adminHead('Sign in', '', ['bare' => true]);
?>
<div class="card" style="padding: 30px 28px;">
    <h2 style="font-size: 22px; text-align: center;">Welcome back</h2>
    <p class="hint" style="text-align: center;">Sign in to manage bookings, emails and your website.</p>

    <?php if ($error): ?>
        <div class="toast error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="fg">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="admin" autocomplete="username" required autofocus>
        </div>
        <div class="fg">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-orange" style="width: 100%; padding: 13px; font-size: 14px;">Sign in</button>
    </form>
</div>
<p class="muted" style="text-align: center; margin-top: 14px;">🔒 Protected area · too many wrong tries locks sign-in for 15 minutes</p>
<?php adminFoot(['bare' => true]); ?>
