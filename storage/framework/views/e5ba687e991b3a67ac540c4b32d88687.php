<?php $__env->startSection('title', '탑배너 관리'); ?>

<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">탑배너 관리</h1>

<?php if(session('success')): ?>
    <div class="wp-notice" style="margin-bottom:16px;"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;">

    
    <div class="wp-widget">
        <div class="wp-widget-header" style="display:flex;align-items:center;justify-content:space-between;">
            <span>등록된 탑배너</span>
            <span style="font-size:12px;font-weight:400;color:#646970;">드래그하여 노출 순서 변경</span>
        </div>
        <div class="wp-widget-body" style="padding:0;">
            <?php if($banners->isEmpty()): ?>
                <p style="padding:24px;text-align:center;color:#8c8f94;font-size:13px;">
                    등록된 탑배너가 없습니다. 오른쪽에서 추가하세요.
                </p>
            <?php else: ?>
            <ul id="banner-sortable" style="list-style:none;margin:0;padding:0;">
                <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li data-id="<?php echo e($banner->id); ?>"
                    style="border-bottom:1px solid #f0f0f1;background:#fff;">
                    
                    <div style="padding:10px 14px 0;display:flex;align-items:center;gap:8px;">
                        <span style="color:#c3c4c7;cursor:grab;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 6h8M8 12h8M8 18h8" stroke-width="2.5" stroke-linecap="round"/></svg>
                        </span>
                        
                        <div style="flex:1;border-radius:4px;padding:6px 12px;background:<?php echo e($banner->bg_color); ?>;color:<?php echo e($banner->text_color); ?>;font-size:<?php echo e($banner->font_size); ?>px;font-weight:<?php echo e($banner->font_weight); ?>;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                            <?php if($banner->link_url): ?>
                                <a href="<?php echo e($banner->link_url); ?>" style="color:<?php echo e($banner->text_color); ?>;text-decoration:none;" onclick="return false;"><?php echo e($banner->text); ?></a>
                            <?php else: ?>
                                <?php echo e($banner->text); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div style="padding:6px 14px 10px 36px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <?php if($banner->is_active): ?>
                            <span class="wp-badge wp-badge-active">활성</span>
                        <?php else: ?>
                            <span class="wp-badge wp-badge-inactive">비활성</span>
                        <?php endif; ?>
                        <?php if($banner->start_at || $banner->end_at): ?>
                        <span style="font-size:11px;color:#8c8f94;">
                            <?php echo e($banner->start_at ? $banner->start_at->format('Y-m-d') : '∞'); ?>

                            ~
                            <?php echo e($banner->end_at   ? $banner->end_at->format('Y-m-d')   : '∞'); ?>

                        </span>
                        <?php endif; ?>
                        <span style="font-size:11px;color:#8c8f94;">
                            닫기 후 <?php echo e($banner->reshow_hours == 0 ? '다시 안 보임' : $banner->reshow_hours.'시간 후 재표시'); ?>

                        </span>
                        <div style="margin-left:auto;display:flex;gap:4px;">
                            <button type="button"
                                    onclick="openEditModal(<?php echo e($banner->id); ?>, <?php echo e(json_encode($banner->text)); ?>, <?php echo e(json_encode($banner->link_url ?? '')); ?>, '<?php echo e($banner->text_color); ?>', '<?php echo e($banner->bg_color); ?>', <?php echo e($banner->font_size); ?>, '<?php echo e($banner->font_weight); ?>', '<?php echo e($banner->start_at ? $banner->start_at->format('Y-m-d\TH:i') : ''); ?>', '<?php echo e($banner->end_at ? $banner->end_at->format('Y-m-d\TH:i') : ''); ?>', <?php echo e($banner->reshow_hours); ?>, <?php echo e($banner->is_active ? 'true' : 'false'); ?>)"
                                    class="wp-btn wp-btn-secondary wp-btn-sm">수정</button>
                            <form method="POST" action="<?php echo e(route('admin.top-banner.delete', $banner->id)); ?>" onsubmit="return confirm('삭제하시겠습니까?');" style="display:inline;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                            </form>
                        </div>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>

    
    <div>
        <div class="wp-widget">
            <div class="wp-widget-header">탑배너 추가</div>
            <div class="wp-widget-body">
                <form method="POST" action="<?php echo e(route('admin.top-banner.store')); ?>" id="add-form">
                    <?php echo csrf_field(); ?>

                    <div class="wp-form-group">
                        <label class="wp-form-label">배너 텍스트 *</label>
                        <input type="text" name="text" class="wp-form-input" required maxlength="500"
                               placeholder="📢 5초면 내 사이트를 뚝딱!">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">링크 URL</label>
                        <input type="text" name="link_url" class="wp-form-input" maxlength="500"
                               placeholder="https://example.com (없으면 비워두세요)">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;">
                        <div class="wp-form-group">
                            <label class="wp-form-label">배경 색상</label>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <input type="color" name="bg_color" value="#1d4ed8" id="add-bg-color"
                                       style="width:36px;height:34px;padding:2px;border:1px solid #8c8f94;border-radius:3px;cursor:pointer;">
                                <input type="text" id="add-bg-hex" value="#1d4ed8"
                                       style="flex:1;padding:6px 8px;font-size:12px;border:1px solid #8c8f94;border-radius:3px;"
                                       oninput="document.getElementById('add-bg-color').value=this.value">
                            </div>
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">텍스트 색상</label>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <input type="color" name="text_color" value="#ffffff" id="add-text-color"
                                       style="width:36px;height:34px;padding:2px;border:1px solid #8c8f94;border-radius:3px;cursor:pointer;">
                                <input type="text" id="add-text-hex" value="#ffffff"
                                       style="flex:1;padding:6px 8px;font-size:12px;border:1px solid #8c8f94;border-radius:3px;"
                                       oninput="document.getElementById('add-text-color').value=this.value">
                            </div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;">
                        <div class="wp-form-group">
                            <label class="wp-form-label">폰트 크기 (px)</label>
                            <input type="number" name="font_size" value="14" min="10" max="32" class="wp-form-input">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">폰트 굵기</label>
                            <select name="font_weight" class="wp-form-input wp-form-select">
                                <option value="400">보통 (400)</option>
                                <option value="600">세미볼드 (600)</option>
                                <option value="700" selected>볼드 (700)</option>
                                <option value="800">엑스트라볼드 (800)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;">
                        <div class="wp-form-group">
                            <label class="wp-form-label">노출 시작일</label>
                            <input type="datetime-local" name="start_at" class="wp-form-input" style="font-size:12px;">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">노출 종료일</label>
                            <input type="datetime-local" name="end_at" class="wp-form-input" style="font-size:12px;">
                        </div>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">닫기 후 재표시 (시간)</label>
                        <input type="number" name="reshow_hours" value="24" min="0" max="8760" class="wp-form-input">
                        <p class="wp-form-help">0 = 닫으면 다시 안 보임 · 24 = 24시간 후 재표시 · 168 = 1주일 후</p>
                    </div>

                    <div class="wp-form-group">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" checked> 활성화 (즉시 노출)
                        </label>
                    </div>

                    
                    <div style="margin-bottom:14px;">
                        <p style="font-size:12px;font-weight:600;color:#1d2327;margin-bottom:6px;">미리보기</p>
                        <div id="add-preview" style="padding:8px 16px;border-radius:4px;background:#1d4ed8;color:#ffffff;font-size:14px;font-weight:700;text-align:center;">
                            📢 배너 텍스트를 입력하세요
                        </div>
                    </div>

                    <button type="submit" class="wp-btn wp-btn-primary" style="width:100%;">배너 추가</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;overflow-y:auto;padding:20px;">
    <div style="background:#fff;border-radius:4px;width:520px;max-width:95vw;box-shadow:0 4px 20px rgba(0,0,0,.3);">
        <div style="padding:14px 16px;border-bottom:1px solid #c3c4c7;font-weight:700;font-size:14px;display:flex;justify-content:space-between;align-items:center;">
            <span>탑배너 수정</span>
            <button type="button" onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;font-size:20px;color:#646970;line-height:1;">×</button>
        </div>
        <form id="edit-form" method="POST" style="padding:16px;">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            <div class="wp-form-group">
                <label class="wp-form-label">배너 텍스트 *</label>
                <input type="text" name="text" id="edit-text" class="wp-form-input" required maxlength="500">
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">링크 URL</label>
                <input type="text" name="link_url" id="edit-link-url" class="wp-form-input" maxlength="500">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">배경 색상</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" name="bg_color" id="edit-bg-color"
                               style="width:36px;height:34px;padding:2px;border:1px solid #8c8f94;border-radius:3px;cursor:pointer;">
                        <input type="text" id="edit-bg-hex"
                               style="flex:1;padding:6px 8px;font-size:12px;border:1px solid #8c8f94;border-radius:3px;"
                               oninput="document.getElementById('edit-bg-color').value=this.value">
                    </div>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">텍스트 색상</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" name="text_color" id="edit-text-color"
                               style="width:36px;height:34px;padding:2px;border:1px solid #8c8f94;border-radius:3px;cursor:pointer;">
                        <input type="text" id="edit-text-hex"
                               style="flex:1;padding:6px 8px;font-size:12px;border:1px solid #8c8f94;border-radius:3px;"
                               oninput="document.getElementById('edit-text-color').value=this.value">
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">폰트 크기 (px)</label>
                    <input type="number" name="font_size" id="edit-font-size" min="10" max="32" class="wp-form-input">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">폰트 굵기</label>
                    <select name="font_weight" id="edit-font-weight" class="wp-form-input wp-form-select">
                        <option value="400">보통 (400)</option>
                        <option value="600">세미볼드 (600)</option>
                        <option value="700">볼드 (700)</option>
                        <option value="800">엑스트라볼드 (800)</option>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">노출 시작일</label>
                    <input type="datetime-local" name="start_at" id="edit-start-at" class="wp-form-input" style="font-size:12px;">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">노출 종료일</label>
                    <input type="datetime-local" name="end_at" id="edit-end-at" class="wp-form-input" style="font-size:12px;">
                </div>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">닫기 후 재표시 (시간)</label>
                <input type="number" name="reshow_hours" id="edit-reshow" min="0" max="8760" class="wp-form-input">
                <p class="wp-form-help">0 = 닫으면 다시 안 보임 · 24 = 24시간 후 재표시</p>
            </div>

            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" name="is_active" id="edit-active" value="1"> 활성화
                </label>
            </div>

            
            <div style="margin-bottom:14px;">
                <p style="font-size:12px;font-weight:600;color:#1d2327;margin-bottom:6px;">미리보기</p>
                <div id="edit-preview" style="padding:8px 16px;border-radius:4px;text-align:center;"></div>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" class="wp-btn wp-btn-primary">저장</button>
                <button type="button" onclick="closeEditModal()" class="wp-btn wp-btn-secondary">취소</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// ── 색상 피커 ↔ 텍스트 동기화 ─────────────────────────────────
