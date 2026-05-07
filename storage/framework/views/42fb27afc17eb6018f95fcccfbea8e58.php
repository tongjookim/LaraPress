
<?php echo $__env->make('skin.layout.cobalt-glow.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<div class="cobalt-bg-glow" aria-hidden="true"></div>

<?php $__topBanners = \App\Models\TopBanner::activeNow(); ?>
<?php if($__topBanners->isNotEmpty()): ?>
<div id="top-banner-wrap">
<?php $__currentLoopData = $__topBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div id="top-banner-<?php echo e($__b->id); ?>"
     data-banner-id="<?php echo e($__b->id); ?>"
     data-reshow="<?php echo e($__b->reshow_hours); ?>"
     style="display:none;position:relative;text-align:center;padding:9px 44px;background:<?php echo e($__b->bg_color); ?>;color:<?php echo e($__b->text_color); ?>;font-size:<?php echo e($__b->font_size); ?>px;font-weight:<?php echo e($__b->font_weight); ?>;line-height:1.4;">
    <?php if($__b->link_url): ?>
        <a href="<?php echo e($__b->link_url); ?>" style="color:<?php echo e($__b->text_color); ?>;text-decoration:none;"><?php echo e($__b->text); ?></a>
    <?php else: ?>
        <?php echo e($__b->text); ?>

    <?php endif; ?>
    <button onclick="closeTopBanner(<?php echo e($__b->id); ?>, <?php echo e($__b->reshow_hours); ?>)"
            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:18px;line-height:1;color:<?php echo e($__b->text_color); ?>;opacity:.7;"
            aria-label="닫기">×</button>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<script>
(function() {
    var bannerData = <?php echo json_encode($__topBanners->map(fn($b) => ['id' => $b->id, 'reshow' => $b->reshow_hours]), 512) ?>;
    bannerData.forEach(function(b) {
        var key = 'tb_closed_' + b.id;
        var closed = localStorage.getItem(key);
        var show = true;
        if (closed) {
            if (b.reshow === 0) {
                show = false;
            } else {
                var elapsed = (Date.now() - parseInt(closed)) / 3600000;
                if (elapsed < b.reshow) show = false;
            }
        }
        if (show) {
            document.getElementById('top-banner-' + b.id).style.display = 'block';
        }
    });
}());
function closeTopBanner(id, reshow) {
    document.getElementById('top-banner-' + id).style.display = 'none';
    if (reshow === 0) {
        localStorage.setItem('tb_closed_' + id, '0');
    } else {
        localStorage.setItem('tb_closed_' + id, Date.now().toString());
    }
}
</script>
<?php endif; ?>


<?php echo $__env->make('skin.layout.cobalt-glow.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php if(session('success')): ?>
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div style="background:rgba(59,130,246,0.08);border-left:3px solid #3b82f6;padding:12px 16px;border-radius:0 8px 8px 0;font-size:13px;color:rgba(255,255,255,0.85);">
        <?php echo e(session('success')); ?>

    </div>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:12px 16px;border-radius:0 8px 8px 0;font-size:13px;color:rgba(255,255,255,0.85);">
        <?php echo e(session('error')); ?>

    </div>
</div>
<?php endif; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <?php echo $__env->yieldContent('content'); ?>
</main>


<?php echo $__env->make('skin.layout.cobalt-glow.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/cobalt-glow/main.blade.php ENDPATH**/ ?>