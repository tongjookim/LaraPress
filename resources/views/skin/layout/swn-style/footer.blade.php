@php
    $footerGroups      = App\Models\NavMenu::footerGroups();
    $footerCategories  = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
@endphp
{{-- 사이트 정보 바 --}}
<div class="bg-gray-100 border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div style="display:grid;grid-template-columns:1fr auto;gap:24px 40px;align-items:start;">

            {{-- 왼쪽: 푸터 메뉴 그룹들 (가로 정렬) --}}
            <div>
                @foreach($footerGroups as $groupName => $groupItems)
                <div style="margin-bottom:14px;">
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#374151;margin-right:12px;">{{ $groupName }}</span>
                    @foreach($groupItems as $fitem)
                    <a href="{{ $fitem->url }}" target="{{ $fitem->target }}"
                       style="font-size:13px;color:#6b7280;text-decoration:none;margin-right:16px;transition:color .15s;"
                       onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#6b7280'">
                        {{ $fitem->label }}
                    </a>
                    @endforeach
                </div>
                @endforeach

                {{-- 카테고리 위젯 --}}
                @if($footerCategories->isNotEmpty())
                <div style="margin-top:{{ empty($footerGroups) ? '0' : '6px' }};padding-top:{{ empty($footerGroups) ? '0' : '12px' }};{{ empty($footerGroups) ? '' : 'border-top:1px solid #e5e7eb;' }}">
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#374151;margin-right:12px;">카테고리</span>
                    @foreach($footerCategories as $cat)
                    <a href="{{ route('news.index') }}?category={{ $cat->slug }}"
                       style="font-size:13px;color:#6b7280;text-decoration:none;margin-right:16px;transition:color .15s;"
                       onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#6b7280'">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- 오른쪽: 사이트 정보 --}}
            <div style="text-align:right;min-width:160px;">
                <p style="font-size:13px;font-weight:700;color:#111827;margin-bottom:4px;">{{ App\Models\Setting::get('site_name', 'Laraboard') }}</p>
                <p style="font-size:12px;color:#6b7280;line-height:1.6;">{{ App\Models\Setting::get('site_description', '커뮤니티 게시판 플랫폼') }}</p>
                <p style="font-size:11px;color:#9ca3af;margin-top:6px;">
                    회원 {{ number_format(App\Models\User::count()) }}명 · 게시글 {{ number_format(App\Models\Post::count()) }}건
                </p>
            </div>

        </div>
    </div>
</div>

{{-- 하단 저작권 + 언론사 정보 --}}
@php
    $pressMasthead            = App\Models\Setting::get('press_masthead');
    $pressRegistrationNumber  = App\Models\Setting::get('press_registration_number');
    $pressPublisher           = App\Models\Setting::get('press_publisher');
    $pressEditor          = App\Models\Setting::get('press_editor');
    $pressAddress         = App\Models\Setting::get('press_address');
    $pressPostalCode      = App\Models\Setting::get('press_postal_code');
    $pressFax             = App\Models\Setting::get('press_fax');
    $pressPhone           = App\Models\Setting::get('press_phone');
    $pressEmail           = App\Models\Setting::get('press_email');
    $pressYouthManager    = App\Models\Setting::get('press_youth_manager');
    $pressPrivacyManager  = App\Models\Setting::get('press_privacy_manager');
    $pressGrievanceManager= App\Models\Setting::get('press_grievance_manager');
    $hasPressInfo         = $pressMasthead || $pressPublisher || $pressAddress;
@endphp
<footer class="bg-gray-800 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 py-6">

        @if($hasPressInfo)
        {{-- 언론사 법적 고지 --}}
        <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:20px;margin-bottom:16px;font-size:12px;line-height:1.8;color:#9ca3af;">
            @if($pressMasthead)
            <span style="font-weight:700;color:#d1d5db;">{{ $pressMasthead }}</span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            @endif
            @if($pressRegistrationNumber)
            <span>등록번호: {{ $pressRegistrationNumber }}</span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            @endif
            @if($pressPublisher)
            <span>발행인: {{ $pressPublisher }}</span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            @endif
            @if($pressEditor)
            <span>편집인: {{ $pressEditor }}</span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            @endif
            @if($pressPhone)
            <span>대표번호: {{ $pressPhone }}</span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            @endif
            @if($pressFax)
            <span>팩스: {{ $pressFax }}</span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            @endif
            @if($pressEmail)
            <span>이메일: <a href="mailto:{{ $pressEmail }}" style="color:#9ca3af;text-decoration:none;">{{ $pressEmail }}</a></span>
            @endif
            @if($pressAddress || $pressPostalCode)
            <br>
            @if($pressPostalCode)<span>우편번호: {{ $pressPostalCode }}</span><span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @if($pressAddress)<span>주소: {{ $pressAddress }}</span>@endif
            @endif
            @if($pressYouthManager || $pressPrivacyManager || $pressGrievanceManager)
            <br>
            @if($pressYouthManager)
            <span>청소년보호책임자: {{ $pressYouthManager }}</span>
            @if($pressPrivacyManager || $pressGrievanceManager)<span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @endif
            @if($pressPrivacyManager)
            <span>개인정보 보호책임자: {{ $pressPrivacyManager }}</span>
            @if($pressGrievanceManager)<span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @endif
            @if($pressGrievanceManager)
            <span>고충처리인: {{ $pressGrievanceManager }}</span>
            @endif
            @endif
        </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-left">
                <p class="font-serif-title text-lg font-bold text-white tracking-tight">
                    {{ App\Models\Setting::get('site_name', 'Laraboard') }}
                </p>
                <p class="text-xs mt-1">&copy; {{ date('Y') }} All rights reserved.</p>
            </div>
            <div class="text-xs text-gray-500">
                Powered by <span class="text-gray-400">Laraboard</span>
            </div>
        </div>
    </div>
</footer>

@if(App\Models\Setting::get('custom_body_script'))
{!! App\Models\Setting::get('custom_body_script') !!}
@endif
</body>
</html>
