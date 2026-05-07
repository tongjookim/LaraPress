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

    <meta name="description" content="<?php echo e(App\Models\Setting::get('meta_description') ?: App\Models\Setting::get('site_description', '')); ?>">
    <meta name="keywords" content="<?php echo e(App\Models\Setting::get('meta_keywords') ?: App\Models\Setting::get('site_keywords', '')); ?>">
    
    <?php if(App\Models\Setting::get('meta_author')): ?>
        <meta name="author" content="<?php echo e(App\Models\Setting::get('meta_author')); ?>">
    <?php endif; ?>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Noto+Serif+KR:wght@400;700;900&family=Noto+Sans+KR:wght@300;400;500;700;900&family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js', 'resources/views/skin/layout/cobalt-glow/style.css']); ?>

    
    <?php
        // Cobalt Glow 테마 기본값으로 설정 변경
        $_cp  = \App\Models\Setting::get('theme_primary',     '#3b82f6'); // 코발트 블루
        $_ca  = \App\Models\Setting::get('theme_accent',      '#60a5fa'); // 연한 블루
        $_ctb = \App\Models\Setting::get('theme_topbar_bg',   '#030712'); // 미드나잇 블랙
        $_ctt = \App\Models\Setting::get('theme_topbar_text', '#ffffff');
        $_cnb = \App\Models\Setting::get('theme_nav_bg',      'rgba(3, 7, 18, 0.8)');
        $_cnt = \App\Models\Setting::get('theme_nav_text',    '#ffffff');
        $_csb = \App\Models\Setting::get('theme_site_bg',     '#030712');
        $_ctx = \App\Models\Setting::get('theme_text',        '#ffffff');

        // 색상 명도 계산 함수
        $_darken  = fn($h,$f=0.78) => sprintf('#%02x%02x%02x',
            (int)(hexdec(substr(ltrim($h,'#'),0,2))*$f),
            (int)(hexdec(substr(ltrim($h,'#'),2,2))*$f),
            (int)(hexdec(substr(ltrim($h,'#'),4,2))*$f));
        
        $_lighten = fn($h,$f=0.9) => sprintf('#%02x%02x%02x',
            min(255,(int)((($r=hexdec(substr(ltrim($h,'#'),0,2)))+(255-$r)*$f))),
            min(255,(int)((($g=hexdec(substr(ltrim($h,'#'),2,2)))+(255-$g)*$f))),
            min(255,(int)((($b=hexdec(substr(ltrim($h,'#'),4,2)))+(255-$b)*$f))));

        $_cpDark   = $_darken($_cp);
        $_cpLight  = $_lighten($_cp, 0.2); // 다크 테마에 맞춰 라이트 강도 조절
        $_cpLight2 = $_lighten($_cp, 0.1);
    ?>

    <style>
    :root {
        /* 사이트 기본 변수 */
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

        /* Cobalt Glow 특화 변수 (기존 swn-style 호환 유지) */
        --swn-primary:      <?php echo e($_cp); ?>;
        --swn-primary-dark: <?php echo e($_cpDark); ?>;
        --swn-accent:       <?php echo e($_ca); ?>;
        --swn-sky:          <?php echo e($_cpLight2); ?>;
        --swn-sky-dark:     <?php echo e($_cpLight); ?>;
        --swn-bg:           <?php echo e($_csb); ?>;
        --swn-text:         <?php echo e($_ctx); ?>;

        /* Cobalt Glow 시그니처 배경 효과 색상 */
        --glow-color:       <?php echo e($_cp); ?>;
    }

    body { 
        background-color: var(--site-bg); 
        color: var(--site-text); 
        font-family: 'Inter', 'Noto Sans KR', sans-serif;
    }

    /* 내비게이션 및 버튼 유틸리티 */
    .site-nav-link { color: var(--site-nav-text); transition: all .2s ease; }
    .site-nav-link:hover { color: var(--site-primary); background: rgba(255,255,255,0.05); }
    .site-nav-active { background: var(--site-primary) !important; color: #fff !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
    
    .site-primary-btn { background: var(--site-primary); color: #fff; border: none; transition: all .2s; }
    .site-primary-btn:hover { background: var(--site-primary-dark); transform: translateY(-1px); }
    
    .site-primary-text { color: var(--site-primary); }
    .site-accent-badge { background: var(--site-accent); color: #fff; }

    /* 레이아웃 전용 클래스 오버라이드 */
    .cobalt-nav { background: var(--site-nav-bg) !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; }
    .cobalt-bg-glow::after { background: radial-gradient(ellipse at center, var(--glow-color) 0%, transparent 70%) !important; opacity: 0.15; }
    </style>

    <?php echo $__env->yieldPushContent('skin-css'); ?>

    
    <?php if(request()->is('admin*')): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/admin.css']); ?>
    <?php endif; ?>

    
    <?php if(App\Models\Setting::get('custom_head_script')): ?>
        <?php echo App\Models\Setting::get('custom_head_script'); ?>

    <?php endif; ?>
</head>
<body class="min-h-screen relative"><?php /**PATH /home/laraboard/www/resources/views/skin/layout/cobalt-glow/head.blade.php ENDPATH**/ ?>