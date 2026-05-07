<?php $__env->startPush('skin-css'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/views/skin/board/' . $board->skin . '/style.css']); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('title', " - {$board->board_name}"); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo e($board->board_name); ?></h1>
    <p class="text-gray-500">총 <?php echo e($posts->total()); ?>개의 게시글</p>
</div>

<!-- Search Bar -->
<div class="mb-6">
    <form method="GET" action="<?php echo e(route('bbs.index', $board->board_id)); ?>" class="flex gap-2">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>"
               placeholder="제목 또는 내용으로 검색..."
               class="flex-1 px-4 py-3 site-input">
        <button type="submit" class="px-6 py-3 rounded-lg font-medium text-white site-primary-btn">
            검색
        </button>
        <?php if(request('search')): ?>
        <a href="<?php echo e(route('bbs.index', $board->board_id)); ?>" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
            초기화
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Posts Grid -->
<div class="space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition card-hover overflow-hidden border border-gray-100">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <?php if($post->is_notice): ?>
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-2"
                          style="background:var(--site-primary-light);color:var(--site-primary);">
                        공지
                    </span>
                    <?php endif; ?>

                    <h2 class="text-xl font-bold mb-3">
                        <a href="<?php echo e(route('bbs.show', [$board->board_id, $post->id])); ?>"
                           class="site-nav-link hover:underline">
                            <?php echo e($post->title); ?>

                        </a>
                    </h2>

                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <?php echo e($post->user->name); ?>

                        </span>
                        <span><?php echo e($post->created_at->format('Y.m.d H:i')); ?></span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <?php echo e(number_format($post->view_count)); ?>

                        </span>
                        <?php if($board->use_comment): ?>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <?php echo e($post->comments_count); ?>

                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-16 bg-white rounded-xl">
        <p class="text-gray-400 text-lg">등록된 게시글이 없습니다.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<div class="mt-8">
    <?php echo e($posts->appends(['search' => request('search')])->links()); ?>

</div>

<!-- Write Button: author 이상만 표시 -->
<?php if(auth()->user()->hasMinRole('author')): ?>
<div class="mt-8 flex justify-end">
    <a href="<?php echo e(route('bbs.create', $board->board_id)); ?>"
       class="px-6 py-3 rounded-lg font-medium text-white site-primary-btn">
        글쓰기
    </a>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('skin.layout.basic.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/skin/board/basic/list.blade.php ENDPATH**/ ?>