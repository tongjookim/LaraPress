{{-- head.blade.php 호출 --}}
@include('skin.layout.swn-style.head')

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
@include('skin.layout.swn-style.navigation')

{{-- 시스템 메시지 표시 --}}
@if(session('success'))
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded text-green-700 text-sm">
        {{ session('success') }}
    </div>
</div>
@endif

<main class="max-w-7xl mx-auto px-4 py-6">
    @yield('content')
</main>

{{-- footer.blade.php 호출 --}}
@include('skin.layout.swn-style.footer')
@stack('scripts')
