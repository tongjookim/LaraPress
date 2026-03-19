<?php $__env->startSection('title', ' — ' . $author->name . ' 기자'); ?>

<?php $__env->startPush('skin-css'); ?>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">
<style>
:root {
    --j-accent:      #1a5276;
    --j-accent-mid:  #2471a3;
    --j-accent-light:#d6eaf8;
    --j-accent-bg:   #eaf4fb;
    --j-red:         #c0392b;
    --j-red-light:   #fadbd8;
    --j-border:      #d5d8dc;
    --j-text:        #1c2833;
    --j-text-sub:    #566573;
    --j-white:       #ffffff;
}

/* ── 페이지 래퍼 ── */
.j-wrapper {
    background: #f4f6f7;
    margin: -2rem -1rem 0;
    padding: 0 1rem 3rem;
}
@media (min-width: 640px)  { .j-wrapper { margin: -2rem -1.5rem 0; padding: 0 1.5rem 3rem; } }
@media (min-width: 1024px) { .j-wrapper { margin: -2rem -2rem 0;   padding: 0 2rem 3rem;   } }

/* ── 히어로 배너 ── */
.j-hero {
    background: linear-gradient(135deg, var(--j-accent) 0%, #154360 100%);
    padding: 36px 0;
    position: relative;
    overflow: hidden;
}
.j-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: repeating-linear-gradient(45deg,
        rgba(255,255,255,.03) 0, rgba(255,255,255,.03) 1px,
        transparent 1px, transparent 40px);
}
.j-hero-inner {
    max-width: 1100px; margin: 0 auto;
    display: flex; align-items: center; gap: 28px;
    position: relative;
}
.j-hero-avatar {
    width: 100px; height: 100px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,.5);
    object-fit: cover;
    flex-shrink: 0;
    background: var(--j-accent-mid);
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; font-weight: 900; color: #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,.25);
}
.j-hero-info { }
.j-hero-role {
    display: inline-block;
    background: rgba(255,255,255,.18);
    color: #d6eaf8;
    font-size: 11px; font-weight: 700; padding: 3px 10px;
    border-radius: 99px; letter-spacing: .06em;
    margin-bottom: 8px;
}
.j-hero-name {
    font-family: 'Noto Sans KR', sans-serif;
    font-size: 26px; font-weight: 900; color: #fff;
    letter-spacing: -.02em; line-height: 1.2; margin-bottom: 6px;
}
.j-hero-username { font-size: 13px; color: rgba(255,255,255,.55); }
.j-hero-stats {
    display: flex; gap: 24px; margin-top: 14px;
}
.j-stat { text-align: left; }
.j-stat-num {
    font-size: 20px; font-weight: 900; color: #fff; line-height: 1;
}
.j-stat-label { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 2px; }

/* ── 메인 2컬럼 ── */
.j-layout {
    max-width: 1100px; margin: 0 auto; padding-top: 24px;
    display: grid; grid-template-columns: 1fr;
    gap: 20px;
}
@media (min-width: 900px) {
    .j-layout { grid-template-columns: 240px 1fr; }
}

/* ── 사이드바 ── */
.j-sidebar { display: flex; flex-direction: column; gap: 14px; }
.j-card {
    background: var(--j-white);
    border: 1px solid var(--j-border);
    border-radius: 6px;
    overflow: hidden;
}
.j-card-header {
    background: var(--j-accent);
    color: #fff;
    font-size: 12px; font-weight: 700; padding: 9px 14px;
    letter-spacing: .04em;
}
.j-card-body { padding: 14px; }

.j-bio {
    font-size: 13px; line-height: 1.75; color: var(--j-text-sub);
    word-break: keep-all;
}

/* 소셜 링크 */
.j-social-list { display: flex; flex-direction: column; gap: 6px; }
.j-social-link {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; font-weight: 600; text-decoration: none;
    padding: 7px 10px; border-radius: 5px;
    transition: background .12s;
    color: var(--j-text);
}
.j-social-link:hover { background: var(--j-accent-bg); }
.j-social-icon {
    width: 28px; height: 28px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 13px;
}

