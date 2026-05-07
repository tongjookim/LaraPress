<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo e(App\Models\Setting::get('meta_title') ?: App\Models\Setting::get('site_name', 'Laraboard')); ?> <?php echo $__env->yieldContent('title'); ?></title>
    <?php if(App\Models\Setting::get('favicon')): ?>
    <link rel="icon" href="<?php echo e(App\Models\Setting::get('favicon')); ?>">
    <?php endif; ?>

    
    <?php echo $__env->yieldPushContent('head-meta'); ?>
    <?php if (! empty(trim($__env->yieldContent('head-meta-override')))): ?>
    <?php else: ?>
    <meta name="description" content="<?php echo e(App\Models\Setting::get('meta_description') ?: App\Models\Setting::get('site_description', '')); ?>">
    <meta name="keywords" content="<?php echo e(App\Models\Setting::get('meta_keywords') ?: App\Models\Setting::get('site_keywords', '')); ?>">
    <?php if(App\Models\Setting::get('meta_author')): ?>
    <meta name="author" content="<?php echo e(App\Models\Setting::get('meta_author')); ?>">
    <?php endif; ?>
    <meta property="og:type"        content="<?php echo e(App\Models\Setting::get('meta_og_type','website')); ?>">
    <meta property="og:site_name"   content="<?php echo e(App\Models\Setting::get('site_name','Laraboard')); ?>">
    <meta property="og:title"       content="<?php echo e(App\Models\Setting::get('meta_title') ?: App\Models\Setting::get('site_name','Laraboard')); ?>">
    <meta property="og:description" content="<?php echo e(App\Models\Setting::get('meta_description') ?: App\Models\Setting::get('site_description','')); ?>">
    <?php if(App\Models\Setting::get('meta_og_image')): ?>
    <meta property="og:image" content="<?php echo e(App\Models\Setting::get('meta_og_image')); ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="<?php echo e(App\Models\Setting::get('meta_twitter_card','summary_large_image')); ?>">
    <?php if(App\Models\Setting::get('rss_enabled','1') === '1'): ?>
    <link rel="alternate" type="application/rss+xml" title="<?php echo e(App\Models\Setting::get('site_name','Laraboard')); ?>" href="<?php echo e(url('/feed')); ?>">
    <?php endif; ?>
    <?php endif; ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <?php
    // 색상 설정 (미설정 시 basic 스킨 기본값)
    $_cp  = \App\Models\Setting::get('theme_primary',     '#7c3aed');
    $_ca  = \App\Models\Setting::get('theme_accent',      '#e8524a');
    $_ctb = \App\Models\Setting::get('theme_topbar_bg',   '#7c3aed');
    $_ctt = \App\Models\Setting::get('theme_topbar_text', '#ffffff');
    $_cnb = \App\Models\Setting::get('theme_nav_bg',      '#ffffff');
    $_cnt = \App\Models\Setting::get('theme_nav_text',    '#4b5563');
    $_csb = \App\Models\Setting::get('theme_site_bg',     '#f9fafb');
    $_ctx = \App\Models\Setting::get('theme_text',        '#1f2937');
    // 자동 파생 색상
    $_darken  = fn($h,$f=0.78) => sprintf('#%02x%02x%02x',
        (int)(hexdec(substr(ltrim($h,'#'),0,2))*$f),
        (int)(hexdec(substr(ltrim($h,'#'),2,2))*$f),
        (int)(hexdec(substr(ltrim($h,'#'),4,2))*$f));
    $_lighten = fn($h,$f=0.9) => sprintf('#%02x%02x%02x',
        min(255,(int)((($r=hexdec(substr(ltrim($h,'#'),0,2)))+(255-$r)*$f))),
        min(255,(int)((($g=hexdec(substr(ltrim($h,'#'),2,2)))+(255-$g)*$f))),
        min(255,(int)((($b=hexdec(substr(ltrim($h,'#'),4,2)))+(255-$b)*$f))));
    $_cpDark  = $_darken($_cp);
    $_cpLight = $_lighten($_cp);
    ?>
    <style>
    :root {
        --site-primary:       <?php echo e($_cp); ?>;
        --site-primary-dark:  <?php echo e($_cpDark); ?>;
        --site-primary-light: <?php echo e($_cpLight); ?>;
        --site-accent:        <?php echo e($_ca); ?>;
        --site-topbar-bg:     <?php echo e($_ctb); ?>;
        --site-topbar-text:   <?php echo e($_ctt); ?>;
        --site-nav-bg:        <?php echo e($_cnb); ?>;
        --site-nav-text:      <?php echo e($_cnt); ?>;
        --site-bg:            <?php echo e($_csb); ?>;
        --site-text:          <?php echo e($_ctx); ?>;
    }
    body { background-color: var(--site-bg); color: var(--site-text); }
    .site-nav-link { color: var(--site-nav-text); transition: color .15s; }
    .site-nav-link:hover { color: var(--site-primary); }
    .site-nav-active { background: var(--site-primary) !important; color: #fff !important; }
    .site-nav-active:hover { background: var(--site-primary-dark) !important; color: #fff !important; }
    .site-primary-btn { background: var(--site-primary); color: #fff; border: none; transition: background .15s; }
    .site-primary-btn:hover { background: var(--site-primary-dark); color: #fff; text-decoration: none; }
    .site-primary-text { color: var(--site-primary); }
    .site-accent-badge { background: var(--site-accent); color: #fff; }
    .site-input { border: 1px solid #d1d5db; border-radius: .5rem; transition: border-color .15s, box-shadow .15s; }
    .site-input:focus { outline: none; border-color: var(--site-primary); box-shadow: 0 0 0 3px rgba(0,0,0,.08); }
    </style>

    <?php echo $__env->yieldPushContent('skin-css'); ?>

    <?php if(request()->is('admin*')): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/admin.css']); ?>
    <?php endif; ?>

    <?php if(App\Models\Setting::get('custom_head_script')): ?>
    <?php echo App\Models\Setting::get('custom_head_script'); ?>

    <?php endif; ?>
</head>
<body style="background-color:var(--site-bg);color:var(--site-text);" class="text-gray-800">
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/basic/head.blade.php ENDPATH**/ ?>