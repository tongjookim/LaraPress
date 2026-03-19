@extends('skin.layout.basic.main')

@section('content')
@php
    use App\Models\Article;
    use App\Models\ArticleCategory;
    use App\Models\Board;
    use App\Models\Post;
    use App\Models\Setting;

    $mainWidgets    = json_decode(Setting::get('home_main_widgets',    '["hero_articles","category_articles","board_sections","stats"]'), true) ?? [];
    $sidebarWidgets = json_decode(Setting::get('home_sidebar_widgets', '["login","notice","popular_articles","boards"]'), true) ?? [];

    $heroArticle     = Article::with(['category','user'])->where('status','published')->orderBy('published_at','desc')->first();
    $categories      = ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $boards          = Board::where('is_active', true)->orderBy('order')->withCount('posts')->get();
    $noticePosts     = Post::where('is_notice', true)->with(['board'])->orderBy('created_at','desc')->limit(5)->get();
    $popularPosts    = Post::with(['board'])->orderBy('view_count','desc')->limit(8)->get();
    $popularArticles = Article::where('status','published')->orderBy('view_count','desc')->limit(6)->get();
@endphp

<div class="flex flex-col lg:flex-row gap-8">

    {{-- 메인 영역 --}}
    <div class="flex-1 min-w-0">

        @foreach($mainWidgets as $widget)

        {{-- 히어로 기사 --}}
        @if($widget === 'hero_articles' && $heroArticle)
        <div class="mb-10">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($heroArticle->thumbnail)
                {{-- 썸네일 있을 때: 전면 이미지 + 하단 오버레이 --}}
                <div style="position:relative;">
                    <img src="{{ $heroArticle->thumbnail }}" alt="{{ $heroArticle->title }}"
                         style="width:100%;height:300px;object-fit:cover;display:block;">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.75) 0%,rgba(0,0,0,.15) 50%,transparent 100%);"></div>
                    <div style="position:absolute;bottom:0;left:0;right:0;padding:28px 32px;">
                        @if($heroArticle->category)
                        <span class="inline-block px-2 py-0.5 text-white text-xs font-bold rounded mb-2"
                              style="background:var(--site-primary);">{{ $heroArticle->category->name }}</span>
                        @endif
                        <a href="{{ route('news.show', $heroArticle->slug) }}">
                            <h2 class="text-2xl font-bold text-white leading-tight hover:opacity-80 transition">{{ $heroArticle->title }}</h2>
                        </a>
                        <p class="text-sm text-gray-300 mt-2">{{ $heroArticle->user->name ?? '' }} · {{ $heroArticle->published_at?->format('Y.m.d') }}</p>
                    </div>
                </div>
                @else
                {{-- 썸네일 없을 때: 텍스트만 --}}
                <div class="p-7">
                    @if($heroArticle->category)
                    <span class="inline-block px-2 py-0.5 text-xs font-bold rounded mb-3"
                          style="background:var(--site-primary-light);color:var(--site-primary);">{{ $heroArticle->category->name }}</span>
                    @endif
                    <a href="{{ route('news.show', $heroArticle->slug) }}">
                        <h2 class="text-2xl font-bold text-gray-900 leading-tight mb-3 site-nav-link hover:underline transition">{{ $heroArticle->title }}</h2>
                    </a>
                    @if($heroArticle->excerpt)
                    <p class="text-gray-500 text-sm leading-relaxed mb-3">{{ Str::limit($heroArticle->excerpt, 140) }}</p>
                    @endif
                    <p class="text-gray-400 text-sm">{{ $heroArticle->user->name ?? '' }} · {{ $heroArticle->published_at?->format('Y.m.d') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- 카테고리별 기사 --}}
        @if($widget === 'category_articles')
        @foreach($categories as $cat)
        @php
            $catArticles = Article::with('user')
                ->where('status','published')
                ->where('category_id', $cat->id)
                ->orderBy('published_at','desc')
                ->limit(4)
                ->get();
        @endphp
        @if($catArticles->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 rounded-full mr-3" style="background:var(--site-primary);display:inline-block;"></span>
                    {{ $cat->name }}
                </h2>
                <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
                   class="text-sm site-primary-text hover:underline font-medium">더보기 →</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-50">
                @foreach($catArticles as $i => $art)
                <a href="{{ route('news.show', $art->slug) }}"
                   class="group flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-{{ $i === 0 ? 'bold' : 'medium' }} text-{{ $i === 0 ? 'base' : 'sm' }} text-gray-900 group-hover:underline transition line-clamp-2 leading-snug site-nav-link">
                            {{ $art->title }}
                        </h3>
                        @if($i === 0 && $art->excerpt)
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($art->excerpt, 90) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1.5">
                            {{ $art->user->name ?? '' }} · {{ $art->published_at?->format('m.d') }}
                        </p>
                    </div>
                    @if($art->thumbnail)
                    <div style="flex-shrink:0;overflow:hidden;border-radius:8px;width:{{ $i === 0 ? '96px' : '72px' }};height:{{ $i === 0 ? '68px' : '52px' }};">
                        <img src="{{ $art->thumbnail }}" alt="{{ $art->title }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach
        @endif

        {{-- 최신 기사 --}}
        @if($widget === 'latest_articles')
        @php $latestArts = Article::with(['category','user'])->where('status','published')->orderBy('published_at','desc')->limit(8)->get(); @endphp
        @if($latestArts->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 rounded-full mr-3" style="background:var(--site-accent);display:inline-block;"></span>
                    최신 기사
                </h2>
                <a href="{{ route('news.index') }}" class="text-sm site-primary-text hover:underline">더보기 →</a>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-50">
                @foreach($latestArts as $art)
                <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition">
                    @if($art->category)
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-semibold rounded"
                          style="background:var(--site-primary-light);color:var(--site-primary);">{{ $art->category->name }}</span>
                    @endif
                    <a href="{{ route('news.show', $art->slug) }}" class="flex-1 text-sm text-gray-800 site-nav-link font-medium truncate hover:underline">{{ $art->title }}</a>
                    <span class="flex-shrink-0 text-xs text-gray-400">{{ $art->published_at?->format('m.d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        {{-- 게시판별 섹션 --}}
        @if($widget === 'board_sections' && $boards->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center mb-4 border-b border-gray-100 pb-3">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 bg-green-500 rounded-full mr-3" style="display:inline-block;"></span>
                    게시판
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($boards->take(4) as $brd)
                @php $boardPosts = $brd->posts()->orderBy('created_at','desc')->limit(5)->get(); @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-sm text-gray-900">{{ $brd->board_name }}</h3>
                        <a href="{{ route('bbs.index', $brd->board_id) }}" class="text-xs site-primary-text hover:underline">더보기 →</a>
                    </div>
                    <div class="px-5">
                        @forelse($boardPosts as $post)
                        <div class="flex items-center gap-2 py-2 border-b border-gray-50 last:border-0">
                            <a href="{{ route('bbs.show', [$brd->board_id, $post->id]) }}"
                               class="flex-1 text-sm text-gray-700 site-nav-link truncate hover:underline">
                                @if($post->is_notice)<span class="text-red-500 font-bold mr-1 text-xs">[공지]</span>@endif
                                {{ $post->title }}
                            </a>
                            <span class="flex-shrink-0 text-xs text-gray-400">{{ $post->created_at->format('m.d') }}</span>
                        </div>
                        @empty
                        <p class="py-6 text-center text-xs text-gray-400">게시글이 없습니다.</p>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 통계 --}}
        @if($widget === 'stats')
        <div class="grid grid-cols-3 gap-5 mb-10">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm">
                <div class="text-3xl font-black text-gray-900 mb-1">{{ number_format(\App\Models\User::count()) }}</div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Users</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm">
                <div class="text-3xl font-black text-gray-900 mb-1">{{ number_format(Post::count()) }}</div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Posts</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm">
                <div class="text-3xl font-black text-gray-900 mb-1">{{ number_format(\App\Models\Comment::count()) }}</div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Comments</p>
            </div>
        </div>
        @endif

        @endforeach
    </div>

    {{-- 사이드바 --}}
    <aside class="w-full lg:w-72 flex-shrink-0">
        @foreach($sidebarWidgets as $widget)

        @if($widget === 'login')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
            @auth
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-3"
                     style="background:var(--site-primary-light);">
                    <span class="text-lg font-bold site-primary-text">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <p class="font-bold text-gray-900 text-sm">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
            </div>
            @else
            <p class="text-sm font-bold text-gray-900 mb-1 text-center">환영합니다!</p>
            <p class="text-xs text-gray-500 mb-4 text-center">로그인하고 참여하세요</p>
            <div class="flex gap-2">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2 rounded-lg text-xs font-bold text-white site-primary-btn">로그인</a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold hover:bg-gray-200 transition">회원가입</a>
            </div>
            @endauth
        </div>
        @endif

        @if($widget === 'notice' && $noticePosts->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-gray-100 bg-red-50">
                <h3 class="font-bold text-sm text-red-700">공지사항</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($noticePosts as $notice)
                <div class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 transition">
                    <a href="{{ route('bbs.show', [$notice->board->board_id ?? 'free', $notice->id]) }}"
                       class="flex-1 text-xs text-gray-700 site-nav-link truncate hover:underline">{{ $notice->title }}</a>
                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $notice->created_at->format('m.d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($widget === 'popular_articles' && $popularArticles->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-gray-100" style="background:var(--site-primary-light);">
                <h3 class="font-bold text-sm site-primary-text">인기 기사</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($popularArticles as $i => $art)
                <div class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 transition">
                    <span class="flex-shrink-0 w-4 h-4 rounded text-xs font-black flex items-center justify-center"
                          style="{{ $i < 3 ? 'background:var(--site-primary);color:#fff;' : 'background:#f3f4f6;color:#6b7280;' }}">{{ $i+1 }}</span>
                    <a href="{{ route('news.show', $art->slug) }}" class="flex-1 text-xs text-gray-700 site-nav-link truncate hover:underline">{{ $art->title }}</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($widget === 'popular_posts' && $popularPosts->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-gray-100" style="background:var(--site-primary-light);">
                <h3 class="font-bold text-sm site-primary-text">인기 게시글</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($popularPosts as $i => $pop)
                <div class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 transition">
                    <span class="flex-shrink-0 w-4 h-4 rounded text-xs font-black flex items-center justify-center"
                          style="{{ $i < 3 ? 'background:var(--site-primary);color:#fff;' : 'background:#f3f4f6;color:#6b7280;' }}">{{ $i+1 }}</span>
                    <a href="{{ route('bbs.show', [$pop->board->board_id ?? 'free', $pop->id]) }}"
                       class="flex-1 text-xs text-gray-700 site-nav-link truncate hover:underline">{{ $pop->title }}</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($widget === 'boards' && $boards->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-sm text-gray-700">게시판 목록</h3>
            </div>
            <div class="p-3">
                @foreach($boards as $brd)
                <a href="{{ route('bbs.index', $brd->board_id) }}"
                   class="flex items-center justify-between py-2 px-2 rounded-lg hover:bg-gray-50 transition group">
                    <span class="text-sm text-gray-700 site-nav-link font-medium">{{ $brd->board_name }}</span>
                    <span class="text-xs text-gray-400">{{ $brd->posts_count }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @endforeach
    </aside>
</div>
@endsection
