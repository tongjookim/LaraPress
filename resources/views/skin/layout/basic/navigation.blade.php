<nav style="background:var(--site-nav-bg);border-bottom:1px solid #e5e7eb;" class="shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-8">
                <a href="/" class="flex items-center gap-2">
                    @if(App\Models\Setting::get('logo_image'))
                        <img src="{{ App\Models\Setting::get('logo_image') }}" alt="{{ App\Models\Setting::get('logo_text') ?: App\Models\Setting::get('site_name', 'Laraboard') }}" style="max-height:36px;max-width:160px;object-fit:contain;">
                    @else
                        <span class="text-2xl font-bold site-primary-text">
                            {{ App\Models\Setting::get('logo_text') ?: App\Models\Setting::get('site_name', 'Laraboard') }}
                        </span>
                    @endif
                    @if(App\Models\Setting::get('logo_tagline'))
                        <span class="hidden lg:block text-xs text-gray-400 font-normal leading-tight" style="max-width:120px;">{{ App\Models\Setting::get('logo_tagline') }}</span>
                    @endif
                </a>
                <div class="hidden md:flex space-x-6">
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
                       class="font-medium transition {{ $active ? 'site-nav-active rounded-md px-2 py-1' : 'site-nav-link' }}">
                        {{ $menu->label }}
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    @if(auth()->user()->canAccessAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden md:inline text-sm site-nav-link">관리자</a>
                    @endif
                    <a href="{{ route('profile.show') }}" class="hidden md:flex text-sm site-nav-link items-center gap-1">
                        @if(auth()->user()->profile_image)
                            <img src="{{ auth()->user()->profile_image }}" alt="" style="width:22px;height:22px;border-radius:50%;object-fit:cover;">
                        @endif
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:inline">
                        @csrf
                        <button class="text-sm site-nav-link" style="background:none;border:none;cursor:pointer;">로그아웃</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hidden md:inline text-sm site-nav-link">로그인</a>
                    <a href="{{ route('register') }}" class="hidden md:inline px-4 py-2 rounded-lg text-sm site-primary-btn">회원가입</a>
                @endauth

                {{-- 모바일 햄버거 버튼 --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg site-nav-link" aria-label="메뉴 열기"
                        onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg id="mobile-menu-icon-open"  class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="mobile-menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 모바일 드롭다운 메뉴 --}}
    <div id="mobile-menu" class="hidden md:hidden" style="border-top:1px solid #e5e7eb;background:var(--site-nav-bg);">
        <div class="px-4 py-3 space-y-1">
            @foreach(App\Models\NavMenu::activeItems() as $menu)
            @php
                $parsed   = parse_url($menu->url);
                $menuPath = $parsed['path'] ?? '/';
                $curPath  = '/' . ltrim(request()->path(), '/');
                $mActive  = $curPath === $menuPath;
            @endphp
            <a href="{{ $menu->url }}" target="{{ $menu->target }}"
               class="block px-3 py-2 rounded-lg font-medium text-sm transition {{ $mActive ? 'site-nav-active' : 'site-nav-link' }}">
                {{ $menu->label }}
            </a>
            @endforeach

            <div style="border-top:1px solid #e5e7eb;margin:8px 0;padding-top:8px;">
                @auth
                    @if(auth()->user()->canAccessAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm site-nav-link">관리자 패널</a>
                    @endif
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm site-nav-link">
                        @if(auth()->user()->profile_image)
                            <img src="{{ auth()->user()->profile_image }}" alt="" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">
                        @endif
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left px-3 py-2 rounded-lg text-sm site-nav-link" style="background:none;border:none;cursor:pointer;">로그아웃</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm site-nav-link">로그인</a>
                    <a href="{{ route('register') }}" class="block mt-1 px-3 py-2 rounded-lg text-sm text-center site-primary-btn">회원가입</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
// 모바일 메뉴 아이콘 토글
document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    var open = !document.getElementById('mobile-menu').classList.contains('hidden');
    document.getElementById('mobile-menu-icon-open').classList.toggle('hidden', open);
    document.getElementById('mobile-menu-icon-close').classList.toggle('hidden', !open);
});
</script>
