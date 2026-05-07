@extends('skin.layout.newyorktimes-style.main')

@section('content')
@php
    use App\Models\Article;
    use App\Models\ArticleCategory;
    use App\Models\Board;
    use App\Models\Post;
    use App\Models\Setting;
    use Illuminate\Support\Str;

    $categories      = ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $boards          = Board::where('is_active', true)->orderBy('order')->withCount('posts')->get();
    $popularArticles = Article::where('status','published')->orderBy('view_count','desc')->limit(5)->get();

    // 최신 기사
    $allLatest = Article::with(['category','user'])
        ->where('status','published')
        ->orderBy('published_at','desc')
        ->limit(30)
        ->get();

    $hero   = $allLatest->get(0);          // 메인 히어로
    $top2   = $allLatest->slice(1, 2);     // 히어로 우측 2개
    $top3   = $allLatest->slice(3, 3);     // 2번째 행 3개
    $top4   = $allLatest->slice(6, 4);     // 3번째 행 4개
    $mid6   = $allLatest->slice(10, 6);    // 미드섹션 6개

    // 기사 요약 헬퍼: excerpt 없을 경우 본문에서 추출
    $summary = function(Article $art, int $len = 120): string {
        if ($art->excerpt) return Str::limit($art->excerpt, $len);
        return Str::limit(strip_tags($art->content), $len);
    };
@endphp

<div class="nyt-container" style="padding-top:16px;">

{{-- ══════════════════════════════════════
     TOP SECTION: 히어로 + 사이드
══════════════════════════════════════ --}}
<section class="nyt-section-block">
    <div class="nyt-home-hero">

        {{-- 메인 히어로 --}}
        @if($hero)
        <div style="padding:12px 20px 16px 0;">
            @if($hero->thumbnail)
            <a href="{{ route('news.show', $hero->slug) }}" style="display:block;margin-bottom:12px;">
                <div style="overflow:hidden;">
                    <img src="{{ $hero->thumbnail }}" alt="{{ $hero->title }}"
                         style="width:100%;height:340px;object-fit:cover;">
                </div>
            </a>
            @endif
            @if($hero->category)
            <span class="nyt-section-label">{{ $hero->category->name }}</span>
            @endif
            <a href="{{ route('news.show', $hero->slug) }}">
                <h2 class="nyt-headline nyt-headline-xl" style="margin-bottom:8px;">{{ $hero->title }}</h2>
            </a>
            <p class="nyt-summary" style="font-size:1rem;line-height:1.6;">{{ $summary($hero, 160) }}</p>
            <p class="nyt-byline">{{ $hero->user->name ?? '' }}
                @if($hero->published_at) · {{ $hero->published_at->locale('ko')->diffForHumans() }}@endif
            </p>
        </div>
        @endif

        {{-- 세로 구분선 --}}
        <div class="nyt-vsep" style="margin:12px 0;"></div>

        {{-- 우측 2개 + 인기기사 --}}
        <div style="padding:12px 0 16px 20px;">
            @foreach($top2 as $art)
            <div style="{{ !$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:12px;margin-top:12px;' : '' }}">
                @if($art->category)
                <span class="nyt-section-label">{{ $art->category->name }}</span>
                @endif
                <a href="{{ route('news.show', $art->slug) }}">
                    <h3 class="nyt-headline nyt-headline-md">{{ $art->title }}</h3>
                </a>
                <p class="nyt-summary" style="font-size:.8125rem;">{{ $summary($art, 80) }}</p>
                <p class="nyt-byline">{{ $art->user->name ?? '' }}</p>
            </div>
            @endforeach

            @if($popularArticles->isNotEmpty())
            <div style="border-top:3px solid #121212;margin-top:16px;padding-top:10px;">
                <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#333;margin:0 0 10px;">많이 본 기사</p>
                @foreach($popularArticles->take(4) as $i => $pop)
                <div style="{{ $i > 0 ? 'border-top:1px solid #e2e2e2;padding-top:8px;margin-top:8px;' : '' }}display:flex;gap:10px;align-items:flex-start;">
                    <span style="font-family:var(--nyt-serif);font-size:1.4rem;font-weight:700;color:#ccc;line-height:1;flex-shrink:0;width:20px;">{{ $i+1 }}</span>
                    <a href="{{ route('news.show', $pop->slug) }}">
                        <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.3;color:#121212;">{{ $pop->title }}</p>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     2번째 행: 3칸 그리드
