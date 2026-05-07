<?php
    $__skin = 'newyorktimes-style';
?>

<?php echo $__env->make('skin.layout.newyorktimes-style.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('skin.layout.newyorktimes-style.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<main style="background:#fff;min-height:60vh;">
    <?php echo $__env->yieldContent('content'); ?>
</main>

<?php echo $__env->make('skin.layout.newyorktimes-style.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
<?php /**PATH /home/laraboard/www/resources/views/skin/layout/newyorktimes-style/main.blade.php ENDPATH**/ ?>