@php
    try {
        $layoutSkin = \App\Models\Setting::get('layout_skin', 'basic');
        if (!view()->exists("skin.layout.{$layoutSkin}.main")) $layoutSkin = 'basic';
    } catch (\Exception $e) { $layoutSkin = 'basic'; }
    $isNyt = $layoutSkin === 'newyorktimes-style';
@endphp
@extends("skin.layout.{$layoutSkin}.main")

@section('title', $q !== '' ? ' - 검색: ' . $q : ' - 검색')

@section('content')

@if($isNyt)
<style>
@media (max-width: 768px) {
    .nyt-search-results-grid { grid-template-columns: 1fr !important; }
    .nyt-search-sidebar { display: none; }
}
</style>
<div class="nyt-container" style="padding-top:24px;padding-bottom:60px;">

    {{-- 검색 헤더 --}}
    <div style="border-top:3px solid var(--nyt-black);padding-top:16px;margin-bottom:24px;">
        @if($q !== '')
            <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--nyt-gray-mid);margin-bottom:8px;">검색 결과</p>
            <h1 style="font-family:var(--nyt-serif);font-size:clamp(1.6rem,4vw,2.4rem);font-weight:700;margin:0 0 8px;">"{{ $q }}"</h1>
            <p style="font-family:var(--nyt-sans);font-size:13px;color:var(--nyt-gray-mid);">
                총 {{ $articles->total() }}건
                &nbsp;·&nbsp;
                <a href="{{ route('news.index') }}" style="color:var(--nyt-gray-mid);text-decoration:underline;">전체 기사 보기</a>
            </p>
        @else
            <h1 style="font-family:var(--nyt-serif);font-size:clamp(1.6rem,4vw,2.4rem);font-weight:700;margin:0 0 8px;">검색</h1>
            <p style="font-family:var(--nyt-sans);font-size:13px;color:var(--nyt-gray-mid);">검색어를 입력하세요.</p>
        @endif
    </div>

    {{-- 검색창 --}}
    <form action="{{ route('news.search') }}" method="GET"
          style="display:flex;align-items:center;gap:8px;border-bottom:2px solid var(--nyt-black);padding-bottom:12px;margin-bottom:32px;">
        <input type="text" name="q" value="{{ $q }}" placeholder="기사 제목, 내용 검색..."
               style="flex:1;border:none;outline:none;font-family:var(--nyt-serif);font-size:1.1rem;color:var(--nyt-black);background:transparent;min-width:0;"
               autofocus>
        <button type="submit"
                style="font-family:var(--nyt-sans);font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;padding:7px 16px;background:var(--nyt-black);color:#fff;border:none;border-radius:2px;cursor:pointer;white-space:nowrap;flex-shrink:0;">
            검색
        </button>
    </form>

    @if($q !== '')
    <div class="nyt-search-results-grid" style="display:grid;grid-template-columns:1fr 280px;gap:0 40px;align-items:start;">

        {{-- 검색 결과 목록 --}}
        <div>
            @if($articles->isEmpty())
            <div style="text-align:center;padding:60px 0;color:var(--nyt-gray-mid);">
                <p style="font-family:var(--nyt-serif);font-size:1.2rem;margin-bottom:12px;">"{{ $q }}"에 대한 검색 결과가 없습니다.</p>
                <p style="font-family:var(--nyt-sans);font-size:13px;">다른 검색어를 시도해보세요.</p>
            </div>
            @else
            @foreach($articles as $article)
            <div style="display:flex;gap:20px;align-items:flex-start;border-bottom:1px solid var(--nyt-border);padding:18px 0;">
                @if($article->thumbnail)
                <a href="{{ route('news.show', $article->slug) }}" style="flex-shrink:0;">
                    <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}"
                         style="width:140px;height:96px;object-fit:cover;">
                </a>
                @endif
                <div style="flex:1;min-width:0;">
                    @if($article->category)
                    <span class="nyt-section-label">{{ $article->category->name }}</span>
                    @endif
                    <a href="{{ route('news.show', $article->slug) }}">
                        <h2 class="nyt-headline nyt-headline-md" style="margin-bottom:6px;">{{ $article->title }}</h2>
                    </a>
                    @if($article->subtitle)
                    <p style="font-family:var(--nyt-serif);font-size:.9rem;color:var(--nyt-gray-dark);margin:0 0 6px;line-height:1.4;">{{ $article->subtitle }}</p>
                    @elseif($article->excerpt)
                    <p class="nyt-summary" style="font-size:.875rem;">{{ Str::limit($article->excerpt, 120) }}</p>
                    @endif
                    <p class="nyt-byline">
                        {{ $article->user->name ?? '' }}
                        &nbsp;·&nbsp;
                        {{ $article->published_at?->format('Y.m.d') }}
                    </p>
                </div>
            </div>
            @endforeach

            {{-- 페이지네이션 --}}
            @if($articles->hasPages())
            <div style="padding:24px 0;">
                {{ $articles->links() }}
            </div>
            @endif
            @endif
        </div>

        {{-- 사이드바 --}}
        <aside class="nyt-search-sidebar">
            @if($popular->count())
            <div style="border-top:3px solid var(--nyt-black);padding-top:10px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 14px;">많이 본 기사</p>
                @foreach($popular as $i => $a)
                <div style="{{ $i>0 ? 'border-top:1px solid var(--nyt-border);padding-top:10px;margin-top:10px;' : '' }}display:flex;gap:10px;">
                    <span style="font-family:var(--nyt-serif);font-size:1.3rem;font-weight:700;color:#ddd;line-height:1;flex-shrink:0;width:18px;">{{ $i+1 }}</span>
                    <div>
                        @if($a->category)<span class="nyt-section-label" style="font-size:10px;">{{ $a->category->name }}</span>@endif
                        <a href="{{ route('news.show', $a->slug) }}">
                            <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.35;color:var(--nyt-black);">{{ $a->title }}</p>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </aside>
    </div>

    @endif {{-- /if q --}}
</div>

@else
{{-- 기본(Basic) 스타일 검색 결과 --}}
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        @if($q !== '') "{{ $q }}" 검색 결과 @else 검색 @endif
    </h1>

    <form action="{{ route('news.search') }}" method="GET" class="flex gap-2 mb-8">
        <input type="text" name="q" value="{{ $q }}" placeholder="검색어 입력..."
               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
               autofocus>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700">검색</button>
    </form>

    @if($q !== '')
        @if($articles->isEmpty())
        <p class="text-gray-500 text-center py-16">"{{ $q }}"에 대한 검색 결과가 없습니다.</p>
        @else
        <p class="text-sm text-gray-500 mb-4">총 {{ $articles->total() }}건</p>
        <div class="divide-y divide-gray-200">
            @foreach($articles as $article)
            <div class="py-4 flex gap-4">
                @if($article->thumbnail)
                <a href="{{ route('news.show', $article->slug) }}" class="flex-shrink-0">
                    <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}" class="w-24 h-16 object-cover rounded">
                </a>
                @endif
                <div class="flex-1 min-w-0">
                    @if($article->category)
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wide">{{ $article->category->name }}</span>
                    @endif
                    <a href="{{ route('news.show', $article->slug) }}" class="block">
                        <h2 class="font-bold text-gray-900 mt-1 leading-snug">{{ $article->title }}</h2>
                    </a>
                    @if($article->excerpt)
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($article->excerpt, 100) }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">{{ $article->user->name ?? '' }} · {{ $article->published_at?->format('Y.m.d') }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @if($articles->hasPages())
        <div class="mt-6">{{ $articles->links() }}</div>
        @endif
        @endif
    @endif
</div>
@endif

@endsection
