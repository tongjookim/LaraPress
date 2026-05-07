<?php $__env->startSection('title', '게시판 관리'); ?>
<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">게시판 관리 <span style="font-size:13px;color:#646970;font-weight:400;">(<?php echo e($boards->count()); ?>개)</span></h1>

<div style="margin-bottom:16px;">
    <a href="<?php echo e(route('admin.board.create')); ?>" class="wp-btn wp-btn-primary">+ 게시판 생성</a>
</div>

<div class="wp-widget">
    <div class="wp-table-wrap"><table class="wp-list-table">
        <thead>
            <tr>
                <th>순서</th>
                <th>게시판 ID</th>
                <th>게시판명</th>
                <th>스킨</th>
                <th>페이지당 글 수</th>
                <th>게시글</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $boards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $board): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($board->order); ?></td>
                <td style="font-family:monospace;font-size:12px;"><?php echo e($board->board_id); ?></td>
                <td style="font-weight:600;">
                    <a href="<?php echo e(route('bbs.index', $board->board_id)); ?>" style="color:#2271b1;text-decoration:none;"><?php echo e($board->board_name); ?></a>
                </td>
                <td><?php echo e($board->skin); ?></td>
                <td><?php echo e($board->posts_per_page); ?></td>
                <td><?php echo e($board->posts->count()); ?>개</td>
                <td>
                    <a href="<?php echo e(route('admin.board.edit', $board->id)); ?>" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                    <form action="<?php echo e(route('admin.board.delete', $board->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;color:#8c8f94;">등록된 게시판이 없습니다.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/boards.blade.php ENDPATH**/ ?>