<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc><?php echo e(url('/')); ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc><?php echo e(url('/news')); ?></loc>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>

    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(url('/news?category=' . $cat->slug)); ?></loc>
        <lastmod><?php echo e($cat->updated_at->toAtomString()); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(url('/news/' . rawurlencode($article->slug))); ?></loc>
        <lastmod><?php echo e(($article->updated_at ?? $article->published_at)->toAtomString()); ?></lastmod>
        <changefreq><?php echo e($freq); ?></changefreq>
        <priority><?php echo e($priority); ?></priority>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(url('/page/' . $page->slug)); ?></loc>
        <lastmod><?php echo e($page->updated_at->toAtomString()); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</urlset>
<?php /**PATH /home/laraboard/www/resources/views/sitemap.blade.php ENDPATH**/ ?>