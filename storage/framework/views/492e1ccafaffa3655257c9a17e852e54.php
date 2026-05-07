<?php $__env->startSection('title', '사이트 설정'); ?>
<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">사이트 설정</h1>

<form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">🖼️ 브랜딩 (로고 &amp; 파비콘)</div>
        <div class="wp-widget-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">로고 텍스트</label>
                    <input type="text" name="logo_text" value="<?php echo e(old('logo_text', $settings['logo_text'])); ?>" class="wp-form-input" placeholder="사이트 이름을 입력하면 기본값 사용">
                    <p class="wp-form-help">비워두면 사이트 이름으로 표시됩니다.</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">태그라인 (로고 아래 문구)</label>
                    <input type="text" name="logo_tagline" value="<?php echo e(old('logo_tagline', $settings['logo_tagline'])); ?>" class="wp-form-input" placeholder="예: 대한민국 대표 커뮤니티">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                
                <div class="wp-form-group">
                    <label class="wp-form-label">로고 이미지</label>
                    <?php if($settings['logo_image']): ?>
                    <div style="margin-bottom:8px;padding:8px;border:1px solid #c3c4c7;border-radius:3px;background:#f6f7f7;display:inline-flex;align-items:center;gap:10px;">
                        <img src="<?php echo e($settings['logo_image']); ?>" alt="현재 로고" style="max-height:40px;max-width:160px;object-fit:contain;">
                        <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#d63638;cursor:pointer;">
                            <input type="checkbox" name="clear_logo_image" value="1"> 삭제
                        </label>
                    </div>
                    <br>
                    <?php endif; ?>
                    <input type="file" name="logo_image_file" accept="image/*" class="wp-form-input" style="padding:4px;">
                    <p class="wp-form-help">PNG, SVG, WebP 권장 · 최대 2MB · 이미지 업로드 시 텍스트 로고 대신 표시됩니다.</p>
                    <?php $__errorArgs = ['logo_image_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="wp-form-group">
                    <label class="wp-form-label">파비콘</label>
                    <?php if($settings['favicon']): ?>
                    <div style="margin-bottom:8px;padding:8px;border:1px solid #c3c4c7;border-radius:3px;background:#f6f7f7;display:inline-flex;align-items:center;gap:10px;">
                        <img src="<?php echo e($settings['favicon']); ?>" alt="현재 파비콘" style="width:32px;height:32px;object-fit:contain;">
                        <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#d63638;cursor:pointer;">
                            <input type="checkbox" name="clear_favicon" value="1"> 삭제
                        </label>
                    </div>
                    <br>
                    <?php endif; ?>
                    <input type="file" name="favicon_file" accept=".ico,.png,.svg,.gif" class="wp-form-input" style="padding:4px;">
                    <p class="wp-form-help">ICO, PNG, SVG 지원 · 최대 512KB · 권장 크기: 32×32px 또는 64×64px</p>
                    <?php $__errorArgs = ['favicon_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">🛠️ 관리자 패널 로고</div>
        <div class="wp-widget-body">
            <p style="font-size:13px;color:#646970;margin-bottom:16px;">관리자 사이드바 상단에 표시되는 로고를 설정합니다. 이미지를 업로드하면 기본 아이콘+텍스트 대신 표시됩니다.</p>

            
            <div style="margin-bottom:16px;padding:12px 16px;background:#1d2327;border-radius:4px;display:inline-flex;align-items:center;gap:10px;">
                <?php if($settings['admin_logo_image']): ?>
                    <img src="<?php echo e($settings['admin_logo_image']); ?>" alt="관리자 로고" style="max-height:32px;max-width:140px;object-fit:contain;">
                <?php else: ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#72aee6" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                    <span style="color:#fff;font-weight:900;font-size:15px;"><?php echo e($settings['admin_logo_text'] ?: 'Laraboard'); ?></span>
                    <span style="color:#72aee6;font-size:10px;">Admin</span>
                <?php endif; ?>
            </div>
            <p style="font-size:11px;color:#8c8f94;margin-bottom:16px;">현재 사이드바 로고 미리보기</p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">관리자 로고 이미지</label>
                    <?php if($settings['admin_logo_image']): ?>
                    <div style="margin-bottom:8px;padding:8px;border:1px solid #c3c4c7;border-radius:3px;background:#f6f7f7;display:inline-flex;align-items:center;gap:10px;">
                        <img src="<?php echo e($settings['admin_logo_image']); ?>" alt="관리자 로고" style="max-height:36px;max-width:140px;object-fit:contain;">
                        <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#d63638;cursor:pointer;">
                            <input type="checkbox" name="clear_admin_logo_image" value="1"> 삭제
                        </label>
                    </div>
                    <br>
                    <?php endif; ?>
                    <input type="file" name="admin_logo_image_file" accept="image/*" class="wp-form-input" style="padding:4px;">
                    <p class="wp-form-help">PNG, SVG, WebP 권장 · 최대 2MB · 어두운 배경에 잘 보이는 밝은 색상 로고 권장</p>
                    <?php $__errorArgs = ['admin_logo_image_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="wp-form-group">
                    <label class="wp-form-label">관리자 로고 텍스트</label>
                    <input type="text" name="admin_logo_text" value="<?php echo e(old('admin_logo_text', $settings['admin_logo_text'])); ?>" class="wp-form-input" placeholder="Laraboard">
                    <p class="wp-form-help">이미지가 없을 때 사이드바에 표시될 텍스트입니다. 비워두면 "Laraboard"로 표시됩니다.</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">📌 기본 설정</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label class="wp-form-label">사이트 이름 *</label>
                <input type="text" name="site_name" value="<?php echo e(old('site_name', $settings['site_name'])); ?>" required class="wp-form-input">
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">사이트 설명</label>
                <textarea name="site_description" rows="3" class="wp-form-input wp-form-textarea"><?php echo e(old('site_description', $settings['site_description'])); ?></textarea>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">사이트 키워드</label>
                <input type="text" name="site_keywords" value="<?php echo e(old('site_keywords', $settings['site_keywords'])); ?>" class="wp-form-input">
                <p class="wp-form-help">쉼표로 구분 (예: 게시판, 커뮤니티, 포럼)</p>
            </div>
        </div>
    </div>

    
    <div class="wp-widget" style="margin-bottom:16px;border-left:3px solid #2271b1;">
        <div class="wp-widget-body" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div>
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">🔍 SEO 설정</div>
                <p style="font-size:13px;color:#646970;margin:0;">메타태그, 사이트맵, RSS 피드 등 SEO 설정은 별도 페이지에서 관리합니다.</p>
            </div>
            <a href="<?php echo e(route('admin.seo')); ?>" class="wp-btn wp-btn-primary" style="white-space:nowrap;">SEO 설정으로 이동 →</a>
        </div>
    </div>

    
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">🎨 스킨 설정</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label class="wp-form-label">레이아웃 스킨</label>
                <select name="layout_skin" class="wp-form-input wp-form-select">
                    <?php $__currentLoopData = $layoutSkins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('layout_skin', $settings['layout_skin']) == $key ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="wp-form-help">홈, 게시판, 로그인 등 프론트엔드에 적용되는 전체 레이아웃</p>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">게시판 스킨</label>
                <select name="board_skin" class="wp-form-input wp-form-select">
                    <?php $__currentLoopData = $boardSkins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('board_skin', $settings['board_skin']) == $key ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">회원 스킨</label>
                <select name="member_skin" class="wp-form-input wp-form-select">
                    <?php $__currentLoopData = $memberSkins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('member_skin', $settings['member_skin']) == $key ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>

    
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">⚙️ 커스텀 스크립트</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label class="wp-form-label">HEAD 영역 스크립트</label>
                <textarea name="custom_head_script" rows="5" class="wp-form-input wp-form-textarea" style="font-family:monospace;font-size:12px;"><?php echo e(old('custom_head_script', $settings['custom_head_script'])); ?></textarea>
                <p class="wp-form-help">Google Analytics, 폰트, CSS 등</p>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">BODY 영역 스크립트</label>
                <textarea name="custom_body_script" rows="5" class="wp-form-input wp-form-textarea" style="font-family:monospace;font-size:12px;"><?php echo e(old('custom_body_script', $settings['custom_body_script'])); ?></textarea>
                <p class="wp-form-help">채팅, 통계 코드 등</p>
            </div>
            <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:3px;padding:8px 12px;font-size:12px;color:#856404;">
                ⚠️ 스크립트 사용 시 주의: 악의적인 코드는 사이트 보안에 위험할 수 있습니다.
            </div>
        </div>
    </div>

    
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">💬 토론 설정</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" name="comments_enabled" value="1"
                           <?php echo e(old('comments_enabled', $settings['comments_enabled']) == '1' ? 'checked' : ''); ?>>
                    기사 댓글 기능 활성화
                </label>
                <p class="wp-form-help">체크 해제 시 모든 기사에서 댓글 입력 및 표시가 숨겨집니다.</p>
            </div>
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" name="comments_require_login" value="1"
                           <?php echo e(old('comments_require_login', $settings['comments_require_login']) == '1' ? 'checked' : ''); ?>>
                    댓글 작성 시 로그인 필요
                </label>
                <p class="wp-form-help">체크 해제 시 비회원도 댓글을 작성할 수 있습니다. (현재는 로그인 필수만 지원)</p>
            </div>
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" name="comments_moderation" value="1"
                           <?php echo e(old('comments_moderation', $settings['comments_moderation']) == '1' ? 'checked' : ''); ?>>
                    댓글 사전 검토 (승인 후 표시)
                </label>
                <p class="wp-form-help">활성화 시 댓글이 관리자 승인 후 표시됩니다.</p>
            </div>
        </div>
    </div>

    
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">📰 언론사 정보 (푸터 표시)</div>
        <div class="wp-widget-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">제호 *</label>
                    <input type="text" name="press_masthead" value="<?php echo e(old('press_masthead', $settings['press_masthead'])); ?>" class="wp-form-input" placeholder="예: 라라보드뉴스">
                    <p class="wp-form-help">언론사 공식 제호</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">등록번호</label>
                    <input type="text" name="press_registration_number" value="<?php echo e(old('press_registration_number', $settings['press_registration_number'])); ?>" class="wp-form-input" placeholder="예: 서울 아 01234">
                    <p class="wp-form-help">인터넷신문 등록번호</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">발행인</label>
                    <input type="text" name="press_publisher" value="<?php echo e(old('press_publisher', $settings['press_publisher'])); ?>" class="wp-form-input" placeholder="홍길동">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">편집인</label>
                    <input type="text" name="press_editor" value="<?php echo e(old('press_editor', $settings['press_editor'])); ?>" class="wp-form-input" placeholder="홍길동">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">대표번호</label>
                    <input type="text" name="press_phone" value="<?php echo e(old('press_phone', $settings['press_phone'])); ?>" class="wp-form-input" placeholder="02-1234-5678">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">팩스번호</label>
                    <input type="text" name="press_fax" value="<?php echo e(old('press_fax', $settings['press_fax'])); ?>" class="wp-form-input" placeholder="02-1234-5679">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">이메일</label>
                    <input type="email" name="press_email" value="<?php echo e(old('press_email', $settings['press_email'])); ?>" class="wp-form-input" placeholder="news@example.com">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">우편번호</label>
                    <input type="text" name="press_postal_code" value="<?php echo e(old('press_postal_code', $settings['press_postal_code'])); ?>" class="wp-form-input" placeholder="12345">
                </div>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">회사주소</label>
                <input type="text" name="press_address" value="<?php echo e(old('press_address', $settings['press_address'])); ?>" class="wp-form-input" placeholder="서울특별시 강남구 테헤란로 123, 456호">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0 24px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">청소년보호책임자</label>
                    <input type="text" name="press_youth_manager" value="<?php echo e(old('press_youth_manager', $settings['press_youth_manager'])); ?>" class="wp-form-input" placeholder="홍길동">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">개인정보 보호책임자</label>
                    <input type="text" name="press_privacy_manager" value="<?php echo e(old('press_privacy_manager', $settings['press_privacy_manager'])); ?>" class="wp-form-input" placeholder="홍길동">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">고충처리인 <span style="color:#8c8f94;font-weight:400;">(선택)</span></label>
                    <input type="text" name="press_grievance_manager" value="<?php echo e(old('press_grievance_manager', $settings['press_grievance_manager'])); ?>" class="wp-form-input" placeholder="홍길동">
                </div>
            </div>
            <div style="background:#e8f0fb;border:1px solid #c5d8f7;border-radius:3px;padding:8px 12px;font-size:12px;color:#1a4f9a;">
                ℹ️ 입력된 정보는 사이트 하단 푸터에 한국 언론사 형식으로 표시됩니다.
            </div>
        </div>
    </div>

    <button type="submit" class="wp-btn wp-btn-primary" style="padding:6px 24px;">설정 저장</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/settings.blade.php ENDPATH**/ ?>