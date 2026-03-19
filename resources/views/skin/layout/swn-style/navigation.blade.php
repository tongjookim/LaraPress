{{-- 최상단 알림바 --}}
<div class="swn-topbar text-center py-2 px-4 text-sm font-medium tracking-tight">
    <span style="opacity:.9;">📢 {{ App\Models\Setting::get('site_description', '커뮤니티에 오신 것을 환영합니다') }}</span>
</div>

{{-- 헤더 --}}
<header class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4">
        {{-- 상단: 날짜 + 유틸 메뉴 --}}
        <div class="flex justify-between items-center py-2 text-xs border-b border-gray-100" style="color:var(--site-nav-text,#4b5563);">
            <span class="swn-datetime">{{ now()->format('Y년 m월 d일 l') }}</span>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-1.5 font-medium site-nav-link transition">
                        @if(auth()->user()->profile_image)
                            <img src="{{ auth()->user()->profile_image }}" alt="" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">
                        @endif
                        {{ auth()->user()->name }}님
                    </a>
                    @if(auth()->user()->canAccessAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="site-nav-link transition">관리자</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="site-nav-link transition" style="background:none;border:none;cursor:pointer;font-size:12px;">로그아웃</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="site-nav-link transition">로그인</a>
                    <a href="{{ route('register') }}" class="site-nav-link transition">회원가입</a>
                @endauth
            </div>
        </div>

        {{-- 로고 영역 --}}
        <div class="flex items-center justify-center py-6 relative">
            @if(App\Models\Setting::get('logo_image'))
            <a href="/" class="block text-center group">
                <img src="{{ App\Models\Setting::get('logo_image') }}"
                     alt="{{ App\Models\Setting::get('logo_text') ?: App\Models\Setting::get('site_name', 'Laraboard') }}"
                     style="max-height:72px;max-width:320px;object-fit:contain;margin:0 auto;">
                @if(App\Models\Setting::get('logo_tagline'))
                <p class="text-xs text-gray-400 mt-2 tracking-widest uppercase">{{ App\Models\Setting::get('logo_tagline') }}</p>
                @endif
            </a>
            @else
            <a href="/" class="text-center group">
                <h1 class="font-serif-title text-4xl md:text-5xl font-black tracking-tight transition site-primary-text"
                    style="color:var(--site-text,#1a1a1a);">
                    {{ App\Models\Setting::get('logo_text') ?: App\Models\Setting::get('site_name', 'The Laraboard') }}
                </h1>
                <p class="text-xs text-gray-400 mt-1 tracking-widest uppercase">
                    {{ App\Models\Setting::get('logo_tagline') ?: 'Community &amp; Board Platform' }}
                </p>
            </a>
            @endif
        </div>
    </div>

    {{-- 네비게이션 바 --}}
    <nav class="swn-nav">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-11 overflow-x-auto">
                <div class="flex items-center gap-0 min-w-0">
                    @foreach(App\Models\NavMenu::activeItems() as $menu)
                    @php
                        $parsed   = parse_url($menu->url);
                        $menuPath = $parsed['path'] ?? '/';
                        $menuQs   = isset($parsed['query']) ? $parsed['query'] : null;
                        $curPath  = '/' . ltrim(request()->path(), '/');
                        $curQs    = request()->getQueryString() ?: null;
                        $active   = $curPath === $menuPath && ($menuQs === null || $curQs === $menuQs);
                    @endphp
                    <a href="{{ $menu->url }}"
                       target="{{ $menu->target }}"
                       class="px-4 py-2 text-sm font-bold whitespace-nowrap transition rounded-t
                              {{ $active ? 'site-nav-active' : 'site-nav-link' }}">
                        {{ $menu->label }}
                    </a>
                    @endforeach
                </div>

                {{-- 간단 검색 --}}
                <div class="hidden md:flex items-center ml-4">
                    <div class="relative">
                        <input type="text" placeholder="검색어 입력"
                               style="border-color:var(--site-primary-light,#cce7f7);width:176px;height:28px;padding:0 28px 0 12px;font-size:12px;border:1px solid;border-radius:4px;background:rgba(255,255,255,.8);"
                               onfocus="this.style.outline='none';this.style.background='#fff';">
                        <svg style="width:14px;height:14px;position:absolute;right:8px;top:50%;transform:translateY(-50%);color:var(--site-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
