@extends('skin.layout.basic.main')
@section('title', ' — 내 프로필')

@push('skin-css')
<style>
.profile-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    max-width: 700px;
    margin: 0 auto;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}
.profile-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #2563eb 100%);
    padding: 36px 36px 60px;
    position: relative;
}
.profile-avatar-wrap {
    position: absolute;
    bottom: -40px;
    left: 36px;
}
.profile-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    border: 4px solid #fff;
    object-fit: cover;
    background: #e8eaf6;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; font-weight: 800; color: #4f46e5;
    box-shadow: 0 2px 10px rgba(0,0,0,.15);
}
.profile-body {
    padding: 52px 36px 32px;
}
.profile-meta {
    margin-bottom: 28px;
}
.profile-name {
    font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 4px;
}
.profile-role {
    display: inline-block;
    font-size: 12px; font-weight: 700; padding: 2px 10px;
    border-radius: 99px;
}
.role-subscriber { background:#f3f4f6; color:#374151; }
.role-author     { background:#dcfce7; color:#166534; }
.role-editor     { background:#dbeafe; color:#1d4ed8; }
.role-admin      { background:#fef3c7; color:#92400e; }

.profile-form-group { margin-bottom: 18px; }
.profile-label {
    display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px;
}
.profile-input {
    width: 100%; padding: 9px 12px; font-size: 14px;
    border: 1px solid #d1d5db; border-radius: 6px;
    background: #fff; color: #111827; transition: border-color .15s;
}
.profile-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.profile-textarea { min-height: 90px; resize: vertical; font-family: inherit; }
.profile-help { font-size: 12px; color: #9ca3af; margin-top: 4px; }
.profile-section-title {
    font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    color: #9ca3af; margin: 24px 0 14px;
    padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;
}
.profile-btn {
    display: inline-block; padding: 9px 24px; font-size: 14px; font-weight: 600;
    border-radius: 6px; border: none; cursor: pointer; transition: all .15s;
    text-decoration: none;
}
.profile-btn-primary { background: #4f46e5; color: #fff; }
.profile-btn-primary:hover { background: #4338ca; }
.profile-btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
.profile-btn-secondary:hover { background: #e5e7eb; }
</style>
@endpush

@section('content')
<div style="max-width:700px;margin:0 auto;">

    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;font-size:14px;color:#166534;margin-bottom:16px;">
        {{ session('success') }}
    </div>
    @endif

    <div class="profile-card">
        {{-- 히어로 헤더 --}}
        <div class="profile-hero">
            <div class="profile-avatar-wrap">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image }}" alt="{{ $user->name }}" class="profile-avatar">
                @else
                    <div class="profile-avatar">{{ mb_substr($user->name, 0, 1) }}</div>
                @endif
            </div>
        </div>

        <div class="profile-body">
            {{-- 이름/역할 --}}
            <div class="profile-meta">
                <div class="profile-name">{{ $user->name }}</div>
                <div style="margin-top:6px;display:flex;align-items:center;gap:8px;">
                    <span class="profile-role role-{{ $user->role }}">{{ $user->roleLabel() }}</span>
                    <span style="font-size:12px;color:#9ca3af;">{{ '@'.$user->username }}</span>
                </div>
                @if($user->bio)
                <p style="margin-top:10px;font-size:14px;color:#6b7280;line-height:1.65;">{{ $user->bio }}</p>
                @endif
            </div>

            {{-- 수정 폼 --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="profile-section-title">기본 정보</div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
                    <div class="profile-form-group">
                        <label class="profile-label">이름 *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="profile-input">
                        @error('name') <p style="color:#dc2626;font-size:12px;margin-top:3px;">{{ $message }}</p> @enderror
                    </div>
                    <div class="profile-form-group">
                        <label class="profile-label">이메일 *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="profile-input">
                        @error('email') <p style="color:#dc2626;font-size:12px;margin-top:3px;">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="profile-form-group">
                    <label class="profile-label">소개</label>
                    <textarea name="bio" rows="3" class="profile-input profile-textarea" placeholder="자신을 소개하는 한 두 문장을 입력하세요.">{{ old('bio', $user->bio) }}</textarea>
                    <p class="profile-help">최대 1000자. 작성자 박스가 활성화된 경우 기사 하단에 표시됩니다.</p>
                </div>

                <div class="profile-section-title">프로필 이미지</div>

                @if($user->profile_image)
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;">
                    <img src="{{ $user->profile_image }}" alt="현재 프로필" style="width:56px;height:56px;border-radius:50%;object-fit:cover;">
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">현재 프로필 이미지</p>
                        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#dc2626;cursor:pointer;">
                            <input type="checkbox" name="clear_profile_image" value="1"> 이미지 삭제
                        </label>
                    </div>
                </div>
                @endif
                <div class="profile-form-group">
                    <input type="file" name="profile_image_file" accept="image/*" class="profile-input" style="padding:6px;">
                    <p class="profile-help">PNG, JPG, WebP · 최대 2MB</p>
                    @error('profile_image_file') <p style="color:#dc2626;font-size:12px;margin-top:3px;">{{ $message }}</p> @enderror
                </div>

                <div class="profile-section-title">소셜 링크</div>

                @php
                $socials = [
                    'social_facebook'  => ['label'=>'Facebook',    'type'=>'url',   'placeholder'=>'https://facebook.com/username',     'icon'=>'<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',  'color'=>'#1877F2'],
                    'social_x'         => ['label'=>'X (Twitter)', 'type'=>'url',   'placeholder'=>'https://x.com/username',             'icon'=>'<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>', 'color'=>'#000'],
                    'social_instagram' => ['label'=>'Instagram',   'type'=>'url',   'placeholder'=>'https://instagram.com/username',    'icon'=>'<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path fill="white" d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line stroke="white" stroke-width="2" x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>', 'color'=>'#E1306C'],
                    'social_linkedin'  => ['label'=>'LinkedIn',    'type'=>'url',   'placeholder'=>'https://linkedin.com/in/username',  'icon'=>'<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>', 'color'=>'#0A66C2'],
                    'social_website'   => ['label'=>'홈페이지',     'type'=>'url',   'placeholder'=>'https://example.com',               'icon'=>'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>', 'color'=>'#6366f1'],
                    'social_blog'      => ['label'=>'블로그',       'type'=>'url',   'placeholder'=>'https://blog.example.com',          'icon'=>'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>', 'color'=>'#f59e0b'],
                    'social_pixabay'   => ['label'=>'Pixabay',      'type'=>'url',   'placeholder'=>'https://pixabay.com/users/username', 'icon'=>'<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><text x="6" y="16" font-size="10" fill="white" font-weight="bold">px</text></svg>', 'color'=>'#2ec66e'],
                    'social_wikipedia' => ['label'=>'Wikipedia',    'type'=>'url',   'placeholder'=>'https://ko.wikipedia.org/wiki/...',  'icon'=>'<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><text x="7" y="16" font-size="11" fill="white" font-weight="bold">W</text></svg>', 'color'=>'#111'],
                    'social_email'     => ['label'=>'공개 이메일',   'type'=>'email', 'placeholder'=>'public@example.com',                'icon'=>'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>', 'color'=>'#6b7280'],
                ];
                @endphp

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
                    @foreach($socials as $field => $meta)
                    <div class="profile-form-group">
                        <label class="profile-label" style="display:flex;align-items:center;gap:6px;">
                            <span style="color:{{ $meta['color'] }};display:flex;align-items:center;">{!! $meta['icon'] !!}</span>
                            {{ $meta['label'] }}
                        </label>
                        <input type="{{ $meta['type'] }}" name="{{ $field }}"
                               value="{{ old($field, $user->$field) }}"
                               class="profile-input"
                               placeholder="{{ $meta['placeholder'] }}">
                        @error($field) <p style="color:#dc2626;font-size:12px;margin-top:3px;">{{ $message }}</p> @enderror
                    </div>
                    @endforeach
                </div>

                <div class="profile-section-title">비밀번호 변경</div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
                    <div class="profile-form-group">
                        <label class="profile-label">새 비밀번호</label>
                        <input type="password" name="password" class="profile-input" placeholder="변경할 경우에만 입력">
                        @error('password') <p style="color:#dc2626;font-size:12px;margin-top:3px;">{{ $message }}</p> @enderror
                    </div>
                    <div class="profile-form-group">
                        <label class="profile-label">새 비밀번호 확인</label>
                        <input type="password" name="password_confirmation" class="profile-input" placeholder="비밀번호 재입력">
                    </div>
                </div>

                <div style="padding-top:16px;border-top:1px solid #f3f4f6;display:flex;gap:10px;align-items:center;">
                    <button type="submit" class="profile-btn profile-btn-primary">저장하기</button>
                    <a href="/" class="profile-btn profile-btn-secondary">홈으로</a>
                    <span style="font-size:12px;color:#9ca3af;margin-left:auto;">아이디: {{ $user->username }}</span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
