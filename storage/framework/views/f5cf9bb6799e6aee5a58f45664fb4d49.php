<?php $__env->startSection('content'); ?>
<?php
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
?>

<div class="nyt-container" style="padding-top:16px;">


<section class="nyt-section-block">
    <div class="nyt-home-hero">

        
        <?php if($hero): ?>
        <div style="padding:12px 20px 16px 0;">
            <?php if($hero->thumbnail): ?>
            <a href="<?php echo e(route('news.show', $hero->slug)); ?>" style="display:block;margin-bottom:12px;">
                <div style="overflow:hidden;">
                    <img src="<?php echo e($hero->thumbnail); ?>" alt="<?php echo e($hero->title); ?>"
                         style="width:100%;height:340px;object-fit:cover;">
                </div>
            </a>
            <?php endif; ?>
            <?php if($hero->category): ?>
            <span class="nyt-section-label"><?php echo e($hero->category->name); ?></span>
            <?php endif; ?>
            <a href="<?php echo e(route('news.show', $hero->slug)); ?>">
                <h2 class="nyt-headline nyt-headline-xl" style="margin-bottom:8px;"><?php echo e($hero->title); ?></h2>
            </a>
            <p class="nyt-summary" style="font-size:1rem;line-height:1.6;"><?php echo e($summary($hero, 160)); ?></p>
            <p class="nyt-byline"><?php echo e($hero->user->name ?? ''); ?>

                <?php if($hero->published_at): ?> · <?php echo e($hero->published_at->locale('ko')->diffForHumans()); ?><?php endif; ?>
            </p>
        </div>
        <?php endif; ?>

        
        <div class="nyt-vsep" style="margin:12px 0;"></div>

        
        <div style="padding:12px 0 16px 20px;">
            <?php $__currentLoopData = $top2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="<?php echo e(!$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:12px;margin-top:12px;' : ''); ?>">
                <?php if($art->category): ?>
                <span class="nyt-section-label"><?php echo e($art->category->name); ?></span>
                <?php endif; ?>
                <a href="<?php echo e(route('news.show', $art->slug)); ?>">
                    <h3 class="nyt-headline nyt-headline-md"><?php echo e($art->title); ?></h3>
                </a>
                <p class="nyt-summary" style="font-size:.8125rem;"><?php echo e($summary($art, 80)); ?></p>
                <p class="nyt-byline"><?php echo e($art->user->name ?? ''); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($popularArticles->isNotEmpty()): ?>
            <div style="border-top:3px solid #121212;margin-top:16px;padding-top:10px;">
                <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#333;margin:0 0 10px;">많이 본 기사</p>
                <?php $__currentLoopData = $popularArticles->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="<?php echo e($i > 0 ? 'border-top:1px solid #e2e2e2;padding-top:8px;margin-top:8px;' : ''); ?>display:flex;gap:10px;align-items:flex-start;">
                    <span style="font-family:var(--nyt-serif);font-size:1.4rem;font-weight:700;color:#ccc;line-height:1;flex-shrink:0;width:20px;"><?php echo e($i+1); ?></span>
                    <a href="<?php echo e(route('news.show', $pop->slug)); ?>">
                        <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.3;color:#121212;"><?php echo e($pop->title); ?></p>
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<?php if($top3->isNotEmpty()): ?>
<section class="nyt-section-block">
    <div class="nyt-grid-3" style="border-bottom:1px solid #e2e2e2;">
        <?php $__currentLoopData = $top3; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="nyt-col-divider" style="padding:14px <?php echo e($loop->last ? '0' : '16px'); ?> 16px <?php echo e($loop->first ? '0' : '16px'); ?>;">
            <?php if($art->thumbnail): ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>" style="display:block;margin-bottom:10px;">
                <img src="<?php echo e($art->thumbnail); ?>" alt="<?php echo e($art->title); ?>"
                     style="width:100%;height:160px;object-fit:cover;">
            </a>
            <?php endif; ?>
            <?php if($art->category): ?>
            <span class="nyt-section-label"><?php echo e($art->category->name); ?></span>
            <?php endif; ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>">
                <h3 class="nyt-headline nyt-headline-sm"><?php echo e($art->title); ?></h3>
            </a>
            <p class="nyt-summary" style="font-size:.8125rem;"><?php echo e($summary($art, 80)); ?></p>
            <p class="nyt-byline"><?php echo e($art->user->name ?? ''); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>


