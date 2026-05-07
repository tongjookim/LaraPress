<?php $__env->startSection('title', '카테고리 관리'); ?>

<?php $__env->startSection('admin-content'); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">카테고리</h1>
    <a href="<?php echo e(route('admin.category.create')); ?>" class="wp-btn wp-btn-primary">+ 새 카테고리</a>
</div>

<div class="wp-widget">
    <div class="wp-widget-header">전체 카테고리 (<?php echo e($categories->count()); ?>)</div>
    <div class="wp-widget-body" style="padding:0;">
        <div class="wp-table-wrap"><table class="wp-list-table">
            <thead>
                <tr>
                    <th style="width:40px;">순서</th>
                    <th>이름</th>
                    <th>슬러그</th>
                    <th>상위 카테고리</th>
                    <th>기사 수</th>
                    <th>상태</th>
                    <th style="width:160px;">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="color:#8c8f94;"><?php echo e($cat->order); ?></td>
                    <td>
                        <strong><?php echo e($cat->name); ?></strong>
                        <?php if($cat->description): ?>
                            <div style="font-size:12px;color:#8c8f94;margin-top:2px;"><?php echo e($cat->description); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-family:monospace;color:#646970;"><?php echo e($cat->slug); ?></td>
                    <td><?php echo e($cat->parent?->name ?? '—'); ?></td>
                    <td><?php echo e($cat->articles()->count()); ?></td>
                    <td>
                        <span class="wp-badge <?php echo e($cat->is_active ? 'wp-badge-active' : 'wp-badge-inactive'); ?>">
                            <?php echo e($cat->is_active ? '활성' : '비활성'); ?>

                        </span>
                    </td>
                    <td>
                        <a href="<?php echo e(route('admin.category.edit', $cat->id)); ?>" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                        <form method="POST" action="<?php echo e(route('admin.category.delete', $cat->id)); ?>" style="display:inline;"
                              onsubmit="return confirm('카테고리를 삭제하시겠습니까?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:32px;color:#8c8f94;">
                        등록된 카테고리가 없습니다.
                        <a href="<?php echo e(route('admin.category.create')); ?>" style="color:#2271b1;">첫 카테고리를 만들어보세요.</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/categories.blade.php ENDPATH**/ ?>