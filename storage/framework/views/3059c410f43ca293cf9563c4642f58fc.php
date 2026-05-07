<?php $__env->startSection('title', isset($page) ? '페이지 수정' : '페이지 생성'); ?>
<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title"><?php echo e(isset($page) ? '페이지 수정' : '새 페이지 생성'); ?></h1>

<div class="wp-widget" style="max-width:800px;">
    <div class="wp-widget-body">
        <form action="<?php echo e(isset($page) ? route('admin.page.update', $page->id) : route('admin.page.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php if(isset($page)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            <div class="wp-form-group">
                <label class="wp-form-label">페이지 제목 *</label>
                <input type="text" id="title" name="title" value="<?php echo e(old('title', $page->title ?? '')); ?>" required class="wp-form-input">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">URL 슬러그 *</label>
                <input type="text" id="slug" name="slug" value="<?php echo e(old('slug', $page->slug ?? '')); ?>" required class="wp-form-input">
                <p class="wp-form-help">영문, 숫자, 하이픈만 사용 · 접속: /page/<?php echo e(old('slug', $page->slug ?? 'your-slug')); ?></p>
                <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">페이지 내용 *</label>
                <textarea name="content" id="se2_content" style="width:100%;height:480px;display:none;"><?php echo e(old('content', $page->content ?? '')); ?></textarea>
                <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">정렬 순서</label>
                <input type="number" name="order" value="<?php echo e(old('order', $page->order ?? 0)); ?>" min="0" class="wp-form-input" style="max-width:120px;">
            </div>

            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $page->is_active ?? true) ? 'checked' : ''); ?>>
                    활성화 (페이지 공개)
                </label>
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;">
                <button type="submit" class="wp-btn wp-btn-primary"><?php echo e(isset($page) ? '수정 저장' : '페이지 생성'); ?></button>
                <a href="<?php echo e(route('admin.pages')); ?>" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>

<?php echo $__env->make('partials.smarteditor', ['editorId' => 'se2_content', 'editorHeight' => 480], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>
document.getElementById('title').addEventListener('input', function() {
    if (!document.getElementById('slug').value || <?php echo e(isset($page) ? 'false' : 'true'); ?>) {
        document.getElementById('slug').value = this.value.toLowerCase()
            .replace(/[^a-z0-9가-힣\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/page-form.blade.php ENDPATH**/ ?>