<?php $__currentLoopData = $categories->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catIdx => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $catArts = Article::with(['user'])
        ->where('status','published')
        ->where('category_id', $cat->id)
        ->orderBy('published_at','desc')
        ->limit(5)
        ->get();
?>
<?php if($catArts->isNotEmpty()): ?>
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2><?php echo e($cat->name); ?></h2>
        <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>" class="more">더보기 →</a>
    </div>

    <?php $catLead = $catArts->first(); $catRest = $catArts->slice(1); ?>
    <div class="nyt-home-cat">

        
        <div style="padding-right:20px;">
            <?php if($catLead->thumbnail): ?>
            <a href="<?php echo e(route('news.show', $catLead->slug)); ?>" style="display:block;margin-bottom:10px;">
                <img src="<?php echo e($catLead->thumbnail); ?>" alt="<?php echo e($catLead->title); ?>"
                     style="width:100%;height:200px;object-fit:cover;">
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('news.show', $catLead->slug)); ?>">
                <h3 class="nyt-headline nyt-headline-lg"><?php echo e($catLead->title); ?></h3>
            </a>
            <p class="nyt-summary"><?php echo e($summary($catLead, 120)); ?></p>
            <p class="nyt-byline"><?php echo e($catLead->user->name ?? ''); ?></p>
        </div>

        <div class="nyt-vsep"></div>

        
        <div style="padding-left:20px;">
            <?php $__currentLoopData = $catRest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="<?php echo e(!$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:10px;margin-top:10px;' : ''); ?>display:flex;gap:10px;align-items:flex-start;">
                <?php if($art->thumbnail): ?>
                <a href="<?php echo e(route('news.show', $art->slug)); ?>" style="flex-shrink:0;">
                    <img src="<?php echo e($art->thumbnail); ?>" alt="<?php echo e($art->title); ?>"
                         style="width:72px;height:52px;object-fit:cover;">
                </a>
                <?php endif; ?>
                <div>
                    <a href="<?php echo e(route('news.show', $art->slug)); ?>">
                        <p style="font-family:var(--nyt-serif);font-size:.875rem;font-weight:700;margin:0 0 3px;line-height:1.3;color:#121212;"><?php echo e($art->title); ?></p>
                    </a>
                    <p class="nyt-byline"><?php echo e($art->user->name ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if($catIdx === 1 && $mid6->isNotEmpty()): ?>
<section class="nyt-section-block">
    <div class="nyt-grid-6" style="border-bottom:1px solid #e2e2e2;padding-bottom:16px;">
        <?php $__currentLoopData = $mid6; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="nyt-col-divider" style="padding:14px <?php echo e($loop->last ? '0' : '10px'); ?> 0 <?php echo e($loop->first ? '0' : '10px'); ?>;">
            <?php if($art->thumbnail): ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>" style="display:block;margin-bottom:8px;">
                <img src="<?php echo e($art->thumbnail); ?>" alt="<?php echo e($art->title); ?>"
                     style="width:100%;height:90px;object-fit:cover;">
            </a>
            <?php endif; ?>
            <?php if($art->category): ?>
            <span class="nyt-section-label" style="font-size:10px;"><?php echo e($art->category->name); ?></span>
            <?php endif; ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>">
                <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:700;margin:0;line-height:1.3;color:#121212;"><?php echo e($art->title); ?></p>
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<div class="nyt-sub-banner">
    <div class="nyt-container">
        <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#aaa;margin:0 0 10px;"><?php echo e(Setting::get('site_name','Laraboard')); ?></p>
        <h3>지금 구독하고 모든 기사를 이용하세요.</h3>
        <p>독자 여러분의 후원이 좋은 저널리즘을 만듭니다.</p>
        <?php if(auth()->guard()->guest()): ?>
        <a href="<?php echo e(route('register')); ?>" class="nyt-sub-btn">무료로 시작하기</a>
        <?php endif; ?>
        <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('news.index')); ?>" class="nyt-sub-btn">전체 기사 보기</a>
        <?php endif; ?>
    </div>
</div>


