<?php
/**
 * Ahmad Fix — Admin: Customer Reviews (homepage testimonials)
 * Add, edit, show/hide and delete the reviews shown on the homepage.
 */

require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['must_change_password'])) {
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

$msg = '';
$msg_type = 'success';
$edit = null;

$clean = fn($v, $max) => mb_substr(trim((string)$v), 0, $max);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        $msg = 'Security check failed. Please try again.';
        $msg_type = 'warning';
    } else {
        $action = $_POST['action'] ?? '';
        $id = (int)($_POST['id'] ?? 0);

        if ($action === 'save') {
            $data = [
                ':name'  => $clean($_POST['customer_name'] ?? '', 100),
                ':loc'   => $clean($_POST['customer_location'] ?? '', 100),
                ':en'    => $clean($_POST['review_text_en'] ?? '', 1000),
                ':ar'    => $clean($_POST['review_text_ar'] ?? '', 1000),
                ':rating'=> max(1, min(5, (int)($_POST['rating'] ?? 5))),
                ':sort'  => (int)($_POST['sort_order'] ?? 0),
                ':active'=> !empty($_POST['is_active']) ? 1 : 0,
            ];
            if ($data[':name'] === '' || ($data[':en'] === '' && $data[':ar'] === '')) {
                $msg = 'Please enter the customer name and the review text (English or Arabic).';
                $msg_type = 'warning';
                $edit = $_POST;
            } else {
                // If only one language was written, show the same text in both
                if ($data[':en'] === '') $data[':en'] = $data[':ar'];
                if ($data[':ar'] === '') $data[':ar'] = $data[':en'];

                if ($id > 0) {
                    $data[':id'] = $id;
                    $pdo->prepare("UPDATE testimonials SET customer_name = :name, customer_location = :loc, review_text_en = :en, review_text_ar = :ar, rating = :rating, sort_order = :sort, is_active = :active WHERE id = :id")->execute($data);
                    $_SESSION['flash'] = ['Review updated.', 'success'];
                } else {
                    $pdo->prepare("INSERT INTO testimonials (customer_name, customer_location, review_text_en, review_text_ar, rating, sort_order, is_active) VALUES (:name, :loc, :en, :ar, :rating, :sort, :active)")->execute($data);
                    $_SESSION['flash'] = ['Review added.', 'success'];
                }
                header('Location: ' . SITE_URL . '/admin/reviews');
                exit;
            }
        } elseif ($action === 'toggle' && $id > 0) {
            $pdo->prepare("UPDATE testimonials SET is_active = 1 - is_active WHERE id = :id")->execute([':id' => $id]);
            $_SESSION['flash'] = ['Review visibility changed.', 'success'];
            header('Location: ' . SITE_URL . '/admin/reviews');
            exit;
        } elseif ($action === 'delete' && $id > 0) {
            $pdo->prepare("DELETE FROM testimonials WHERE id = :id")->execute([':id' => $id]);
            $_SESSION['flash'] = ['Review deleted.', 'warning'];
            header('Location: ' . SITE_URL . '/admin/reviews');
            exit;
        }
    }
}

