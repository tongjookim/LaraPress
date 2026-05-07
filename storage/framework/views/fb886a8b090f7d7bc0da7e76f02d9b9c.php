<?php
    $siteName = App\Models\Setting::get('site_name', 'Laraboard');
    $logoImage = App\Models\Setting::get('logo_image');
    $logoText  = App\Models\Setting::get('logo_text') ?: $siteName;
    $navMenus  = App\Models\NavMenu::activeItems();
    $categories = App\Models\ArticleCategory::with(['children' => fn($q) => $q->where('is_active', true)->orderBy('order')])
        ->where('is_active', true)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get();
?>


<div class="nyt-topbar">
    <div class="nyt-topbar-inner">
        <div class="nyt-topbar-links">
            <?php $__currentLoopData = $navMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($menu->url); ?>" target="<?php echo e($menu->target); ?>"><?php echo e($menu->label); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="nyt-topbar-actions">
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->canAccessAdmin()): ?>
                    <a href="<?php echo e(route('admin.dashboard')); ?>">관리자</a>
                <?php endif; ?>
                <a href="<?php echo e(route('profile.show')); ?>"><?php echo e(auth()->user()->name); ?></a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" style="background:none;border:1px solid var(--nyt-black);border-radius:2px;padding:3px 10px;font-size:11px;font-weight:700;cursor:pointer;font-family:var(--nyt-sans);">로그아웃</button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>">로그인</a>
                <a href="<?php echo e(route('register')); ?>" class="filled">회원가입</a>
            <?php endif; ?>
        </div>
    </div>
</div>


<header class="nyt-masthead">
    <div class="nyt-container">
        <p class="nyt-masthead-date">
            <?php echo e(now()->locale('ko')->isoFormat('YYYY년 M월 D일 dddd')); ?>

        </p>

        <?php if($logoImage): ?>
            <a href="/" style="display:inline-block;margin-bottom:8px;">
                <img src="<?php echo e($logoImage); ?>" alt="<?php echo e($logoText); ?>" style="max-height:56px;max-width:400px;object-fit:contain;margin:0 auto;">
            </a>
        <?php else: ?>
            <a href="/" class="nyt-masthead-logo"><?php echo e($logoText); ?></a>
        <?php endif; ?>

        <div class="nyt-masthead-rules">
            <hr class="nyt-masthead-rule1">
            <hr class="nyt-masthead-rule2">
        </div>
    </div>
</header>


<nav class="nyt-secnav" aria-label="섹션 내비게이션">
    <div class="nyt-secnav-scroll">
    <div class="nyt-secnav-inner">
        <a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->is('/') ? 'active' : ''); ?>">홈</a>
        <a href="<?php echo e(route('news.index')); ?>" class="<?php echo e(request()->routeIs('news.index') && !request()->has('category') ? 'active' : ''); ?>">전체 기사</a>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($cat->children->isNotEmpty()): ?>
        <div class="nyt-secnav-item">
            <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>"
               class="<?php echo e(request()->routeIs('news.index') && request('category') === $cat->slug ? 'active' : ''); ?>">
                <?php echo e($cat->name); ?><span class="nyt-caret"></span>
            </a>
            <div class="nyt-secnav-dropdown">
                <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('news.index', ['category' => $child->slug])); ?>"
                   class="<?php echo e(request('category') === $child->slug ? 'active' : ''); ?>">
                    <?php echo e($child->name); ?>

                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php else: ?>
        <div class="nyt-secnav-item">
            <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>"
               class="<?php echo e(request()->routeIs('news.index') && request('category') === $cat->slug ? 'active' : ''); ?>">
                <?php echo e($cat->name); ?>

            </a>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <button class="nyt-search-btn" id="nytSearchToggle" aria-label="검색" aria-expanded="false">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    </div>
</nav>


<div class="nyt-search-overlay" id="nytSearchOverlay" role="search" aria-hidden="true">
    <div class="nyt-search-overlay-inner">
        <form action="<?php echo e(route('news.search')); ?>" method="GET" class="nyt-search-form">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="nyt-search-form-icon">
                <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="18" y2="18"/>
            </svg>
            <input type="text" name="q" id="nytSearchInput" class="nyt-search-input"
                   placeholder="기사 검색..." autocomplete="off"
                   value="<?php echo e(request('q')); ?>">
            <button type="submit" class="nyt-search-submit">검색</button>
            <button type="button" class="nyt-search-close" id="nytSearchClose" aria-label="닫기">×</button>
        </form>
    </div>
</div>

