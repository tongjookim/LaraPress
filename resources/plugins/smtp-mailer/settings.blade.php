@extends('admin.layout')
@section('title', 'SMTP 메일러 설정')

@section('admin-content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('admin.plugins') }}" style="color:#646970;text-decoration:none;font-size:13px;">← 플러그인</a>
    <span style="color:#c3c4c7;">/</span>
    <h1 class="wp-page-title" style="margin:0;">SMTP 메일러 설정</h1>
</div>

@if(session('success'))
    <div class="wp-notice" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="wp-notice wp-notice-error" style="margin-bottom:16px;">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('admin.plugin.settings.update', 'smtp-mailer') }}">
@csrf

<div class="admin-grid-2" style="gap:20px;">

    {{-- SMTP 서버 설정 --}}
    <div>
        <div class="wp-widget">
            <div class="wp-widget-title">SMTP 서버 설정</div>
            <div class="wp-widget-body">

                <table class="wp-form-table">
                    <tr>
                        <th><label for="smtp_host">SMTP 호스트</label></th>
                        <td>
                            <input type="text" id="smtp_host" name="smtp_host"
                                   value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                                   placeholder="smtp.gmail.com" class="wp-input" style="width:100%;">
                            <p class="description">메일 서버 주소</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="smtp_port">포트</label></th>
                        <td>
                            <input type="number" id="smtp_port" name="smtp_port"
                                   value="{{ old('smtp_port', $settings['smtp_port'] ?? '587') }}"
                                   placeholder="587" class="wp-input" style="width:120px;">
                            <p class="description">TLS: 587 &nbsp;·&nbsp; SSL: 465 &nbsp;·&nbsp; 인증없음: 25</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="smtp_encryption">암호화</label></th>
                        <td>
                            <select id="smtp_encryption" name="smtp_encryption" class="wp-input" style="width:160px;">
                                @foreach(['tls' => 'TLS (권장)', 'ssl' => 'SSL', '' => '없음'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($settings['smtp_encryption'] ?? 'tls') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="smtp_username">사용자 이름</label></th>
                        <td>
                            <input type="text" id="smtp_username" name="smtp_username"
                                   value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                                   placeholder="your@email.com" class="wp-input" style="width:100%;"
                                   autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="smtp_password">비밀번호</label></th>
                        <td>
                            <input type="password" id="smtp_password" name="smtp_password"
                                   value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                                   placeholder="{{ isset($settings['smtp_password']) && $settings['smtp_password'] ? '저장됨 (변경하려면 입력)' : '비밀번호 입력' }}"
                                   class="wp-input" style="width:100%;"
                                   autocomplete="new-password">
                            <p class="description">Gmail은 앱 비밀번호를 사용하세요</p>
                        </td>
                    </tr>
                </table>

            </div>
        </div>

        {{-- 발신자 설정 --}}
        <div class="wp-widget" style="margin-top:16px;">
            <div class="wp-widget-title">발신자 설정</div>
            <div class="wp-widget-body">
                <table class="wp-form-table">
                    <tr>
                        <th><label for="smtp_from_address">발신 이메일</label></th>
                        <td>
                            <input type="email" id="smtp_from_address" name="smtp_from_address"
                                   value="{{ old('smtp_from_address', $settings['smtp_from_address'] ?? config('mail.from.address')) }}"
                                   placeholder="noreply@example.com" class="wp-input" style="width:100%;">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="smtp_from_name">발신자 이름</label></th>
                        <td>
                            <input type="text" id="smtp_from_name" name="smtp_from_name"
                                   value="{{ old('smtp_from_name', $settings['smtp_from_name'] ?? config('mail.from.name')) }}"
                                   placeholder="사이트 이름" class="wp-input" style="width:100%;">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- 사이드 가이드 --}}
    <div>
        <div class="wp-widget">
            <div class="wp-widget-title">주요 SMTP 서버 정보</div>
            <div class="wp-widget-body" style="font-size:13px;">

                <div style="margin-bottom:16px;">
                    <strong style="color:#1d2327;">Gmail</strong>
                    <table style="width:100%;margin-top:6px;border-collapse:collapse;font-size:12px;">
                        <tr><td style="padding:3px 0;color:#646970;width:80px;">호스트</td><td><code style="background:#f0f0f1;padding:1px 5px;border-radius:2px;">smtp.gmail.com</code></td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">포트</td><td>587 (TLS) 또는 465 (SSL)</td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">비밀번호</td><td>Google 앱 비밀번호 필요</td></tr>
                    </table>
                </div>

                <div style="margin-bottom:16px;">
                    <strong style="color:#1d2327;">Naver</strong>
                    <table style="width:100%;margin-top:6px;border-collapse:collapse;font-size:12px;">
                        <tr><td style="padding:3px 0;color:#646970;width:80px;">호스트</td><td><code style="background:#f0f0f1;padding:1px 5px;border-radius:2px;">smtp.naver.com</code></td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">포트</td><td>587 (TLS) 또는 465 (SSL)</td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">비밀번호</td><td>네이버 계정 비밀번호</td></tr>
                    </table>
                </div>

                <div style="margin-bottom:16px;">
                    <strong style="color:#1d2327;">Kakao</strong>
                    <table style="width:100%;margin-top:6px;border-collapse:collapse;font-size:12px;">
                        <tr><td style="padding:3px 0;color:#646970;width:80px;">호스트</td><td><code style="background:#f0f0f1;padding:1px 5px;border-radius:2px;">smtp.kakao.com</code></td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">포트</td><td>587 (TLS)</td></tr>
                    </table>
                </div>

                <div>
                    <strong style="color:#1d2327;">로컬 서버 (sendmail/postfix)</strong>
                    <table style="width:100%;margin-top:6px;border-collapse:collapse;font-size:12px;">
                        <tr><td style="padding:3px 0;color:#646970;width:80px;">호스트</td><td><code style="background:#f0f0f1;padding:1px 5px;border-radius:2px;">127.0.0.1</code></td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">포트</td><td>25</td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">암호화</td><td>없음</td></tr>
                        <tr><td style="padding:3px 0;color:#646970;">인증</td><td>불필요 (비워두세요)</td></tr>
                    </table>
                </div>

            </div>
        </div>

        <div class="wp-widget" style="margin-top:16px;">
            <div class="wp-widget-title">플러그인 상태</div>
            <div class="wp-widget-body" style="font-size:13px;">
                @php $isActive = in_array('smtp-mailer', json_decode(\App\Models\Setting::get('active_plugins', '[]'), true) ?: []); @endphp
                @if($isActive)
                    <div style="display:flex;align-items:center;gap:8px;color:#2d7a3a;">
                        <span style="display:inline-block;width:10px;height:10px;background:#2d7a3a;border-radius:50%;"></span>
                        <strong>활성화됨</strong>
                    </div>
                    <p style="margin:8px 0 0;color:#646970;">SMTP를 통해 메일이 발송됩니다.</p>
                @else
                    <div style="display:flex;align-items:center;gap:8px;color:#8c8f94;">
                        <span style="display:inline-block;width:10px;height:10px;background:#c3c4c7;border-radius:50%;"></span>
                        <strong>비활성화됨</strong>
                    </div>
                    <p style="margin:8px 0 0;color:#646970;">설정 저장 후 플러그인 목록에서 활성화하세요.</p>
                @endif
            </div>
        </div>
    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px;">
    <button type="submit" class="wp-btn wp-btn-primary">설정 저장</button>
    <a href="{{ route('admin.plugins') }}" class="wp-btn wp-btn-secondary">취소</a>
</div>

</form>
@endsection
