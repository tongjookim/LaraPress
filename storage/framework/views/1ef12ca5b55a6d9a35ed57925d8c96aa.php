<?php
    try {
        $layoutSkin = \App\Models\Setting::get('layout_skin', 'basic');
        if (!view()->exists("skin.layout.{$layoutSkin}.main")) $layoutSkin = 'basic';
    } catch (\Exception $e) { $layoutSkin = 'basic'; }
    $isNyt = $layoutSkin === 'newyorktimes-style';
?>


<?php $__env->startSection('title', $q !== '' ? ' - 검색: ' . $q : ' - 검색'); ?>

<?php $__env->startSection('content'); ?>

<?php if($isNyt): ?>
<style>
@media (max-width: 768px) {
    .nyt-search-results-grid { grid-template-columns: 1fr !important; }
    .nyt-search-sidebar { display: none; }
}
</style>
<div class="nyt-container" style="padding-top:24px;padding-bottom:60px;">

    
    <div style="border-top:3px solid var(--nyt-black);padding-top:16px;margin-bottom:24px;">
        <?php if($q !== ''): ?>
            <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--nyt-gray-mid);margin-bottom:8px;">검색 결과</p>
            <h1 style="font-family:var(--nyt-serif);font-size:clamp(1.6rem,4vw,2.4rem);font-weight:700;margin:0 0 8px;">"<?php echo e($q); ?>"</h1>
            <p style="font-family:var(--nyt-sans);font-size:13px;color:var(--nyt-gray-mid);">
                총 <?php echo e($articles->total()); ?>건
                &nbsp;·&nbsp;
                <a href="<?php echo e(route('news.index')); ?>" style="color:var(--nyt-gray-mid);text-decoration:underline;">전체 기사 보기</a>
            </p>
        <?php else: ?>
            <h1 style="font-family:var(--nyt-serif);font-size:clamp(1.6rem,4vw,2.4rem);font-weight:700;margin:0 0 8px;">검색</h1>
            <p style="font-family:var(--nyt-sans);font-size:13px;color:var(--nyt-gray-mid);">검색어를 입력하세요.</p>
        <?php endif; ?>
    </div>

    
    <form action="<?php echo e(route('news.search')); ?>" method="GET"
          style="display:flex;align-items:center;gap:8px;border-bottom:2px solid var(--nyt-black);padding-bottom:12px;margin-bottom:32px;">
        <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="기사 제목, 내용 검색..."
               style="flex:1;border:none;outline:none;font-family:var(--nyt-serif);font-size:1.1rem;color:var(--nyt-black);background:transparent;min-width:0;"
               autofocus>
        <button type="submit"
                style="font-family:var(--nyt-sans);font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;padding:7px 16px;background:var(--nyt-black);color:#fff;border:none;border-radius:2px;cursor:pointer;white-space:nowrap;flex-shrink:0;">
            검색
        </button>
    </form>

    <?php if($q !== ''): ?>
    <div class="nyt-search-results-grid" style="display:grid;grid-template-columns:1fr 280px;gap:0 40px;align-items:start;">

        
        <div>
            <?php if($articles->isEmpty()): ?>
            <div style="text-align:center;padding:60px 0;color:var(--nyt-gray-mid);">
                <p style="font-family:var(--nyt-serif);font-size:1.2rem;margin-bottom:12px;">"<?php echo e($q); ?>"에 대한 검색 결과가 없습니다.</p>
                <p style="font-family:var(--nyt-sans);font-size:13px;">다른 검색어를 시도해보세요.</p>
            </div>
            <?php else: ?>
            <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="display:flex;gap:20px;align-items:flex-start;border-bottom:1px solid var(--nyt-border);padding:18px 0;">
                <?php if($article->thumbnail): ?>
                <a href="<?php echo e(route('news.show', $article->slug)); ?>" style="flex-shrink:0;">
                    <img src="<?php echo e($article->thumbnail); ?>" alt="<?php echo e($article->title); ?>"
                         style="width:140px;height:96px;object-fit:cover;">
                </a>
                <?php endif; ?>
                <div style="flex:1;min-width:0;">
                    <?php if($article->category): ?>
                    <span class="nyt-section-label"><?php echo e($article->category->name); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('news.show', $article->slug)); ?>">
                        <h2 class="nyt-headline nyt-headline-md" style="margin-bottom:6px;"><?php echo e($article->title); ?></h2>
                    </a>
                    <?php if($article->subtitle): ?>
                    <p style="font-family:var(--nyt-serif);font-size:.9rem;color:var(--nyt-gray-dark);margin:0 0 6px;line-height:1.4;"><?php echo e($article->subtitle); ?></p>
                    <?php elseif($article->excerpt): ?>
                    <p class="nyt-summary" style="font-size:.875rem;"><?php echo e(Str::limit($article->excerpt, 120)); ?></p>
                    <?php endif; ?>
                    <p class="nyt-byline">
                        <?php echo e($article->user->name ?? ''); ?>

                        &nbsp;·&nbsp;
                        <?php echo e($article->published_at?->format('Y.m.d')); ?>

                    </p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($articles->hasPages()): ?>
            <div style="padding:24px 0;">
                <?php echo e($articles->links()); ?>

            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        
        <aside class="nyt-search-sidebar">
            <?php if($popular->count()): ?>
            <div style="border-top:3px solid var(--nyt-black);padding-top:10px;">
                <p style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;margin:0 0 14px;">많이 본 기사</p>
                <?php $__currentLoopData = $popular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="<?php echo e($i>0 ? 'border-top:1px solid var(--nyt-border);padding-top:10px;margin-top:10px;' : ''); ?>display:flex;gap:10px;">
                    <span style="font-family:var(--nyt-serif);font-size:1.3rem;font-weight:700;color:#ddd;line-height:1;flex-shrink:0;width:18px;"><?php echo e($i+1); ?></span>
                    <div>
                        <?php if($a->category): ?><span class="nyt-section-label" style="font-size:10px;"><?php echo e($a->category->name); ?></span><?php endif; ?>
                        <a href="<?php echo e(route('news.show', $a->slug)); ?>">
                            <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.35;color:var(--nyt-black);"><?php echo e($a->title); ?></p>
                        </a>
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

