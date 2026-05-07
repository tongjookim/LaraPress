<?php $__env->startSection('title', '기사 댓글 관리'); ?>

<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">기사 댓글 관리</h1>

<?php if(session('success')): ?>
    <div class="wp-notice" style="margin-bottom:16px;"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="wp-notice wp-notice-error" style="margin-bottom:16px;"><?php echo e(session('error')); ?></div>
<?php endif; ?>


<div style="display:flex;gap:0;border-bottom:2px solid #c3c4c7;margin-bottom:20px;flex-wrap:wrap;">
    <?php
    $tabs = [
        'all'      => ['전체',       $counts['all']],
        'pending'  => ['승인 대기',  $counts['pending']],
        'approved' => ['승인됨',     $counts['approved']],
        'trashed'  => ['휴지통',     $counts['trashed']],
    ];
    ?>
    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label, $count]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('admin.article-comments', array_filter(['tab'=>$key==='all'?null:$key, 'q'=>$search?:null]))); ?>"
       style="padding:8px 16px;font-size:13px;font-weight:600;text-decoration:none;border-bottom:2px solid <?php echo e($tab===$key ? '#2271b1' : 'transparent'); ?>;margin-bottom:-2px;color:<?php echo e($tab===$key ? '#2271b1' : '#646970'); ?>;">
        <?php echo e($label); ?>

        <?php if($count > 0): ?>
        <span style="display:inline-block;min-width:18px;padding:0 5px;font-size:11px;font-weight:700;border-radius:9px;text-align:center;line-height:18px;margin-left:4px;
                     background:<?php echo e($key==='pending' ? '#d63638' : '#c3c4c7'); ?>;color:#fff;"><?php echo e($count); ?></span>
        <?php endif; ?>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="<?php echo e(route('admin.article-comments')); ?>" style="display:flex;gap:6px;flex:1;min-width:200px;">
        <?php if($tab !== 'all'): ?> <input type="hidden" name="tab" value="<?php echo e($tab); ?>"> <?php endif; ?>
        <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="내용, 작성자, 기사 제목 검색…"
               class="wp-form-input" style="flex:1;max-width:320px;">
        <button type="submit" class="wp-btn wp-btn-secondary">검색</button>
        <?php if($search): ?>
        <a href="<?php echo e(route('admin.article-comments', $tab!=='all'?['tab'=>$tab]:[])); ?>" class="wp-btn wp-btn-secondary">✕ 초기화</a>
        <?php endif; ?>
    </form>

    <form id="bulk-form" method="POST" action="<?php echo e(route('admin.article-comment.bulk')); ?>" style="display:flex;gap:6px;align-items:center;">
        <?php echo csrf_field(); ?>
        <select name="action" class="wp-form-input wp-form-select" style="width:auto;height:34px;font-size:13px;">
            <option value="">일괄 처리 선택</option>
            <?php if($tab !== 'trashed'): ?>
            <option value="approve">승인</option>
            <option value="unapprove">승인 취소</option>
            <option value="delete">삭제</option>
            <?php endif; ?>
            <?php if($tab === 'trashed'): ?>
            <option value="restore">복원</option>
            <option value="force_delete">영구 삭제</option>
            <?php endif; ?>
        </select>
        <button type="button" onclick="submitBulk()" class="wp-btn wp-btn-secondary">적용</button>
    </form>
</div>


