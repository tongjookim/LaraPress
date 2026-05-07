<?php $__env->startSection('title', '플러그인'); ?>

<?php $__env->startSection('admin-content'); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">플러그인</h1>
    <span style="font-size:13px;color:#646970;"><?php echo e(count($plugins)); ?>개 플러그인</span>
</div>

<?php if(session('success')): ?>
    <div class="wp-notice" style="margin-bottom:16px;"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="wp-notice wp-notice-error" style="margin-bottom:16px;"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div style="background:#fff3cd;border-left:4px solid #ffc107;padding:12px 16px;border-radius:3px;font-size:13px;color:#856404;margin-bottom:20px;">
    플러그인 파일은 <code style="background:rgba(0,0,0,.06);padding:2px 5px;border-radius:2px;">/resources/plugins/</code> 디렉토리에 업로드하세요. 각 플러그인은 독립된 하위 디렉토리로 구성됩니다.
</div>

<?php if(empty($plugins)): ?>
    <div class="wp-widget">
        <div class="wp-widget-body" style="text-align:center;padding:60px;color:#8c8f94;">
            <svg style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p style="font-size:14px;">설치된 플러그인이 없습니다.</p>
            <p style="font-size:12px;margin-top:6px;"><code>/resources/plugins/</code>에 플러그인 디렉토리를 업로드하세요.</p>
        </div>
    </div>
<?php else: ?>
    <table class="wp-list-table" style="width:100%;">
        <thead>
            <tr>
                <th style="width:36px;"></th>
                <th>플러그인</th>
                <th style="width:90px;">버전</th>
                <th style="width:80px;">크기</th>
                <th style="width:160px;text-align:right;">작업</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $plugins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plugin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr id="plugin-row-<?php echo e($plugin['name']); ?>" style="<?php echo e($plugin['active'] ? 'background:#f0f7f0;' : ''); ?>">
                
                <td style="text-align:center;">
                    <?php if($plugin['active']): ?>
                        <span title="활성화됨" style="display:inline-block;width:10px;height:10px;background:#2d7a3a;border-radius:50%;"></span>
                    <?php else: ?>
                        <span title="비활성화됨" style="display:inline-block;width:10px;height:10px;background:#c3c4c7;border-radius:50%;"></span>
                    <?php endif; ?>
                </td>

                
                <td style="padding:14px 12px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <strong style="font-size:14px;color:#1d2327;"><?php echo e($plugin['label']); ?></strong>
                        <?php if($plugin['system']): ?>
                            <span style="font-size:10px;background:#f0f0f1;border:1px solid #c3c4c7;color:#646970;padding:1px 6px;border-radius:2px;">시스템</span>
                        <?php endif; ?>
                        <?php if($plugin['active']): ?>
                            <span style="font-size:10px;background:#d7edda;color:#2d7a3a;padding:1px 6px;border-radius:2px;">활성</span>
                        <?php else: ?>
                            <span style="font-size:10px;background:#f0f0f1;color:#8c8f94;padding:1px 6px;border-radius:2px;">비활성</span>
                        <?php endif; ?>
                    </div>
                    <?php if($plugin['description']): ?>
                        <p style="font-size:12px;color:#646970;margin:4px 0 0;"><?php echo e($plugin['description']); ?></p>
                    <?php endif; ?>
                    <p style="font-size:11px;color:#8c8f94;margin:3px 0 0;">
                        디렉토리: <code style="background:#f0f0f1;padding:1px 4px;border-radius:2px;"><?php echo e($plugin['name']); ?></code>
                        <?php if($plugin['author']): ?>
                            &nbsp;·&nbsp; 제작: <?php echo e(is_array($plugin['author']) ? ($plugin['author']['name'] ?? '') : $plugin['author']); ?>

                        <?php endif; ?>
                        <?php if($plugin['homepage']): ?>
                            &nbsp;·&nbsp; <a href="<?php echo e($plugin['homepage']); ?>" target="_blank" rel="noopener" style="color:#2271b1;">홈페이지</a>
                        <?php endif; ?>
                    </p>
                </td>

                
                <td style="font-size:13px;color:#646970;white-space:nowrap;">
                    <?php echo e($plugin['version'] ?: '—'); ?>

                </td>

                
                <td style="font-size:13px;color:#646970;white-space:nowrap;">
                    <?php echo e($plugin['size']); ?>

                </td>

                
                <td style="text-align:right;white-space:nowrap;padding:0 12px;">
                    <?php if($plugin['active']): ?>
                        <form method="POST" action="<?php echo e(route('admin.plugin.deactivate', $plugin['name'])); ?>" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="wp-btn wp-btn-secondary" style="font-size:12px;padding:4px 10px;">
                                비활성화
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('admin.plugin.activate', $plugin['name'])); ?>" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="wp-btn wp-btn-primary" style="font-size:12px;padding:4px 10px;">
                                활성화
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if($plugin['has_settings']): ?>
                        <a href="<?php echo e(route('admin.plugin.settings', $plugin['name'])); ?>"
                           class="wp-btn wp-btn-secondary" style="font-size:12px;padding:4px 10px;margin-left:4px;">
                            설정
                        </a>
                    <?php endif; ?>

                    <?php if(!$plugin['system']): ?>
                        <form method="POST" action="<?php echo e(route('admin.plugin.delete', $plugin['name'])); ?>" style="display:inline;margin-left:4px;"
                              onsubmit="return confirm('<?php echo e(addslashes($plugin['label'])); ?> 플러그인을 삭제하시겠습니까?\n디렉토리가 완전히 삭제됩니다.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" style="font-size:12px;padding:4px 10px;background:#fce8e6;border:1px solid #f5aca6;border-radius:3px;color:#d63638;cursor:pointer;">
                                삭제
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table></div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/plugins.blade.php ENDPATH**/ ?>