<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        <?php if($q !== ''): ?> "<?php echo e($q); ?>" 검색 결과 <?php else: ?> 검색 <?php endif; ?>
    </h1>

    <form action="<?php echo e(route('news.search')); ?>" method="GET" class="flex gap-2 mb-8">
        <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="검색어 입력..."
               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
               autofocus>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700">검색</button>
    </form>

    <?php if($q !== ''): ?>
        <?php if($articles->isEmpty()): ?>
        <p class="text-gray-500 text-center py-16">"<?php echo e($q); ?>"에 대한 검색 결과가 없습니다.</p>
        <?php else: ?>
        <p class="text-sm text-gray-500 mb-4">총 <?php echo e($articles->total()); ?>건</p>
        <div class="divide-y divide-gray-200">
            <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="py-4 flex gap-4">
                <?php if($article->thumbnail): ?>
                <a href="<?php echo e(route('news.show', $article->slug)); ?>" class="flex-shrink-0">
                    <img src="<?php echo e($article->thumbnail); ?>" alt="<?php echo e($article->title); ?>" class="w-24 h-16 object-cover rounded">
                </a>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <?php if($article->category): ?>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wide"><?php echo e($article->category->name); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('news.show', $article->slug)); ?>" class="block">
                        <h2 class="font-bold text-gray-900 mt-1 leading-snug"><?php echo e($article->title); ?></h2>
                    </a>
                    <?php if($article->excerpt): ?>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2"><?php echo e(Str::limit($article->excerpt, 100)); ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-400 mt-1"><?php echo e($article->user->name ?? ''); ?> · <?php echo e($article->published_at?->format('Y.m.d')); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($articles->hasPages()): ?>
        <div class="mt-6"><?php echo e($articles->links()); ?></div>
        <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make("skin.layout.{$layoutSkin}.main", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/articles/search.blade.php ENDPATH**/ ?>