function syncColor(pickerId, hexId) {
    var picker = document.getElementById(pickerId);
    var hex    = document.getElementById(hexId);
    if (!picker || !hex) return;
    picker.addEventListener('input', function() { hex.value = this.value; updatePreview(); });
    hex.addEventListener('input', function() { picker.value = this.value; updatePreview(); });
}
syncColor('add-bg-color',   'add-bg-hex');
syncColor('add-text-color', 'add-text-hex');
syncColor('edit-bg-color',  'edit-bg-hex');
syncColor('edit-text-color','edit-text-hex');

// ── 추가 폼 실시간 미리보기 ───────────────────────────────────
function updatePreview() {
    var form = document.getElementById('add-form');
    if (!form) return;
    var preview = document.getElementById('add-preview');
    preview.style.background  = form.querySelector('[name=bg_color]').value;
    preview.style.color       = form.querySelector('[name=text_color]').value;
    preview.style.fontSize    = form.querySelector('[name=font_size]').value + 'px';
    preview.style.fontWeight  = form.querySelector('[name=font_weight]').value;
    var txt = form.querySelector('[name=text]').value;
    preview.textContent = txt || '배너 텍스트를 입력하세요';
}
document.getElementById('add-form').addEventListener('input', updatePreview);

// ── 수정 모달 실시간 미리보기 ─────────────────────────────────
function updateEditPreview() {
    var p = document.getElementById('edit-preview');
    p.style.background = document.getElementById('edit-bg-color').value;
    p.style.color      = document.getElementById('edit-text-color').value;
    p.style.fontSize   = document.getElementById('edit-font-size').value + 'px';
    p.style.fontWeight = document.getElementById('edit-font-weight').value;
    var txt = document.getElementById('edit-text').value;
    p.textContent = txt || '배너 텍스트를 입력하세요';
}
['edit-text','edit-bg-color','edit-bg-hex','edit-text-color','edit-text-hex','edit-font-size','edit-font-weight']
    .forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', updateEditPreview);
    });

