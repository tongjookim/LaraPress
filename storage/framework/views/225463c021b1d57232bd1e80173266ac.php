<?php
    $footerGroups      = App\Models\NavMenu::footerGroups();
    $footerCategories  = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
?>

<div class="bg-gray-100 border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div style="display:grid;grid-template-columns:1fr auto;gap:24px 40px;align-items:start;">

            
            <div>
                <?php $__currentLoopData = $footerGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $groupItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="margin-bottom:14px;">
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#374151;margin-right:12px;"><?php echo e($groupName); ?></span>
                    <?php $__currentLoopData = $groupItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($fitem->url); ?>" target="<?php echo e($fitem->target); ?>"
                       style="font-size:13px;color:#6b7280;text-decoration:none;margin-right:16px;transition:color .15s;"
                       onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#6b7280'">
                        <?php echo e($fitem->label); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($footerCategories->isNotEmpty()): ?>
                <div style="margin-top:<?php echo e(empty($footerGroups) ? '0' : '6px'); ?>;padding-top:<?php echo e(empty($footerGroups) ? '0' : '12px'); ?>;<?php echo e(empty($footerGroups) ? '' : 'border-top:1px solid #e5e7eb;'); ?>">
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#374151;margin-right:12px;">카테고리</span>
                    <?php $__currentLoopData = $footerCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('news.index')); ?>?category=<?php echo e($cat->slug); ?>"
                       style="font-size:13px;color:#6b7280;text-decoration:none;margin-right:16px;transition:color .15s;"
                       onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#6b7280'">
                        <?php echo e($cat->name); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>

            
            <div style="text-align:right;min-width:160px;">
                <p style="font-size:13px;font-weight:700;color:#111827;margin-bottom:4px;"><?php echo e(App\Models\Setting::get('site_name', 'Laraboard')); ?></p>
                <p style="font-size:12px;color:#6b7280;line-height:1.6;"><?php echo e(App\Models\Setting::get('site_description', '커뮤니티 게시판 플랫폼')); ?></p>
                <p style="font-size:11px;color:#9ca3af;margin-top:6px;">
                    회원 <?php echo e(number_format(App\Models\User::count())); ?>명 · 게시글 <?php echo e(number_format(App\Models\Post::count())); ?>건
                </p>
            </div>

        </div>
    </div>
</div>


<?php
    $pressMasthead            = App\Models\Setting::get('press_masthead');
    $pressRegistrationNumber  = App\Models\Setting::get('press_registration_number');
    $pressPublisher           = App\Models\Setting::get('press_publisher');
    $pressEditor          = App\Models\Setting::get('press_editor');
    $pressAddress         = App\Models\Setting::get('press_address');
    $pressPostalCode      = App\Models\Setting::get('press_postal_code');
    $pressFax             = App\Models\Setting::get('press_fax');
    $pressPhone           = App\Models\Setting::get('press_phone');
    $pressEmail           = App\Models\Setting::get('press_email');
    $pressYouthManager    = App\Models\Setting::get('press_youth_manager');
    $pressPrivacyManager  = App\Models\Setting::get('press_privacy_manager');
    $pressGrievanceManager= App\Models\Setting::get('press_grievance_manager');
    $hasPressInfo         = $pressMasthead || $pressPublisher || $pressAddress;
?>
<footer class="bg-gray-800 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 py-6">

        <?php if($hasPressInfo): ?>
        
        <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:20px;margin-bottom:16px;font-size:12px;line-height:1.8;color:#9ca3af;">
            <?php if($pressMasthead): ?>
            <span style="font-weight:700;color:#d1d5db;"><?php echo e($pressMasthead); ?></span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            <?php endif; ?>
            <?php if($pressRegistrationNumber): ?>
            <span>등록번호: <?php echo e($pressRegistrationNumber); ?></span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            <?php endif; ?>
            <?php if($pressPublisher): ?>
            <span>발행인: <?php echo e($pressPublisher); ?></span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            <?php endif; ?>
            <?php if($pressEditor): ?>
            <span>편집인: <?php echo e($pressEditor); ?></span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            <?php endif; ?>
            <?php if($pressPhone): ?>
            <span>대표번호: <?php echo e($pressPhone); ?></span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            <?php endif; ?>
            <?php if($pressFax): ?>
            <span>팩스: <?php echo e($pressFax); ?></span>
            <span style="margin:0 6px;color:#4b5563;">|</span>
            <?php endif; ?>
            <?php if($pressEmail): ?>
            <span>이메일: <a href="mailto:<?php echo e($pressEmail); ?>" style="color:#9ca3af;text-decoration:none;"><?php echo e($pressEmail); ?></a></span>
            <?php endif; ?>
            <?php if($pressAddress || $pressPostalCode): ?>
            <br>
            <?php if($pressPostalCode): ?><span>우편번호: <?php echo e($pressPostalCode); ?></span><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php if($pressAddress): ?><span>주소: <?php echo e($pressAddress); ?></span><?php endif; ?>
            <?php endif; ?>
            <?php if($pressYouthManager || $pressPrivacyManager || $pressGrievanceManager): ?>
            <br>
            <?php if($pressYouthManager): ?>
            <span>청소년보호책임자: <?php echo e($pressYouthManager); ?></span>
            <?php if($pressPrivacyManager || $pressGrievanceManager): ?><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php endif; ?>
            <?php if($pressPrivacyManager): ?>
            <span>개인정보 보호책임자: <?php echo e($pressPrivacyManager); ?></span>
            <?php if($pressGrievanceManager): ?><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php endif; ?>
            <?php if($pressGrievanceManager): ?>
            <span>고충처리인: <?php echo e($pressGrievanceManager); ?></span>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-left">
                <p class="font-serif-title text-lg font-bold text-white tracking-tight">
                    <?php echo e(App\Models\Setting::get('site_name', 'Laraboard')); ?>

                </p>
                <p class="text-xs mt-1">&copy; <?php echo e(date('Y')); ?> All rights reserved.</p>
            </div>
            <div class="text-xs text-gray-500">
                Powered by <span class="text-gray-400">Laraboard</span>
            </div>
        </div>
    </div>
</footer>

<?php if(App\Models\Setting::get('custom_body_script')): ?>
<?php echo App\Models\Setting::get('custom_body_script'); ?>

<?php endif; ?>
</body>
</html>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/swn-style/footer.blade.php ENDPATH**/ ?>