<div class="wp-widget">
    <div class="wp-widget-body" style="padding:0;">
        <?php if($comments->isEmpty()): ?>
        <p style="padding:32px;text-align:center;color:#8c8f94;font-size:13px;">
            <?php if($search): ?> 검색 결과가 없습니다.
            <?php elseif($tab==='pending'): ?> 승인 대기 중인 댓글이 없습니다.
            <?php elseif($tab==='trashed'): ?> 휴지통이 비어 있습니다.
            <?php else: ?> 등록된 댓글이 없습니다. <?php endif; ?>
        </p>
        <?php else: ?>
        <table class="wp-list-table" style="table-layout:fixed;">
            <colgroup>
                <col style="width:36px;">
                <col style="width:130px;">
                <col>
                <col style="width:180px;">
                <col style="width:76px;">
                <col style="width:100px;">
                <col style="width:120px;">
            </colgroup>
            <thead>
                <tr>
                    <th style="padding:10px 12px;">
                        <input type="checkbox" id="check-all" onchange="toggleAll(this)">
                    </th>
                    <th>작성자</th>
                    <th>댓글 내용</th>
                    <th>기사</th>
                    <th>상태</th>
                    <th>작성일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr id="row-<?php echo e($comment->id); ?>" style="<?php echo e($comment->trashed() ? 'opacity:.6;' : ''); ?>">
                    
                    <td>
                        <input type="checkbox" class="row-check" name="ids[]"
                               form="bulk-form" value="<?php echo e($comment->id); ?>">
                    </td>

                    
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <?php if($comment->user?->profile_image): ?>
                                <img src="<?php echo e($comment->user->profile_image); ?>" style="width:24px;height:24px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                            <?php else: ?>
                                <span style="width:24px;height:24px;border-radius:50%;background:#e0e0e0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#555;flex-shrink:0;">
                                    <?php echo e(mb_substr($comment->user?->name ?? '?', 0, 1)); ?>

                                </span>
                            <?php endif; ?>
                            <div style="min-width:0;">
                                <p style="font-size:12px;font-weight:600;color:#1d2327;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($comment->user?->name ?? '(탈퇴)'); ?></p>
                                <p style="font-size:11px;color:#8c8f94;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($comment->user?->email ?? ''); ?></p>
                            </div>
                        </div>
                    </td>

                    
                    <td>
                        <?php if($comment->parent_id): ?>
                        <span style="font-size:10px;color:#8c8f94;display:block;margin-bottom:2px;">↳ 답글</span>
                        <?php endif; ?>
                        <p class="comment-text-<?php echo e($comment->id); ?>"
                           style="font-size:13px;color:#1d2327;line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;"><?php echo e($comment->content); ?></p>
                        
                        <div id="edit-area-<?php echo e($comment->id); ?>" style="display:none;margin-top:6px;">
                            <form method="POST" action="<?php echo e(route('admin.article-comment.update', $comment->id)); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <textarea name="content" rows="3"
                                    style="width:100%;padding:6px 8px;font-size:12px;border:1px solid #2271b1;border-radius:3px;resize:vertical;font-family:inherit;"><?php echo e($comment->content); ?></textarea>
                                <div style="display:flex;gap:6px;margin-top:4px;">
                                    <button type="submit" class="wp-btn wp-btn-primary wp-btn-sm">저장</button>
                                    <button type="button" onclick="cancelEdit(<?php echo e($comment->id); ?>)" class="wp-btn wp-btn-secondary wp-btn-sm">취소</button>
                                </div>
                            </form>
                        </div>
                    </td>

                    
                    <td>
                        <?php if($comment->article): ?>
                        <a href="<?php echo e(route('news.show', $comment->article->slug)); ?>" target="_blank"
                           style="font-size:12px;color:#2271b1;text-decoration:none;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            <?php echo e($comment->article->title); ?>

                        </a>
                        <?php else: ?>
                        <span style="font-size:12px;color:#8c8f94;">(삭제된 기사)</span>
                        <?php endif; ?>
                    </td>

                    
                    <td>
                        <?php if($comment->trashed()): ?>
                            <span class="wp-badge wp-badge-inactive">삭제됨</span>
                        <?php elseif($comment->is_approved): ?>
                            <span class="wp-badge wp-badge-active">승인</span>
                        <?php else: ?>
                            <span class="wp-badge wp-badge-pending">대기</span>
                        <?php endif; ?>
                    </td>

                    
                    <td>
                        <span style="font-size:12px;color:#646970;"><?php echo e($comment->created_at->format('Y-m-d')); ?></span>
                        <br>
                        <span style="font-size:11px;color:#8c8f94;"><?php echo e($comment->created_at->format('H:i')); ?></span>
                    </td>

                    
                    <td>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <?php if($comment->trashed()): ?>
                                
                                <form method="POST" action="<?php echo e(route('admin.article-comment.restore', $comment->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" style="width:100%;">복원</button>
                                </form>
                                
                                <form method="POST" action="<?php echo e(route('admin.article-comment.force-delete', $comment->id)); ?>"
                                      onsubmit="return confirm('영구 삭제하시겠습니까? 복원이 불가능합니다.');">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" style="width:100%;">영구삭제</button>
                                </form>
                            <?php else: ?>
                                
                                <form method="POST" action="<?php echo e(route('admin.article-comment.approve', $comment->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            class="wp-btn wp-btn-sm <?php echo e($comment->is_approved ? 'wp-btn-warning' : 'wp-btn-primary'); ?>"
                                            style="width:100%;">
                                        <?php echo e($comment->is_approved ? '승인 취소' : '승인'); ?>

                                    </button>
                                </form>
                                
                                <button type="button"
                                        onclick="toggleEdit(<?php echo e($comment->id); ?>)"
                                        class="wp-btn wp-btn-secondary wp-btn-sm" style="width:100%;">수정</button>
                                
                                <form method="POST" action="<?php echo e(route('admin.article-comment.delete', $comment->id)); ?>"
                                      onsubmit="return confirm('댓글을 삭제하시겠습니까?');">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" style="width:100%;">삭제</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table></div>
        <?php endif; ?>
    </div>
</div>


<?php if($comments->hasPages()): ?>
<div style="margin-top:16px;display:flex;justify-content:center;">
    <?php echo e($comments->links()); ?>

</div>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── 전체 선택/해제 ───────────────────────────────────────────
function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(function(cb) { cb.checked = master.checked; });
}
document.querySelectorAll('.row-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var all  = document.querySelectorAll('.row-check');
        var chk  = document.querySelectorAll('.row-check:checked');
        document.getElementById('check-all').indeterminate = chk.length > 0 && chk.length < all.length;
        document.getElementById('check-all').checked = chk.length === all.length;
    });
});

// ── 일괄 처리 실행 ───────────────────────────────────────────
function submitBulk() {
    var action = document.querySelector('[name=action]').value;
    if (!action) { alert('처리 방식을 선택하세요.'); return; }
    var checked = document.querySelectorAll('.row-check:checked');
    if (!checked.length) { alert('댓글을 선택하세요.'); return; }
    if (action === 'force_delete' && !confirm(checked.length + '개 댓글을 영구 삭제하시겠습니까?')) return;
    document.getElementById('bulk-form').submit();
}

// ── 인라인 수정 토글 ─────────────────────────────────────────
function toggleEdit(id) {
    var area = document.getElementById('edit-area-' + id);
    var text = document.querySelector('.comment-text-' + id);
    var open = area.style.display === 'none';
    area.style.display = open ? 'block' : 'none';
    text.style.display = open ? 'none'  : '';
}
function cancelEdit(id) {
    document.getElementById('edit-area-' + id).style.display = 'none';
    document.querySelector('.comment-text-' + id).style.display = '';
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/article-comments.blade.php ENDPATH**/ ?>