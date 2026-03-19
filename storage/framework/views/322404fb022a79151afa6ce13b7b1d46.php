<?php
    try {
        $layoutSkin = \App\Models\Setting::get('layout_skin', 'basic');
        if (!view()->exists("skin.layout.{$layoutSkin}.main")) $layoutSkin = 'basic';
    } catch (\Exception $e) { $layoutSkin = 'basic'; }
    $isNyt = $layoutSkin === 'newyorktimes-style';
?>


<?php $__env->startSection('title', isset($searchQuery) && $searchQuery !== '' ? ' - 검색: ' . $searchQuery : ($currentCategory ? ' - ' . $currentCategory->name : ' - 뉴스')); ?>

<?php $__env->startSection('content'); ?>

<?php if($isNyt): ?>

<div class="nyt-container" style="padding-top:16px;">

    
    <?php if(isset($searchQuery) && $searchQuery !== ''): ?>
    <div class="nyt-section-header" style="display:block;">
        <p style="font-family:var(--nyt-sans);font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--nyt-gray-mid);margin-bottom:6px;">검색 결과</p>
        <h1 style="font-size:clamp(1.4rem,4vw,2rem);">"<?php echo e($searchQuery); ?>"</h1>
        <p style="font-family:var(--nyt-sans);font-size:13px;color:var(--nyt-gray-mid);margin-top:4px;">
            <?php echo e($articles->total()); ?>건의 기사
            <a href="<?php echo e(route('news.index')); ?>" style="margin-left:12px;color:var(--nyt-gray-mid);text-decoration:underline;">검색 초기화</a>
        </p>
    </div>
    <?php else: ?>
    <div class="nyt-section-header">
        <h1><?php echo e($currentCategory ? $currentCategory->name : '전체 기사'); ?></h1>
        <?php if($currentCategory): ?>
        <a href="<?php echo e(route('news.index')); ?>" class="more">전체 보기 →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <?php if((!isset($searchQuery) || $searchQuery === '') && $categories->count()): ?>
    <div style="display:flex;gap:0;overflow-x:auto;border-bottom:1px solid #e2e2e2;margin-bottom:16px;scrollbar-width:none;">
        <a href="<?php echo e(route('news.index')); ?>"
           style="flex-shrink:0;padding:8px 16px;font-family:var(--nyt-sans);font-size:13px;font-weight:600;border-bottom:<?php echo e(!$currentCategory ? '3px solid #121212' : '3px solid transparent'); ?>;color:<?php echo e(!$currentCategory ? '#121212' : '#666'); ?>;white-space:nowrap;text-decoration:none;">
            전체
        </a>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>"
           style="flex-shrink:0;padding:8px 16px;font-family:var(--nyt-sans);font-size:13px;font-weight:600;border-bottom:<?php echo e($currentCategory?->id === $cat->id ? '3px solid #121212' : '3px solid transparent'); ?>;color:<?php echo e($currentCategory?->id === $cat->id ? '#121212' : '#666'); ?>;white-space:nowrap;text-decoration:none;">
            <?php echo e($cat->name); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <?php if($articles->isEmpty()): ?>
    <div style="text-align:center;padding:80px 0;color:#999;">
        <?php if(isset($searchQuery) && $searchQuery !== ''): ?>
        <p style="font-family:var(--nyt-serif);font-size:1.2rem;">"<?php echo e($searchQuery); ?>"에 대한 검색 결과가 없습니다.</p>
        <a href="<?php echo e(route('news.index')); ?>" style="font-family:var(--nyt-sans);font-size:13px;color:#666;text-decoration:underline;">전체 기사 보기</a>
        <?php else: ?>
        <p style="font-family:var(--nyt-serif);font-size:1.2rem;">등록된 기사가 없습니다.</p>
        <?php endif; ?>
    </div>
    <?php else: ?>

    <div style="display:grid;grid-template-columns:1fr 280px;gap:0 32px;">

        
        <div>
            
            <?php if($featuredArticles->count() >= 1): ?>
            <?php $hero = $featuredArticles->get(0); $subs = $featuredArticles->slice(1, 2)->values(); ?>
            <div style="border-top:3px solid #121212;padding:14px 0 16px;<?php echo e($subs->count() ? 'display:grid;grid-template-columns:2fr 1px 1fr;gap:0;' : ''); ?>border-bottom:1px solid #e2e2e2;margin-bottom:0;">

                
                <div style="padding-right:<?php echo e($subs->count() ? '20px' : '0'); ?>;">
                    <?php if($hero->thumbnail): ?>
                    <a href="<?php echo e(route('news.show', $hero->slug)); ?>" style="display:block;margin-bottom:12px;">
                        <img src="<?php echo e($hero->thumbnail); ?>" alt="<?php echo e($hero->title); ?>"
                             style="width:100%;height:280px;object-fit:cover;">
                    </a>
                    <?php endif; ?>
                    <?php if($hero->category): ?>
                    <span class="nyt-section-label"><?php echo e($hero->category->name); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('news.show', $hero->slug)); ?>">
                        <h2 class="nyt-headline nyt-headline-xl" style="margin-bottom:8px;"><?php echo e($hero->title); ?></h2>
                    </a>
                    <?php if($hero->excerpt): ?>
                    <p class="nyt-summary"><?php echo e(Str::limit($hero->excerpt, 160)); ?></p>
                    <?php endif; ?>
                    <p class="nyt-byline"><?php echo e($hero->user->name ?? ''); ?> · <?php echo e($hero->published_at?->format('Y.m.d')); ?></p>
                </div>

                <?php if($subs->count()): ?>
                <div style="background:#e2e2e2;margin:0;"></div>
                <div style="padding-left:20px;">
                    <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="<?php echo e(!$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:12px;margin-top:12px;' : ''); ?>">
                        <?php if($sub->thumbnail): ?>
                        <a href="<?php echo e(route('news.show', $sub->slug)); ?>" style="display:block;margin-bottom:8px;">
                            <img src="<?php echo e($sub->thumbnail); ?>" alt="<?php echo e($sub->title); ?>"
                                 style="width:100%;height:120px;object-fit:cover;">
                        </a>
                        <?php endif; ?>
                        <?php if($sub->category): ?>
                        <span class="nyt-section-label"><?php echo e($sub->category->name); ?></span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('news.show', $sub->slug)); ?>">
                            <h3 class="nyt-headline nyt-headline-sm"><?php echo e($sub->title); ?></h3>
                        </a>
                        <p class="nyt-byline"><?php echo e($sub->user->name ?? ''); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <?php $featuredIds = isset($featuredArticles) ? $featuredArticles->pluck('id')->all() : []; ?>
            <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!in_array($article->id, $featuredIds)): ?>
            <div style="display:flex;gap:16px;align-items:flex-start;border-bottom:1px solid #e2e2e2;padding:14px 0;">
                <div style="flex:1;min-width:0;">
                    <?php if($article->category): ?>
                    <span class="nyt-section-label"><?php echo e($article->category->name); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('news.show', $article->slug)); ?>">
                        <h3 class="nyt-headline nyt-headline-sm" style="margin-bottom:5px;"><?php echo e($article->title); ?></h3>
                    </a>
                    <?php if($article->excerpt): ?>
                    <p class="nyt-summary" style="font-size:.8125rem;"><?php echo e(Str::limit($article->excerpt, 100)); ?></p>
                    <?php endif; ?>
                    <p class="nyt-byline"><?php echo e($article->user->name ?? ''); ?> · <?php echo e($article->published_at?->format('Y.m.d')); ?></p>
                </div>
                <?php if($article->thumbnail): ?>
                <a href="<?php echo e(route('news.show', $article->slug)); ?>" style="flex-shrink:0;">
                    <img src="<?php echo e($article->thumbnail); ?>" alt="<?php echo e($article->title); ?>"
                         style="width:120px;height:86px;object-fit:cover;">
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($articles->hasPages()): ?>
            <div style="padding:20px 0;border-top:1px solid #e2e2e2;">
                <?php echo e($articles->links()); ?>

            </div>
            <?php endif; ?>
        </div>

        
        <aside>
            <?php
                $popular = \App\Models\Article::with('user')->where('status','published')->orderBy('view_count','desc')->limit(6)->get();
                $recent  = \App\Models\Article::with('user')->where('status','published')->orderBy('published_at','desc')->limit(5)->get();
            ?>

            <?php if($popular->count()): ?>
            <div style="border-top:3px solid #121212;padding-top:10px;margin-bottom:24px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 14px;">많이 본 기사</p>
                <?php $__currentLoopData = $popular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="<?php echo e($i>0 ? 'border-top:1px solid #e2e2e2;padding-top:10px;margin-top:10px;' : ''); ?>display:flex;gap:10px;">
                    <span style="font-family:var(--nyt-serif);font-size:1.3rem;font-weight:700;color:#ddd;line-height:1;flex-shrink:0;width:18px;"><?php echo e($i+1); ?></span>
                    <div>
                        <?php if($a->category): ?><span class="nyt-section-label" style="font-size:10px;"><?php echo e($a->category->name); ?></span><?php endif; ?>
                        <a href="<?php echo e(route('news.show', $a->slug)); ?>">
                            <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.35;color:#121212;"><?php echo e($a->title); ?></p>
                        </a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php if($recent->count()): ?>
            <div style="border-top:3px solid #121212;padding-top:10px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 12px;">최신 기사</p>
                <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="<?php echo e(!$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:10px;margin-top:10px;' : ''); ?>display:flex;gap:10px;align-items:flex-start;">
                    <?php if($a->thumbnail): ?>
                    <a href="<?php echo e(route('news.show', $a->slug)); ?>" style="flex-shrink:0;">
                        <img src="<?php echo e($a->thumbnail); ?>" alt="<?php echo e($a->title); ?>"
                             style="width:64px;height:46px;object-fit:cover;">
                    </a>
                    <?php endif; ?>
                    <div style="flex:1;min-width:0;">
                        <a href="<?php echo e(route('news.show', $a->slug)); ?>">
                            <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.35;color:#121212;"><?php echo e($a->title); ?></p>
                        </a>
                        <p class="nyt-byline" style="margin-top:3px;"><?php echo e($a->published_at?->format('m.d')); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </aside>
    </div>
    <?php endif; ?>
