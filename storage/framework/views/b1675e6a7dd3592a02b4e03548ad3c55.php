<?php $__env->startSection('title', '페이지 관리'); ?>
<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">페이지 관리</h1>

<div style="margin-bottom:16px;">
    <a href="<?php echo e(route('admin.page.create')); ?>" class="wp-btn wp-btn-primary">+ 새 페이지</a>
</div>

<div class="wp-widget">
    <div class="wp-table-wrap"><table class="wp-list-table">
        <thead>
            <tr>
                <th>순서</th>
                <th>제목</th>
                <th>URL</th>
                <th>상태</th>
                <th>등록일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($page->order); ?></td>
                <td style="font-weight:600;"><?php echo e($page->title); ?></td>
                <td><a href="<?php echo e(route('page.show', $page->slug)); ?>" target="_blank" style="color:#2271b1;">/page/<?php echo e($page->slug); ?></a></td>
                <td>
                    <?php if($page->is_active): ?>
                        <span class="wp-badge wp-badge-active">활성</span>
                    <?php else: ?>
                        <span class="wp-badge wp-badge-inactive">비활성</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($page->created_at->format('Y-m-d')); ?></td>
                <td>
                    <a href="<?php echo e(route('admin.page.edit', $page->id)); ?>" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                    <form action="<?php echo e(route('admin.page.delete', $page->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" style="text-align:center;padding:30px;color:#8c8f94;">등록된 페이지가 없습니다.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/pages.blade.php ENDPATH**/ ?>