if (!empty($_SESSION['flash'])) {
    [$msg, $msg_type] = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

if ($edit === null && isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

$reviews = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC")->fetchAll();
$v = fn($k, $d = '') => e($edit[$k] ?? $d);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Customer Reviews — Ahmad Fix Admin</title>
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', 'Tajawal', sans-serif; }
        body { background: #0b0f19; color: #f8fafc; min-height: 100vh; padding-bottom: 50px; }
        header { background: #151d2a; border-bottom: 1px solid #233044; padding: 16px 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
        .brand { display: flex; align-items: center; gap: 14px; text-decoration: none; color: #fff; }
        .brand img { max-height: 44px; }
        .brand h1 { font-size: 20px; font-weight: 800; color: #ff7a00; }
        .nav { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: none; }
        .btn-blue { background: #1e293b; color: #38bdf8; border: 1px solid #0284c7; }
        .btn-orange { background: #ff7a00; color: #fff; }
        .btn-red { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid #ef4444; }
        .btn-gray { background: #1e293b; color: #cbd5e1; border: 1px solid #334155; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .toast { padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; background: rgba(16,185,129,0.12); border: 1px solid #10b981; color: #6ee7b7; }
        .toast.warning { background: rgba(245,158,11,0.12); border-color: #f59e0b; color: #fcd34d; }
        .note { background: #151d2a; border: 1px solid #233044; border-left: 4px solid #ff7a00; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; color: #cbd5e1; font-size: 14px; line-height: 1.6; }
        .grid { display: grid; grid-template-columns: 1fr 1.3fr; gap: 24px; align-items: start; }
        .card { background: #151d2a; border: 1px solid #233044; border-radius: 16px; padding: 24px; }
        .card h2 { font-size: 17px; margin-bottom: 18px; }
        label { display: block; font-size: 12px; font-weight: 700; color: #94a3b8; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        input[type=text], input[type=number], textarea, select { width: 100%; padding: 11px 12px; background: #0b0f19; border: 1px solid #334155; border-radius: 8px; color: #fff; font-size: 14px; outline: none; }
        textarea { min-height: 90px; resize: vertical; line-height: 1.5; }
        input:focus, textarea:focus, select:focus { border-color: #ff7a00; }
        .fg { margin-bottom: 14px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .check { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #cbd5e1; margin: 6px 0 18px; text-transform: none; letter-spacing: 0; font-weight: 500; }
        .review { border: 1px solid #233044; border-radius: 12px; padding: 16px; margin-bottom: 12px; background: #0f1623; }
        .review.hidden { opacity: 0.55; }
        .review-top { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 8px; }
        .review-name { font-weight: 800; }
        .review-loc { color: #94a3b8; font-size: 13px; }
        .stars { color: #fbbf24; letter-spacing: 2px; }
        .review p { color: #cbd5e1; font-size: 14px; line-height: 1.6; margin-bottom: 6px; }
        .review p[dir=rtl] { text-align: right; }
        .badge { font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px; }
        .badge-on { background: rgba(16,185,129,0.15); color: #34d399; }
        .badge-off { background: rgba(148,163,184,0.15); color: #94a3b8; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .actions form { display: inline; }
        .actions .btn { padding: 6px 12px; font-size: 12px; }
        @media (max-width: 860px) { .grid { grid-template-columns: 1fr; } header { padding: 14px 16px; } }
    </style>
</head>
<body>

<header>
    <a href="<?php echo SITE_URL; ?>" class="brand" target="_blank" rel="noopener">
        <img src="<?php echo ASSETS_URL; ?>/images/logo-header.png" alt="Ahmad Fix">
        <h1>Customer Reviews</h1>
    </a>
    <div class="nav">
        <a href="<?php echo SITE_URL; ?>/admin/index" class="btn btn-blue">📋 Leads</a>
        <a href="<?php echo SITE_URL; ?>/admin/emails" class="btn btn-blue">✉️ Emails</a>
        <a href="<?php echo SITE_URL; ?>/admin/settings" class="btn btn-blue">⚙️ Settings</a>
        <a href="<?php echo SITE_URL; ?>/admin/logout" class="btn btn-red">Sign Out</a>
    </div>
</header>

<div class="container">
    <?php if ($msg): ?>
        <div class="toast <?php echo $msg_type === 'warning' ? 'warning' : ''; ?>">✓ <?php echo e($msg); ?></div>
    <?php endif; ?>

    <div class="note">
        These reviews appear on the homepage. Only publish <strong>real</strong> reviews from real customers
        (Google's guidelines require this). Tip: copy your best Google reviews here, with the customer's permission.
    </div>

    <div class="grid">
        <!-- Add / edit form -->
        <div class="card">
            <h2><?php echo !empty($edit['id']) ? '✏️ Edit Review #' . (int)$edit['id'] : '➕ Add a Review'; ?></h2>
            <form method="POST" action="<?php echo SITE_URL; ?>/admin/reviews">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?php echo (int)($edit['id'] ?? 0); ?>">
                <div class="row">
                    <div class="fg">
                        <label for="customer_name">Customer name *</label>
                        <input type="text" id="customer_name" name="customer_name" maxlength="100" required value="<?php echo $v('customer_name'); ?>">
                    </div>
                    <div class="fg">
                        <label for="customer_location">City / area</label>
                        <input type="text" id="customer_location" name="customer_location" maxlength="100" placeholder="Dammam" value="<?php echo $v('customer_location'); ?>">
                    </div>
                </div>
                <div class="fg">
                    <label for="review_text_en">Review (English)</label>
                    <textarea id="review_text_en" name="review_text_en" maxlength="1000"><?php echo $v('review_text_en'); ?></textarea>
                </div>
                <div class="fg">
                    <label for="review_text_ar">Review (Arabic)</label>
                    <textarea id="review_text_ar" name="review_text_ar" maxlength="1000" dir="rtl"><?php echo $v('review_text_ar'); ?></textarea>
                </div>
                <div class="row">
                    <div class="fg">
                        <label for="rating">Stars</label>
                        <select id="rating" name="rating">
                            <?php for ($r = 5; $r >= 1; $r--): ?>
                            <option value="<?php echo $r; ?>" <?php echo (int)($edit['rating'] ?? 5) === $r ? 'selected' : ''; ?>><?php echo str_repeat('★', $r); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="fg">
                        <label for="sort_order">Order (1 = first)</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?php echo $v('sort_order', '0'); ?>">
                    </div>
                </div>
                <label class="check"><input type="checkbox" name="is_active" value="1" <?php echo !isset($edit['is_active']) || (int)$edit['is_active'] === 1 ? 'checked' : ''; ?>> Show on website</label>
                <div class="actions">
                    <button type="submit" class="btn btn-orange"><?php echo !empty($edit['id']) ? 'Save Changes' : 'Add Review'; ?></button>
                    <?php if (!empty($edit['id'])): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/reviews" class="btn btn-gray">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- List -->
        <div class="card">
            <h2>All Reviews (<?php echo count($reviews); ?>)</h2>
            <?php if (!$reviews): ?>
                <p style="color:#94a3b8;">No reviews yet. When there are none, the reviews section is hidden on the homepage.</p>
            <?php endif; ?>
            <?php foreach ($reviews as $r): ?>
            <div class="review <?php echo $r['is_active'] ? '' : 'hidden'; ?>">
                <div class="review-top">
                    <div>
                        <span class="review-name"><?php echo e($r['customer_name']); ?></span>
                        <span class="review-loc"><?php echo $r['customer_location'] ? '· ' . e($r['customer_location']) : ''; ?></span>
                    </div>
                    <div>
                        <span class="stars"><?php echo str_repeat('★', (int)$r['rating']); ?></span>
                        <span class="badge <?php echo $r['is_active'] ? 'badge-on' : 'badge-off'; ?>"><?php echo $r['is_active'] ? 'VISIBLE' : 'HIDDEN'; ?></span>
                    </div>
                </div>
                <p><?php echo e($r['review_text_en']); ?></p>
                <p dir="rtl"><?php echo e($r['review_text_ar']); ?></p>
                <div class="actions">
                    <a href="<?php echo SITE_URL; ?>/admin/reviews?edit=<?php echo (int)$r['id']; ?>" class="btn btn-blue">Edit</a>
                    <form method="POST" action="<?php echo SITE_URL; ?>/admin/reviews">
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                        <button type="submit" class="btn btn-gray"><?php echo $r['is_active'] ? 'Hide' : 'Show'; ?></button>
                    </form>
                    <form method="POST" action="<?php echo SITE_URL; ?>/admin/reviews" onsubmit="return confirm('Delete this review?');">
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                        <button type="submit" class="btn btn-red">Delete</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>
