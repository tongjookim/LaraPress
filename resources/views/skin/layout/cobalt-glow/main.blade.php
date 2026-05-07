{{-- head.blade.php 호출 --}}
@include('skin.layout.cobalt-glow.head')

{{-- 배경 Glow 레이어 --}}
<div class="cobalt-bg-glow" aria-hidden="true"></div>

@php $__topBanners = \App\Models\TopBanner::activeNow(); @endphp
@if($__topBanners->isNotEmpty())
<div id="top-banner-wrap">
@foreach($__topBanners as $__b)
<div id="top-banner-{{ $__b->id }}"
     data-banner-id="{{ $__b->id }}"
     data-reshow="{{ $__b->reshow_hours }}"
     style="display:none;position:relative;text-align:center;padding:9px 44px;background:{{ $__b->bg_color }};color:{{ $__b->text_color }};font-size:{{ $__b->font_size }}px;font-weight:{{ $__b->font_weight }};line-height:1.4;">
    @if($__b->link_url)
        <a href="{{ $__b->link_url }}" style="color:{{ $__b->text_color }};text-decoration:none;">{{ $__b->text }}</a>
    @else
        {{ $__b->text }}
    @endif
    <button onclick="closeTopBanner({{ $__b->id }}, {{ $__b->reshow_hours }})"
            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:18px;line-height:1;color:{{ $__b->text_color }};opacity:.7;"
            aria-label="닫기">×</button>
</div>
@endforeach
</div>
<script>
(function() {
    var bannerData = @json($__topBanners->map(fn($b) => ['id' => $b->id, 'reshow' => $b->reshow_hours]));
    bannerData.forEach(function(b) {
        var key = 'tb_closed_' + b.id;
        var closed = localStorage.getItem(key);
        var show = true;
        if (closed) {
            if (b.reshow === 0) {
                show = false;
            } else {
                var elapsed = (Date.now() - parseInt(closed)) / 3600000;
                if (elapsed < b.reshow) show = false;
            }
        }
        if (show) {
            document.getElementById('top-banner-' + b.id).style.display = 'block';
        }
    });
}());
function closeTopBanner(id, reshow) {
    document.getElementById('top-banner-' + id).style.display = 'none';
    if (reshow === 0) {
        localStorage.setItem('tb_closed_' + id, '0');
    } else {
        localStorage.setItem('tb_closed_' + id, Date.now().toString());
    }
}
</script>
@endif

{{-- navigation.blade.php 호출 --}}
@include('skin.layout.cobalt-glow.navigation')

{{-- 시스템 메시지 표시 --}}
@if(session('success'))
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div style="background:rgba(59,130,246,0.08);border-left:3px solid #3b82f6;padding:12px 16px;border-radius:0 8px 8px 0;font-size:13px;color:rgba(255,255,255,0.85);">
        {{ session('success') }}
    </div>
</div>
@endif
@if(session('error'))
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:12px 16px;border-radius:0 8px 8px 0;font-size:13px;color:rgba(255,255,255,0.85);">
        {{ session('error') }}
    </div>
</div>
@endif

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @yield('content')
</main>

{{-- footer.blade.php 호출 --}}
@include('skin.layout.cobalt-glow.footer')
@stack('scripts')
</body>
</html>
