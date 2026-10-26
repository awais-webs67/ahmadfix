<?php
/**
 * Ahmad Fix — 404 Page
 */
?>
<section class="page-404">
    <div class="container">
        <h1>404</h1>
        <h2><?php echo $lang === 'ar' ? 'الصفحة غير موجودة' : 'Page Not Found'; ?></h2>
        <p><?php echo $lang === 'ar' ? 'عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها.' : 'Sorry, the page you are looking for does not exist or has been moved.'; ?></p>
        <a href="<?php echo url(); ?>" class="btn btn-primary btn-lg">
            <?php echo __('back_to_home'); ?>
        </a>
    </div>
</section>
