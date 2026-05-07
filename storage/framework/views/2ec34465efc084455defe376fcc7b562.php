<?php $__env->startSection('title', isset($board) ? '게시판 수정' : '게시판 생성'); ?>
<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title"><?php echo e(isset($board) ? '게시판 수정' : '게시판 생성'); ?></h1>

<div class="wp-widget" style="max-width:600px;">
    <div class="wp-widget-body">
        <form action="<?php echo e(isset($board) ? route('admin.board.update', $board->id) : route('admin.board.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php if(isset($board)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            <?php if (! (isset($board))): ?>
            <div class="wp-form-group">
                <label class="wp-form-label">게시판 ID *</label>
                <input type="text" name="board_id" value="<?php echo e(old('board_id')); ?>" required class="wp-form-input">
                <p class="wp-form-help">영문, 숫자, 하이픈만 사용 (예: free, notice, qna)</p>
                <?php $__errorArgs = ['board_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php endif; ?>

            <div class="wp-form-group">
                <label class="wp-form-label">게시판 이름 *</label>
                <input type="text" name="board_name" value="<?php echo e(old('board_name', $board->board_name ?? '')); ?>" required class="wp-form-input">
                <?php $__errorArgs = ['board_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">스킨 *</label>
                <select name="skin" class="wp-form-input wp-form-select">
                    <?php $skins = ['basic' => 'Basic']; ?>
                    <?php $__currentLoopData = $skins ?? ['basic' => 'Basic']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('skin', $board->skin ?? 'basic') == $key ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">페이지당 글 수 *</label>
                <input type="number" name="posts_per_page" value="<?php echo e(old('posts_per_page', $board->posts_per_page ?? 15)); ?>" min="5" max="100" required class="wp-form-input" style="max-width:120px;">
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;">
                <button type="submit" class="wp-btn wp-btn-primary"><?php echo e(isset($board) ? '수정 저장' : '게시판 생성'); ?></button>
                <a href="<?php echo e(route('admin.boards')); ?>" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/board-form.blade.php ENDPATH**/ ?>