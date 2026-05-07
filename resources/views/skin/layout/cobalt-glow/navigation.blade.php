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
    <nav class="cobalt-nav">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between gap-4">
            {{-- 메뉴 링크 --}}
            @php
                $_navCategories = \App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
                $_navBoards     = \App\Models\Board::where('is_active', true)->orderBy('order')->limit(4)->get();
            @endphp
            <div class="flex items-center overflow-x-auto" style="scrollbar-width:none;gap:0;">
                <a href="{{ route('news.index') }}"
                   class="site-nav-link {{ request()->routeIs('news.index') && !request('category') ? 'site-nav-active' : '' }}"
                   style="white-space:nowrap;">전체기사</a>
                @foreach($_navCategories as $_cat)
                <a href="{{ route('news.index', ['category' => $_cat->slug]) }}"
                   class="site-nav-link {{ request()->routeIs('news.index') && request('category') === $_cat->slug ? 'site-nav-active' : '' }}"
                   style="white-space:nowrap;">{{ $_cat->name }}</a>
                @endforeach
                @foreach($_navBoards as $_brd)
                <a href="{{ route('bbs.index', $_brd->board_id) }}"
                   class="site-nav-link {{ request()->is('bbs/'.$_brd->board_id.'*') ? 'site-nav-active' : '' }}"
                   style="white-space:nowrap;">{{ $_brd->board_name }}</a>
                @endforeach
            </div>
            {{-- 우측 유틸 --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('news.search') }}"
                   style="display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;color:rgba(255,255,255,0.5);transition:all .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='#fff';"
                   onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.5)';"
                   aria-label="검색">
                    <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="18" y2="18"/>
                    </svg>
                </a>
            </div>
        </div>
    </nav>
</header>