<script>
(function () {
    var activeDropdown = null;

    function openDropdown(item) {
        closeAll();
        var dropdown = item.querySelector('.nyt-secnav-dropdown');
        if (!dropdown) return;
        var rect = item.querySelector('a').getBoundingClientRect();
        dropdown.style.top  = (rect.bottom + window.scrollY) + 'px';
        dropdown.style.left = (rect.left  + window.scrollX) + 'px';
        dropdown.classList.add('open');
        activeDropdown = { item: item, dropdown: dropdown };
    }

    function closeAll() {
        document.querySelectorAll('.nyt-secnav-dropdown.open').forEach(function (d) {
            d.classList.remove('open');
        });
        activeDropdown = null;
    }

    document.querySelectorAll('.nyt-secnav-item').forEach(function (item) {
        var dropdown = item.querySelector('.nyt-secnav-dropdown');
        if (!dropdown) return;

        item.addEventListener('mouseenter', function () { openDropdown(item); });
        item.addEventListener('mouseleave', function (e) {
            // 드롭다운으로 이동하는 경우엔 닫지 않음
            if (!e.relatedTarget || !item.contains(e.relatedTarget)) {
                closeAll();
            }
        });
        dropdown.addEventListener('mouseleave', function (e) {
            if (!e.relatedTarget || !item.contains(e.relatedTarget)) {
                closeAll();
            }
        });
    });

    // 다른 곳 클릭 시 닫기
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nyt-secnav-item')) closeAll();
    });

    // 스크롤 시 드롭다운 위치 재계산
    window.addEventListener('scroll', function () {
        if (activeDropdown) {
            var rect = activeDropdown.item.querySelector('a').getBoundingClientRect();
            activeDropdown.dropdown.style.top  = (rect.bottom + window.scrollY) + 'px';
            activeDropdown.dropdown.style.left = (rect.left  + window.scrollX) + 'px';
        }
    }, { passive: true });

    // ── 검색 오버레이 토글 ──
    var searchToggle  = document.getElementById('nytSearchToggle');
    var searchOverlay = document.getElementById('nytSearchOverlay');
    var searchClose   = document.getElementById('nytSearchClose');
    var searchInput   = document.getElementById('nytSearchInput');

    function openSearch() {
        searchOverlay.classList.add('open');
        searchOverlay.setAttribute('aria-hidden', 'false');
        searchToggle.classList.add('active');
        searchToggle.setAttribute('aria-expanded', 'true');
        searchInput.focus();
        searchInput.select();
    }

    function closeSearch() {
        searchOverlay.classList.remove('open');
        searchOverlay.setAttribute('aria-hidden', 'true');
        searchToggle.classList.remove('active');
        searchToggle.setAttribute('aria-expanded', 'false');
    }

    searchToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        searchOverlay.classList.contains('open') ? closeSearch() : openSearch();
    });

    searchClose.addEventListener('click', closeSearch);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSearch();
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nyt-search-overlay') && !e.target.closest('#nytSearchToggle')) {
            closeSearch();
        }
        if (!e.target.closest('.nyt-secnav-item')) closeAll();
    });
}());
</script>

<?php $__topBanners = \App\Models\TopBanner::activeNow(); ?>
<?php if($__topBanners->isNotEmpty()): ?>
<div id="top-banner-wrap">
<?php $__currentLoopData = $__topBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div id="top-banner-<?php echo e($__b->id); ?>" data-banner-id="<?php echo e($__b->id); ?>" data-reshow="<?php echo e($__b->reshow_hours); ?>"
     style="display:none;position:relative;text-align:center;padding:9px 44px;background:<?php echo e($__b->bg_color); ?>;color:<?php echo e($__b->text_color); ?>;font-size:<?php echo e($__b->font_size); ?>px;font-weight:<?php echo e($__b->font_weight); ?>;line-height:1.4;">
    <?php if($__b->link_url): ?><a href="<?php echo e($__b->link_url); ?>" style="color:<?php echo e($__b->text_color); ?>;text-decoration:none;"><?php echo e($__b->text); ?></a>
    <?php else: ?><?php echo e($__b->text); ?><?php endif; ?>
    <button onclick="closeTopBanner(<?php echo e($__b->id); ?>, <?php echo e($__b->reshow_hours); ?>)"
            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:18px;line-height:1;color:<?php echo e($__b->text_color); ?>;opacity:.7;" aria-label="닫기">×</button>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<script>
(function(){
    var d=<?php echo json_encode($__topBanners->map(fn($b)=>['id'=>$b->id, 'reshow'=>$b->reshow_hours]), 512) ?>;
    d.forEach(function(b){
        var k='tb_closed_'+b.id,c=localStorage.getItem(k),s=true;
        if(c){if(b.reshow===0){s=false;}else{var e=(Date.now()-parseInt(c))/3600000;if(e<b.reshow)s=false;}}
        if(s)document.getElementById('top-banner-'+b.id).style.display='block';
    });
}());
function closeTopBanner(id,r){
    document.getElementById('top-banner-'+id).style.display='none';
    localStorage.setItem('tb_closed_'+id,r===0?'0':Date.now().toString());
}
</script>
<?php endif; ?>

<?php if(session('success')): ?>
<div class="nyt-container" style="margin-top:12px;">
    <div style="background:#f0fdf4;border-left:3px solid #16a34a;padding:10px 14px;font-size:14px;color:#166534;"><?php echo e(session('success')); ?></div>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="nyt-container" style="margin-top:12px;">
    <div style="background:#fef2f2;border-left:3px solid #dc2626;padding:10px 14px;font-size:14px;color:#991b1b;"><?php echo e(session('error')); ?></div>
</div>
<?php endif; ?>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/newyorktimes-style/navigation.blade.php ENDPATH**/ ?>