<?php $__env->startSection('title', '회원 관리'); ?>
<?php $__env->startSection('admin-content'); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">회원 관리 <span style="font-size:13px;color:#646970;font-weight:400;">(<?php echo e($users->total()); ?>명)</span></h1>
    <a href="<?php echo e(route('admin.user.create')); ?>" class="wp-btn wp-btn-primary">+ 회원 추가</a>
</div>

<div class="wp-widget">
    <div class="wp-table-wrap"><table class="wp-list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>아이디</th>
                <th>이름</th>
                <th>이메일</th>
                <th>권한</th>
                <th>상태</th>
                <th>가입일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($user->id); ?></td>
                <td style="font-weight:600;"><?php echo e($user->username); ?></td>
                <td><?php echo e($user->name); ?></td>
                <td><?php echo e($user->email); ?></td>
                <td>
                    <?php
                        $roleStyles = [
                            'subscriber' => 'background:#f0f0f1;color:#50575e;',
                            'author'     => 'background:#dcfce7;color:#166534;',
                            'editor'     => 'background:#dbeafe;color:#1d4ed8;',
                            'admin'      => 'background:#fef3c7;color:#92400e;',
                        ];
                    ?>
                    <span class="wp-badge" style="<?php echo e($roleStyles[$user->role] ?? 'background:#f0f0f1;color:#50575e;'); ?>"><?php echo e($user->roleLabel()); ?></span>
                </td>
                <td>
                    <?php if($user->is_active): ?>
                        <span class="wp-badge wp-badge-active">활성</span>
                    <?php else: ?>
                        <span class="wp-badge wp-badge-inactive">비활성</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($user->created_at->format('Y-m-d')); ?></td>
                <td>
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        <a href="<?php echo e(route('admin.user.edit', $user->id)); ?>" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                        <?php if($user->id !== auth()->id()): ?>
                        <form action="<?php echo e(route('admin.user.role', $user->id)); ?>" method="POST" style="display:inline;gap:0;"><?php echo csrf_field(); ?>
                            <select name="role" onchange="this.form.submit()" style="font-size:12px;padding:2px 4px;border:1px solid #8c8f94;border-radius:3px;cursor:pointer;background:#fff;">
                                <option value="subscriber" <?php echo e($user->role=='subscriber'?'selected':''); ?>>구독자</option>
                                <option value="author"     <?php echo e($user->role=='author'    ?'selected':''); ?>>작성자</option>
                                <option value="editor"     <?php echo e($user->role=='editor'    ?'selected':''); ?>>편집자</option>
                                <option value="admin"      <?php echo e($user->role=='admin'     ?'selected':''); ?>>관리자</option>
                            </select>
                        </form>
                        <?php endif; ?>
                        <form action="<?php echo e(route('admin.user.toggle', $user->id)); ?>" method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                            <button class="wp-btn wp-btn-sm <?php echo e($user->is_active ? 'wp-btn-warning' : 'wp-btn-primary'); ?>"><?php echo e($user->is_active ? '비활성' : '활성'); ?></button>
                        </form>
                        <?php if($user->id !== auth()->id()): ?>
                        <form action="<?php echo e(route('admin.user.delete', $user->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" style="text-align:center;padding:30px;color:#8c8f94;">등록된 회원이 없습니다.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>

<div style="margin-top:16px;"><?php echo e($users->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/users.blade.php ENDPATH**/ ?>