══════════════════════════════════════ --}}
@if($top3->isNotEmpty())
<section class="nyt-section-block">
    <div class="nyt-grid-3" style="border-bottom:1px solid #e2e2e2;">
        @foreach($top3 as $art)
        <div class="nyt-col-divider" style="padding:14px {{ $loop->last ? '0' : '16px' }} 16px {{ $loop->first ? '0' : '16px' }};">
            @if($art->thumbnail)
            <a href="{{ route('news.show', $art->slug) }}" style="display:block;margin-bottom:10px;">
                <img src="{{ $art->thumbnail }}" alt="{{ $art->title }}"
                     style="width:100%;height:160px;object-fit:cover;">
            </a>
            @endif
            @if($art->category)
            <span class="nyt-section-label">{{ $art->category->name }}</span>
            @endif
            <a href="{{ route('news.show', $art->slug) }}">
                <h3 class="nyt-headline nyt-headline-sm">{{ $art->title }}</h3>
            </a>
            <p class="nyt-summary" style="font-size:.8125rem;">{{ $summary($art, 80) }}</p>
            <p class="nyt-byline">{{ $art->user->name ?? '' }}</p>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- ══════════════════════════════════════
     카테고리별 섹션
══════════════════════════════════════ --}}
@foreach($categories->take(4) as $catIdx => $cat)
@php
    $catArts = Article::with(['user'])
        ->where('status','published')
        ->where('category_id', $cat->id)
        ->orderBy('published_at','desc')
        ->limit(5)
        ->get();
@endphp
@if($catArts->isNotEmpty())
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2>{{ $cat->name }}</h2>
        <a href="{{ route('news.index', ['category' => $cat->slug]) }}" class="more">더보기 →</a>
    </div>

    @php $catLead = $catArts->first(); $catRest = $catArts->slice(1); @endphp
    <div class="nyt-home-cat">

        {{-- 리드 기사 --}}
        <div style="padding-right:20px;">
            @if($catLead->thumbnail)
            <a href="{{ route('news.show', $catLead->slug) }}" style="display:block;margin-bottom:10px;">
                <img src="{{ $catLead->thumbnail }}" alt="{{ $catLead->title }}"
                     style="width:100%;height:200px;object-fit:cover;">
            </a>
            @endif
            <a href="{{ route('news.show', $catLead->slug) }}">
                <h3 class="nyt-headline nyt-headline-lg">{{ $catLead->title }}</h3>
            </a>
            <p class="nyt-summary">{{ $summary($catLead, 120) }}</p>
            <p class="nyt-byline">{{ $catLead->user->name ?? '' }}</p>
        </div>

        <div class="nyt-vsep"></div>

        {{-- 나머지 기사 목록 --}}
        <div style="padding-left:20px;">
            @foreach($catRest as $art)
            <div style="{{ !$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:10px;margin-top:10px;' : '' }}display:flex;gap:10px;align-items:flex-start;">
                @if($art->thumbnail)
                <a href="{{ route('news.show', $art->slug) }}" style="flex-shrink:0;">
                    <img src="{{ $art->thumbnail }}" alt="{{ $art->title }}"
                         style="width:72px;height:52px;object-fit:cover;">
                </a>
                @endif
                <div>
                    <a href="{{ route('news.show', $art->slug) }}">
                        <p style="font-family:var(--nyt-serif);font-size:.875rem;font-weight:700;margin:0 0 3px;line-height:1.3;color:#121212;">{{ $art->title }}</p>
                    </a>
                    <p class="nyt-byline">{{ $art->user->name ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 2번째 카테고리 이후: 6칸 그리드 형식 추가 --}}
@if($catIdx === 1 && $mid6->isNotEmpty())
<section class="nyt-section-block">
    <div class="nyt-grid-6" style="border-bottom:1px solid #e2e2e2;padding-bottom:16px;">
        @foreach($mid6 as $art)
        <div class="nyt-col-divider" style="padding:14px {{ $loop->last ? '0' : '10px' }} 0 {{ $loop->first ? '0' : '10px' }};">
            @if($art->thumbnail)
            <a href="{{ route('news.show', $art->slug) }}" style="display:block;margin-bottom:8px;">
                <img src="{{ $art->thumbnail }}" alt="{{ $art->title }}"
                     style="width:100%;height:90px;object-fit:cover;">
            </a>
            @endif
            @if($art->category)
            <span class="nyt-section-label" style="font-size:10px;">{{ $art->category->name }}</span>
            @endif
            <a href="{{ route('news.show', $art->slug) }}">
                <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.3;color:#121212;">{{ $art->title }}</p>
            </a>
        </div>
        @endforeach
    </div>
</section>
@endif

@endforeach

{{-- ══════════════════════════════════════
     구독 배너
══════════════════════════════════════ --}}
<div class="nyt-sub-banner">
    <div class="nyt-container">
        <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#aaa;margin:0 0 10px;">{{ Setting::get('site_name','Laraboard') }}</p>
        <h3>지금 구독하고 모든 기사를 이용하세요.</h3>
        <p>독자 여러분의 후원이 좋은 저널리즘을 만듭니다.</p>
        @guest
        <a href="{{ route('register') }}" class="nyt-sub-btn">무료로 시작하기</a>
        @endguest
        @auth
        <a href="{{ route('news.index') }}" class="nyt-sub-btn">전체 기사 보기</a>
        @endauth
    </div>
</div>

