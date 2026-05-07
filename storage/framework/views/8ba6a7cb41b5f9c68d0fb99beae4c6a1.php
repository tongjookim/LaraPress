<?php
    $footerGroups     = App\Models\NavMenu::footerGroups();
    $footerCategories = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $siteName         = App\Models\Setting::get('site_name', 'Laraboard');
    $boards           = App\Models\Board::where('is_active', true)->orderBy('order')->limit(8)->get();

    $pressMasthead           = App\Models\Setting::get('press_masthead');
    $pressRegistrationNumber = App\Models\Setting::get('press_registration_number');
    $pressPublisher          = App\Models\Setting::get('press_publisher');
    $pressEditor             = App\Models\Setting::get('press_editor');
    $pressAddress            = App\Models\Setting::get('press_address');
    $pressPostalCode         = App\Models\Setting::get('press_postal_code');
    $pressFax                = App\Models\Setting::get('press_fax');
    $pressPhone              = App\Models\Setting::get('press_phone');
    $pressEmail              = App\Models\Setting::get('press_email');
    $pressYouthManager       = App\Models\Setting::get('press_youth_manager');
    $pressPrivacyManager     = App\Models\Setting::get('press_privacy_manager');
    $pressGrievanceManager   = App\Models\Setting::get('press_grievance_manager');
    $hasPressInfo            = $pressMasthead || $pressPublisher || $pressAddress;
?>

<footer style="background:#fff;border-top:3px solid #121212;margin-top:32px;">

    
    <div style="border-bottom:1px solid #e2e2e2;padding:32px 0 24px;">
        <div class="nyt-container">
            
            <div style="text-align:center;margin-bottom:24px;">
                <a href="/" style="font-family:var(--nyt-serif);font-size:2rem;font-weight:700;color:#121212;letter-spacing:-.02em;">
                    <?php echo e(App\Models\Setting::get('logo_text') ?: $siteName); ?>

                </a>
            </div>

            
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:24px 16px;">

                <?php if($footerCategories->isNotEmpty()): ?>
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;">카테고리</p>
                    <?php $__currentLoopData = $footerCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('news.index', ['category' => $cat->slug])); ?>"
                       style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;line-height:1.4;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'"><?php echo e($cat->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <?php if($boards->isNotEmpty()): ?>
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;">커뮤니티</p>
                    <?php $__currentLoopData = $boards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('bbs.index', $brd->board_id)); ?>"
                       style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;line-height:1.4;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'"><?php echo e($brd->board_name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <?php $__currentLoopData = $footerGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $groupItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;"><?php echo e($groupName); ?></p>
                    <?php $__currentLoopData = $groupItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($fitem->url); ?>" target="<?php echo e($fitem->target); ?>"
                       style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;line-height:1.4;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'"><?php echo e($fitem->label); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <div>
                    <p style="font-family:var(--nyt-sans);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#121212;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #e2e2e2;">계정</p>
                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('profile.show')); ?>" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">내 프로필</a>
                    <?php if(auth()->user()->canAccessAdmin()): ?>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">관리자 패널</a>
                    <?php endif; ?>
                    <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">로그인</a>
                    <a href="<?php echo e(route('register')); ?>" style="display:block;font-family:var(--nyt-sans);font-size:13px;color:#333;padding:3px 0;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#333'">회원가입</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    
    <div style="padding:16px 0 24px;">
        <div class="nyt-container">

            <?php if($hasPressInfo): ?>
            <div style="font-family:var(--nyt-sans);font-size:11.5px;color:#666;line-height:1.9;margin-bottom:12px;">
                <?php if($pressMasthead): ?><span style="font-weight:700;color:#333;"><?php echo e($pressMasthead); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressRegistrationNumber): ?><span>등록번호: <?php echo e($pressRegistrationNumber); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressPublisher): ?><span>발행인: <?php echo e($pressPublisher); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressEditor): ?><span>편집인: <?php echo e($pressEditor); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressPhone): ?><span>대표번호: <?php echo e($pressPhone); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressFax): ?><span>팩스: <?php echo e($pressFax); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressEmail): ?><span>이메일: <a href="mailto:<?php echo e($pressEmail); ?>" style="color:#666;"><?php echo e($pressEmail); ?></a></span><?php endif; ?>
                <?php if($pressAddress || $pressPostalCode): ?>
                <br>
                <?php if($pressPostalCode): ?><span>우편번호: <?php echo e($pressPostalCode); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressAddress): ?><span>주소: <?php echo e($pressAddress); ?></span><?php endif; ?>
                <?php endif; ?>
                <?php if($pressYouthManager || $pressPrivacyManager || $pressGrievanceManager): ?>
                <br>
                <?php if($pressYouthManager): ?><span>청소년보호책임자: <?php echo e($pressYouthManager); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressPrivacyManager): ?><span>개인정보 보호책임자: <?php echo e($pressPrivacyManager); ?></span><span style="margin:0 8px;color:#ccc;">|</span><?php endif; ?>
                <?php if($pressGrievanceManager): ?><span>고충처리인: <?php echo e($pressGrievanceManager); ?></span><?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <p style="font-family:var(--nyt-sans);font-size:12px;color:#999;margin:0;">
                    &copy; <?php echo e(date('Y')); ?> <?php echo e($siteName); ?>. All Rights Reserved.
                </p>
                <div style="display:flex;gap:16px;">
                    <a href="<?php echo e(url('/feed')); ?>" style="font-family:var(--nyt-sans);font-size:12px;color:#999;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#999'">RSS</a>
                    <a href="<?php echo e(url('/sitemap.xml')); ?>" style="font-family:var(--nyt-sans);font-size:12px;color:#999;"
                       onmouseover="this.style.color='#121212'" onmouseout="this.style.color='#999'">Sitemap</a>
                </div>
            </div>

        </div>
    </div>

</footer>

<?php if(App\Models\Setting::get('custom_body_script')): ?>
<?php echo App\Models\Setting::get('custom_body_script'); ?>

<?php endif; ?>
</body>
</html>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/newyorktimes-style/footer.blade.php ENDPATH**/ ?>