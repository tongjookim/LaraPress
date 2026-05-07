{{-- resources/views/skin/layout/cobalt-glow/footer.blade.php --}}
@php
    $footerGroups      = App\Models\NavMenu::footerGroups();
    $footerCategories  = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
    
    // 언론사 정보 로드
    $pressInfo = [
        'masthead' => App\Models\Setting::get('press_masthead'),
        'reg_num'  => App\Models\Setting::get('press_registration_number'),
        'pub'      => App\Models\Setting::get('press_publisher'),
        'edt'      => App\Models\Setting::get('press_editor'),
        'addr'     => App\Models\Setting::get('press_address'),
        'post'     => App\Models\Setting::get('press_postal_code'),
        'phone'    => App\Models\Setting::get('press_phone'),
        'fax'      => App\Models\Setting::get('press_fax'),
        'email'    => App\Models\Setting::get('press_email'),
        'youth'    => App\Models\Setting::get('press_youth_manager'),
        'priv'     => App\Models\Setting::get('press_privacy_manager'),
        'griev'    => App\Models\Setting::get('press_grievance_manager'),
    ];
    $hasPressInfo = $pressInfo['masthead'] || $pressInfo['pub'] || $pressInfo['addr'];
@endphp

{{-- 1. 사이트 정보 바 (통계 및 메뉴) --}}
<div class="footer-info-bar mt-20">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            {{-- 메뉴 및 카테고리 영역 --}}
            <div class="lg:col-span-2 space-y-6">
                @foreach($footerGroups as $groupName => $groupItems)
                <div class="flex flex-wrap items-center gap-y-2">
                    <span class="footer-nav-label w-24">{{ $groupName }}</span>
                    <div class="flex flex-wrap gap-x-5">
                        @foreach($groupItems as $fitem)
                        <a href="{{ $fitem->url }}" target="{{ $fitem->target }}" class="footer-link">{{ $fitem->label }}</a>
                        @endforeach
                    </div>
                </div>
                @endforeach

                @if($footerCategories->isNotEmpty())
                <div class="flex flex-wrap items-center gap-y-2 pt-4 border-t border-white/5">
                    <span class="footer-nav-label w-24">카테고리</span>
                    <div class="flex flex-wrap gap-x-5">
                        @foreach($footerCategories as $cat)
                        <a href="{{ route('news.index', ['category' => $cat->slug]) }}" class="footer-link">{{ $cat->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- 사이트 통계 및 설명 --}}
            <div class="text-right border-l border-white/5 pl-10 hidden lg:block">
                <p class="text-white font-bold text-lg mb-1">{{ App\Models\Setting::get('site_name', 'Cobalt Glow') }}</p>
                <p class="text-white/50 text-xs mb-4">{{ App\Models\Setting::get('site_description', 'Modern Community Platform') }}</p>
                <div class="inline-flex items-center gap-3 px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-full text-[10px] font-bold text-blue-400 uppercase tracking-widest">
                    Users {{ number_format(App\Models\User::count()) }} <span class="opacity-20">|</span> Posts {{ number_format(App\Models\Post::count()) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. 하단 저작권 + 언론사 법적 정보 --}}
<footer class="bg-gray-950 py-10">
    <div class="max-w-7xl mx-auto px-4">
        @if($hasPressInfo)
        <div class="press-info-section pt-8 mb-8">
            <div class="flex flex-wrap gap-y-1">
                @if($pressInfo['masthead'])<span class="text-white font-bold mr-2">{{ $pressInfo['masthead'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['reg_num'])<span>등록번호: {{ $pressInfo['reg_num'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['pub'])<span>발행인: {{ $pressInfo['pub'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['edt'])<span>편집인: {{ $pressInfo['edt'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['phone'])<span>대표번호: {{ $pressInfo['phone'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['fax'])<span>팩스: {{ $pressInfo['fax'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['email'])<span>이메일: <a href="mailto:{{ $pressInfo['email'] }}" class="hover:text-blue-400">{{ $pressInfo['email'] }}</a></span>@endif
            </div>
            <div class="flex flex-wrap gap-y-1 mt-1">
                @if($pressInfo['post'])<span>우편번호: ({{ $pressInfo['post'] }})</span><span class="press-divider">|</span>@endif
                @if($pressInfo['addr'])<span>주소: {{ $pressInfo['addr'] }}</span>@endif
            </div>
            <div class="flex flex-wrap gap-y-1 mt-1 opacity-60">
                @if($pressInfo['youth'])<span>청소년보호책임자: {{ $pressInfo['youth'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['priv'])<span>개인정보보호책임자: {{ $pressInfo['priv'] }}</span><span class="press-divider">|</span>@endif
                @if($pressInfo['griev'])<span>고충처리인: {{ $pressInfo['griev'] }}</span>@endif
            </div>
        </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-t border-white/5 pt-8">
            <div class="text-center md:text-left">
                <p class="text-xl font-black text-white italic tracking-tighter">COBALT <span class="text-blue-500">GLOW</span></p>
                <p class="text-[10px] text-white/30 mt-1 uppercase tracking-widest">&copy; {{ date('Y') }} {{ App\Models\Setting::get('site_name') }}. All rights reserved.</p>
            </div>
            <div class="flex items-center gap-6">
                <span class="text-[10px] text-white/20 tracking-widest uppercase">Powered by Laraboard v10</span>
                {{-- 소셜 아이콘 등이 들어갈 자리 --}}
            </div>
        </div>
    </div>
</footer>