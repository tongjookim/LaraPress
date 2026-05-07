<?php $__env->startSection('title', '회원 수정'); ?>
<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">회원 정보 수정 <span style="font-size:13px;color:#646970;font-weight:400;"><?php echo e($user->username); ?></span></h1>

<div class="wp-widget" style="max-width:700px;">
    <div class="wp-widget-body">
        <form action="<?php echo e(route('admin.user.update', $user->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            
            <div style="font-size:12px;font-weight:700;color:#8c8f94;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid #f0f0f1;">기본 정보</div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">아이디 *</label>
                    <input type="text" name="username" value="<?php echo e(old('username', $user->username)); ?>" required class="wp-form-input">
                    <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">이름 *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="wp-form-input">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">이메일 *</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required class="wp-form-input">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">새 비밀번호</label>
                    <input type="password" name="password" class="wp-form-input" placeholder="변경 시에만 입력">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">역할</label>
                    <select name="role" class="wp-form-input wp-form-select">
                        <option value="subscriber" <?php echo e(old('role', $user->role) == 'subscriber' ? 'selected' : ''); ?>>구독자</option>
                        <option value="author"     <?php echo e(old('role', $user->role) == 'author'     ? 'selected' : ''); ?>>작성자</option>
                        <option value="editor"     <?php echo e(old('role', $user->role) == 'editor'     ? 'selected' : ''); ?>>편집자</option>
                        <option value="admin"      <?php echo e(old('role', $user->role) == 'admin'      ? 'selected' : ''); ?>>관리자</option>
                    </select>
                </div>
                <div class="wp-form-group" style="padding-top:28px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" <?php echo e($user->is_active ? 'checked' : ''); ?>>
                        계정 활성화
                    </label>
                </div>
            </div>

            
            <div style="font-size:12px;font-weight:700;color:#8c8f94;text-transform:uppercase;letter-spacing:.05em;margin:20px 0 12px;padding-bottom:6px;border-bottom:1px solid #f0f0f1;">프로필 &amp; 작성자 박스</div>

            <div class="wp-form-group">
                <label class="wp-form-label">프로필 이미지</label>
                <?php if($user->profile_image): ?>
                <div style="margin-bottom:8px;display:flex;align-items:center;gap:12px;">
                    <img src="<?php echo e($user->profile_image); ?>" alt="프로필" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid #c3c4c7;">
                    <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#d63638;cursor:pointer;">
                        <input type="checkbox" name="clear_profile_image" value="1"> 이미지 삭제
                    </label>
                </div>
                <?php endif; ?>
                <input type="file" name="profile_image_file" accept="image/*" class="wp-form-input" style="padding:4px;">
                <p class="wp-form-help">PNG, JPG, WebP · 최대 2MB · 권장 크기: 200×200px 이상</p>
                <?php $__errorArgs = ['profile_image_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">소개 (Bio)</label>
                <textarea name="bio" rows="4" class="wp-form-input wp-form-textarea" placeholder="작성자 소개 문구 (기사 하단 작성자 박스에 표시됩니다)"><?php echo e(old('bio', $user->bio)); ?></textarea>
                <p class="wp-form-help">최대 1000자</p>
                <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="wp-form-group">
                <label style="display:flex;align-items:flex-start;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="author_box_enabled" value="1" <?php echo e($user->author_box_enabled ? 'checked' : ''); ?> style="margin-top:2px;">
                    <div>
                        <span style="font-weight:600;color:#1d2327;">기사 하단 작성자 박스 표시</span>
                        <p class="wp-form-help" style="margin-top:2px;">활성화 시 이 회원이 작성한 기사 본문 하단에 작성자 소개 박스가 표시됩니다.</p>
                    </div>
                </label>
            </div>

            
            <div style="font-size:12px;font-weight:700;color:#8c8f94;text-transform:uppercase;letter-spacing:.05em;margin:20px 0 12px;padding-bottom:6px;border-bottom:1px solid #f0f0f1;">소셜 링크</div>

            <?php
            $socials = [
                'social_facebook'  => ['label'=>'Facebook',   'placeholder'=>'https://facebook.com/username',    'icon'=>'f'],
                'social_x'         => ['label'=>'X (Twitter)','placeholder'=>'https://x.com/username',            'icon'=>'𝕏'],
                'social_instagram' => ['label'=>'Instagram',  'placeholder'=>'https://instagram.com/username',   'icon'=>'i'],
                'social_linkedin'  => ['label'=>'LinkedIn',   'placeholder'=>'https://linkedin.com/in/username', 'icon'=>'in'],
                'social_website'   => ['label'=>'홈페이지',    'placeholder'=>'https://example.com',              'icon'=>'🌐'],
                'social_blog'      => ['label'=>'블로그',      'placeholder'=>'https://blog.example.com',         'icon'=>'📝'],
                'social_pixabay'   => ['label'=>'Pixabay',    'placeholder'=>'https://pixabay.com/users/username','icon'=>'px'],
                'social_wikipedia' => ['label'=>'Wikipedia',  'placeholder'=>'https://ko.wikipedia.org/wiki/...',  'icon'=>'W'],
                'social_email'     => ['label'=>'공개 이메일', 'placeholder'=>'public@example.com',               'icon'=>'@'],
            ];
            ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                <?php $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="wp-form-group">
                    <label class="wp-form-label"><?php echo e($meta['label']); ?></label>
                    <?php if($field === 'social_email'): ?>
                    <input type="email" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $user->$field)); ?>" class="wp-form-input" placeholder="<?php echo e($meta['placeholder']); ?>">
                    <?php else: ?>
                    <input type="url" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $user->$field)); ?>" class="wp-form-input" placeholder="<?php echo e($meta['placeholder']); ?>">
                    <?php endif; ?>
                    <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;display:flex;gap:8px;">
                <button type="submit" class="wp-btn wp-btn-primary">저장</button>
                <a href="<?php echo e(route('admin.users')); ?>" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/user-edit.blade.php ENDPATH**/ ?>