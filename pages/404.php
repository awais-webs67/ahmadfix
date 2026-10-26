<?php
/**
 * Ahmad Fix — 404 Page
 * Real 404 status + noindex (set in the router/header), with helpful links.
 */
?>

<section class="page-404">
    <div class="container">
        <h1>404</h1>
        <h2><?php echo __('not_found_title'); ?></h2>
        <p><?php echo __('not_found_text'); ?></p>
        <div class="not-found-links">
            <?php foreach (getServices() as $svc): ?>
            <a href="<?php echo url(localized($svc, 'slug')); ?>" class="btn btn-outline btn-sm"><?php echo e(localized($svc, 'name')); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo url('contact'); ?>" class="btn btn-outline btn-sm"><?php echo __('nav_contact'); ?></a>
        </div>
        <a href="<?php echo url(); ?>" class="btn btn-primary btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            <?php echo __('back_to_home'); ?>
        </a>
    </div>
</section>
