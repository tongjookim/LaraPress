<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>관리자 @yield('title') - {{ App\Models\Setting::get('site_name', 'Laraboard') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f0f0f1;
            color: #1d2327;
            -webkit-font-smoothing: antialiased;
        }

        /* ── 상단바 ── */
        .wp-topbar {
            position: fixed; top: 0; left: 0; right: 0; height: 46px; z-index: 300;
            background: #1d2327;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 14px; font-size: 13px; gap: 10px;
        }
        .wp-topbar a { color: #c3c4c7; text-decoration: none; transition: color .15s; }
        .wp-topbar a:hover { color: #72aee6; }
        .wp-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .wp-topbar-right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }

        /* 햄버거 버튼 */
        .wp-hamburger {
            display: none;
            background: none; border: none; cursor: pointer;
            padding: 6px; color: #c3c4c7; flex-shrink: 0;
            border-radius: 4px; transition: background .15s;
        }
        .wp-hamburger:hover { background: #2c3338; color: #fff; }
        .wp-hamburger svg { display: block; }

        /* ── 오버레이 ── */
        .wp-sidebar-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 198;
            background: rgba(0,0,0,.5);
        }
        .wp-sidebar-overlay.open { display: block; }

        /* ── 사이드바 ── */
        .wp-sidebar {
            position: fixed; top: 46px; left: 0; bottom: 0; width: 240px; z-index: 199;
            background: #1d2327; overflow-y: auto;
            transition: transform .25s cubic-bezier(.4,0,.2,1);
        }
        .wp-sidebar::-webkit-scrollbar { width: 0; }
        .wp-sidebar-logo {
            padding: 16px 14px 12px; border-bottom: 1px solid #3c434a;
        }
        .wp-sidebar-logo a {
            color: #fff; font-weight: 900; font-size: 16px; text-decoration: none;
            display: flex; align-items: center; gap: 8px;
        }
        .wp-sidebar-logo span { font-size: 10px; color: #72aee6; }

        .wp-menu-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; color: #c3c4c7; font-size: 13px; font-weight: 400;
            text-decoration: none; transition: all .15s; border-left: 3px solid transparent;
        }
        .wp-menu-item:hover { background: #2c3338; color: #72aee6; }
        .wp-menu-item.active {
            background: #2271b1; color: #fff; border-left-color: #72aee6;
            font-weight: 700;
        }
        .wp-menu-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        .wp-menu-separator {
            height: 1px; background: #3c434a; margin: 8px 14px;
        }
        .wp-menu-label {
            padding: 12px 14px 4px; font-size: 10px; font-weight: 700;
            color: #8c8f94; text-transform: uppercase; letter-spacing: 0.08em;
        }

        /* ── 메인 영역 ── */
        .wp-main {
            margin-left: 240px; margin-top: 46px; min-height: calc(100vh - 46px);
        }
        .wp-content { padding: 20px 24px; }
        .wp-page-title {
            font-size: 22px; font-weight: 400; color: #1d2327; margin-bottom: 20px;
            line-height: 1.3;
        }

        /* ── 반응형 그리드 유틸리티 ── */
        .admin-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px; margin-bottom: 20px;
        }
        .admin-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .wp-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* ── 위젯 카드 ── */
        .wp-widget {
            background: #fff; border: 1px solid #c3c4c7;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .wp-widget-header {
            padding: 10px 14px; border-bottom: 1px solid #c3c4c7;
            font-size: 14px; font-weight: 700; color: #1d2327;
            background: #f6f7f7;
        }
        .wp-widget-body { padding: 14px; }

        /* ── 알림 ── */
        .wp-notice {
            border-left: 4px solid #00a32a; background: #fff;
            padding: 10px 14px; margin-bottom: 16px; font-size: 13px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .wp-notice-error { border-left-color: #d63638; }

        /* ── 테이블 ── */
        .wp-list-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .wp-list-table th {
            text-align: left; padding: 10px 12px; font-weight: 600;
            border-bottom: 1px solid #c3c4c7; color: #1d2327; background: #f6f7f7;
            white-space: nowrap;
        }
        .wp-list-table td {
            padding: 10px 12px; border-bottom: 1px solid #f0f0f1;
            vertical-align: middle;
        }
        .wp-list-table tr:hover td { background: #f6f7f7; }

        /* ── 버튼 ── */
        .wp-btn {
            display: inline-block; padding: 4px 12px; font-size: 13px;
            border-radius: 3px; text-decoration: none; cursor: pointer;
            border: 1px solid; font-weight: 400; line-height: 2;
            transition: all .15s; white-space: nowrap;
        }
        .wp-btn-primary { background: #2271b1; border-color: #2271b1; color: #fff; }
        .wp-btn-primary:hover { background: #135e96; border-color: #135e96; color: #fff; }
        .wp-btn-secondary { background: #f6f7f7; border-color: #8c8f94; color: #2c3338; }
        .wp-btn-secondary:hover { background: #f0f0f1; border-color: #2271b1; color: #2271b1; }
        .wp-btn-danger { background: #d63638; border-color: #d63638; color: #fff; }
        .wp-btn-danger:hover { background: #b32d2e; }
        .wp-btn-sm { padding: 2px 8px; font-size: 12px; }
        .wp-btn-warning { background: #dba617; border-color: #dba617; color: #fff; }

        /* ── 폼 ── */
        .wp-form-label { display: block; font-size: 13px; font-weight: 600; color: #1d2327; margin-bottom: 6px; }
        .wp-form-input {
            width: 100%; padding: 8px 10px; font-size: 13px;
            border: 1px solid #8c8f94; border-radius: 3px;
            background: #fff; color: #2c3338; line-height: 1.5;
        }
        .wp-form-input:focus { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; outline: none; }
        .wp-form-textarea { min-height: 100px; resize: vertical; font-family: inherit; }
        .wp-form-select { height: 38px; }
        .wp-form-help { font-size: 12px; color: #646970; margin-top: 4px; }
        .wp-form-group { margin-bottom: 16px; }

        /* ── 뱃지 ── */
        .wp-badge { display: inline-block; padding: 2px 8px; border-radius: 2px; font-size: 11px; font-weight: 600; }
        .wp-badge-admin { background: #dbeafe; color: #1d4ed8; }
        .wp-badge-active { background: #dcfce7; color: #166534; }
        .wp-badge-inactive { background: #fee2e2; color: #991b1b; }
        .wp-badge-pending { background: #fef9c3; color: #854d0e; }

        /* ── 반응형: 태블릿 (< 1024px) ── */
        @media (max-width: 1024px) {
            .admin-grid-4 { grid-template-columns: repeat(2, 1fr); }
        }

        /* ── 반응형: 모바일 (< 768px) ── */
        @media (max-width: 768px) {
            .wp-hamburger { display: flex; align-items: center; justify-content: center; }
            .wp-topbar-site-name { display: none; }

            .wp-sidebar {
                transform: translateX(-100%);
                top: 46px; width: 260px;
            }
            .wp-sidebar.open { transform: translateX(0); }

            .wp-main { margin-left: 0; }

            .admin-grid-4 { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 16px; }
            .admin-grid-2 { grid-template-columns: 1fr; }

            .wp-content { padding: 14px 12px; }
            .wp-page-title { font-size: 18px; margin-bottom: 14px; }
            .wp-widget-body { padding: 12px; }
        }

        /* ── 반응형: 소형 모바일 (< 480px) ── */
        @media (max-width: 480px) {
            .admin-grid-4 { grid-template-columns: 1fr 1fr; gap: 8px; }
            .wp-topbar-greeting { display: none; }
            .wp-content { padding: 10px; }
        }
    </style>
</head>
<body>

{{-- 사이드바 오버레이 (모바일) --}}
<div class="wp-sidebar-overlay" id="sidebarOverlay"></div>

{{-- 상단바 --}}
<div class="wp-topbar">
    <div class="wp-topbar-left">
        <button class="wp-hamburger" id="sidebarToggle" aria-label="메뉴 열기">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:6px;color:#c3c4c7;text-decoration:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
            <span class="wp-topbar-site-name">{{ App\Models\Setting::get('site_name', 'Laraboard') }}</span>
        </a>
        <a href="/" title="사이트 보기" style="color:#8c8f94;" class="wp-topbar-site-name">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </a>
    </div>
    <div class="wp-topbar-right">
        <span class="wp-topbar-greeting" style="color:#8c8f94;font-size:12px;">안녕하세요, {{ auth()->user()->name }}님</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button style="background:none;border:none;color:#c3c4c7;cursor:pointer;font-size:12px;padding:0;" onmouseover="this.style.color='#72aee6'" onmouseout="this.style.color='#c3c4c7'">로그아웃</button>
        </form>
    </div>
</div>

{{-- 사이드바 --}}
<div class="wp-sidebar" id="wpSidebar">
    <div class="wp-sidebar-logo">
        @php
            $adminLogoImage = App\Models\Setting::get('admin_logo_image', '');
            $adminLogoText  = App\Models\Setting::get('admin_logo_text', '') ?: 'Laraboard';
        @endphp
        <a href="{{ route('admin.dashboard') }}">
            @if($adminLogoImage)
                <img src="{{ $adminLogoImage }}" alt="{{ $adminLogoText }}" style="max-height:30px;max-width:150px;object-fit:contain;">
            @else
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#72aee6" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                {{ $adminLogoText }}
                <span>Admin</span>
            @endif
        </a>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="wp-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        <span>알림판</span>
    </a>

    <div class="wp-menu-separator"></div>
    <div class="wp-menu-label">콘텐츠</div>

    <a href="{{ route('admin.articles') }}" class="wp-menu-item {{ request()->routeIs('admin.articles', 'admin.article.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        <span>기사 관리</span>
    </a>

    @if(auth()->user()->hasMinRole('editor'))
    <a href="{{ route('admin.article-comments') }}" class="wp-menu-item {{ request()->routeIs('admin.article-comments') ? 'active' : '' }}">
        @php $pendingComments = \App\Models\ArticleComment::whereNull('deleted_at')->where('is_approved', false)->count(); @endphp
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <span>댓글 관리
            @if($pendingComments > 0)
            <span style="display:inline-block;min-width:16px;padding:0 4px;font-size:10px;font-weight:700;border-radius:8px;text-align:center;line-height:16px;background:#d63638;color:#fff;margin-left:4px;">{{ $pendingComments }}</span>
            @endif
        </span>
    </a>

    <a href="{{ route('admin.categories') }}" class="wp-menu-item {{ request()->routeIs('admin.categories', 'admin.category.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        <span>카테고리</span>
    </a>

    <a href="{{ route('admin.media') }}" class="wp-menu-item {{ request()->routeIs('admin.media', 'admin.media.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span>미디어</span>
    </a>
    @endif

    @if(auth()->user()->hasMinRole('admin'))
    <a href="{{ route('admin.boards') }}" class="wp-menu-item {{ request()->routeIs('admin.boards', 'admin.board.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <span>게시판 관리</span>
    </a>

    <a href="{{ route('admin.pages') }}" class="wp-menu-item {{ request()->routeIs('admin.pages', 'admin.page.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        <span>페이지 관리</span>
    </a>

    <a href="{{ route('admin.tools') }}" class="wp-menu-item {{ request()->routeIs('admin.tools*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        <span>도구</span>
    </a>
    @endif

    <div class="wp-menu-separator"></div>
    <div class="wp-menu-label">계정</div>

    <a href="{{ route('admin.my-profile') }}" class="wp-menu-item {{ request()->routeIs('admin.my-profile') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>내 프로필 편집</span>
    </a>

    @if(auth()->user()->hasMinRole('admin'))
    <div class="wp-menu-separator"></div>
    <div class="wp-menu-label">사용자</div>

    <a href="{{ route('admin.users') }}" class="wp-menu-item {{ request()->routeIs('admin.users', 'admin.user.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span>회원 관리</span>
    </a>

    <div class="wp-menu-separator"></div>
    <div class="wp-menu-label">외관</div>

    <a href="{{ route('admin.theme') }}" class="wp-menu-item {{ request()->routeIs('admin.theme*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
        <span>테마 설정</span>
    </a>

    <a href="{{ route('admin.top-banners') }}" class="wp-menu-item {{ request()->routeIs('admin.top-banners') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        <span>탑배너</span>
    </a>

    <div class="wp-menu-separator"></div>
    <div class="wp-menu-label">설정</div>

    <a href="{{ route('admin.settings') }}" class="wp-menu-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>사이트 설정</span>
    </a>

    <a href="{{ route('admin.seo') }}" class="wp-menu-item {{ request()->routeIs('admin.seo*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <span>SEO 설정</span>
    </a>

    <a href="{{ route('admin.statistics') }}" class="wp-menu-item {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>통계</span>
    </a>

    <a href="{{ route('admin.mail') }}" class="wp-menu-item {{ request()->routeIs('admin.mail*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <span>메일 관리</span>
    </a>

    <a href="{{ route('admin.plugins') }}" class="wp-menu-item {{ request()->routeIs('admin.plugins', 'admin.plugin.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <span>플러그인</span>
    </a>
    @endif

    <div class="wp-menu-separator"></div>

    <a href="/" class="wp-menu-item">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>사이트 보기</span>
    </a>
</div>

{{-- 메인 영역 --}}
<div class="wp-main">
    <div class="wp-content">

        @if(session('success'))
            <div class="wp-notice">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="wp-notice wp-notice-error">{{ session('error') }}</div>
        @endif

        @yield('admin-content')
    </div>
</div>

@stack('scripts')

<script>
(function () {
    var sidebar  = document.getElementById('wpSidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var toggle   = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (toggle) toggle.addEventListener('click', function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // 사이드바 내 링크 클릭 시 모바일에서 자동 닫기
    if (sidebar) sidebar.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
})();
</script>
</body>
</html>
