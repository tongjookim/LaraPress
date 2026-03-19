@php
    try {
        $layoutSkin = \App\Models\Setting::get('layout_skin', 'basic');
        if (!view()->exists("skin.layout.{$layoutSkin}.main")) $layoutSkin = 'basic';
    } catch (\Exception $e) { $layoutSkin = 'basic'; }
    $isNyt = $layoutSkin === 'newyorktimes-style';
@endphp
@extends("skin.layout.{$layoutSkin}.main")

@section('title', $currentCategory ? ' - ' . $currentCategory->name : ' - 뉴스')

@section('content')

@if($isNyt)
{{-- ══════════════════════════════════════
     NYT 스타일 기사 목록
══════════════════════════════════════ --}}
<div class="nyt-container" style="padding-top:16px;">

    {{-- 섹션 헤더 --}}
    <div class="nyt-section-header">
        <h1>{{ $currentCategory ? $currentCategory->name : '전체 기사' }}</h1>
        @if($currentCategory)
        <a href="{{ route('news.index') }}" class="more">전체 보기 →</a>
        @endif
    </div>

    {{-- 카테고리 탭 --}}
    @if($categories->count())
    <div style="display:flex;gap:0;overflow-x:auto;border-bottom:1px solid #e2e2e2;margin-bottom:16px;scrollbar-width:none;">
        <a href="{{ route('news.index') }}"
           style="flex-shrink:0;padding:8px 16px;font-family:var(--nyt-sans);font-size:13px;font-weight:600;border-bottom:{{ !$currentCategory ? '3px solid #121212' : '3px solid transparent' }};color:{{ !$currentCategory ? '#121212' : '#666' }};white-space:nowrap;text-decoration:none;">
            전체
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
           style="flex-shrink:0;padding:8px 16px;font-family:var(--nyt-sans);font-size:13px;font-weight:600;border-bottom:{{ $currentCategory?->id === $cat->id ? '3px solid #121212' : '3px solid transparent' }};color:{{ $currentCategory?->id === $cat->id ? '#121212' : '#666' }};white-space:nowrap;text-decoration:none;">
            {{ $cat->name }}
        </a>
        @endforeach
    </div>
    @endif

    @if($articles->isEmpty())
    <div style="text-align:center;padding:80px 0;color:#999;">
        <p style="font-family:var(--nyt-serif);font-size:1.2rem;">등록된 기사가 없습니다.</p>
    </div>
    @else

    <div style="display:grid;grid-template-columns:1fr 280px;gap:0 32px;">

        {{-- 기사 목록 --}}
        <div>
            {{-- 피처드 히어로 --}}
            @if($featuredArticles->count() >= 1)
            @php $hero = $featuredArticles->get(0); $subs = $featuredArticles->slice(1, 2)->values(); @endphp
            <div style="border-top:3px solid #121212;padding:14px 0 16px;{{ $subs->count() ? 'display:grid;grid-template-columns:2fr 1px 1fr;gap:0;' : '' }}border-bottom:1px solid #e2e2e2;margin-bottom:0;">

                {{-- 메인 히어로 --}}
                <div style="padding-right:{{ $subs->count() ? '20px' : '0' }};">
                    @if($hero->thumbnail)
                    <a href="{{ route('news.show', $hero->slug) }}" style="display:block;margin-bottom:12px;">
                        <img src="{{ $hero->thumbnail }}" alt="{{ $hero->title }}"
                             style="width:100%;height:280px;object-fit:cover;">
                    </a>
                    @endif
                    @if($hero->category)
                    <span class="nyt-section-label">{{ $hero->category->name }}</span>
                    @endif
                    <a href="{{ route('news.show', $hero->slug) }}">
                        <h2 class="nyt-headline nyt-headline-xl" style="margin-bottom:8px;">{{ $hero->title }}</h2>
                    </a>
                    @if($hero->excerpt)
                    <p class="nyt-summary">{{ Str::limit($hero->excerpt, 160) }}</p>
                    @endif
                    <p class="nyt-byline">{{ $hero->user->name ?? '' }} · {{ $hero->published_at?->format('Y.m.d') }}</p>
                </div>

                @if($subs->count())
                <div style="background:#e2e2e2;margin:0;"></div>
                <div style="padding-left:20px;">
                    @foreach($subs as $sub)
                    <div style="{{ !$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:12px;margin-top:12px;' : '' }}">
                        @if($sub->thumbnail)
                        <a href="{{ route('news.show', $sub->slug) }}" style="display:block;margin-bottom:8px;">
                            <img src="{{ $sub->thumbnail }}" alt="{{ $sub->title }}"
                                 style="width:100%;height:120px;object-fit:cover;">
                        </a>
                        @endif
                        @if($sub->category)
                        <span class="nyt-section-label">{{ $sub->category->name }}</span>
                        @endif
                        <a href="{{ route('news.show', $sub->slug) }}">
                            <h3 class="nyt-headline nyt-headline-sm">{{ $sub->title }}</h3>
                        </a>
                        <p class="nyt-byline">{{ $sub->user->name ?? '' }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- 나머지 기사 목록 --}}
            @php $featuredIds = isset($featuredArticles) ? $featuredArticles->pluck('id')->all() : []; @endphp
            @foreach($articles as $article)
            @if(!in_array($article->id, $featuredIds))
            <div style="display:flex;gap:16px;align-items:flex-start;border-bottom:1px solid #e2e2e2;padding:14px 0;">
                <div style="flex:1;min-width:0;">
                    @if($article->category)
                    <span class="nyt-section-label">{{ $article->category->name }}</span>
                    @endif
                    <a href="{{ route('news.show', $article->slug) }}">
                        <h3 class="nyt-headline nyt-headline-sm" style="margin-bottom:5px;">{{ $article->title }}</h3>
                    </a>
                    @if($article->excerpt)
                    <p class="nyt-summary" style="font-size:.8125rem;">{{ Str::limit($article->excerpt, 100) }}</p>
                    @endif
                    <p class="nyt-byline">{{ $article->user->name ?? '' }} · {{ $article->published_at?->format('Y.m.d') }}</p>
                </div>
                @if($article->thumbnail)
                <a href="{{ route('news.show', $article->slug) }}" style="flex-shrink:0;">
                    <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}"
                         style="width:120px;height:86px;object-fit:cover;">
                </a>
                @endif
            </div>
            @endif
            @endforeach

            {{-- 페이지네이션 --}}
            @if($articles->hasPages())
            <div style="padding:20px 0;border-top:1px solid #e2e2e2;">
                {{ $articles->links() }}
            </div>
            @endif
        </div>

        {{-- 사이드바 --}}
        <aside>
            @php
                $popular = \App\Models\Article::with('user')->where('status','published')->orderBy('view_count','desc')->limit(6)->get();
                $recent  = \App\Models\Article::with('user')->where('status','published')->orderBy('published_at','desc')->limit(5)->get();
            @endphp

            @if($popular->count())
            <div style="border-top:3px solid #121212;padding-top:10px;margin-bottom:24px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 14px;">많이 본 기사</p>
                @foreach($popular as $i => $a)
                <div style="{{ $i>0 ? 'border-top:1px solid #e2e2e2;padding-top:10px;margin-top:10px;' : '' }}display:flex;gap:10px;">
                    <span style="font-family:var(--nyt-serif);font-size:1.3rem;font-weight:700;color:#ddd;line-height:1;flex-shrink:0;width:18px;">{{ $i+1 }}</span>
                    <div>
                        @if($a->category)<span class="nyt-section-label" style="font-size:10px;">{{ $a->category->name }}</span>@endif
                        <a href="{{ route('news.show', $a->slug) }}">
                            <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.35;color:#121212;">{{ $a->title }}</p>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($recent->count())
            <div style="border-top:3px solid #121212;padding-top:10px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 12px;">최신 기사</p>
                @foreach($recent as $a)
                <div style="{{ !$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:10px;margin-top:10px;' : '' }}display:flex;gap:10px;align-items:flex-start;">
                    @if($a->thumbnail)
                    <a href="{{ route('news.show', $a->slug) }}" style="flex-shrink:0;">
                        <img src="{{ $a->thumbnail }}" alt="{{ $a->title }}"
                             style="width:64px;height:46px;object-fit:cover;">
                    </a>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ route('news.show', $a->slug) }}">
                            <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.35;color:#121212;">{{ $a->title }}</p>
                        </a>
                        <p class="nyt-byline" style="margin-top:3px;">{{ $a->published_at?->format('m.d') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </aside>
    </div>
    @endif
</div>

@else
{{-- ══════════════════════════════════════
     기본(Basic) 스타일 기사 목록 (기존 유지)
══════════════════════════════════════ --}}

{{-- 카테고리 탭 필터 --}}
@if($categories->count())
<div class="mb-5 {{ $currentCategory ? '' : '-mt-2' }} border-b border-gray-200">
    <div class="flex items-center gap-0 overflow-x-auto">
        <a href="{{ route('news.index') }}"
           class="flex-shrink-0 px-4 py-2.5 text-sm font-bold border-b-2 transition whitespace-nowrap
                  {{ !$currentCategory ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
            전체
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
           class="flex-shrink-0 px-4 py-2.5 text-sm font-bold border-b-2 transition whitespace-nowrap
                  {{ $currentCategory?->id === $cat->id ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
            {{ $cat->name }}
        </a>
        @endforeach
    </div>
</div>
@endif

@if($articles->isEmpty())
<div class="text-center py-20 text-gray-400">
    <p class="font-medium">등록된 기사가 없습니다.</p>
</div>
@else

<div class="flex flex-col lg:flex-row gap-6">
    <div class="flex-1 min-w-0">
        @if($featuredArticles->count() >= 2)
        @php $hero = $featuredArticles->get(0); $subs = $featuredArticles->slice(1, 2)->values(); @endphp
        <div class="grid grid-cols-3 gap-3 mb-6">
            <a href="{{ route('news.show', $hero->slug) }}" class="col-span-2 relative rounded-lg overflow-hidden group block" style="height:224px;">
                <img src="{{ $hero->thumbnail }}" alt="{{ $hero->title }}" style="width:100%;height:100%;object-fit:cover;" class="group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    @if($hero->category)<span class="inline-block px-2 py-0.5 text-xs font-bold bg-blue-600 text-white rounded mb-2">{{ $hero->category->name }}</span>@endif
                    <h2 class="text-white font-bold text-base leading-snug line-clamp-2 mb-1">{{ $hero->title }}</h2>
                    <p class="text-white/70 text-xs">{{ $hero->user->name }} · {{ $hero->published_at?->format('Y.m.d') }}</p>
                </div>
            </a>
            <div class="col-span-1 flex flex-col gap-3" style="height:224px;">
                @foreach($subs as $sub)
                <a href="{{ route('news.show', $sub->slug) }}" class="relative rounded-lg overflow-hidden group block" style="flex:1;min-height:0;">
                    <img src="{{ $sub->thumbnail }}" alt="{{ $sub->title }}" style="width:100%;height:100%;object-fit:cover;" class="group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        @if($sub->category)<span class="inline-block px-1.5 py-0.5 text-xs font-bold bg-blue-600 text-white rounded mb-1">{{ $sub->category->name }}</span>@endif
                        <h3 class="text-white font-bold text-xs leading-snug line-clamp-2">{{ $sub->title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @php $featuredIds = isset($featuredArticles) ? $featuredArticles->pluck('id')->all() : []; @endphp
        <div class="bg-white rounded-lg overflow-hidden mb-6 border border-gray-100">
            @foreach($articles as $article)
            @if(!in_array($article->id, $featuredIds))
            <a href="{{ route('news.show', $article->slug) }}" class="group flex items-start gap-4 px-4 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                @if($article->thumbnail)
                <div class="flex-shrink-0 relative" style="width:140px;height:100px;">
                    <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
                    @if($article->category)<span class="absolute bottom-1.5 left-1.5 px-1.5 py-0.5 text-white rounded" style="font-size:10px;font-weight:700;background:rgba(37,99,235,0.9);">{{ $article->category->name }}</span>@endif
                </div>
                @else
                <div class="flex-shrink-0 bg-gray-100 rounded flex items-center justify-center" style="width:140px;height:100px;"></div>
                @endif
                <div class="flex-1 min-w-0 py-0.5">
                    <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-blue-700 mb-2">{{ $article->title }}</h3>
                    @php $preview = $article->excerpt ?: Str::limit(strip_tags($article->content), 100); @endphp
                    @if($preview)<p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-2">{{ $preview }}</p>@endif
                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <span class="font-medium text-gray-600">{{ $article->user->name }}</span>
                        <span>·</span><span>{{ $article->published_at?->format('Y.m.d') }}</span>
                        <span>·</span><span>조회 {{ number_format($article->view_count) }}</span>
                    </div>
                </div>
            </a>
            @endif
            @endforeach
        </div>
        @if($articles->hasPages())<div class="mt-4">{{ $articles->links() }}</div>@endif
    </div>

    <aside class="w-full lg:w-64 flex-shrink-0">
        @php $popular = \App\Models\Article::with('user')->where('status','published')->orderBy('view_count','desc')->limit(5)->get(); @endphp
        @if($popular->count())
        <div class="bg-white rounded-lg border border-gray-100 overflow-hidden mb-4">
            <div class="px-4 py-2.5 bg-blue-50 border-b border-blue-100"><h3 class="font-bold text-sm text-blue-700">인기기사</h3></div>
            <div class="px-3 py-1">
                @foreach($popular as $i => $a)
                <a href="{{ route('news.show', $a->slug) }}" class="flex items-start gap-2 py-2 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-3 px-3 transition group">
                    @if($a->thumbnail)
                    <div class="flex-shrink-0 relative" style="width:64px;height:46px;">
                        <img src="{{ $a->thumbnail }}" alt="{{ $a->title }}" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 rounded flex items-center justify-center font-black {{ $i < 3 ? 'bg-blue-600 text-white' : 'bg-gray-500 text-white' }}" style="font-size:9px;">{{ $i + 1 }}</span>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 line-clamp-2 leading-snug group-hover:text-blue-700">{{ $a->title }}</p>
                        <span class="text-xs text-gray-400 mt-0.5 block">{{ $a->published_at?->format('Y.m.d') }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </aside>
</div>
@endif
@endif

@endsection
