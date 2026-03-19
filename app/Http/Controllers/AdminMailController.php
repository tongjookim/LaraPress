<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminMailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'role:admin']);
    }

    public function index()
    {
        $userCount = User::where('is_active', true)->count();

        $activePlugins  = json_decode(Setting::get('active_plugins', '[]'), true) ?: [];
        $smtpActive     = in_array('smtp-mailer', $activePlugins);
        $smtpSettings   = json_decode(Setting::get('plugin_smtp-mailer_settings', '{}'), true) ?: [];

        // 런타임에 실제 적용된 메일 설정
        $mailConfig = [
            'driver'      => config('mail.default'),
            'host'        => config('mail.mailers.smtp.host'),
            'port'        => config('mail.mailers.smtp.port'),
            'encryption'  => config('mail.mailers.smtp.encryption'),
            'username'    => config('mail.mailers.smtp.username'),
            'from_address'=> config('mail.from.address'),
            'from_name'   => config('mail.from.name'),
        ];

        return view('admin.mail', compact('userCount', 'smtpActive', 'smtpSettings', 'mailConfig'));
    }

    /** SMTP 플러그인 설정을 runtime config에 강제 적용하고 캐시 초기화 */
    private function applySmtpIfActive(): string
    {
        $active = json_decode(Setting::get('active_plugins', '[]'), true) ?: [];
        if (!in_array('smtp-mailer', $active)) {
            return config('mail.default', 'sendmail');
        }

        $s = json_decode(Setting::get('plugin_smtp-mailer_settings', '{}'), true) ?: [];
        if (empty($s['smtp_host'])) {
            return config('mail.default', 'sendmail');
        }

        // runtime config 강제 덮어쓰기
        config([
            'mail.default'                 => 'smtp',
            'mail.mailers.smtp.host'       => $s['smtp_host'],
            'mail.mailers.smtp.port'       => (int) ($s['smtp_port'] ?? 587),
            'mail.mailers.smtp.encryption' => $s['smtp_encryption'] ?? 'tls',
            'mail.mailers.smtp.username'   => $s['smtp_username'] ?? null,
            'mail.mailers.smtp.password'   => $s['smtp_password'] ?? null,
            'mail.from.address'            => $s['smtp_from_address'] ?? config('mail.from.address'),
            'mail.from.name'               => $s['smtp_from_name']    ?? config('mail.from.name'),
        ]);

        // MailManager 캐시 초기화 → 다음 Mail::send() 시 새 설정으로 mailer 재생성
        Mail::forgetMailers();

        return 'smtp';
    }

    /** 테스트 메일 발송 */
    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $siteName = Setting::get('site_name', 'Laraboard');
        $to       = $request->input('test_email');
        $driver   = $this->applySmtpIfActive();
        $host     = config('mail.mailers.smtp.host', '');
        $from     = config('mail.from.address');
        $username = config('mail.mailers.smtp.username', '');

        // Gmail: 발신 주소가 인증 계정과 다르면 경고
        $warning = null;
        if ($driver === 'smtp' && str_contains($host, 'gmail') && $from !== $username) {
            $warning = "Gmail은 발신 주소({$from})가 인증 계정({$username})과 일치해야 합니다. SMTP 설정에서 발신 이메일을 {$username}으로 변경하세요.";
        }

        try {
            Mail::send([], [], function ($message) use ($to, $siteName) {
                $message->to($to)
                    ->subject("[{$siteName}] 테스트 메일입니다")
                    ->html($this->testMailHtml($siteName));
            });

            $msg = "[{$driver}] {$to} 으로 테스트 메일을 발송했습니다. 스팸함도 확인해 보세요.";
            if ($warning) {
                return back()->with('error', "발송은 됐지만 주의: {$warning}");
            }
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $detail = $e->getMessage();
            if ($e->getPrevious()) {
                $detail .= ' — ' . $e->getPrevious()->getMessage();
            }
            $hint = '';
            if (str_contains($detail, 'Authentication')) {
                $hint = ' (인증 실패: 아이디/비밀번호 또는 앱 비밀번호를 확인하세요)';
            } elseif (str_contains($detail, 'Connection refused') || str_contains($detail, 'getaddrinfo')) {
                $hint = ' (서버에 연결할 수 없습니다: 호스트/포트를 확인하세요)';
            } elseif (str_contains($detail, 'ssl') || str_contains($detail, 'SSL') || str_contains($detail, 'tls')) {
                $hint = ' (암호화 설정 오류: TLS↔SSL 또는 포트를 바꿔보세요)';
            }
            return back()->with('error', "메일 발송 실패 [{$driver}]: {$detail}{$hint}");
        }
    }

    /** SMTP 연결 진단 */
    public function diagnose()
    {
        $driver   = $this->applySmtpIfActive();
        $host     = config('mail.mailers.smtp.host', '');
        $port     = config('mail.mailers.smtp.port', 587);
        $enc      = config('mail.mailers.smtp.encryption', 'tls');
        $username = config('mail.mailers.smtp.username', '');
        $from     = config('mail.from.address', '');

        $result = [
            'driver'   => $driver,
            'host'     => $host,
            'port'     => $port,
            'enc'      => $enc,
            'username' => $username,
            'from'     => $from,
            'tcp'      => null,
            'smtp'     => null,
            'warnings' => [],
        ];

        if ($driver !== 'smtp' || !$host) {
            $result['smtp'] = 'SMTP 드라이버가 활성화되어 있지 않습니다.';
            return response()->json($result);
        }

        // Gmail 발신 주소 불일치 경고
        if (str_contains($host, 'gmail') && $from !== $username) {
            $result['warnings'][] = "Gmail: 발신 주소({$from})가 인증 계정({$username})과 다릅니다. 발신 이메일을 {$username}으로 변경하세요.";
        }

        // TCP 연결 테스트
        $errno = $errstr = null;
        $sock = @fsockopen($host, (int)$port, $errno, $errstr, 5);
        if ($sock) {
            fclose($sock);
            $result['tcp'] = "OK — {$host}:{$port} 연결 성공";
        } else {
            $result['tcp'] = "FAIL — {$host}:{$port} 연결 실패: {$errstr} ({$errno})";
            $result['smtp'] = '포트 연결 실패로 SMTP 테스트를 건너뜁니다.';
            return response()->json($result);
        }

        // SMTP 인증 테스트 (실제 발송 없이)
        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                $host, (int)$port, $enc === 'ssl'
            );
            if ($username) {
                $transport->setUsername($username);
                $transport->setPassword(config('mail.mailers.smtp.password', ''));
            }
            $transport->start();
            $transport->stop();
            $result['smtp'] = 'OK — SMTP 인증 성공';
        } catch (\Throwable $e) {
            $result['smtp'] = 'FAIL — ' . $e->getMessage();
        }

        return response()->json($result);
    }

    /** 전체 메일 발송 */
    public function send(Request $request)
    {
        $request->validate([
            'subject'    => 'required|max:200',
            'body'       => 'required',
            'target'     => 'required|in:all,subscriber,author,editor,admin',
        ]);

        $this->applySmtpIfActive();
        $siteName = Setting::get('site_name', 'Laraboard');
        $subject  = $request->input('subject');
        $body     = $request->input('body');
        $target   = $request->input('target');

        $query = User::where('is_active', true)->whereNotNull('email');
        if ($target !== 'all') {
            $query->where('role', $target);
        }

        $users = $query->get(['name', 'email']);

        if ($users->isEmpty()) {
            return back()->with('error', '발송 대상 회원이 없습니다.');
        }

        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                $html = $this->buildMailHtml($siteName, $subject, $body, $user->name);
                Mail::send([], [], function ($message) use ($user, $subject, $html, $siteName) {
                    $message->to($user->email, $user->name)
                        ->subject($subject)
                        ->html($html);
                });
                $sent++;
            } catch (\Throwable) {
                $failed++;
            }
        }

        $msg = "{$sent}명에게 메일을 발송했습니다.";
        if ($failed > 0) $msg .= " ({$failed}건 실패)";

        return back()->with('success', $msg);
    }

    private function testMailHtml(string $siteName): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="ko">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0f0f1;font-family:'Noto Sans KR',sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f0f1;padding:32px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">
      <tr><td style="background:#1d2327;padding:24px 32px;">
        <p style="margin:0;font-size:20px;font-weight:900;color:#fff;">{$siteName}</p>
      </td></tr>
      <tr><td style="padding:32px;">
        <h2 style="margin:0 0 16px;font-size:20px;color:#1d2327;">메일 발송 테스트</h2>
        <p style="margin:0 0 16px;color:#646970;line-height:1.7;">이 메일은 <strong>{$siteName}</strong> 관리자 패널에서 발송된 테스트 메일입니다.</p>
        <p style="margin:0;color:#646970;font-size:13px;">메일이 정상적으로 수신되었다면 메일 서버 설정이 올바르게 완료된 것입니다.</p>
      </td></tr>
      <tr><td style="padding:16px 32px 24px;border-top:1px solid #f0f0f1;">
        <p style="margin:0;font-size:12px;color:#8c8f94;text-align:center;">{$siteName} &mdash; 이 메일은 자동 발송되었습니다.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body></html>
HTML;
    }

    private function buildMailHtml(string $siteName, string $subject, string $body, string $name): string
    {
        $safeBody = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
        return <<<HTML
<!DOCTYPE html>
<html lang="ko">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0f0f1;font-family:'Noto Sans KR',sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f0f1;padding:32px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">
      <tr><td style="background:#1d2327;padding:24px 32px;">
        <p style="margin:0;font-size:20px;font-weight:900;color:#fff;">{$siteName}</p>
      </td></tr>
      <tr><td style="padding:32px;">
        <p style="margin:0 0 8px;color:#646970;font-size:14px;">안녕하세요, <strong style="color:#1d2327;">{$name}</strong>님.</p>
        <h2 style="margin:0 0 20px;font-size:20px;color:#1d2327;">{$subject}</h2>
        <div style="color:#374151;line-height:1.8;font-size:15px;">{$safeBody}</div>
      </td></tr>
      <tr><td style="padding:16px 32px 24px;border-top:1px solid #f0f0f1;">
        <p style="margin:0;font-size:12px;color:#8c8f94;text-align:center;">{$siteName} &mdash; 이 메일은 자동 발송되었습니다.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body></html>
HTML;
    }
}
