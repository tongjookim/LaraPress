<?php $__env->startSection('title', '메일 관리'); ?>
<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">메일 관리</h1>

<?php if(session('success')): ?>
    <div class="wp-notice" style="margin-bottom:16px;"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="wp-notice wp-notice-error" style="margin-bottom:16px; white-space:pre-wrap;"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="admin-grid-2" style="align-items:start;gap:20px;">

    
    <div class="wp-widget">
        <div class="wp-widget-title">테스트 메일 발송</div>
        <div class="wp-widget-body">
            <p style="font-size:13px;color:#646970;margin-bottom:16px;">
                메일 서버 설정이 올바른지 확인하기 위해 테스트 메일을 발송합니다.
            </p>
            <form action="<?php echo e(route('admin.mail.test')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">수신 이메일</label>
                    <input type="email" name="test_email"
                           value="<?php echo e(old('test_email', auth()->user()->email)); ?>"
                           required class="wp-input" style="width:100%;" placeholder="test@example.com">
                    <?php $__errorArgs = ['test_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <button type="submit" class="wp-btn wp-btn-primary">테스트 메일 발송</button>
                    <button type="button" class="wp-btn wp-btn-secondary" onclick="runDiagnose()">SMTP 연결 진단</button>
                </div>
            </form>

            <div id="diagnose-result" style="display:none;margin-top:16px;padding:14px;background:#f6f7f7;border-radius:4px;font-size:12px;font-family:monospace;white-space:pre-wrap;line-height:1.7;"></div>
        </div>
    </div>

    
    <div>
        <div class="wp-widget">
            <div class="wp-widget-title" style="display:flex;align-items:center;justify-content:space-between;">
                <span>현재 메일 설정</span>
                <?php if($smtpActive): ?>
                    <a href="<?php echo e(route('admin.plugin.settings', 'smtp-mailer')); ?>"
                       style="font-size:12px;color:#2271b1;text-decoration:none;">SMTP 설정 변경 →</a>
                <?php else: ?>
                    <a href="<?php echo e(route('admin.plugins')); ?>"
                       style="font-size:12px;color:#2271b1;text-decoration:none;">플러그인 관리 →</a>
                <?php endif; ?>
            </div>
            <div class="wp-widget-body">

                
                <?php if($smtpActive && !empty($smtpSettings['smtp_host'])): ?>
                    <div style="display:flex;align-items:center;gap:8px;padding:10px 12px;background:#d7edda;border-radius:4px;margin-bottom:14px;font-size:13px;color:#2d7a3a;">
                        <span style="display:inline-block;width:8px;height:8px;background:#2d7a3a;border-radius:50%;flex-shrink:0;"></span>
                        <strong>SMTP 메일러 플러그인 활성화됨</strong>
                    </div>
                <?php elseif($smtpActive && empty($smtpSettings['smtp_host'])): ?>
                    <div style="padding:10px 12px;background:#fef9c3;border-left:3px solid #dba617;border-radius:3px;margin-bottom:14px;font-size:13px;color:#854d0e;">
                        SMTP 플러그인이 활성화되어 있지만 서버 주소가 설정되지 않았습니다.
                        <a href="<?php echo e(route('admin.plugin.settings', 'smtp-mailer')); ?>" style="color:#854d0e;font-weight:600;">설정하기 →</a>
                    </div>
                <?php else: ?>
                    <div style="padding:10px 12px;background:#f6f7f7;border-radius:4px;margin-bottom:14px;font-size:13px;color:#646970;">
                        현재 서버 내장 메일(<strong>sendmail/postfix</strong>)을 사용 중입니다.
                        외부 SMTP를 사용하려면
                        <a href="<?php echo e(route('admin.plugins')); ?>" style="color:#2271b1;">SMTP 메일러 플러그인</a>을 활성화하세요.
                    </div>
                <?php endif; ?>

                
                <table style="width:100%;font-size:13px;border-collapse:collapse;">
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:7px 0;color:#646970;width:42%;">발송 방식</td>
                        <td style="padding:7px 0;">
                            <strong><?php echo e(strtoupper($mailConfig['driver'])); ?></strong>
                            <?php if($mailConfig['driver'] === 'smtp'): ?>
                                <span style="font-size:11px;background:#d7edda;color:#2d7a3a;padding:1px 6px;border-radius:2px;margin-left:6px;">플러그인 적용</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:7px 0;color:#646970;">발신자 이름</td>
                        <td style="padding:7px 0;font-weight:600;"><?php echo e($mailConfig['from_name'] ?: '—'); ?></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:7px 0;color:#646970;">발신자 주소</td>
                        <td style="padding:7px 0;font-weight:600;"><?php echo e($mailConfig['from_address'] ?: '—'); ?></td>
                    </tr>
                    <?php if($mailConfig['driver'] === 'smtp'): ?>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:7px 0;color:#646970;">SMTP 호스트</td>
                        <td style="padding:7px 0;font-weight:600;"><?php echo e($mailConfig['host'] ?: '—'); ?></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:7px 0;color:#646970;">포트 / 암호화</td>
                        <td style="padding:7px 0;font-weight:600;">
                            <?php echo e($mailConfig['port'] ?: '—'); ?>

                            <?php if($mailConfig['encryption']): ?>
                                / <?php echo e(strtoupper($mailConfig['encryption'])); ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:7px 0;color:#646970;">인증 계정</td>
                        <td style="padding:7px 0;font-weight:600;"><?php echo e($mailConfig['username'] ?: '—'); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

</div>


<div class="wp-widget" style="margin-top:20px;">
    <div class="wp-widget-title">전체 메일 발송</div>
    <div class="wp-widget-body">
        <div style="padding:10px 14px;margin-bottom:20px;background:#fef9c3;border-left:4px solid #dba617;font-size:13px;color:#854d0e;border-radius:3px;">
            ⚠️ 이 기능은 선택한 대상 회원 전체에게 메일을 발송합니다. 신중하게 사용해주세요.
            현재 활성 회원 수: <strong><?php echo e($userCount); ?>명</strong>
        </div>

        <form action="<?php echo e(route('admin.mail.send')); ?>" method="POST"
              onsubmit="return confirm('선택한 대상에게 메일을 발송하시겠습니까?')">
            <?php echo csrf_field(); ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">발송 대상</label>
                    <select name="target" class="wp-input" style="width:100%;">
                        <option value="all">전체 회원 (<?php echo e($userCount); ?>명)</option>
                        <option value="subscriber">구독자만</option>
                        <option value="author">작성자 이상</option>
                        <option value="editor">편집자 이상</option>
                        <option value="admin">관리자만</option>
                    </select>
                </div>
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">제목</label>
                    <input type="text" name="subject" value="<?php echo e(old('subject')); ?>"
                           required class="wp-input" style="width:100%;" placeholder="메일 제목을 입력하세요">
                    <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">내용</label>
                <textarea name="body" rows="10" required
                          class="wp-input" style="width:100%;resize:vertical;"
                          placeholder="메일 본문을 입력하세요. 줄바꿈은 그대로 반영됩니다."><?php echo e(old('body')); ?></textarea>
                <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p style="color:#d63638;font-size:12px;margin-top:4px;"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;display:flex;align-items:center;gap:12px;">
                <button type="submit" class="wp-btn wp-btn-primary">메일 발송</button>
                <span style="font-size:12px;color:#8c8f94;">발송 중에는 페이지를 닫지 마세요.</span>
            </div>
        </form>
    </div>
</div>

<script>
function runDiagnose() {
    const box = document.getElementById('diagnose-result');
    box.style.display = 'block';
    box.textContent = '진단 중...';
    fetch('<?php echo e(route('admin.mail.diagnose')); ?>')
        .then(r => r.json())
        .then(d => {
            let lines = [];
            lines.push('드라이버  : ' + d.driver);
            if (d.driver === 'smtp') {
                lines.push('호스트    : ' + d.host + ':' + d.port + ' (' + (d.enc || '암호화없음') + ')');
                lines.push('인증계정  : ' + (d.username || '(없음)'));
                lines.push('발신주소  : ' + (d.from || '(없음)'));
                lines.push('');
                lines.push('TCP 연결  : ' + d.tcp);
                lines.push('SMTP 인증 : ' + d.smtp);
            } else {
                lines.push(d.smtp || 'SMTP 비활성');
            }
            if (d.warnings && d.warnings.length) {
                lines.push('');
                lines.push('⚠️ 경고:');
                d.warnings.forEach(w => lines.push('  ' + w));
            }
            box.textContent = lines.join('\n');
            box.style.background = (d.tcp && d.tcp.startsWith('FAIL')) || (d.smtp && d.smtp.startsWith('FAIL'))
                ? '#fce8e6' : '#d7edda';
        })
        .catch(e => { box.textContent = '진단 요청 실패: ' + e; box.style.background = '#fce8e6'; });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/mail.blade.php ENDPATH**/ ?>