// ── 수정 모달 열기/닫기 ───────────────────────────────────────
function openEditModal(id, text, linkUrl, textColor, bgColor, fontSize, fontWeight, startAt, endAt, reshowHours, isActive) {
    document.getElementById('edit-form').action = '/admin/top-banners/' + id;
    document.getElementById('edit-text').value       = text;
    document.getElementById('edit-link-url').value   = linkUrl;
    document.getElementById('edit-text-color').value = textColor;
    document.getElementById('edit-text-hex').value   = textColor;
    document.getElementById('edit-bg-color').value   = bgColor;
    document.getElementById('edit-bg-hex').value     = bgColor;
    document.getElementById('edit-font-size').value  = fontSize;
    document.getElementById('edit-font-weight').value= fontWeight;
    document.getElementById('edit-start-at').value   = startAt;
    document.getElementById('edit-end-at').value     = endAt;
    document.getElementById('edit-reshow').value     = reshowHours;
    document.getElementById('edit-active').checked   = isActive;
    updateEditPreview();
    document.getElementById('edit-modal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// ── 드래그 순서 변경 ──────────────────────────────────────────
(function() {
    var list = document.getElementById('banner-sortable');
    if (!list) return;
    var dragging = null;

    list.querySelectorAll('li').forEach(function(item) {
        item.setAttribute('draggable', 'true');
        item.style.cursor = 'grab';

        item.addEventListener('dragstart', function() {
            dragging = this;
            this.style.opacity = '.4';
        });
        item.addEventListener('dragend', function() {
            this.style.opacity = '';
            var ids = Array.from(list.querySelectorAll('li[data-id]')).map(function(li) { return li.dataset.id; });
            fetch('/admin/top-banners/reorder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ ids: ids }),
            });
        });
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            var rect = this.getBoundingClientRect();
            list.insertBefore(dragging, e.clientY < rect.top + rect.height / 2 ? this : this.nextSibling);
        });
    });
}());
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/top-banners.blade.php ENDPATH**/ ?>