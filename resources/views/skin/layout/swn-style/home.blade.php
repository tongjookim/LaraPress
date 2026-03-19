@extends('skin.layout.swn-style.main')

@section('content')
@php
    use App\Models\Article;
    use App\Models\ArticleCategory;
    use App\Models\Board;
    use App\Models\Post;
    use App\Models\Setting;

    $mainWidgets    = json_decode(Setting::get('home_main_widgets',    '["hero_articles","category_articles","board_sections","stats"]'), true) ?? [];
    $sidebarWidgets = json_decode(Setting::get('home_sidebar_widgets', '["login","notice","popular_articles","boards"]'), true) ?? [];

    // 공통 데이터
    $heroArticle     = Article::with(['category','user'])->where('status','published')->orderBy('published_at','desc')->first();
    $categories      = ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $boards          = Board::where('is_active', true)->orderBy('order')->get();
    $noticePosts     = Post::where('is_notice', true)->with(['board'])->orderBy('created_at','desc')->limit(5)->get();
    $popularPosts    = Post::with(['board'])->orderBy('view_count','desc')->limit(8)->get();
    $popularArticles = Article::where('status','published')->orderBy('view_count','desc')->limit(6)->get();
@endphp

<div class="flex flex-col lg:flex-row gap-6">

    {{-- 메인 영역 --}}
    <div class="flex-1 min-w-0">

        @foreach($mainWidgets as $widget)

        {{-- ── 히어로 기사 ── --}}
        @if($widget === 'hero_articles' && $heroArticle)
        <div class="mb-8">
            <div class="swn-card rounded-lg overflow-hidden">
                @if($heroArticle->thumbnail)
                {{-- 썸네일 있을 때: 전면 이미지 + 하단 텍스트 오버레이 --}}
                <div style="position:relative;">
                    <img src="{{ $heroArticle->thumbnail }}" alt="{{ $heroArticle->title }}"
                         style="width:100%;height:280px;object-fit:cover;display:block;">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(10,20,40,.85) 0%,rgba(10,20,40,.2) 50%,transparent 100%);"></div>
                    <div style="position:absolute;bottom:0;left:0;right:0;padding:24px;">
                        @if($heroArticle->category)
                        <span class="swn-badge swn-badge-category mb-2 inline-block">{{ $heroArticle->category->name }}</span>
                        @endif
                        <a href="{{ route('news.show', $heroArticle->slug) }}">
                            <h2 class="swn-article-title text-2xl md:text-3xl leading-tight" style="color:#ffffff;">{{ $heroArticle->title }}</h2>
                        </a>
                        <div class="swn-article-meta mt-2" style="color:rgba(255,255,255,.7);">
                            <span>{{ $heroArticle->user->name ?? '' }}</span>
                            <span class="mx-2">·</span>
                            <span>{{ $heroArticle->published_at?->format('Y.m.d H:i') }}</span>
                        </div>
                    </div>
                </div>
                @else
                {{-- 썸네일 없을 때: 텍스트만 --}}
                <div class="p-6">
                    @if($heroArticle->category)
                    <span class="swn-badge swn-badge-category mb-2 inline-block">{{ $heroArticle->category->name }}</span>
                    @endif
                    <a href="{{ route('news.show', $heroArticle->slug) }}">
                        <h2 class="swn-article-title text-gray-900 text-2xl leading-tight mb-2">{{ $heroArticle->title }}</h2>
                    </a>
                    @if($heroArticle->excerpt)
                    <p class="text-sm text-gray-500 leading-relaxed mb-3">{{ Str::limit($heroArticle->excerpt, 120) }}</p>
                    @endif
                    <div class="swn-article-meta">
                        <span>{{ $heroArticle->user->name ?? '' }}</span>
                        <span class="mx-2">·</span>
                        <span>{{ $heroArticle->published_at?->format('Y.m.d H:i') }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── 카테고리별 기사 ── --}}
        @if($widget === 'category_articles')
        @foreach($categories as $cat)
        @php
            $catArticles = Article::with('user')
                ->where('status', 'published')
                ->where('category_id', $cat->id)
                ->orderBy('published_at', 'desc')
                ->limit(4)
                ->get();
        @endphp
        @if($catArticles->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="swn-section-title text-lg text-gray-900">{{ $cat->name }}</h2>
                <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">더보기 →</a>
            </div>
            <div class="swn-card rounded-lg overflow-hidden">
                @foreach($catArticles as $i => $art)
                <div class="swn-news-item flex items-center gap-4 {{ $i === 0 ? 'py-4' : '' }}">
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('news.show', $art->slug) }}">
                            <p class="swn-article-title {{ $i === 0 ? 'text-base font-bold' : 'text-sm' }} leading-snug">{{ $art->title }}</p>
                        </a>
                        @if($i === 0 && $art->excerpt)
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($art->excerpt, 80) }}</p>
                        @endif
                        <div class="swn-article-meta mt-1">
                            <span>{{ $art->user->name ?? '' }}</span>
                            <span>{{ $art->published_at?->format('m.d H:i') }}</span>
                            <span>조회 {{ number_format($art->view_count) }}</span>
                        </div>
                    </div>
                    @if($art->thumbnail)
                    <a href="{{ route('news.show', $art->slug) }}" class="flex-shrink-0">
                        <img src="{{ $art->thumbnail }}" alt="{{ $art->title }}"
                             style="width:{{ $i === 0 ? '100px' : '72px' }};height:{{ $i === 0 ? '72px' : '52px' }};object-fit:cover;border-radius:4px;">
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach
        @endif

        {{-- ── 최신 기사 목록 ── --}}
        @if($widget === 'latest_articles')
        @php $latestArts = Article::with(['category','user'])->where('status','published')->orderBy('published_at','desc')->limit(10)->get(); @endphp
        @if($latestArts->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="swn-section-title text-lg text-gray-900">최신 기사</h2>
                <a href="{{ route('news.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">더보기 →</a>
            </div>
            <div class="swn-card rounded-lg">
                @foreach($latestArts as $art)
                <div class="swn-news-item px-5 flex items-start gap-3">
                    @if($art->category)
                    <span class="swn-badge swn-badge-category flex-shrink-0 mt-0.5">{{ $art->category->name }}</span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('news.show', $art->slug) }}" class="swn-article-title text-sm block truncate">{{ $art->title }}</a>
                        <div class="swn-article-meta mt-0.5">
                            <span>{{ $art->user->name ?? '' }}</span>
                            <span>{{ $art->published_at?->format('m.d H:i') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        {{-- ── 게시판별 섹션 ── --}}
        @if($widget === 'board_sections' && $boards->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($boards->take(4) as $board)
            @php $boardPosts = $board->posts()->with('user')->orderBy('created_at','desc')->limit(5)->get(); @endphp
            <div class="swn-card rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-sm text-gray-900">{{ $board->board_name }}</h3>
                    <a href="{{ route('bbs.index', $board->board_id) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">더보기 →</a>
                </div>
                <div class="px-5">
                    @forelse($boardPosts as $post)
                    <div class="swn-news-item flex items-center justify-between gap-3">
                        <a href="{{ route('bbs.show', [$board->board_id, $post->id]) }}"
                           class="swn-article-title text-sm truncate flex-1">
                            @if($post->is_notice)<span class="text-red-500 font-bold mr-1">[공지]</span>@endif
                            {{ $post->title }}
                        </a>
                        <span class="swn-datetime flex-shrink-0">{{ $post->created_at->format('m.d') }}</span>
                    </div>
                    @empty
                    <div class="py-8 text-center text-gray-400 text-xs">게시글이 없습니다.</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── 사이트 통계 ── --}}
        @if($widget === 'stats')
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900">{{ number_format(Article::where('status','published')->count()) }}</p>
                <p class="text-xs text-gray-500 mt-1 font-medium">기사</p>
            </div>
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900">{{ number_format(Post::count()) }}</p>
                <p class="text-xs text-gray-500 mt-1 font-medium">게시글</p>
            </div>
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900">{{ number_format(\App\Models\Comment::count()) }}</p>
                <p class="text-xs text-gray-500 mt-1 font-medium">댓글</p>
            </div>
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900">{{ number_format(\App\Models\User::count()) }}</p>
                <p class="text-xs text-gray-500 mt-1 font-medium">회원</p>
            </div>
        </div>
        @endif

        @endforeach
    </div>

    {{-- 사이드바 --}}
    <aside class="w-full lg:w-72 flex-shrink-0">
        @foreach($sidebarWidgets as $widget)

        {{-- 로그인 위젯 --}}
        @if($widget === 'login')
        <div class="swn-card rounded-lg p-5 mb-5">
            @auth
            <div class="text-center">
                <div class="w-14 h-14 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                    <span class="text-xl font-bold text-blue-600">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <p class="font-bold text-gray-900 text-sm">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->email }}</p>
            </div>
            @else
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900 mb-1">환영합니다!</p>
                <p class="text-xs text-gray-400 mb-4">로그인하고 커뮤니티에 참여하세요</p>
                <div class="flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2 bg-blue-600 text-white rounded text-xs font-bold hover:bg-blue-700 transition">로그인</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded text-xs font-bold hover:bg-gray-200 transition">회원가입</a>
                </div>
            </div>
            @endauth
        </div>
        @endif

        {{-- 공지사항 --}}
        @if($widget === 'notice' && $noticePosts->isNotEmpty())
        <div class="swn-card rounded-lg overflow-hidden mb-5">
            <div class="px-4 py-3 bg-red-50 border-b border-red-100">
                <h3 class="font-bold text-sm text-red-700">공지사항</h3>
            </div>
            <div class="px-4">
                @foreach($noticePosts as $notice)
                <div class="swn-news-item flex items-center justify-between">
                    <a href="{{ route('bbs.show', [$notice->board->board_id ?? 'free', $notice->id]) }}"
                       class="swn-article-title text-xs truncate flex-1">{{ $notice->title }}</a>
                    <span class="swn-datetime flex-shrink-0 ml-2">{{ $notice->created_at->format('m.d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 인기 기사 --}}
        @if($widget === 'popular_articles' && $popularArticles->isNotEmpty())
        <div class="swn-card rounded-lg overflow-hidden mb-5">
            <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                <h3 class="font-bold text-sm text-blue-700">인기 기사</h3>
            </div>
            <div class="px-4">
                @foreach($popularArticles as $i => $art)
                <div class="swn-news-item flex items-start gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center text-xs font-black
                                 {{ $i < 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }}">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('news.show', $art->slug) }}" class="swn-article-title text-xs block truncate">{{ $art->title }}</a>
                        <span class="swn-datetime">조회 {{ number_format($art->view_count) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 인기 게시글 --}}
        @if($widget === 'popular_posts' && $popularPosts->isNotEmpty())
        <div class="swn-card rounded-lg overflow-hidden mb-5">
            <div class="px-4 py-3 bg-purple-50 border-b border-purple-100">
                <h3 class="font-bold text-sm text-purple-700">인기 게시글</h3>
            </div>
            <div class="px-4">
                @foreach($popularPosts as $i => $pop)
                <div class="swn-news-item flex items-start gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center text-xs font-black
                                 {{ $i < 3 ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-500' }}">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('bbs.show', [$pop->board->board_id ?? 'free', $pop->id]) }}"
                           class="swn-article-title text-xs block truncate">{{ $pop->title }}</a>
                        <span class="swn-datetime">조회 {{ number_format($pop->view_count) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 게시판 바로가기 --}}
        @if($widget === 'boards' && $boards->isNotEmpty())
        <div class="swn-card rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="font-bold text-sm text-gray-700">게시판 목록</h3>
            </div>
            <div class="p-4">
                @foreach($boards as $board)
                <a href="{{ route('bbs.index', $board->board_id) }}"
                   class="flex items-center justify-between py-2 px-2 rounded hover:bg-gray-50 transition text-sm group">
                    <span class="text-gray-700 group-hover:text-blue-600 font-medium">{{ $board->board_name }}</span>
                    <span class="text-xs text-gray-400">{{ $board->posts->count() }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @endforeach
    </aside>

</div>
@endsection
