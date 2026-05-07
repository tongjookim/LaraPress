<?php
    $footerGroups     = App\Models\NavMenu::footerGroups();
    $footerCategories = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get();
    $hasFooterContent = !empty($footerGroups) || $footerCategories->isNotEmpty();
?>

<?php if($hasFooterContent): ?>
<div style="background:#1f2937;border-top:1px solid #374151;">
    <div class="max-w-7xl mx-auto px-4 py-8">

        <?php $__currentLoopData = $footerGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $groupItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="margin-bottom:12px;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-right:12px;"><?php echo e($groupName); ?></span>
            <?php $__currentLoopData = $groupItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($fitem->url); ?>" target="<?php echo e($fitem->target); ?>"
               style="font-size:13px;color:#d1d5db;text-decoration:none;margin-right:16px;transition:color .15s;"
               onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#d1d5db'">
                <?php echo e($fitem->label); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($footerCategories->isNotEmpty()): ?>
        <div style="<?php echo e(!empty($footerGroups) ? 'margin-top:8px;padding-top:12px;border-top:1px solid #374151;' : ''); ?>">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-right:12px;">카테고리</span>
            <?php $__currentLoopData = $footerCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('news.index')); ?>?category=<?php echo e($cat->slug); ?>"
               style="font-size:13px;color:#d1d5db;text-decoration:none;margin-right:16px;transition:color .15s;"
               onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#d1d5db'">
                <?php echo e($cat->name); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>

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
<footer class="bg-gray-800 text-gray-400 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <?php if($hasPressInfo): ?>
        <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:20px;margin-bottom:16px;font-size:12px;line-height:1.8;color:#9ca3af;">
            <?php if($pressMasthead): ?><span style="font-weight:700;color:#d1d5db;"><?php echo e($pressMasthead); ?></span><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php if($pressRegistrationNumber): ?><span>등록번호: <?php echo e($pressRegistrationNumber); ?></span><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php if($pressPublisher): ?><span>발행인: <?php echo e($pressPublisher); ?></span><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php if($pressEditor): ?><span>편집인: <?php echo e($pressEditor); ?></span><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php if($pressPhone): ?><span>대표번호: <?php echo e($pressPhone); ?></span><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php if($pressFax): ?><span>팩스: <?php echo e($pressFax); ?></span><span style="margin:0 6px;color:#4b5563;">|</span><?php endif; ?>
            <?php if($pressEmail): ?><span>이메일: <a href="mailto:<?php echo e($pressEmail); ?>" style="color:#9ca3af;text-decoration:none;"><?php echo e($pressEmail); ?></a></span><?php endif; ?>
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
            <?php if($pressGrievanceManager): ?><span>고충처리인: <?php echo e($pressGrievanceManager); ?></span><?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="text-center text-sm">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(App\Models\Setting::get('site_name', 'Laraboard')); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

    <?php if(App\Models\Setting::get('custom_body_script')): ?>
    <?php echo App\Models\Setting::get('custom_body_script'); ?>

    <?php endif; ?>
</body>
</html>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/basic/footer.blade.php ENDPATH**/ ?>