@php
    $footerGroups     = App\Models\NavMenu::footerGroups();
    $footerCategories = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $hasFooterContent = !empty($footerGroups) || $footerCategories->isNotEmpty();
@endphp

@if($hasFooterContent)
<div style="background:#1f2937;border-top:1px solid #374151;">
    <div class="max-w-7xl mx-auto px-4 py-8">

        @foreach($footerGroups as $groupName => $groupItems)
        <div style="margin-bottom:12px;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-right:12px;">{{ $groupName }}</span>
            @foreach($groupItems as $fitem)
            <a href="{{ $fitem->url }}" target="{{ $fitem->target }}"
               style="font-size:13px;color:#d1d5db;text-decoration:none;margin-right:16px;transition:color .15s;"
               onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#d1d5db'">
                {{ $fitem->label }}
            </a>
            @endforeach
        </div>
        @endforeach

        @if($footerCategories->isNotEmpty())
        <div style="{{ !empty($footerGroups) ? 'margin-top:8px;padding-top:12px;border-top:1px solid #374151;' : '' }}">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-right:12px;">카테고리</span>
            @foreach($footerCategories as $cat)
            <a href="{{ route('news.index') }}?category={{ $cat->slug }}"
               style="font-size:13px;color:#d1d5db;text-decoration:none;margin-right:16px;transition:color .15s;"
               onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#d1d5db'">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endif

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
<footer class="bg-gray-800 text-gray-400 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-6">
        @if($hasPressInfo)
        <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:20px;margin-bottom:16px;font-size:12px;line-height:1.8;color:#9ca3af;">
            @if($pressMasthead)<span style="font-weight:700;color:#d1d5db;">{{ $pressMasthead }}</span><span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @if($pressRegistrationNumber)<span>등록번호: {{ $pressRegistrationNumber }}</span><span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @if($pressPublisher)<span>발행인: {{ $pressPublisher }}</span><span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @if($pressEditor)<span>편집인: {{ $pressEditor }}</span><span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @if($pressPhone)<span>대표번호: {{ $pressPhone }}</span><span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @if($pressFax)<span>팩스: {{ $pressFax }}</span><span style="margin:0 6px;color:#4b5563;">|</span>@endif
            @if($pressEmail)<span>이메일: <a href="mailto:{{ $pressEmail }}" style="color:#9ca3af;text-decoration:none;">{{ $pressEmail }}</a></span>@endif
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
            @if($pressGrievanceManager)<span>고충처리인: {{ $pressGrievanceManager }}</span>@endif
            @endif
        </div>
        @endif
        <div class="text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ App\Models\Setting::get('site_name', 'Laraboard') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

    @if(App\Models\Setting::get('custom_body_script'))
    {!! App\Models\Setting::get('custom_body_script') !!}
    @endif
</body>
</html>
