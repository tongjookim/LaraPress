<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title><![CDATA[<?php echo e($title); ?>]]></title>
        <link><?php echo e(url('/')); ?></link>
        <description><![CDATA[<?php echo e($description); ?>]]></description>
        <language>ko</language>
        <atom:link href="<?php echo e(url('/feed')); ?>" rel="self" type="application/rss+xml"/>
        <lastBuildDate><?php echo e(now()->toRfc1123String()); ?></lastBuildDate>
        <generator>Laraboard CMS</generator>

        <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <item>
            <title><![CDATA[<?php echo e($article->title); ?>]]></title>
            <link><?php echo e(url('/news/' . rawurlencode($article->slug))); ?></link>
            <guid isPermaLink="true"><?php echo e(url('/news/' . rawurlencode($article->slug))); ?></guid>
            <pubDate><?php echo e($article->published_at->toRfc1123String()); ?></pubDate>
            <?php if($article->user): ?>
            <dc:creator><![CDATA[<?php echo e($article->user->name); ?>]]></dc:creator>
            <?php endif; ?>
            <?php if($article->category): ?>
            <category><![CDATA[<?php echo e($article->category->name); ?>]]></category>
            <?php endif; ?>
            <?php if($article->excerpt): ?>
            <description><![CDATA[<?php echo e($article->excerpt); ?>]]></description>
            <?php endif; ?>
            <?php if($includeContent && $article->content): ?>
            <content:encoded><![CDATA[<?php echo $article->content; ?>]]></content:encoded>
            <?php endif; ?>
            <?php if($article->thumbnail): ?>
            <enclosure url="<?php echo e($article->thumbnail); ?>" type="image/jpeg" length="0"/>
            <?php endif; ?>
        </item>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </channel>
</rss>
<?php /**PATH /home/laraboard/www/resources/views/feed.blade.php ENDPATH**/ ?>