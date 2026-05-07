
<div class="swn-topbar text-center py-2 px-4 text-sm font-medium tracking-tight">
    <span style="opacity:.9;">📢 <?php echo e(App\Models\Setting::get('site_description', '커뮤니티에 오신 것을 환영합니다')); ?></span>
</div>


<header class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="flex justify-between items-center py-2 text-xs border-b border-gray-100" style="color:var(--site-nav-text,#4b5563);">
            <span class="swn-datetime"><?php echo e(now()->format('Y년 m월 d일 l')); ?></span>
            <div class="flex items-center gap-4">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('profile.show')); ?>" class="flex items-center gap-1.5 font-medium site-nav-link transition">
                        <?php if(auth()->user()->profile_image): ?>
                            <img src="<?php echo e(auth()->user()->profile_image); ?>" alt="" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">
                        <?php endif; ?>
                        <?php echo e(auth()->user()->name); ?>님
                    </a>
                    <?php if(auth()->user()->canAccessAdmin()): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="site-nav-link transition">관리자</a>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button class="site-nav-link transition" style="background:none;border:none;cursor:pointer;font-size:12px;">로그아웃</button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="site-nav-link transition">로그인</a>
                    <a href="<?php echo e(route('register')); ?>" class="site-nav-link transition">회원가입</a>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="flex items-center justify-center py-6 relative">
            <?php if(App\Models\Setting::get('logo_image')): ?>
            <a href="/" class="block text-center group">
                <img src="<?php echo e(App\Models\Setting::get('logo_image')); ?>"
                     alt="<?php echo e(App\Models\Setting::get('logo_text') ?: App\Models\Setting::get('site_name', 'Laraboard')); ?>"
                     style="max-height:72px;max-width:320px;object-fit:contain;margin:0 auto;">
                <?php if(App\Models\Setting::get('logo_tagline')): ?>
                <p class="text-xs text-gray-400 mt-2 tracking-widest uppercase"><?php echo e(App\Models\Setting::get('logo_tagline')); ?></p>
                <?php endif; ?>
            </a>
            <?php else: ?>
            <a href="/" class="text-center group">
                <h1 class="font-serif-title text-4xl md:text-5xl font-black tracking-tight transition site-primary-text"
                    style="color:var(--site-text,#1a1a1a);">
                    <?php echo e(App\Models\Setting::get('logo_text') ?: App\Models\Setting::get('site_name', 'The Laraboard')); ?>

                </h1>
                <p class="text-xs text-gray-400 mt-1 tracking-widest uppercase">
                    <?php echo e(App\Models\Setting::get('logo_tagline') ?: 'Community &amp; Board Platform'); ?>

                </p>
            </a>
            <?php endif; ?>
        </div>
    </div>

    
    <nav class="cobalt-nav">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between gap-4">
            
            <?php
                $_navCategories = \App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
                $_navBoards     = \App\Models\Board::where('is_active', true)->orderBy('order')->limit(4)->get();
            ?>
            <div class="flex items-center overflow-x-auto" style="scrollbar-width:none;gap:0;">
                <a href="<?php echo e(route('news.index')); ?>"
                   class="site-nav-link <?php echo e(request()->routeIs('news.index') && !request('category') ? 'site-nav-active' : ''); ?>"
                   style="white-space:nowrap;">전체기사</a>
                <?php $__currentLoopData = $_navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('news.index', ['category' => $_cat->slug])); ?>"
                   class="site-nav-link <?php echo e(request()->routeIs('news.index') && request('category') === $_cat->slug ? 'site-nav-active' : ''); ?>"
                   style="white-space:nowrap;"><?php echo e($_cat->name); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $_navBoards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_brd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('bbs.index', $_brd->board_id)); ?>"
                   class="site-nav-link <?php echo e(request()->is('bbs/'.$_brd->board_id.'*') ? 'site-nav-active' : ''); ?>"
                   style="white-space:nowrap;"><?php echo e($_brd->board_name); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="<?php echo e(route('news.search')); ?>"
                   style="display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;color:rgba(255,255,255,0.5);transition:all .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='#fff';"
                   onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.5)';"
                   aria-label="검색">
                    <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="18" y2="18"/>
                    </svg>
                </a>
            </div>
        </div>
    </nav>
</header>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/cobalt-glow/navigation.blade.php ENDPATH**/ ?>