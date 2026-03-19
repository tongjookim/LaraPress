@php
    $footerGroups     = App\Models\NavMenu::footerGroups();
    $footerCategories = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $siteName         = App\Models\Setting::get('site_name', 'Laraboard');
    $boards           = App\Models\Board::where('is_active', true)->orderBy('order')->limit(8)->get();

    $pressMasthead           = App\Models\Setting::get('press_masthead');
    $pressRegistrationNumber = App\Models\Setting::get('press_registration_number');
    $pressPublisher          = App\Models\Setting::get('press_publisher');
    $pressEditor             = App\Models\Setting::get('press_editor');
    $pressAddress            = App\Models\Setting::get('press_address');
    $pressPostalCode         = App\Models\Setting::get('press_postal_code');
    $pressFax                = App\Models\Setting::get('press_fax');
    $pressPhone              = App\Models\Setting::get('press_phone');
    $pressEmail              = App\Models\Setting::get('press_email');
    $pressYouthManager       = App\Models\Setting::get('press_youth_manager');
    $pressPrivacyManager     = App\Models\Setting::get('press_privacy_manager');
    $pressGrievanceManager   = App\Models\Setting::get('press_grievance_manager');
    $hasPressInfo            = $pressMasthead || $pressPublisher || $pressAddress;
@endphp

<footer style="background:#fff;border-top:3px solid #121212;margin-top:32px;">

    {{-- 상단 푸터: 로고 + 링크 그리드 --}}
    <div style="border-bottom:1px solid #e2e2e2;padding:32px 0 24px;">
        <div class="nyt-container">
            {{-- 마스트헤드 --}}
            <div style="text-align:center;margin-bottom:24px;">
                <a href="/" style="font-family:var(--nyt-serif);font-size:2rem;font-weight:700;color:#121212;letter-spacing:-.02em;">
                    {{ App\Models\Setting::get('logo_text') ?: $siteName }}
                </a>
            </div>

            {{-- 링크 그리드 --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:24px 16px;">

                @if($footerCategories->isNotEmpty())
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;">카테고리</p>
                    @foreach($footerCategories as $cat)
                    <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                       style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;line-height:1.4;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">{{ $cat->name }}</a>
                    @endforeach
                </div>
                @endif

                @if($boards->isNotEmpty())
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;">커뮤니티</p>
                    @foreach($boards as $brd)
                    <a href="{{ route('bbs.index', $brd->board_id) }}"
                       style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;line-height:1.4;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">{{ $brd->board_name }}</a>
                    @endforeach
                </div>
                @endif

                @foreach($footerGroups as $groupName => $groupItems)
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;">{{ $groupName }}</p>
                    @foreach($groupItems as $fitem)
                    <a href="{{ $fitem->url }}" target="{{ $fitem->target }}"
                       style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;line-height:1.4;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">{{ $fitem->label }}</a>
                    @endforeach
                </div>
                @endforeach

                {{-- 계정 --}}
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;">계정</p>
                    @auth
                    <a href="{{ route('profile.show') }}" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">내 프로필</a>
                    @if(auth()->user()->canAccessAdmin())
                    <a href="{{ route('admin.dashboard') }}" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">관리자 패널</a>
                    @endif
                    @else
                    <a href="{{ route('login') }}" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">로그인</a>
                    <a href="{{ route('register') }}" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">회원가입</a>
                    @endauth
                </div>

            </div>
        </div>
    </div>

    {{-- 하단 푸터: 저작권 + 언론사 정보 --}}
    <div style="padding:16px 0 24px;">
        <div class="nyt-container">

            @if($hasPressInfo)
            <div style="font-family:var(--nyt-sans);font-size:11.5px;color:#666;line-height:1.9;margin-bottom:12px;">
                @if($pressMasthead)<span style="font-weight:700;color:#333;">{{ $pressMasthead }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressRegistrationNumber)<span>등록번호: {{ $pressRegistrationNumber }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressPublisher)<span>발행인: {{ $pressPublisher }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressEditor)<span>편집인: {{ $pressEditor }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressPhone)<span>대표번호: {{ $pressPhone }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressFax)<span>팩스: {{ $pressFax }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressEmail)<span>이메일: <a href="mailto:{{ $pressEmail }}" style="color:#666;">{{ $pressEmail }}</a></span>@endif
                @if($pressAddress || $pressPostalCode)
                <br>
                @if($pressPostalCode)<span>우편번호: {{ $pressPostalCode }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressAddress)<span>주소: {{ $pressAddress }}</span>@endif
                @endif
                @if($pressYouthManager || $pressPrivacyManager || $pressGrievanceManager)
                <br>
                @if($pressYouthManager)<span>청소년보호책임자: {{ $pressYouthManager }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressPrivacyManager)<span>개인정보 보호책임자: {{ $pressPrivacyManager }}</span><span style="margin:0 8px;color:#ccc;">|</span>@endif
                @if($pressGrievanceManager)<span>고충처리인: {{ $pressGrievanceManager }}</span>@endif
                @endif
            </div>
            @endif

            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <p style="font-family:var(--nyt-sans);font-size:12px;color:#999;margin:0;">
                    &copy; {{ date('Y') }} {{ $siteName }}. All Rights Reserved.
                </p>
                <div style="display:flex;gap:16px;">
                    <a href="{{ url('/feed') }}" style="font-family:var(--nyt-sans);font-size:12px;color:#999;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#999'">RSS</a>
                    <a href="{{ url('/sitemap.xml') }}" style="font-family:var(--nyt-sans);font-size:12px;color:#999;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#999'">Sitemap</a>
                </div>
            </div>

        </div>
    </div>

</footer>

@if(App\Models\Setting::get('custom_body_script'))
{!! App\Models\Setting::get('custom_body_script') !!}
@endif
</body>
</html>