/* 카테고리 목록 */
.j-cat-list { display: flex; flex-direction: column; }
.j-cat-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 0; border-bottom: 1px solid #f0f2f3; font-size: 13px;
}
.j-cat-item:last-child { border-bottom: none; }
.j-cat-item a { color: var(--j-text); text-decoration: none; font-weight: 500; }
.j-cat-item a:hover { color: var(--j-accent-mid); }
.j-cat-count {
    font-size: 11px; font-weight: 700; color: var(--j-accent-mid);
    background: var(--j-accent-bg); padding: 1px 7px; border-radius: 99px;
}

/* ── 메인 콘텐츠 ── */
.j-main { display: flex; flex-direction: column; gap: 20px; }

/* 섹션 타이틀 */
.j-section-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 15px; font-weight: 900; color: var(--j-text);
    padding-bottom: 10px;
    border-bottom: 2px solid var(--j-accent);
    margin-bottom: 14px;
}
.j-section-title .j-section-cat {
    display: inline-block;
    background: var(--j-red); color: #fff;
    font-size: 10px; font-weight: 800; letter-spacing: .05em;
    padding: 2px 8px; border-radius: 3px;
}
.j-section-more {
    margin-left: auto; font-size: 12px; color: var(--j-text-sub);
    text-decoration: none; font-weight: 500;
}
.j-section-more:hover { color: var(--j-accent-mid); }

/* 카테고리 기사 블록 — 가로 스크롤 카드 */
.j-cat-articles {
    background: var(--j-white); border: 1px solid var(--j-border); border-radius: 6px;
    padding: 16px;
}
.j-cat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 14px;
}
.j-cat-card {
    display: flex; flex-direction: column; text-decoration: none;
    border-radius: 4px; overflow: hidden; transition: box-shadow .15s;
}
.j-cat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.j-cat-card__img {
    width: 100%; height: 110px; object-fit: cover; display: block;
    background: #e8ecee;
}
.j-cat-card__no-img {
    width: 100%; height: 110px; background: var(--j-accent-bg);
    display: flex; align-items: center; justify-content: center;
    color: var(--j-accent-mid); font-size: 11px;
}
.j-cat-card__body { padding: 8px 4px 4px; }
.j-cat-card__title {
    font-size: 13px; font-weight: 700; color: var(--j-text);
    line-height: 1.45;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
}
.j-cat-card:hover .j-cat-card__title { color: var(--j-accent-mid); }
.j-cat-card__date { font-size: 11px; color: var(--j-text-sub); margin-top: 4px; }

/* 전체 기사 그리드 */
.j-all-articles {
    background: var(--j-white); border: 1px solid var(--j-border); border-radius: 6px;
    padding: 16px;
}
.j-article-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
}
@media (min-width: 640px) { .j-article-grid { grid-template-columns: 1fr 1fr; } }

.j-article-row {
    display: flex; gap: 12px; align-items: flex-start;
    padding: 12px 0; border-bottom: 1px solid #f0f2f3; text-decoration: none;
}
.j-article-row:last-child { border-bottom: none; }
.j-article-row__img {
    width: 80px; height: 56px; object-fit: cover;
    border-radius: 3px; flex-shrink: 0; display: block;
    background: var(--j-accent-bg);
}
.j-article-row__no-img {
    width: 80px; height: 56px; border-radius: 3px; flex-shrink: 0;
    background: var(--j-accent-bg);
    display: flex; align-items: center; justify-content: center;
    color: var(--j-accent-mid); font-size: 10px;
}
.j-article-row__body { flex: 1; min-width: 0; }
.j-article-row__title {
    font-size: 13px; font-weight: 700; color: var(--j-text);
    line-height: 1.45;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden; margin-bottom: 4px;
}
.j-article-row:hover .j-article-row__title { color: var(--j-accent-mid); }
.j-article-row__meta { font-size: 11px; color: var(--j-text-sub); }
.j-article-row__cat {
    display: inline-block; font-size: 10px; font-weight: 700;
    color: var(--j-red); margin-right: 6px;
}