{{-- ══════════════════════════════════════
     나머지 카테고리 (소형 그리드)
══════════════════════════════════════ --}}
@foreach($categories->slice(4) as $cat)
@php
    $catArts2 = Article::with(['user'])
        ->where('status','published')
        ->where('category_id', $cat->id)
        ->orderBy('published_at','desc')
        ->limit(4)
        ->get();
@endphp
@if($catArts2->isNotEmpty())
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2>{{ $cat->name }}</h2>
        <a href="{{ route('news.index', ['category' => $cat->slug]) }}" class="more">더보기 →</a>
    </div>
    <div class="nyt-grid-4" style="border-bottom:1px solid #e2e2e2;padding-bottom:16px;">
        @foreach($catArts2 as $art)
        <div class="nyt-col-divider" style="padding:12px {{ $loop->last ? '0' : '14px' }} 0 {{ $loop->first ? '0' : '14px' }};">
            @if($art->thumbnail)
            <a href="{{ route('news.show', $art->slug) }}" style="display:block;margin-bottom:8px;">
                <img src="{{ $art->thumbnail }}" alt="{{ $art->title }}"
                     style="width:100%;height:120px;object-fit:cover;">
            </a>
            @endif
            <a href="{{ route('news.show', $art->slug) }}">
                <p style="font-family:var(--nyt-serif);font-size:.9375rem;font-weight:700;margin:0 0 5px;line-height:1.3;color:#121212;">{{ $art->title }}</p>
            </a>
            <p class="nyt-summary" style="font-size:.8125rem;">{{ $summary($art, 70) }}</p>
            <p class="nyt-byline">{{ $art->user->name ?? '' }}</p>
        </div>
        @endforeach
    </div>
</section>
@endif
@endforeach

{{-- ══════════════════════════════════════
     게시판 섹션
══════════════════════════════════════ --}}
@if($boards->isNotEmpty())
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2>커뮤니티</h2>
    </div>
    <div class="nyt-grid-3" style="border-bottom:1px solid #e2e2e2;padding-bottom:16px;">
        @foreach($boards->take(3) as $brd)
        @php $bPosts = $brd->posts()->with('user')->orderBy('created_at','desc')->limit(5)->get(); @endphp
        <div class="nyt-col-divider" style="padding:12px {{ $loop->last ? '0' : '20px' }} 0 {{ $loop->first ? '0' : '20px' }};">
            <div style="display:flex;justify-content:space-between;align-items:baseline;border-top:3px solid #121212;padding-top:8px;margin-bottom:10px;">
                <span style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;">{{ $brd->board_name }}</span>
                <a href="{{ route('bbs.index', $brd->board_id) }}"
                   style="font-family:var(--nyt-sans);font-size:.75rem;color:var(--nyt-section);font-weight:600;text-transform:uppercase;">더보기</a>
            </div>
            @forelse($bPosts as $post)
            <div style="{{ !$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:8px;margin-top:8px;' : '' }}">
                <a href="{{ route('bbs.show', [$brd->board_id, $post->id]) }}">
                    <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:{{ $loop->first ? '700' : '400' }};margin:0 0 2px;line-height:1.35;color:#121212;">
                        @if($post->is_notice)<span style="color:#d63638;font-size:.7rem;font-weight:700;">[공지] </span>@endif
                        {{ $post->title }}
                    </p>
                </a>
                <p class="nyt-byline">{{ $post->user->name ?? '' }} · {{ $post->created_at->format('m.d') }}</p>
            </div>
            @empty
            <p style="font-size:.8125rem;color:#999;padding:8px 0;">게시글이 없습니다.</p>
            @endforelse
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- ══════════════════════════════════════
     최신 기사 4칸 그리드
══════════════════════════════════════ --}}
@if($top4->isNotEmpty())
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2>최신 기사</h2>
        <a href="{{ route('news.index') }}" class="more">전체 보기 →</a>
    </div>
    <div class="nyt-grid-4" style="padding-bottom:24px;">
        @foreach($top4 as $art)
        <div class="nyt-col-divider" style="padding:14px {{ $loop->last ? '0' : '16px' }} 0 {{ $loop->first ? '0' : '16px' }};">
            @if($art->thumbnail)
            <a href="{{ route('news.show', $art->slug) }}" style="display:block;margin-bottom:10px;">
                <img src="{{ $art->thumbnail }}" alt="{{ $art->title }}"
                     style="width:100%;height:130px;object-fit:cover;">
            </a>
            @endif
            @if($art->category)
            <span class="nyt-section-label">{{ $art->category->name }}</span>
            @endif
            <a href="{{ route('news.show', $art->slug) }}">
                <h3 class="nyt-headline nyt-headline-sm">{{ $art->title }}</h3>
            </a>
            <p class="nyt-summary" style="font-size:.8125rem;">{{ $summary($art, 70) }}</p>
            <p class="nyt-byline">{{ $art->user->name ?? '' }}</p>
        </div>
        @endforeach
    </div>
</section>
@endif

</div>{{-- .nyt-container --}}
@endsection
