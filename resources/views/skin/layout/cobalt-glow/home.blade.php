{{-- resources/views/skin/layout/cobalt-glow/home.blade.php --}}
@extends('skin.layout.cobalt-glow.main')

@section('content')
@php
    use App\Models\Article;
    use App\Models\ArticleCategory;
    use App\Models\Board;
    use App\Models\Post;
    use App\Models\Setting;

    $mainWidgets    = json_decode(Setting::get('home_main_widgets',    '["hero_articles","category_articles","board_sections","stats"]'), true) ?? [];
    $sidebarWidgets = json_decode(Setting::get('home_sidebar_widgets', '["login","notice","popular_articles","boards"]'), true) ?? [];

    // 공통 데이터 로드
    $heroArticle     = Article::with(['category','user'])->where('status','published')->orderBy('published_at','desc')->first();
    $categories      = ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $boards          = Board::where('is_active', true)->orderBy('order')->get();
    $noticePosts     = Post::where('is_notice', true)->with(['board'])->orderBy('created_at','desc')->limit(5)->get();
    $popularPosts    = Post::with(['board'])->orderBy('view_count','desc')->limit(8)->get();
    $popularArticles = Article::where('status','published')->orderBy('view_count','desc')->limit(6)->get();
@endphp

<div class="flex flex-col lg:flex-row gap-8">

    {{-- ── 메인 영역 ── --}}
    <div class="flex-1 min-w-0">
        @foreach($mainWidgets as $widget)

            {{-- 1. 히어로 기사 (메인 대형 카드) --}}
            @if($widget === 'hero_articles' && $heroArticle)
            <div class="mb-10">
                <div class="cobalt-card p-0 overflow-hidden group">
                    <div class="cobalt-card-glow"></div>
                    @if($heroArticle->thumbnail)
                        <div class="relative">
                            <img src="{{ $heroArticle->thumbnail }}" alt="{{ $heroArticle->title }}" class="w-full h-[350px] object-cover transition duration-700 group-hover:scale-105">
                            {{-- 오버레이 그라데이션: Cobalt Glow 톤 --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                @if($heroArticle->category)
                                    <span class="inline-block px-3 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-full mb-3 shadow-lg shadow-blue-600/30 uppercase tracking-widest">
                                        {{ $heroArticle->category->name }}
                                    </span>
                                @endif
                                <a href="{{ route('news.show', $heroArticle->slug) }}">
                                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight hover:text-blue-400 transition cursor-pointer">
                                        {{ $heroArticle->title }}
                                    </h2>
                                </a>
                                <div class="flex items-center gap-3 mt-4 text-white/50 text-sm">
                                    <span class="font-bold text-white/80">{{ $heroArticle->user->name ?? 'Admin' }}</span>
                                    <span class="w-1 h-1 bg-white/20 rounded-full"></span>
                                    <span>{{ $heroArticle->published_at?->format('Y.m.d H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-8">
                            {{-- 썸네일 없을 때의 디자인 --}}
                            <h2 class="text-3xl font-black text-white mb-4">{{ $heroArticle->title }}</h2>
                            <p class="text-white/60 leading-relaxed mb-6">{{ Str::limit($heroArticle->excerpt, 150) }}</p>
                            <a href="{{ route('news.show', $heroArticle->slug) }}" class="text-blue-500 font-bold hover:underline">Read More →</a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- 2. 카테고리별 섹션 (그리드 타입) --}}
            @if($widget === 'category_articles')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                @foreach($categories as $cat)
                    @php
                        $catArticles = Article::with('user')->where('status', 'published')->where('category_id', $cat->id)
                            ->orderBy('published_at', 'desc')->limit(4)->get();
                    @endphp
                    @if($catArticles->isNotEmpty())
                    <div class="space-y-4">
                        <div class="flex items-center justify-between px-1">
                            <h2 class="text-sm font-black text-blue-500 uppercase tracking-widest">{{ $cat->name }}</h2>
                            <a href="{{ route('news.index', ['category' => $cat->slug]) }}" class="text-[10px] text-white/30 hover:text-white transition">VIEW ALL</a>
                        </div>
                        <div class="cobalt-card p-5 space-y-4">
                            @foreach($catArticles as $i => $art)
                                <div class="flex gap-4 {{ !$loop->last ? 'pb-4 border-b border-white/5' : '' }}">
                                    <div class="flex-1">
                                        <a href="{{ route('news.show', $art->slug) }}" class="text-sm font-bold text-white hover:text-blue-400 line-clamp-2 leading-snug">
                                            {{ $art->title }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-2 text-[10px] text-white/40">
                                            <span>{{ $art->published_at?->format('m.d') }}</span>
                                            <span class="w-0.5 h-0.5 bg-white/10"></span>
                                            <span>조회 {{ number_format($art->view_count) }}</span>
                                        </div>
                                    </div>
                                    @if($art->thumbnail)
                                        <img src="{{ $art->thumbnail }}" class="w-16 h-12 object-cover rounded bg-gray-800">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif

            {{-- 3. 게시판별 섹션 (가로형 리스트) --}}
            @if($widget === 'board_sections')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                @foreach($boards->take(4) as $board)
                @php $boardPosts = $board->posts()->orderBy('created_at','desc')->limit(5)->get(); @endphp
                <div class="cobalt-card p-0 overflow-hidden">
                    <div class="bg-blue-500/5 px-5 py-3 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-xs font-black text-white tracking-widest uppercase">{{ $board->board_name }}</h3>
                        <a href="{{ route('bbs.index', $board->board_id) }}" class="text-[10px] text-blue-500 font-bold hover:text-blue-400">MORE</a>
                    </div>
                    <div class="p-4 space-y-2.5">
                        @forelse($boardPosts as $post)
                            <div class="flex justify-between items-center text-sm group">
                                <a href="{{ route('bbs.show', [$board->board_id, $post->id]) }}" class="text-white/70 group-hover:text-blue-400 truncate pr-4">
                                    @if($post->is_notice)<span class="text-blue-500 font-bold">[공지]</span>@endif
                                    {{ $post->title }}
                                </a>
                                <span class="text-[10px] text-white/20 flex-shrink-0">{{ $post->created_at->format('m.d') }}</span>
                            </div>
                        @empty
                            <div class="py-10 text-center text-white/20 text-xs italic">No posts found.</div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- 4. 실시간 통계 --}}
            @if($widget === 'stats')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                @php 
                    $stats = [
                        ['label' => 'Articles', 'value' => Article::where('status','published')->count()],
                        ['label' => 'Posts',    'value' => Post::count()],
                        ['label' => 'Comments', 'value' => \App\Models\Comment::count()],
                        ['label' => 'Members',  'value' => \App\Models\User::count()],
                    ];
                @endphp
                @foreach($stats as $stat)
                <div class="cobalt-card p-6 text-center group">
                    <div class="cobalt-card-glow"></div>
                    <p class="text-3xl font-black text-white group-hover:text-blue-400 transition">{{ number_format($stat['value']) }}</p>
                    <p class="text-[10px] font-bold text-white/30 uppercase tracking-[0.2em] mt-2">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
            @endif

        @endforeach
    </div>

    {{-- ── 사이드바 ── --}}
    <aside class="w-full lg:w-80 flex-shrink-0 space-y-6">
        @foreach($sidebarWidgets as $widget)

            {{-- 1. 로그인 위젯 --}}
            @if($widget === 'login')
            <div class="cobalt-card p-6 border-blue-500/10 shadow-lg shadow-blue-500/5">
                @auth
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-xl font-black text-white shadow-lg shadow-blue-600/30">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-black text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-white/40 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-6">
                    <a href="{{ route('profile.show') }}" class="py-2 text-center text-[10px] font-bold bg-white/5 text-white/60 rounded-lg hover:bg-white/10 transition">MY PAGE</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 text-center text-[10px] font-bold bg-blue-600/10 text-blue-500 rounded-lg hover:bg-blue-600 hover:text-white transition">LOGOUT</button>
                    </form>
                </div>
                @else
                <div class="text-center">
                    <h3 class="font-black text-white mb-1 uppercase tracking-tighter">Welcome Back</h3>
                    <p class="text-[10px] text-white/40 mb-6 uppercase tracking-widest">Join our cobalt community</p>
                    <a href="{{ route('login') }}" class="block w-full py-3 bg-blue-600 text-white text-xs font-black rounded-xl hover:bg-blue-500 transition shadow-lg shadow-blue-600/20 mb-3 uppercase">Sign In</a>
                    <a href="{{ route('register') }}" class="block w-full py-3 bg-white/5 text-white/60 text-xs font-black rounded-xl hover:bg-white/10 transition uppercase">Create Account</a>
                </div>
                @endauth
            </div>
            @endif

            {{-- 2. 공지사항 --}}
            @if($widget === 'notice' && $noticePosts->isNotEmpty())
            <div class="cobalt-card p-0 overflow-hidden border-blue-500/20">
                <div class="bg-blue-600 px-5 py-3 flex items-center gap-2">
                    <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">Notice</h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($noticePosts as $notice)
                        <a href="{{ route('bbs.show', [$notice->board->board_id ?? 'free', $notice->id]) }}" class="flex justify-between items-center group">
                            <span class="text-xs text-white/70 group-hover:text-white truncate pr-4">{{ $notice->title }}</span>
                            <span class="text-[10px] text-white/20">{{ $notice->created_at->format('m.d') }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 3. 인기 기사 --}}
            @if($widget === 'popular_articles' && $popularArticles->isNotEmpty())
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] px-1">Popular Articles</h3>
                <div class="cobalt-card p-5 space-y-5">
                    @foreach($popularArticles as $i => $art)
                        <div class="flex items-start gap-4 group">
                            <span class="text-xl font-black {{ $i < 3 ? 'text-blue-500' : 'text-white/10' }} italic leading-none">{{ $i + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('news.show', $art->slug) }}" class="text-xs font-bold text-white/80 group-hover:text-blue-400 line-clamp-2 leading-snug">
                                    {{ $art->title }}
                                </a>
                                <p class="text-[9px] text-white/20 mt-1 uppercase tracking-widest">Views {{ number_format($art->view_count) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 4. 게시판 목록 --}}
            @if($widget === 'boards' && $boards->isNotEmpty())
            <div class="cobalt-card p-5">
                <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] mb-4">Board Index</h3>
                <div class="space-y-1">
                    @foreach($boards as $board)
                        <a href="{{ route('bbs.index', $board->board_id) }}" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-blue-600/10 group transition">
                            <span class="text-xs font-bold text-white/60 group-hover:text-blue-400">{{ $board->board_name }}</span>
                            <span class="text-[10px] text-white/10 group-hover:text-blue-500/50">{{ number_format($board->posts->count()) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

        @endforeach
    </aside>

</div>
@endsection