<?php $__currentLoopData = $categories->slice(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $catArts2 = Article::with(['user'])
        ->where('status','published')
        ->where('category_id', $cat->id)
        ->orderBy('published_at','desc')
        ->limit(4)
        ->get();
?>
<?php if($catArts2->isNotEmpty()): ?>
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2><?php echo e($cat->name); ?></h2>
        <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>" class="more">더보기 →</a>
    </div>
    <div class="nyt-grid-4" style="border-bottom:1px solid #e2e2e2;padding-bottom:16px;">
        <?php $__currentLoopData = $catArts2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="nyt-col-divider" style="padding:12px <?php echo e($loop->last ? '0' : '14px'); ?> 0 <?php echo e($loop->first ? '0' : '14px'); ?>;">
            <?php if($art->thumbnail): ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>" style="display:block;margin-bottom:8px;">
                <img src="<?php echo e($art->thumbnail); ?>" alt="<?php echo e($art->title); ?>"
                     style="width:100%;height:120px;object-fit:cover;">
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>">
                <p style="font-family:var(--nyt-serif);font-size:.9375rem;font-weight:700;margin:0 0 5px;line-height:1.3;color:#121212;"><?php echo e($art->title); ?></p>
            </a>
            <p class="nyt-summary" style="font-size:.8125rem;"><?php echo e($summary($art, 70)); ?></p>
            <p class="nyt-byline"><?php echo e($art->user->name ?? ''); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<?php if($boards->isNotEmpty()): ?>
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2>커뮤니티</h2>
    </div>
    <div class="nyt-grid-3" style="border-bottom:1px solid #e2e2e2;padding-bottom:16px;">
        <?php $__currentLoopData = $boards->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $bPosts = $brd->posts()->with('user')->orderBy('created_at','desc')->limit(5)->get(); ?>
        <div class="nyt-col-divider" style="padding:12px <?php echo e($loop->last ? '0' : '20px'); ?> 0 <?php echo e($loop->first ? '0' : '20px'); ?>;">
            <div style="display:flex;justify-content:space-between;align-items:baseline;border-top:3px solid #121212;padding-top:8px;margin-bottom:10px;">
                <span style="font-family:var(--nyt-serif);font-size:1.1rem;font-weight:700;"><?php echo e($brd->board_name); ?></span>
                <a href="<?php echo e(route('bbs.index', $brd->board_id)); ?>"
                   style="font-family:var(--nyt-sans);font-size:.75rem;color:var(--nyt-section);font-weight:600;text-transform:uppercase;">더보기</a>
            </div>
            <?php $__empty_1 = true; $__currentLoopData = $bPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="<?php echo e(!$loop->first ? 'border-top:1px solid #e2e2e2;padding-top:8px;margin-top:8px;' : ''); ?>">
                <a href="<?php echo e(route('bbs.show', [$brd->board_id, $post->id])); ?>">
                    <p style="font-family:var(--nyt-serif);font-size:.8125rem;font-weight:<?php echo e($loop->first ? '700' : '400'); ?>;margin:0 0 2px;line-height:1.35;color:#121212;">
                        <?php if($post->is_notice): ?><span style="color:#d63638;font-size:.7rem;font-weight:700;">[공지] </span><?php endif; ?>
                        <?php echo e($post->title); ?>

                    </p>
                </a>
                <p class="nyt-byline"><?php echo e($post->user->name ?? ''); ?> · <?php echo e($post->created_at->format('m.d')); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p style="font-size:.8125rem;color:#999;padding:8px 0;">게시글이 없습니다.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>


<?php if($top4->isNotEmpty()): ?>
<section class="nyt-section-block">
    <div class="nyt-section-header">
        <h2>최신 기사</h2>
        <a href="<?php echo e(route('news.index')); ?>" class="more">전체 보기 →</a>
    </div>
    <div class="nyt-grid-4" style="padding-bottom:24px;">
        <?php $__currentLoopData = $top4; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="nyt-col-divider" style="padding:14px <?php echo e($loop->last ? '0' : '16px'); ?> 0 <?php echo e($loop->first ? '0' : '16px'); ?>;">
            <?php if($art->thumbnail): ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>" style="display:block;margin-bottom:10px;">
                <img src="<?php echo e($art->thumbnail); ?>" alt="<?php echo e($art->title); ?>"
                     style="width:100%;height:130px;object-fit:cover;">
            </a>
            <?php endif; ?>
            <?php if($art->category): ?>
            <span class="nyt-section-label"><?php echo e($art->category->name); ?></span>
            <?php endif; ?>
            <a href="<?php echo e(route('news.show', $art->slug)); ?>">
                <h3 class="nyt-headline nyt-headline-sm"><?php echo e($art->title); ?></h3>
            </a>
            <p class="nyt-summary" style="font-size:.8125rem;"><?php echo e($summary($art, 70)); ?></p>
            <p class="nyt-byline"><?php echo e($art->user->name ?? ''); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skin.layout.newyorktimes-style.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/skin/layout/newyorktimes-style/home.blade.php ENDPATH**/ ?>