@extends('skin.layout.newyorktimes-style.main')

@section('content')
<div class="nyt-container" style="padding-top:20px;">

    {{-- 섹션 헤더 --}}
    <div class="nyt-section-header" style="margin-bottom:0;">
        <h1 style="font-family:var(--nyt-serif);font-size:1.6rem;font-weight:700;margin:0;">
            @yield('list-title', '전체 기사')
        </h1>
    </div>
    <hr class="nyt-rule-light" style="margin:0 0 20px;">

    <div class="nyt-list-wrap">

        {{-- 기사 목록 --}}
        <div>
            @yield('list-content')
        </div>

        {{-- 사이드바 --}}
        <aside>
            {{-- 인기 기사 --}}
            @php $sidePopular = App\Models\Article::where('status','published')->orderBy('view_count','desc')->limit(6)->get(); @endphp
            @if($sidePopular->isNotEmpty())
            <div style="border-top:3px solid #121212;padding-top:10px;margin-bottom:24px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 14px;">많이 본 기사</p>
                @foreach($sidePopular as $i => $art)
                <div style="{{ $i>0 ? 'border-top:1px solid #e2e2e2;padding-top:10px;margin-top:10px;' : '' }}display:flex;gap:10px;">
                    <span style="font-family:var(--nyt-serif);font-size:1.3rem;font-weight:700;color:#ddd;line-height:1;flex-shrink:0;width:18px;">{{ $i+1 }}</span>
                    <a href="{{ route('news.show', $art->slug) }}">
                        <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.35;color:#121212;">{{ $art->title }}</p>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            {{-- 카테고리 --}}
            @php $sideCats = App\Models\ArticleCategory::where('is_active',true)->orderBy('order')->get(); @endphp
            @if($sideCats->isNotEmpty())
            <div style="border-top:3px solid #121212;padding-top:10px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 10px;">섹션</p>
                @foreach($sideCats as $cat)
                <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                   style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:6px 0;border-bottom:1px solid #e2e2e2;"
                   onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>
            @endif
        </aside>
    </div>
</div>
@endsection