/* 페이지네이션 */
.j-pagination {
    display: flex; justify-content: center; gap: 4px;
    margin-top: 16px; padding-top: 14px;
    border-top: 1px solid var(--j-border);
}
.j-pagination a, .j-pagination span {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 32px; height: 32px; padding: 0 8px;
    border: 1px solid var(--j-border); border-radius: 4px;
    font-size: 13px; color: var(--j-text-sub); text-decoration: none;
    background: #fff; transition: all .12s;
}
.j-pagination a:hover { border-color: var(--j-accent-mid); color: var(--j-accent-mid); }
.j-pagination .active span {
    background: var(--j-accent); border-color: var(--j-accent);
    color: #fff; font-weight: 700;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="j-wrapper">


<div class="j-hero">
    <div class="j-hero-inner">
        <?php if($author->profile_image): ?>
            <img src="<?php echo e($author->profile_image); ?>" alt="<?php echo e($author->name); ?>" class="j-hero-avatar">
        <?php else: ?>
            <div class="j-hero-avatar"><?php echo e(mb_substr($author->name, 0, 1)); ?></div>
        <?php endif; ?>
        <div class="j-hero-info">
            <div class="j-hero-role"><?php echo e($author->roleLabel()); ?></div>
            <div class="j-hero-name"><?php echo e($author->name); ?></div>
            <div class="j-hero-username"><?php echo e('@'.$author->username); ?></div>
            <div class="j-hero-stats">
                <div class="j-stat">
                    <div class="j-stat-num"><?php echo e(number_format($totalArticles)); ?></div>
                    <div class="j-stat-label">기사</div>
                </div>
                <div class="j-stat">
                    <div class="j-stat-num"><?php echo e(number_format($totalViews)); ?></div>
                    <div class="j-stat-label">누적 조회</div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="j-layout">

    
    <aside class="j-sidebar">

        
        <?php if($author->bio): ?>
        <div class="j-card">
            <div class="j-card-header">기자 소개</div>
            <div class="j-card-body">
                <p class="j-bio"><?php echo e($author->bio); ?></p>
            </div>
        </div>
        <?php endif; ?>

        
        <?php
        $socialDefs = [
            'social_email'     => ['label'=>'이메일',      'color'=>'#6b7280', 'bg'=>'#f3f4f6', 'prefix'=>'mailto:',
                'svg'=>'<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>'],
            'social_website'   => ['label'=>'홈페이지',     'color'=>'#6366f1', 'bg'=>'#ede9fe', 'prefix'=>'',
                'svg'=>'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>'],
            'social_facebook'  => ['label'=>'Facebook',    'color'=>'#1877F2', 'bg'=>'#dbeafe', 'prefix'=>'',
                'svg'=>'<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>'],
            'social_x'         => ['label'=>'X (Twitter)', 'color'=>'#000',    'bg'=>'#f3f4f6', 'prefix'=>'',
                'svg'=>'<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
            'social_instagram' => ['label'=>'Instagram',   'color'=>'#E1306C', 'bg'=>'#fce7f3', 'prefix'=>'',
                'svg'=>'<rect x="2" y="2" width="20" height="20" rx="5"/><path fill="none" stroke="white" stroke-width="2" d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line stroke="white" stroke-width="2" x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>'],
            'social_linkedin'  => ['label'=>'LinkedIn',    'color'=>'#0A66C2', 'bg'=>'#dbeafe', 'prefix'=>'',
                'svg'=>'<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>'],
            'social_blog'      => ['label'=>'블로그',       'color'=>'#f59e0b', 'bg'=>'#fef3c7', 'prefix'=>'',
                'svg'=>'<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>'],
            'social_pixabay'   => ['label'=>'Pixabay',     'color'=>'#2ec66e', 'bg'=>'#d1fae5', 'prefix'=>'',
                'svg'=>'<circle cx="12" cy="12" r="10" fill="currentColor"/><text x="6" y="16" font-size="8" fill="white" font-weight="bold" font-family="sans-serif">px</text>'],
            'social_wikipedia' => ['label'=>'Wikipedia',   'color'=>'#333',    'bg'=>'#f3f4f6', 'prefix'=>'',
                'svg'=>'<circle cx="12" cy="12" r="10" fill="currentColor"/><text x="7" y="16.5" font-size="11" fill="white" font-weight="bold" font-family="serif">W</text>'],
        ];
        $hasSocials = collect($socialDefs)->keys()->some(fn($f) => !empty($author->$f));
        ?>

        <?php if($hasSocials): ?>
        <div class="j-card">
            <div class="j-card-header">연락처 &amp; 소셜</div>
            <div class="j-card-body" style="padding:8px;">
                <div class="j-social-list">
                    <?php $__currentLoopData = $socialDefs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $def): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(!empty($author->$field)): ?>
                    <?php $href = $def['prefix'] ? $def['prefix'].$author->$field : $author->$field; ?>
                    <a href="<?php echo e($href); ?>" target="<?php echo e(str_starts_with($href,'mailto') ? '_self' : '_blank'); ?>" rel="noopener noreferrer" class="j-social-link">
                        <span class="j-social-icon" style="background:<?php echo e($def['bg']); ?>;color:<?php echo e($def['color']); ?>;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="<?php echo e(in_array($field,['social_facebook','social_x','social_pixabay','social_wikipedia']) ? 'currentColor' : 'none'); ?>" stroke="<?php echo e(in_array($field,['social_facebook','social_x','social_pixabay','social_wikipedia']) ? 'none' : 'currentColor'); ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $def['svg']; ?></svg>
                        </span>
                        <?php echo e($def['label']); ?>

                        <?php if($field === 'social_email'): ?>
                        <span style="font-size:11px;color:#9ca3af;font-weight:400;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:120px;"><?php echo e($author->$field); ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($categoriesWithArticles->count()): ?>
        <div class="j-card">
            <div class="j-card-header">작성 카테고리</div>
            <div class="j-card-body" style="padding:4px 14px;">
                <div class="j-cat-list">
                    <?php $__currentLoopData = $categoriesWithArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $cnt = \App\Models\Article::where('status','published')->where('user_id',$author->id)->where('category_id',$cat->id)->count(); ?>
                    <div class="j-cat-item">
                        <a href="<?php echo e(route('news.index', ['category'=>$cat->slug])); ?>"><?php echo e($cat->name); ?></a>
                        <span class="j-cat-count"><?php echo e($cnt); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </aside>

    
    <main class="j-main">

        
        <?php $__currentLoopData = $categoriesWithArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="j-cat-articles">
            <div class="j-section-title">
                <span class="j-section-cat"><?php echo e($cat->name); ?></span>
                <?php echo e($cat->name); ?> 최신 기사
                <a href="<?php echo e(route('news.index', ['category'=>$cat->slug])); ?>" class="j-section-more">더보기 ›</a>
            </div>
            <div class="j-cat-grid">
                <?php $__currentLoopData = $cat->recentArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('news.show', $a->slug)); ?>" class="j-cat-card">
                    <?php if($a->thumbnail): ?>
                        <img src="<?php echo e($a->thumbnail); ?>" alt="<?php echo e($a->title); ?>" class="j-cat-card__img">
                    <?php else: ?>
                        <div class="j-cat-card__no-img">이미지 없음</div>
                    <?php endif; ?>
                    <div class="j-cat-card__body">
                        <div class="j-cat-card__title"><?php echo e($a->title); ?></div>
                        <div class="j-cat-card__date"><?php echo e($a->published_at?->format('Y.m.d') ?? $a->created_at->format('Y.m.d')); ?></div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div class="j-all-articles">
            <div class="j-section-title">
                전체 기사
                <span style="font-size:12px;font-weight:400;color:var(--j-text-sub);margin-left:4px;"><?php echo e(number_format($totalArticles)); ?>건</span>
            </div>

            <div class="j-article-grid">
                <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('news.show', $a->slug)); ?>" class="j-article-row">
                    <?php if($a->thumbnail): ?>
                        <img src="<?php echo e($a->thumbnail); ?>" alt="<?php echo e($a->title); ?>" class="j-article-row__img">
                    <?php else: ?>
                        <div class="j-article-row__no-img">No img</div>
                    <?php endif; ?>
                    <div class="j-article-row__body">
                        <?php if($a->category): ?>
                            <span class="j-article-row__cat"><?php echo e($a->category->name); ?></span>
                        <?php endif; ?>
                        <div class="j-article-row__title"><?php echo e($a->title); ?></div>
                        <div class="j-article-row__meta">
                            <?php echo e($a->published_at?->format('Y.m.d') ?? $a->created_at->format('Y.m.d')); ?>

                            &nbsp;·&nbsp; 조회 <?php echo e(number_format($a->view_count)); ?>

                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p style="color:var(--j-text-sub);font-size:14px;padding:20px 0;grid-column:1/-1;">작성된 기사가 없습니다.</p>
                <?php endif; ?>
            </div>

            
            <?php if($articles->hasPages()): ?>
            <div class="j-pagination">
                <?php echo $articles->links(); ?>

            </div>
            <?php endif; ?>
        </div>

    </main>

</div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('skin.layout.basic.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/articles/author.blade.php ENDPATH**/ ?>