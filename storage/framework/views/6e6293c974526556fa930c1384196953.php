<?php $__env->startSection('content'); ?>
<?php
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
?>

<div class="flex flex-col lg:flex-row gap-8">

    
    <div class="flex-1 min-w-0">
        <?php $__currentLoopData = $mainWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($widget === 'hero_articles' && $heroArticle): ?>
            <div class="mb-10">
                <div class="cobalt-card p-0 overflow-hidden group">
                    <div class="cobalt-card-glow"></div>
                    <?php if($heroArticle->thumbnail): ?>
                        <div class="relative">
                            <img src="<?php echo e($heroArticle->thumbnail); ?>" alt="<?php echo e($heroArticle->title); ?>" class="w-full h-[350px] object-cover transition duration-700 group-hover:scale-105">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                <?php if($heroArticle->category): ?>
                                    <span class="inline-block px-3 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-full mb-3 shadow-lg shadow-blue-600/30 uppercase tracking-widest">
                                        <?php echo e($heroArticle->category->name); ?>

                                    </span>
                                <?php endif; ?>
                                <a href="<?php echo e(route('news.show', $heroArticle->slug)); ?>">
                                    <h2 class="text-3xl md:text-4xl font-black text-white leading-tight hover:text-blue-400 transition cursor-pointer">
                                        <?php echo e($heroArticle->title); ?>

                                    </h2>
                                </a>
                                <div class="flex items-center gap-3 mt-4 text-white/50 text-sm">
                                    <span class="font-bold text-white/80"><?php echo e($heroArticle->user->name ?? 'Admin'); ?></span>
                                    <span class="w-1 h-1 bg-white/20 rounded-full"></span>
                                    <span><?php echo e($heroArticle->published_at?->format('Y.m.d H:i')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-8">
                            
                            <h2 class="text-3xl font-black text-white mb-4"><?php echo e($heroArticle->title); ?></h2>
                            <p class="text-white/60 leading-relaxed mb-6"><?php echo e(Str::limit($heroArticle->excerpt, 150)); ?></p>
                            <a href="<?php echo e(route('news.show', $heroArticle->slug)); ?>" class="text-blue-500 font-bold hover:underline">Read More →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($widget === 'category_articles'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $catArticles = Article::with('user')->where('status', 'published')->where('category_id', $cat->id)
                            ->orderBy('published_at', 'desc')->limit(4)->get();
                    ?>
                    <?php if($catArticles->isNotEmpty()): ?>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between px-1">
                            <h2 class="text-sm font-black text-blue-500 uppercase tracking-widest"><?php echo e($cat->name); ?></h2>
                            <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>" class="text-[10px] text-white/30 hover:text-white transition">VIEW ALL</a>
                        </div>
                        <div class="cobalt-card p-5 space-y-4">
                            <?php $__currentLoopData = $catArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex gap-4 <?php echo e(!$loop->last ? 'pb-4 border-b border-white/5' : ''); ?>">
                                    <div class="flex-1">
                                        <a href="<?php echo e(route('news.show', $art->slug)); ?>" class="text-sm font-bold text-white hover:text-blue-400 line-clamp-2 leading-snug">
                                            <?php echo e($art->title); ?>

                                        </a>
                                        <div class="flex items-center gap-2 mt-2 text-[10px] text-white/40">
                                            <span><?php echo e($art->published_at?->format('m.d')); ?></span>
                                            <span class="w-0.5 h-0.5 bg-white/10"></span>
                                            <span>조회 <?php echo e(number_format($art->view_count)); ?></span>
                                        </div>
                                    </div>
                                    <?php if($art->thumbnail): ?>
                                        <img src="<?php echo e($art->thumbnail); ?>" class="w-16 h-12 object-cover rounded bg-gray-800">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <?php if($widget === 'board_sections'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <?php $__currentLoopData = $boards->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $board): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $boardPosts = $board->posts()->orderBy('created_at','desc')->limit(5)->get(); ?>
                <div class="cobalt-card p-0 overflow-hidden">
                    <div class="bg-blue-500/5 px-5 py-3 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-xs font-black text-white tracking-widest uppercase"><?php echo e($board->board_name); ?></h3>
                        <a href="<?php echo e(route('bbs.index', $board->board_id)); ?>" class="text-[10px] text-blue-500 font-bold hover:text-blue-400">MORE</a>
                    </div>
                    <div class="p-4 space-y-2.5">
                        <?php $__empty_1 = true; $__currentLoopData = $boardPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex justify-between items-center text-sm group">
                                <a href="<?php echo e(route('bbs.show', [$board->board_id, $post->id])); ?>" class="text-white/70 group-hover:text-blue-400 truncate pr-4">
                                    <?php if($post->is_notice): ?><span class="text-blue-500 font-bold">[공지]</span><?php endif; ?>
                                    <?php echo e($post->title); ?>

                                </a>
                                <span class="text-[10px] text-white/20 flex-shrink-0"><?php echo e($post->created_at->format('m.d')); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="py-10 text-center text-white/20 text-xs italic">No posts found.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <?php if($widget === 'stats'): ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                <?php 
                    $stats = [
                        ['label' => 'Articles', 'value' => Article::where('status','published')->count()],
                        ['label' => 'Posts',    'value' => Post::count()],
                        ['label' => 'Comments', 'value' => \App\Models\Comment::count()],
                        ['label' => 'Members',  'value' => \App\Models\User::count()],
                    ];
                ?>
                <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cobalt-card p-6 text-center group">
                    <div class="cobalt-card-glow"></div>
                    <p class="text-3xl font-black text-white group-hover:text-blue-400 transition"><?php echo e(number_format($stat['value'])); ?></p>
                    <p class="text-[10px] font-bold text-white/30 uppercase tracking-[0.2em] mt-2"><?php echo e($stat['label']); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <aside class="w-full lg:w-80 flex-shrink-0 space-y-6">
        <?php $__currentLoopData = $sidebarWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($widget === 'login'): ?>
            <div class="cobalt-card p-6 border-blue-500/10 shadow-lg shadow-blue-500/5">
                <?php if(auth()->guard()->check()): ?>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-xl font-black text-white shadow-lg shadow-blue-600/30">
                        <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-black text-white truncate"><?php echo e(auth()->user()->name); ?></p>
                        <p class="text-[10px] text-white/40 truncate"><?php echo e(auth()->user()->email); ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-6">
                    <a href="<?php echo e(route('profile.show')); ?>" class="py-2 text-center text-[10px] font-bold bg-white/5 text-white/60 rounded-lg hover:bg-white/10 transition">MY PAGE</a>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full py-2 text-center text-[10px] font-bold bg-blue-600/10 text-blue-500 rounded-lg hover:bg-blue-600 hover:text-white transition">LOGOUT</button>
                    </form>
                </div>
                <?php else: ?>
                <div class="text-center">
                    <h3 class="font-black text-white mb-1 uppercase tracking-tighter">Welcome Back</h3>
                    <p class="text-[10px] text-white/40 mb-6 uppercase tracking-widest">Join our cobalt community</p>
                    <a href="<?php echo e(route('login')); ?>" class="block w-full py-3 bg-blue-600 text-white text-xs font-black rounded-xl hover:bg-blue-500 transition shadow-lg shadow-blue-600/20 mb-3 uppercase">Sign In</a>
                    <a href="<?php echo e(route('register')); ?>" class="block w-full py-3 bg-white/5 text-white/60 text-xs font-black rounded-xl hover:bg-white/10 transition uppercase">Create Account</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <?php if($widget === 'notice' && $noticePosts->isNotEmpty()): ?>
            <div class="cobalt-card p-0 overflow-hidden border-blue-500/20">
                <div class="bg-blue-600 px-5 py-3 flex items-center gap-2">
                    <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">Notice</h3>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__currentLoopData = $noticePosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('bbs.show', [$notice->board->board_id ?? 'free', $notice->id])); ?>" class="flex justify-between items-center group">
                            <span class="text-xs text-white/70 group-hover:text-white truncate pr-4"><?php echo e($notice->title); ?></span>
                            <span class="text-[10px] text-white/20"><?php echo e($notice->created_at->format('m.d')); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($widget === 'popular_articles' && $popularArticles->isNotEmpty()): ?>
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] px-1">Popular Articles</h3>
                <div class="cobalt-card p-5 space-y-5">
                    <?php $__currentLoopData = $popularArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start gap-4 group">
                            <span class="text-xl font-black <?php echo e($i < 3 ? 'text-blue-500' : 'text-white/10'); ?> italic leading-none"><?php echo e($i + 1); ?></span>
                            <div class="flex-1 min-w-0">
                                <a href="<?php echo e(route('news.show', $art->slug)); ?>" class="text-xs font-bold text-white/80 group-hover:text-blue-400 line-clamp-2 leading-snug">
                                    <?php echo e($art->title); ?>

                                </a>
                                <p class="text-[9px] text-white/20 mt-1 uppercase tracking-widest">Views <?php echo e(number_format($art->view_count)); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($widget === 'boards' && $boards->isNotEmpty()): ?>
            <div class="cobalt-card p-5">
                <h3 class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] mb-4">Board Index</h3>
                <div class="space-y-1">
                    <?php $__currentLoopData = $boards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $board): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('bbs.index', $board->board_id)); ?>" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-blue-600/10 group transition">
                            <span class="text-xs font-bold text-white/60 group-hover:text-blue-400"><?php echo e($board->board_name); ?></span>
                            <span class="text-[10px] text-white/10 group-hover:text-blue-500/50"><?php echo e(number_format($board->posts->count())); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </aside>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('skin.layout.cobalt-glow.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/skin/layout/cobalt-glow/home.blade.php ENDPATH**/ ?>