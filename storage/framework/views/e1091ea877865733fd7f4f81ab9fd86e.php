<?php $__env->startSection('content'); ?>
<?php
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
?>

<div class="flex flex-col lg:flex-row gap-8">

    
    <div class="flex-1 min-w-0">

        <?php $__currentLoopData = $mainWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($widget === 'hero_articles' && $heroArticle): ?>
        <div class="mb-10">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <?php if($heroArticle->thumbnail): ?>
                
                <div style="position:relative;">
                    <img src="<?php echo e($heroArticle->thumbnail); ?>" alt="<?php echo e($heroArticle->title); ?>"
                         style="width:100%;height:300px;object-fit:cover;display:block;">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.75) 0%,rgba(0,0,0,.15) 50%,transparent 100%);"></div>
                    <div style="position:absolute;bottom:0;left:0;right:0;padding:28px 32px;">
                        <?php if($heroArticle->category): ?>
                        <span class="inline-block px-2 py-0.5 text-white text-xs font-bold rounded mb-2"
                              style="background:var(--site-primary);"><?php echo e($heroArticle->category->name); ?></span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('news.show', $heroArticle->slug)); ?>">
                            <h2 class="text-2xl font-bold text-white leading-tight hover:opacity-80 transition"><?php echo e($heroArticle->title); ?></h2>
                        </a>
                        <p class="text-sm text-gray-300 mt-2"><?php echo e($heroArticle->user->name ?? ''); ?> · <?php echo e($heroArticle->published_at?->format('Y.m.d')); ?></p>
                    </div>
                </div>
                <?php else: ?>
                
                <div class="p-7">
                    <?php if($heroArticle->category): ?>
                    <span class="inline-block px-2 py-0.5 text-xs font-bold rounded mb-3"
                          style="background:var(--site-primary-light);color:var(--site-primary);"><?php echo e($heroArticle->category->name); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('news.show', $heroArticle->slug)); ?>">
                        <h2 class="text-2xl font-bold text-gray-900 leading-tight mb-3 site-nav-link hover:underline transition"><?php echo e($heroArticle->title); ?></h2>
                    </a>
                    <?php if($heroArticle->excerpt): ?>
                    <p class="text-gray-500 text-sm leading-relaxed mb-3"><?php echo e(Str::limit($heroArticle->excerpt, 140)); ?></p>
                    <?php endif; ?>
                    <p class="text-gray-400 text-sm"><?php echo e($heroArticle->user->name ?? ''); ?> · <?php echo e($heroArticle->published_at?->format('Y.m.d')); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'category_articles'): ?>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $catArticles = Article::with('user')
                ->where('status','published')
                ->where('category_id', $cat->id)
                ->orderBy('published_at','desc')
                ->limit(4)
                ->get();
        ?>
        <?php if($catArticles->isNotEmpty()): ?>
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 rounded-full mr-3" style="background:var(--site-primary);display:inline-block;"></span>
                    <?php echo e($cat->name); ?>

                </h2>
                <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>"
                   class="text-sm site-primary-text hover:underline font-medium">더보기 →</a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-50">
                <?php $__currentLoopData = $catArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('news.show', $art->slug)); ?>"
                   class="group flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-<?php echo e($i === 0 ? 'bold' : 'medium'); ?> text-<?php echo e($i === 0 ? 'base' : 'sm'); ?> text-gray-900 group-hover:underline transition line-clamp-2 leading-snug site-nav-link">
                            <?php echo e($art->title); ?>

                        </h3>
                        <?php if($i === 0 && $art->excerpt): ?>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?php echo e(Str::limit($art->excerpt, 90)); ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-gray-400 mt-1.5">
                            <?php echo e($art->user->name ?? ''); ?> · <?php echo e($art->published_at?->format('m.d')); ?>

                        </p>
                    </div>
                    <?php if($art->thumbnail): ?>
                    <div style="flex-shrink:0;overflow:hidden;border-radius:8px;width:<?php echo e($i === 0 ? '96px' : '72px'); ?>;height:<?php echo e($i === 0 ? '68px' : '52px'); ?>;">
                        <img src="<?php echo e($art->thumbnail); ?>" alt="<?php echo e($art->title); ?>"
                             style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <?php endif; ?>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        
        <?php if($widget === 'latest_articles'): ?>
        <?php $latestArts = Article::with(['category','user'])->where('status','published')->orderBy('published_at','desc')->limit(8)->get(); ?>
        <?php if($latestArts->isNotEmpty()): ?>
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 rounded-full mr-3" style="background:var(--site-accent);display:inline-block;"></span>
                    최신 기사
                </h2>
                <a href="<?php echo e(route('news.index')); ?>" class="text-sm site-primary-text hover:underline">더보기 →</a>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-50">
                <?php $__currentLoopData = $latestArts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition">
                    <?php if($art->category): ?>
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-semibold rounded"
                          style="background:var(--site-primary-light);color:var(--site-primary);"><?php echo e($art->category->name); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('news.show', $art->slug)); ?>" class="flex-1 text-sm text-gray-800 site-nav-link font-medium truncate hover:underline"><?php echo e($art->title); ?></a>
                    <span class="flex-shrink-0 text-xs text-gray-400"><?php echo e($art->published_at?->format('m.d')); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        
        <?php if($widget === 'board_sections' && $boards->isNotEmpty()): ?>
        <div class="mb-10">
            <div class="flex items-center mb-4 border-b border-gray-100 pb-3">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="w-1.5 h-6 bg-green-500 rounded-full mr-3" style="display:inline-block;"></span>
                    게시판
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <?php $__currentLoopData = $boards->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $boardPosts = $brd->posts()->orderBy('created_at','desc')->limit(5)->get(); ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-sm text-gray-900"><?php echo e($brd->board_name); ?></h3>
                        <a href="<?php echo e(route('bbs.index', $brd->board_id)); ?>" class="text-xs site-primary-text hover:underline">더보기 →</a>
                    </div>
                    <div class="px-5">
                        <?php $__empty_1 = true; $__currentLoopData = $boardPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center gap-2 py-2 border-b border-gray-50 last:border-0">
                            <a href="<?php echo e(route('bbs.show', [$brd->board_id, $post->id])); ?>"
                               class="flex-1 text-sm text-gray-700 site-nav-link truncate hover:underline">
                                <?php if($post->is_notice): ?><span class="text-red-500 font-bold mr-1 text-xs">[공지]</span><?php endif; ?>
                                <?php echo e($post->title); ?>

                            </a>
                            <span class="flex-shrink-0 text-xs text-gray-400"><?php echo e($post->created_at->format('m.d')); ?></span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="py-6 text-center text-xs text-gray-400">게시글이 없습니다.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($widget === 'stats'): ?>
        <div class="grid grid-cols-3 gap-5 mb-10">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm">
                <div class="text-3xl font-black text-gray-900 mb-1"><?php echo e(number_format(\App\Models\User::count())); ?></div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Users</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm">
                <div class="text-3xl font-black text-gray-900 mb-1"><?php echo e(number_format(Post::count())); ?></div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Posts</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm">
                <div class="text-3xl font-black text-gray-900 mb-1"><?php echo e(number_format(\App\Models\Comment::count())); ?></div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Comments</p>
            </div>
        </div>
        <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <aside class="w-full lg:w-72 flex-shrink-0">
        <?php $__currentLoopData = $sidebarWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php if($widget === 'login'): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
            <?php if(auth()->guard()->check()): ?>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-3"
                     style="background:var(--site-primary-light);">
                    <span class="text-lg font-bold site-primary-text"><?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?></span>
                </div>
                <p class="font-bold text-gray-900 text-sm"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-xs text-gray-400 mt-0.5"><?php echo e(auth()->user()->email); ?></p>
            </div>
            <?php else: ?>
            <p class="text-sm font-bold text-gray-900 mb-1 text-center">환영합니다!</p>
            <p class="text-xs text-gray-500 mb-4 text-center">로그인하고 참여하세요</p>
            <div class="flex gap-2">
                <a href="<?php echo e(route('login')); ?>" class="flex-1 text-center py-2 rounded-lg text-xs font-bold text-white site-primary-btn">로그인</a>
                <a href="<?php echo e(route('register')); ?>" class="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold hover:bg-gray-200 transition">회원가입</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if($widget === 'notice' && $noticePosts->isNotEmpty()): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-gray-100 bg-red-50">
                <h3 class="font-bold text-sm text-red-700">공지사항</h3>
            </div>
            <div class="divide-y divide-gray-50">
                <?php $__currentLoopData = $noticePosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 transition">
                    <a href="<?php echo e(route('bbs.show', [$notice->board->board_id ?? 'free', $notice->id])); ?>"
                       class="flex-1 text-xs text-gray-700 site-nav-link truncate hover:underline"><?php echo e($notice->title); ?></a>
                    <span class="text-xs text-gray-400 flex-shrink-0"><?php echo e($notice->created_at->format('m.d')); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($widget === 'popular_articles' && $popularArticles->isNotEmpty()): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-gray-100" style="background:var(--site-primary-light);">
                <h3 class="font-bold text-sm site-primary-text">인기 기사</h3>
            </div>
            <div class="divide-y divide-gray-50">
                <?php $__currentLoopData = $popularArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 transition">
                    <span class="flex-shrink-0 w-4 h-4 rounded text-xs font-black flex items-center justify-center"
                          style="<?php echo e($i < 3 ? 'background:var(--site-primary);color:#fff;' : 'background:#f3f4f6;color:#6b7280;'); ?>"><?php echo e($i+1); ?></span>
                    <a href="<?php echo e(route('news.show', $art->slug)); ?>" class="flex-1 text-xs text-gray-700 site-nav-link truncate hover:underline"><?php echo e($art->title); ?></a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($widget === 'popular_posts' && $popularPosts->isNotEmpty()): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-gray-100" style="background:var(--site-primary-light);">
                <h3 class="font-bold text-sm site-primary-text">인기 게시글</h3>
            </div>
            <div class="divide-y divide-gray-50">
                <?php $__currentLoopData = $popularPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 transition">
                    <span class="flex-shrink-0 w-4 h-4 rounded text-xs font-black flex items-center justify-center"
                          style="<?php echo e($i < 3 ? 'background:var(--site-primary);color:#fff;' : 'background:#f3f4f6;color:#6b7280;'); ?>"><?php echo e($i+1); ?></span>
                    <a href="<?php echo e(route('bbs.show', [$pop->board->board_id ?? 'free', $pop->id])); ?>"
                       class="flex-1 text-xs text-gray-700 site-nav-link truncate hover:underline"><?php echo e($pop->title); ?></a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($widget === 'boards' && $boards->isNotEmpty()): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-sm text-gray-700">게시판 목록</h3>
            </div>
            <div class="p-3">
                <?php $__currentLoopData = $boards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('bbs.index', $brd->board_id)); ?>"
                   class="flex items-center justify-between py-2 px-2 rounded-lg hover:bg-gray-50 transition group">
                    <span class="text-sm text-gray-700 site-nav-link font-medium"><?php echo e($brd->board_name); ?></span>
                    <span class="text-xs text-gray-400"><?php echo e($brd->posts_count); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </aside>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skin.layout.basic.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/skin/layout/basic/home.blade.php ENDPATH**/ ?>