</div>

<?php else: ?>



<?php if($categories->count()): ?>
<div class="mb-5 <?php echo e($currentCategory ? '' : '-mt-2'); ?> border-b border-gray-200">
    <div class="flex items-center gap-0 overflow-x-auto">
        <a href="<?php echo e(route('news.index')); ?>"
           class="flex-shrink-0 px-4 py-2.5 text-sm font-bold border-b-2 transition whitespace-nowrap
                  <?php echo e(!$currentCategory ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-800'); ?>">
            전체
        </a>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>"
           class="flex-shrink-0 px-4 py-2.5 text-sm font-bold border-b-2 transition whitespace-nowrap
                  <?php echo e($currentCategory?->id === $cat->id ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-800'); ?>">
            <?php echo e($cat->name); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<?php if($articles->isEmpty()): ?>
<div class="text-center py-20 text-gray-400">
    <p class="font-medium">등록된 기사가 없습니다.</p>
</div>
<?php else: ?>

<div class="flex flex-col lg:flex-row gap-6">
    <div class="flex-1 min-w-0">
        <?php if($featuredArticles->count() >= 2): ?>
        <?php $hero = $featuredArticles->get(0); $subs = $featuredArticles->slice(1, 2)->values(); ?>
        <div class="grid grid-cols-3 gap-3 mb-6">
            <a href="<?php echo e(route('news.show', $hero->slug)); ?>" class="col-span-2 relative rounded-lg overflow-hidden group block" style="height:224px;">
                <img src="<?php echo e($hero->thumbnail); ?>" alt="<?php echo e($hero->title); ?>" style="width:100%;height:100%;object-fit:cover;" class="group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <?php if($hero->category): ?><span class="inline-block px-2 py-0.5 text-xs font-bold bg-blue-600 text-white rounded mb-2"><?php echo e($hero->category->name); ?></span><?php endif; ?>
                    <h2 class="text-white font-bold text-base leading-snug line-clamp-2 mb-1"><?php echo e($hero->title); ?></h2>
                    <p class="text-white/70 text-xs"><?php echo e($hero->user->name); ?> · <?php echo e($hero->published_at?->format('Y.m.d')); ?></p>
                </div>
            </a>
            <div class="col-span-1 flex flex-col gap-3" style="height:224px;">
                <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('news.show', $sub->slug)); ?>" class="relative rounded-lg overflow-hidden group block" style="flex:1;min-height:0;">
                    <img src="<?php echo e($sub->thumbnail); ?>" alt="<?php echo e($sub->title); ?>" style="width:100%;height:100%;object-fit:cover;" class="group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        <?php if($sub->category): ?><span class="inline-block px-1.5 py-0.5 text-xs font-bold bg-blue-600 text-white rounded mb-1"><?php echo e($sub->category->name); ?></span><?php endif; ?>
                        <h3 class="text-white font-bold text-xs leading-snug line-clamp-2"><?php echo e($sub->title); ?></h3>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php $featuredIds = isset($featuredArticles) ? $featuredArticles->pluck('id')->all() : []; ?>
        <div class="bg-white rounded-lg overflow-hidden mb-6 border border-gray-100">
            <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!in_array($article->id, $featuredIds)): ?>
            <a href="<?php echo e(route('news.show', $article->slug)); ?>" class="group flex items-start gap-4 px-4 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                <?php if($article->thumbnail): ?>
                <div class="flex-shrink-0 relative" style="width:140px;height:100px;">
                    <img src="<?php echo e($article->thumbnail); ?>" alt="<?php echo e($article->title); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
                    <?php if($article->category): ?><span class="absolute bottom-1.5 left-1.5 px-1.5 py-0.5 text-white rounded" style="font-size:10px;font-weight:700;background:rgba(37,99,235,0.9);"><?php echo e($article->category->name); ?></span><?php endif; ?>
                </div>
                <?php else: ?>
                <div class="flex-shrink-0 bg-gray-100 rounded flex items-center justify-center" style="width:140px;height:100px;"></div>
                <?php endif; ?>
                <div class="flex-1 min-w-0 py-0.5">
                    <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-blue-700 mb-2"><?php echo e($article->title); ?></h3>
                    <?php $preview = $article->excerpt ?: Str::limit(strip_tags($article->content), 100); ?>
                    <?php if($preview): ?><p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-2"><?php echo e($preview); ?></p><?php endif; ?>
                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <span class="font-medium text-gray-600"><?php echo e($article->user->name); ?></span>
                        <span>·</span><span><?php echo e($article->published_at?->format('Y.m.d')); ?></span>
                        <span>·</span><span>조회 <?php echo e(number_format($article->view_count)); ?></span>
                    </div>
                </div>
            </a>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($articles->hasPages()): ?><div class="mt-4"><?php echo e($articles->links()); ?></div><?php endif; ?>
    </div>

    <aside class="w-full lg:w-64 flex-shrink-0">
        <?php $popular = \App\Models\Article::with('user')->where('status','published')->orderBy('view_count','desc')->limit(5)->get(); ?>
        <?php if($popular->count()): ?>
        <div class="bg-white rounded-lg border border-gray-100 overflow-hidden mb-4">
            <div class="px-4 py-2.5 bg-blue-50 border-b border-blue-100"><h3 class="font-bold text-sm text-blue-700">인기기사</h3></div>
            <div class="px-3 py-1">
                <?php $__currentLoopData = $popular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('news.show', $a->slug)); ?>" class="flex items-start gap-2 py-2 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-3 px-3 transition group">
                    <?php if($a->thumbnail): ?>
                    <div class="flex-shrink-0 relative" style="width:64px;height:46px;">
                        <img src="<?php echo e($a->thumbnail); ?>" alt="<?php echo e($a->title); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 rounded flex items-center justify-center font-black <?php echo e($i < 3 ? 'bg-blue-600 text-white' : 'bg-gray-500 text-white'); ?>" style="font-size:9px;"><?php echo e($i + 1); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 line-clamp-2 leading-snug group-hover:text-blue-700"><?php echo e($a->title); ?></p>
                        <span class="text-xs text-gray-400 mt-0.5 block"><?php echo e($a->published_at?->format('Y.m.d')); ?></span>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </aside>
</div>
<?php endif; ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make("skin.layout.{$layoutSkin}.main", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/articles/list.blade.php ENDPATH**/ ?>