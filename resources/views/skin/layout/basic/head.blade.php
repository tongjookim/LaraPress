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

    {{-- 기본 메타태그 (개별 페이지가 @stack('head-meta')로 덮어쓸 수 있음) --}}
    @stack('head-meta')
    @hasSection('head-meta-override')
    @else
    <meta name="description" content="{{ App\Models\Setting::get('meta_description') ?: App\Models\Setting::get('site_description', '') }}">
    <meta name="keywords" content="{{ App\Models\Setting::get('meta_keywords') ?: App\Models\Setting::get('site_keywords', '') }}">
    @if(App\Models\Setting::get('meta_author'))
    <meta name="author" content="{{ App\Models\Setting::get('meta_author') }}">
    @endif
    <meta property="og:type"        content="{{ App\Models\Setting::get('meta_og_type','website') }}">
    <meta property="og:site_name"   content="{{ App\Models\Setting::get('site_name','Laraboard') }}">
    <meta property="og:title"       content="{{ App\Models\Setting::get('meta_title') ?: App\Models\Setting::get('site_name','Laraboard') }}">
    <meta property="og:description" content="{{ App\Models\Setting::get('meta_description') ?: App\Models\Setting::get('site_description','') }}">
    @if(App\Models\Setting::get('meta_og_image'))
    <meta property="og:image" content="{{ App\Models\Setting::get('meta_og_image') }}">
    @endif
    <meta name="twitter:card" content="{{ App\Models\Setting::get('meta_twitter_card','summary_large_image') }}">
    @if(App\Models\Setting::get('rss_enabled','1') === '1')
    <link rel="alternate" type="application/rss+xml" title="{{ App\Models\Setting::get('site_name','Laraboard') }}" href="{{ url('/feed') }}">
    @endif
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
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

    @stack('skin-css')

    @if(request()->is('admin*'))
        @vite(['resources/css/admin.css'])
    @endif

    @if(App\Models\Setting::get('custom_head_script'))
    {!! App\Models\Setting::get('custom_head_script') !!}
    @endif
</head>
<body style="background-color:var(--site-bg);color:var(--site-text);" class="text-gray-800">
