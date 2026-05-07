<?php $__env->startSection('content'); ?>
<?php
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
?>

<div class="flex flex-col lg:flex-row gap-6">

    
    <div class="flex-1 min-w-0">

        <?php $__currentLoopData = $mainWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($widget === 'hero_articles' && $heroArticle): ?>
        <div class="mb-8">
            <div class="swn-card rounded-lg overflow-hidden">
                <?php if($heroArticle->thumbnail): ?>
                
                <div style="position:relative;">
                    <img src="<?php echo e($heroArticle->thumbnail); ?>" alt="<?php echo e($heroArticle->title); ?>"
                         style="width:100%;height:280px;object-fit:cover;display:block;">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(10,20,40,.85) 0%,rgba(10,20,40,.2) 50%,transparent 100%);"></div>
                    <div style="position:absolute;bottom:0;left:0;right:0;padding:24px;">
                        <?php if($heroArticle->category): ?>
                        <span class="swn-badge swn-badge-category mb-2 inline-block"><?php echo e($heroArticle->category->name); ?></span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('news.show', $heroArticle->slug)); ?>">
                            <h2 class="swn-article-title text-2xl md:text-3xl leading-tight" style="color:#ffffff;"><?php echo e($heroArticle->title); ?></h2>
                        </a>
                        <div class="swn-article-meta mt-2" style="color:rgba(255,255,255,.7);">
                            <span><?php echo e($heroArticle->user->name ?? ''); ?></span>
                            <span class="mx-2">·</span>
                            <span><?php echo e($heroArticle->published_at?->format('Y.m.d H:i')); ?></span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                
                <div class="p-6">
                    <?php if($heroArticle->category): ?>
                    <span class="swn-badge swn-badge-category mb-2 inline-block"><?php echo e($heroArticle->category->name); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('news.show', $heroArticle->slug)); ?>">
                        <h2 class="swn-article-title text-gray-900 text-2xl leading-tight mb-2"><?php echo e($heroArticle->title); ?></h2>
                    </a>
                    <?php if($heroArticle->excerpt): ?>
                    <p class="text-sm text-gray-500 leading-relaxed mb-3"><?php echo e(Str::limit($heroArticle->excerpt, 120)); ?></p>
                    <?php endif; ?>
                    <div class="swn-article-meta">
                        <span><?php echo e($heroArticle->user->name ?? ''); ?></span>
                        <span class="mx-2">·</span>
                        <span><?php echo e($heroArticle->published_at?->format('Y.m.d H:i')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'category_articles'): ?>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $catArticles = Article::with('user')
                ->where('status', 'published')
                ->where('category_id', $cat->id)
                ->orderBy('published_at', 'desc')
                ->limit(4)
                ->get();
        ?>
        <?php if($catArticles->isNotEmpty()): ?>
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="swn-section-title text-lg text-gray-900"><?php echo e($cat->name); ?></h2>
                <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>"
                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">더보기 →</a>
            </div>
            <div class="swn-card rounded-lg overflow-hidden">
                <?php $__currentLoopData = $catArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="swn-news-item flex items-center gap-4 <?php echo e($i === 0 ? 'py-4' : ''); ?>">
                    <div class="flex-1 min-w-0">
                        <a href="<?php echo e(route('news.show', $art->slug)); ?>">
                            <p class="swn-article-title <?php echo e($i === 0 ? 'text-base font-bold' : 'text-sm'); ?> leading-snug"><?php echo e($art->title); ?></p>
                        </a>
                        <?php if($i === 0 && $art->excerpt): ?>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?php echo e(Str::limit($art->excerpt, 80)); ?></p>
                        <?php endif; ?>
                        <div class="swn-article-meta mt-1">
                            <span><?php echo e($art->user->name ?? ''); ?></span>
                            <span><?php echo e($art->published_at?->format('m.d H:i')); ?></span>
                            <span>조회 <?php echo e(number_format($art->view_count)); ?></span>
                        </div>
                    </div>
                    <?php if($art->thumbnail): ?>
                    <a href="<?php echo e(route('news.show', $art->slug)); ?>" class="flex-shrink-0">
                        <img src="<?php echo e($art->thumbnail); ?>" alt="<?php echo e($art->title); ?>"
                             style="width:<?php echo e($i === 0 ? '100px' : '72px'); ?>;height:<?php echo e($i === 0 ? '72px' : '52px'); ?>;object-fit:cover;border-radius:4px;">
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        
        <?php if($widget === 'latest_articles'): ?>
        <?php $latestArts = Article::with(['category','user'])->where('status','published')->orderBy('published_at','desc')->limit(10)->get(); ?>
        <?php if($latestArts->isNotEmpty()): ?>
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="swn-section-title text-lg text-gray-900">최신 기사</h2>
                <a href="<?php echo e(route('news.index')); ?>" class="text-xs text-blue-600 hover:text-blue-800 font-medium">더보기 →</a>
            </div>
            <div class="swn-card rounded-lg">
                <?php $__currentLoopData = $latestArts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="swn-news-item px-5 flex items-start gap-3">
                    <?php if($art->category): ?>
                    <span class="swn-badge swn-badge-category flex-shrink-0 mt-0.5"><?php echo e($art->category->name); ?></span>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <a href="<?php echo e(route('news.show', $art->slug)); ?>" class="swn-article-title text-sm block truncate"><?php echo e($art->title); ?></a>
                        <div class="swn-article-meta mt-0.5">
                            <span><?php echo e($art->user->name ?? ''); ?></span>
                            <span><?php echo e($art->published_at?->format('m.d H:i')); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        
        <?php if($widget === 'board_sections' && $boards->isNotEmpty()): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <?php $__currentLoopData = $boards->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $board): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $boardPosts = $board->posts()->with('user')->orderBy('created_at','desc')->limit(5)->get(); ?>
            <div class="swn-card rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-sm text-gray-900"><?php echo e($board->board_name); ?></h3>
                    <a href="<?php echo e(route('bbs.index', $board->board_id)); ?>" class="text-xs text-blue-600 hover:text-blue-800 font-medium">더보기 →</a>
                </div>
                <div class="px-5">
                    <?php $__empty_1 = true; $__currentLoopData = $boardPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="swn-news-item flex items-center justify-between gap-3">
                        <a href="<?php echo e(route('bbs.show', [$board->board_id, $post->id])); ?>"
                           class="swn-article-title text-sm truncate flex-1">
                            <?php if($post->is_notice): ?><span class="text-red-500 font-bold mr-1">[공지]</span><?php endif; ?>
                            <?php echo e($post->title); ?>

                        </a>
                        <span class="swn-datetime flex-shrink-0"><?php echo e($post->created_at->format('m.d')); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="py-8 text-center text-gray-400 text-xs">게시글이 없습니다.</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'stats'): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900"><?php echo e(number_format(Article::where('status','published')->count())); ?></p>
                <p class="text-xs text-gray-500 mt-1 font-medium">기사</p>
            </div>
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900"><?php echo e(number_format(Post::count())); ?></p>
                <p class="text-xs text-gray-500 mt-1 font-medium">게시글</p>
            </div>
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900"><?php echo e(number_format(\App\Models\Comment::count())); ?></p>
                <p class="text-xs text-gray-500 mt-1 font-medium">댓글</p>
            </div>
            <div class="swn-stat-card rounded-lg p-4 text-center">
                <p class="text-2xl font-black text-gray-900"><?php echo e(number_format(\App\Models\User::count())); ?></p>
                <p class="text-xs text-gray-500 mt-1 font-medium">회원</p>
            </div>
        </div>
        <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <aside class="w-full lg:w-72 flex-shrink-0">
        <?php $__currentLoopData = $sidebarWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($widget === 'login'): ?>
        <div class="swn-card rounded-lg p-5 mb-5">
            <?php if(auth()->guard()->check()): ?>
            <div class="text-center">
                <div class="w-14 h-14 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                    <span class="text-xl font-bold text-blue-600"><?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?></span>
                </div>
                <p class="font-bold text-gray-900 text-sm"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-xs text-gray-400 mt-1"><?php echo e(auth()->user()->email); ?></p>
            </div>
            <?php else: ?>
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900 mb-1">환영합니다!</p>
                <p class="text-xs text-gray-400 mb-4">로그인하고 커뮤니티에 참여하세요</p>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('login')); ?>" class="flex-1 text-center py-2 bg-blue-600 text-white rounded text-xs font-bold hover:bg-blue-700 transition">로그인</a>
                    <a href="<?php echo e(route('register')); ?>" class="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded text-xs font-bold hover:bg-gray-200 transition">회원가입</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'notice' && $noticePosts->isNotEmpty()): ?>
        <div class="swn-card rounded-lg overflow-hidden mb-5">
            <div class="px-4 py-3 bg-red-50 border-b border-red-100">
                <h3 class="font-bold text-sm text-red-700">공지사항</h3>
            </div>
            <div class="px-4">
                <?php $__currentLoopData = $noticePosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="swn-news-item flex items-center justify-between">
                    <a href="<?php echo e(route('bbs.show', [$notice->board->board_id ?? 'free', $notice->id])); ?>"
                       class="swn-article-title text-xs truncate flex-1"><?php echo e($notice->title); ?></a>
                    <span class="swn-datetime flex-shrink-0 ml-2"><?php echo e($notice->created_at->format('m.d')); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'popular_articles' && $popularArticles->isNotEmpty()): ?>
        <div class="swn-card rounded-lg overflow-hidden mb-5">
            <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                <h3 class="font-bold text-sm text-blue-700">인기 기사</h3>
            </div>
            <div class="px-4">
                <?php $__currentLoopData = $popularArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="swn-news-item flex items-start gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center text-xs font-black
                                 <?php echo e($i < 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500'); ?>"><?php echo e($i + 1); ?></span>
                    <div class="flex-1 min-w-0">
                        <a href="<?php echo e(route('news.show', $art->slug)); ?>" class="swn-article-title text-xs block truncate"><?php echo e($art->title); ?></a>
                        <span class="swn-datetime">조회 <?php echo e(number_format($art->view_count)); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'popular_posts' && $popularPosts->isNotEmpty()): ?>
        <div class="swn-card rounded-lg overflow-hidden mb-5">
            <div class="px-4 py-3 bg-purple-50 border-b border-purple-100">
                <h3 class="font-bold text-sm text-purple-700">인기 게시글</h3>
            </div>
            <div class="px-4">
                <?php $__currentLoopData = $popularPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="swn-news-item flex items-start gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center text-xs font-black
                                 <?php echo e($i < 3 ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-500'); ?>"><?php echo e($i + 1); ?></span>
                    <div class="flex-1 min-w-0">
                        <a href="<?php echo e(route('bbs.show', [$pop->board->board_id ?? 'free', $pop->id])); ?>"
                           class="swn-article-title text-xs block truncate"><?php echo e($pop->title); ?></a>
                        <span class="swn-datetime">조회 <?php echo e(number_format($pop->view_count)); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'boards' && $boards->isNotEmpty()): ?>
        <div class="swn-card rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="font-bold text-sm text-gray-700">게시판 목록</h3>
            </div>
            <div class="p-4">
                <?php $__currentLoopData = $boards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $board): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('bbs.index', $board->board_id)); ?>"
                   class="flex items-center justify-between py-2 px-2 rounded hover:bg-gray-50 transition text-sm group">
                    <span class="text-gray-700 group-hover:text-blue-600 font-medium"><?php echo e($board->board_name); ?></span>
                    <span class="text-xs text-gray-400"><?php echo e($board->posts->count()); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </aside>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skin.layout.swn-style.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/skin/layout/swn-style/home.blade.php ENDPATH**/ ?>