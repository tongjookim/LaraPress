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
    <meta name="keywords"    content="<?php echo e(App\Models\Setting::get('meta_keywords') ?: App\Models\Setting::get('site_keywords', '')); ?>">
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="<?php echo e(App\Models\Setting::get('site_name','Laraboard')); ?>">
    <meta property="og:title"       content="<?php echo e(App\Models\Setting::get('meta_title') ?: App\Models\Setting::get('site_name','Laraboard')); ?>">
    <meta property="og:description" content="<?php echo e(App\Models\Setting::get('meta_description') ?: App\Models\Setting::get('site_description','')); ?>">
    <?php if(App\Models\Setting::get('meta_og_image')): ?>
    <meta property="og:image" content="<?php echo e(App\Models\Setting::get('meta_og_image')); ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <?php if(App\Models\Setting::get('rss_enabled','1') === '1'): ?>
    <link rel="alternate" type="application/rss+xml" title="<?php echo e(App\Models\Setting::get('site_name','Laraboard')); ?>" href="<?php echo e(url('/feed')); ?>">
    <?php endif; ?>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
    /* ── NYT 색상/폰트 변수 ── */
    :root {
        --nyt-black:      #121212;
        --nyt-gray-dark:  #333333;
        --nyt-gray-mid:   #666666;
        --nyt-gray-light: #999999;
        --nyt-gray-bg:    #f7f7f5;
        --nyt-border:     #e2e2e2;
        --nyt-section:    #6288a5;
        --nyt-link:       #326891;
        --nyt-serif:      'Libre Baskerville', 'Georgia', 'Times New Roman', serif;
        --nyt-sans:       'Source Sans 3', 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }

    *, *::before, *::after { box-sizing: border-box; }

    html { font-size: 16px; }

    body {
        font-family: var(--nyt-sans);
        color: var(--nyt-black);
        background: #fff;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
    }

    a { color: inherit; text-decoration: none; }
    a:hover { text-decoration: underline; }

    img { display: block; max-width: 100%; }

    /* ── NYT 그리드 ── */
    .nyt-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    /* 6칸 그리드 */
    .nyt-grid-6 { display: grid; grid-template-columns: repeat(6, 1fr); gap: 0; }
    .nyt-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; }
    .nyt-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; }
    .nyt-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0; }

    .nyt-col-1 { grid-column: span 1; }
    .nyt-col-2 { grid-column: span 2; }
    .nyt-col-3 { grid-column: span 3; }
    .nyt-col-4 { grid-column: span 4; }

    /* ── 구분선 ── */
    .nyt-rule       { border: none; border-top: 3px solid var(--nyt-black); margin: 0; }
    .nyt-rule-light { border: none; border-top: 1px solid var(--nyt-border); margin: 0; }
    .nyt-rule-mid   { border: none; border-top: 1px solid #ccc; margin: 12px 0; }

    /* 세로 구분선 (컬럼 사이) */
    .nyt-col-divider { border-right: 1px solid var(--nyt-border); }
    .nyt-col-divider:last-child { border-right: none; }

    /* ── 섹션 라벨 ── */
    .nyt-section-label {
        font-family: var(--nyt-sans);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--nyt-section);
        display: block;
        margin-bottom: 4px;
    }

    /* ── 헤드라인 ── */
    .nyt-headline {
        font-family: var(--nyt-serif);
        font-weight: 700;
        line-height: 1.2;
        color: var(--nyt-black);
        margin: 0 0 6px;
    }
    .nyt-headline:hover { color: var(--nyt-link); }

    .nyt-headline-xl  { font-size: 2.2rem; }
    .nyt-headline-lg  { font-size: 1.5rem; }
    .nyt-headline-md  { font-size: 1.15rem; }
    .nyt-headline-sm  { font-size: 1rem; }
    .nyt-headline-xs  { font-size: .875rem; }

    /* ── 서머리 ── */
    .nyt-summary {
        font-family: var(--nyt-sans);
        font-size: .875rem;
        color: var(--nyt-gray-dark);
        line-height: 1.5;
        margin: 0 0 6px;
    }

    /* ── 바이라인 ── */
    .nyt-byline {
        font-family: var(--nyt-sans);
        font-size: .75rem;
        color: var(--nyt-gray-mid);
        margin: 0;
    }

    /* ── 기사 아이템 패딩 ── */
    .nyt-story { padding: 10px 14px 14px; }
    .nyt-story-img { overflow: hidden; }
    .nyt-story-img img { width: 100%; height: 100%; object-fit: cover; transition: opacity .2s; }
    .nyt-story-img img:hover { opacity: .92; }

    /* ── 섹션 헤더 ── */
    .nyt-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0 8px;
        border-top: 3px solid var(--nyt-black);
        margin-bottom: 0;
    }
    .nyt-section-header h2 {
        font-family: var(--nyt-serif);
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0;
        color: var(--nyt-black);
    }
    .nyt-section-header a.more {
        font-family: var(--nyt-sans);
        font-size: .75rem;
        color: var(--nyt-section);
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 600;
    }
    .nyt-section-header a.more:hover { text-decoration: underline; }

    /* ── 섹션 블록 ── */
    .nyt-section-block { margin-bottom: 24px; }

    /* ── 구독 배너 ── */
    .nyt-sub-banner {
        background: #1a1a1a;
        color: #fff;
        padding: 32px 0;
        margin: 32px 0;
        text-align: center;
    }
    .nyt-sub-banner h3 {
        font-family: var(--nyt-serif);
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0 0 8px;
        color: #fff;
    }
    .nyt-sub-banner p {
        font-size: .875rem;
        color: #aaa;
        margin: 0 0 20px;
    }
    .nyt-sub-btn {
        display: inline-block;
        padding: 10px 28px;
        background: #fff;
        color: #121212;
        font-family: var(--nyt-sans);
        font-size: .875rem;
        font-weight: 700;
        border-radius: 2px;
        text-decoration: none;
        transition: background .15s;
    }
    .nyt-sub-btn:hover { background: #f0f0f0; text-decoration: none; }

    /* ── 상단 유틸 바 ── */
    .nyt-topbar {
        background: #fff;
        border-bottom: 1px solid var(--nyt-border);
        font-family: var(--nyt-sans);
        font-size: 12px;
    }
    .nyt-topbar-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .nyt-topbar-links { display: flex; align-items: center; gap: 14px; flex-wrap: nowrap; overflow: hidden; }
    .nyt-topbar-links a { color: var(--nyt-gray-dark); font-weight: 600; white-space: nowrap; }
    .nyt-topbar-links a:hover { text-decoration: underline; }
    .nyt-topbar-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
    .nyt-topbar-actions a {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border: 1px solid var(--nyt-black);
        border-radius: 2px;
        color: var(--nyt-black);
        white-space: nowrap;
    }
    .nyt-topbar-actions a:hover { text-decoration: none; background: var(--nyt-black); color: #fff; }
    .nyt-topbar-actions a.filled { background: var(--nyt-black); color: #fff; }
    .nyt-topbar-actions a.filled:hover { background: #333; }

    /* ── 마스트헤드 ── */
    .nyt-masthead {
        padding: 10px 0 0;
        text-align: center;
    }
    .nyt-masthead .nyt-container {
        border-bottom: 1px solid var(--nyt-border);
    }
    .nyt-masthead-date {
        font-family: var(--nyt-sans);
        font-size: 11.5px;
        color: var(--nyt-gray-mid);
        margin-bottom: 4px;
        letter-spacing: .02em;
    }
    .nyt-masthead-logo {
        font-family: var(--nyt-serif);
        font-size: clamp(2rem, 6vw, 3.8rem);
        font-weight: 700;
        letter-spacing: -.02em;
        color: var(--nyt-black);
        line-height: 1;
        margin: 0;
        display: block;
    }
    .nyt-masthead-logo:hover { text-decoration: none; color: var(--nyt-black); }
    .nyt-masthead-rules {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin: 8px 0 0;
    }
    .nyt-masthead-rule1 { border: none; border-top: 3px solid var(--nyt-black); margin: 0; }
    .nyt-masthead-rule2 { border: none; border-top: 1px solid var(--nyt-black); margin: 0; }

    /* ── 섹션 내비 ── */
    .nyt-secnav {
        background: #fff;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .nyt-secnav-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .nyt-secnav-scroll::-webkit-scrollbar { display: none; }
    .nyt-secnav-inner {
        display: flex;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
        gap: 0;
        white-space: nowrap;
        border-bottom: none;
    }
    .nyt-secnav-inner a {
        font-family: var(--nyt-sans);
        font-size: 13px;
        font-weight: 600;
        color: var(--nyt-gray-dark);
        padding: 10px 14px 9px;
        display: inline-block;
        border-bottom: 3px solid transparent;
        transition: border-color .15s;
        margin-bottom: -1px;
    }
    .nyt-secnav-inner a:hover { border-bottom-color: var(--nyt-black); text-decoration: none; color: var(--nyt-black); }
    .nyt-secnav-inner a.active { border-bottom-color: var(--nyt-black); color: var(--nyt-black); }

    /* ── 섹션 내비 드롭다운 ── */
    .nyt-secnav-item {
        display: inline-block;
        position: relative;
    }
    .nyt-secnav-item > a {
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .nyt-secnav-item > a .nyt-caret {
        width: 7px; height: 7px;
        border-right: 1.5px solid currentColor;
        border-bottom: 1.5px solid currentColor;
        transform: rotate(45deg);
        margin-top: -3px;
        flex-shrink: 0;
    }
    .nyt-secnav-dropdown {
        display: none;
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        min-width: 160px;
        background: #fff;
        border: 1px solid var(--nyt-border);
        border-top: 2px solid var(--nyt-black);
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,.12);
    }
    .nyt-secnav-dropdown.open {
        display: block;
    }
    .nyt-secnav-dropdown a {
        display: block;
        padding: 9px 16px;
        font-family: var(--nyt-sans);
        font-size: 13px;
        font-weight: 600;
        color: var(--nyt-gray-dark);
        border-bottom: 1px solid var(--nyt-border) !important;
        white-space: nowrap;
    }
    .nyt-secnav-dropdown a:last-child { border-bottom: none !important; }
    .nyt-secnav-dropdown a:hover { background: var(--nyt-gray-bg); color: var(--nyt-black); text-decoration: none; }

    /* ── 검색 버튼 ── */
    .nyt-search-btn {
        margin-left: auto;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--nyt-gray-dark);
        border-radius: 2px;
        transition: color .15s, background .15s;
    }
    .nyt-search-btn:hover { color: var(--nyt-black); background: var(--nyt-gray-bg); }
    .nyt-search-btn.active { color: var(--nyt-black); }

    /* ── 검색 오버레이 ── */
    .nyt-search-overlay {
        display: none;
        background: #fff;
        border-bottom: none;
        box-shadow: 0 4px 16px rgba(0,0,0,.10);
        z-index: 200;
    }
    .nyt-search-overlay.open { display: block; }
    .nyt-search-overlay-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 12px 16px;
    }
    .nyt-search-form {
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid var(--nyt-black);
        padding-bottom: 8px;
    }
    .nyt-search-form-icon { flex-shrink: 0; color: var(--nyt-gray-mid); }
    .nyt-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-family: var(--nyt-serif);
        font-size: 1.2rem;
        color: var(--nyt-black);
        background: transparent;
        min-width: 0;
    }
    .nyt-search-input::placeholder { color: #aaa; }
    .nyt-search-submit {
        font-family: var(--nyt-sans);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        padding: 6px 14px;
        background: var(--nyt-black);
        color: #fff;
        border: none;
        border-radius: 2px;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .nyt-search-submit:hover { background: #333; }
    .nyt-search-close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 22px;
        color: var(--nyt-gray-mid);
        line-height: 1;
        flex-shrink: 0;
    }
    .nyt-search-close:hover { color: var(--nyt-black); }

    /* ── 홈/목록 레이아웃 클래스 ── */
    /* 히어로 그리드: 2fr | 1px 구분선 | 1fr */
    .nyt-home-hero {
        display: grid;
        grid-template-columns: 2fr 1px 1fr;
        gap: 0;
        border-top: none;
        margin-top: 12px;
        border-bottom: 1px solid var(--nyt-border);
    }
    /* 카테고리 그리드: 2fr | 1px 구분선 | 1fr */
    .nyt-home-cat {
        display: grid;
        grid-template-columns: 2fr 1px 1fr;
        gap: 0;
        border-bottom: 1px solid var(--nyt-border);
        padding: 14px 0 16px;
    }
    /* 세로 구분선 */
    .nyt-vsep { background: var(--nyt-border); }
    /* 목록 + 사이드바 레이아웃 */
    .nyt-list-wrap {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 0 32px;
    }

    /* ── 반응형 ── */
    @media (max-width: 1024px) {
        .nyt-grid-6 { grid-template-columns: repeat(3, 1fr); }
        .nyt-grid-4 { grid-template-columns: repeat(2, 1fr); }
        .nyt-headline-xl { font-size: 1.8rem; }
    }
    @media (max-width: 768px) {
        /* 그리드 */
        .nyt-grid-6, .nyt-grid-4, .nyt-grid-3 { grid-template-columns: 1fr 1fr; }
        .nyt-grid-2 { grid-template-columns: 1fr; }
        .nyt-col-3, .nyt-col-4 { grid-column: span 2; }
        /* 홈 레이아웃 */
        .nyt-home-hero, .nyt-home-cat { grid-template-columns: 1fr; }
        .nyt-vsep { display: none; }
        /* 목록 레이아웃 */
        .nyt-list-wrap { grid-template-columns: 1fr; }
        .nyt-list-wrap > aside {
            margin-top: 32px;
            border-top: 3px solid var(--nyt-black);
            padding-top: 16px;
        }
        /* 타이포 */
        .nyt-headline-xl { font-size: 1.5rem; }
        .nyt-headline-lg { font-size: 1.25rem; }
        /* 네비 */
        .nyt-topbar-links { display: none; }
        .nyt-masthead-logo { font-size: 2rem; }
        /* 마스트헤드 날짜 */
        .nyt-masthead-date { display: none; }
    }
    @media (max-width: 480px) {
        .nyt-grid-6, .nyt-grid-4, .nyt-grid-3, .nyt-grid-2 { grid-template-columns: 1fr; }
        .nyt-col-1, .nyt-col-2, .nyt-col-3, .nyt-col-4 { grid-column: span 1; }
        .nyt-col-divider { border-right: none; border-bottom: 1px solid var(--nyt-border); }
        .nyt-story { padding: 12px 0; }
        /* 컨테이너 패딩 축소 */
        .nyt-container { padding: 0 12px; }
        /* 홈 히어로 내부 패딩 리셋 */
        .nyt-home-hero > div:first-child { padding-right: 0 !important; }
        .nyt-home-cat > div:first-child { padding-right: 0 !important; }
        .nyt-home-cat > div:last-child {
            padding-left: 0 !important;
            border-top: 1px solid var(--nyt-border);
            margin-top: 14px;
            padding-top: 14px !important;
        }
    }
    /* PC 화면에서는 가로 스크롤 속성을 풀어주어 드롭다운 메뉴가 잘리지 않게 합니다 */
    @media (min-width: 769px) {
        .nyt-secnav-scroll {
            overflow: visible;
        }
    }
    </style>
    <?php echo $__env->yieldPushContent('skin-css'); ?>

    <?php if(request()->is('admin*')): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/admin.css']); ?>
    <?php endif; ?>
    <?php if(App\Models\Setting::get('custom_head_script')): ?>
    <?php echo App\Models\Setting::get('custom_head_script'); ?>

    <?php endif; ?>
</head>
<body>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/newyorktimes-style/head.blade.php ENDPATH**/ ?>