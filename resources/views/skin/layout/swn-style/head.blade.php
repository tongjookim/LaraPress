<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ App\Models\Setting::get('meta_title') ?: App\Models\Setting::get('site_name', 'Laraboard') }} @yield('title')</title>
    @if(App\Models\Setting::get('favicon'))
    <link rel="icon" href="{{ App\Models\Setting::get('favicon') }}">
    @endif

    <meta name="description" content="{{ App\Models\Setting::get('meta_description') ?: App\Models\Setting::get('site_description', '') }}">
    <meta name="keywords" content="{{ App\Models\Setting::get('meta_keywords') ?: App\Models\Setting::get('site_keywords', '') }}">
    @if(App\Models\Setting::get('meta_author'))
    <meta name="author" content="{{ App\Models\Setting::get('meta_author') }}">
    @endif

    {{-- 뉴스 포털용 폰트 --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+KR:wght@400;700;900&family=Noto+Sans+KR:wght@300;400;500;700;900&family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">

    {{-- Tailwind + 공통 JS + SWN 레이아웃 CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/views/skin/layout/swn-style/style.css'])

    @php
    // 색상 설정 (미설정 시 swn-style 기본값)
    $_cp  = \App\Models\Setting::get('theme_primary',     '#1a6fb5');
    $_ca  = \App\Models\Setting::get('theme_accent',      '#e8524a');
    $_ctb = \App\Models\Setting::get('theme_topbar_bg',   '#e8524a');
    $_ctt = \App\Models\Setting::get('theme_topbar_text', '#ffffff');
    $_cnb = \App\Models\Setting::get('theme_nav_bg',      '#e8f4fd');
    $_cnt = \App\Models\Setting::get('theme_nav_text',    '#1e3a5f');
    $_csb = \App\Models\Setting::get('theme_site_bg',     '#f5f5f5');
    $_ctx = \App\Models\Setting::get('theme_text',        '#1a1a1a');
    $_darken  = fn($h,$f=0.78) => sprintf('#%02x%02x%02x',
        (int)(hexdec(substr(ltrim($h,'#'),0,2))*$f),
        (int)(hexdec(substr(ltrim($h,'#'),2,2))*$f),
        (int)(hexdec(substr(ltrim($h,'#'),4,2))*$f));
    $_lighten = fn($h,$f=0.9) => sprintf('#%02x%02x%02x',
        min(255,(int)((($r=hexdec(substr(ltrim($h,'#'),0,2)))+(255-$r)*$f))),
        min(255,(int)((($g=hexdec(substr(ltrim($h,'#'),2,2)))+(255-$g)*$f))),
        min(255,(int)((($b=hexdec(substr(ltrim($h,'#'),4,2)))+(255-$b)*$f))));
    $_cpDark   = $_darken($_cp);
    $_cpLight  = $_lighten($_cp, 0.88);
    $_cpLight2 = $_lighten($_cp, 0.94);
    @endphp
    <style>
    :root {
        --site-primary:       {{ $_cp }};
        --site-primary-dark:  {{ $_cpDark }};
        --site-primary-light: {{ $_cpLight }};
        --site-accent:        {{ $_ca }};
        --site-topbar-bg:     {{ $_ctb }};
        --site-topbar-text:   {{ $_ctt }};
        --site-nav-bg:        {{ $_cnb }};
        --site-nav-text:      {{ $_cnt }};
        --site-bg:            {{ $_csb }};
        --site-text:          {{ $_ctx }};
        /* swn-style 변수 덮어쓰기 */
        --swn-primary:      {{ $_cp }};
        --swn-primary-dark: {{ $_cpDark }};
        --swn-accent:       {{ $_ca }};
        --swn-sky:          {{ $_cpLight2 }};
        --swn-sky-dark:     {{ $_cpLight }};
        --swn-bg:           {{ $_csb }};
        --swn-text:         {{ $_ctx }};
    }
    body { background-color: var(--site-bg); color: var(--site-text); }
    .site-nav-link { color: var(--site-nav-text); transition: color .15s; }
    .site-nav-link:hover { color: var(--site-primary); background: var(--site-primary-light); }
    .site-nav-active { background: var(--site-primary) !important; color: #fff !important; }
    .site-nav-active:hover { background: var(--site-primary-dark) !important; color: #fff !important; }
    .site-primary-btn { background: var(--site-primary); color: #fff; border: none; transition: background .15s; }
    .site-primary-btn:hover { background: var(--site-primary-dark); color: #fff; }
    .site-primary-text { color: var(--site-primary); }
    .site-accent-badge { background: var(--site-accent); color: #fff; }
    /* swn-style 특화 오버라이드 */
    .swn-topbar { background: var(--site-topbar-bg) !important; color: var(--site-topbar-text) !important; }
    .swn-nav { background: var(--site-nav-bg) !important; border-bottom-color: var(--site-primary) !important; }
    </style>

    @stack('skin-css')

    @if(request()->is('admin*'))
        @vite(['resources/css/admin.css'])
    @endif

    @if(App\Models\Setting::get('custom_head_script'))
    {!! App\Models\Setting::get('custom_head_script') !!}
    @endif
</head>
<body class="min-h-screen">
