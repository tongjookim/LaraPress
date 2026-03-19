@extends('admin.layout')
@section('title', '회원 수정')
@section('admin-content')
<h1 class="wp-page-title">회원 정보 수정 <span style="font-size:13px;color:#646970;font-weight:400;">{{ $user->username }}</span></h1>

<div class="wp-widget" style="max-width:700px;">
    <div class="wp-widget-body">
        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- 기본 정보 --}}
            <div style="font-size:12px;font-weight:700;color:#8c8f94;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid #f0f0f1;">기본 정보</div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">아이디 *</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="wp-form-input">
                    @error('username') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">이름 *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="wp-form-input">
                    @error('name') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">이메일 *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="wp-form-input">
                    @error('email') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">새 비밀번호</label>
                    <input type="password" name="password" class="wp-form-input" placeholder="변경 시에만 입력">
                    @error('password') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">역할</label>
                    <select name="role" class="wp-form-input wp-form-select">
                        <option value="subscriber" {{ old('role', $user->role) == 'subscriber' ? 'selected' : '' }}>구독자</option>
                        <option value="author"     {{ old('role', $user->role) == 'author'     ? 'selected' : '' }}>작성자</option>
                        <option value="editor"     {{ old('role', $user->role) == 'editor'     ? 'selected' : '' }}>편집자</option>
                        <option value="admin"      {{ old('role', $user->role) == 'admin'      ? 'selected' : '' }}>관리자</option>
                    </select>
                </div>
                <div class="wp-form-group" style="padding-top:28px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                        계정 활성화
                    </label>
                </div>
            </div>

            {{-- 프로필 / 작성자 박스 --}}
            <div style="font-size:12px;font-weight:700;color:#8c8f94;text-transform:uppercase;letter-spacing:.05em;margin:20px 0 12px;padding-bottom:6px;border-bottom:1px solid #f0f0f1;">프로필 &amp; 작성자 박스</div>

            <div class="wp-form-group">
                <label class="wp-form-label">프로필 이미지</label>
                @if($user->profile_image)
                <div style="margin-bottom:8px;display:flex;align-items:center;gap:12px;">
                    <img src="{{ $user->profile_image }}" alt="프로필" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid #c3c4c7;">
                    <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#d63638;cursor:pointer;">
                        <input type="checkbox" name="clear_profile_image" value="1"> 이미지 삭제
                    </label>
                </div>
                @endif
                <input type="file" name="profile_image_file" accept="image/*" class="wp-form-input" style="padding:4px;">
                <p class="wp-form-help">PNG, JPG, WebP · 최대 2MB · 권장 크기: 200×200px 이상</p>
                @error('profile_image_file') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">소개 (Bio)</label>
                <textarea name="bio" rows="4" class="wp-form-input wp-form-textarea" placeholder="작성자 소개 문구 (기사 하단 작성자 박스에 표시됩니다)">{{ old('bio', $user->bio) }}</textarea>
                <p class="wp-form-help">최대 1000자</p>
                @error('bio') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label style="display:flex;align-items:flex-start;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="author_box_enabled" value="1" {{ $user->author_box_enabled ? 'checked' : '' }} style="margin-top:2px;">
                    <div>
                        <span style="font-weight:600;color:#1d2327;">기사 하단 작성자 박스 표시</span>
                        <p class="wp-form-help" style="margin-top:2px;">활성화 시 이 회원이 작성한 기사 본문 하단에 작성자 소개 박스가 표시됩니다.</p>
                    </div>
                </label>
            </div>

            {{-- 소셜 링크 --}}
            <div style="font-size:12px;font-weight:700;color:#8c8f94;text-transform:uppercase;letter-spacing:.05em;margin:20px 0 12px;padding-bottom:6px;border-bottom:1px solid #f0f0f1;">소셜 링크</div>

            @php
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
            @endphp

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                @foreach($socials as $field => $meta)
                <div class="wp-form-group">
                    <label class="wp-form-label">{{ $meta['label'] }}</label>
                    @if($field === 'social_email')
                    <input type="email" name="{{ $field }}" value="{{ old($field, $user->$field) }}" class="wp-form-input" placeholder="{{ $meta['placeholder'] }}">
                    @else
                    <input type="url" name="{{ $field }}" value="{{ old($field, $user->$field) }}" class="wp-form-input" placeholder="{{ $meta['placeholder'] }}">
                    @endif
                    @error($field) <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>
                @endforeach
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;display:flex;gap:8px;">
                <button type="submit" class="wp-btn wp-btn-primary">저장</button>
                <a href="{{ route('admin.users') }}" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>
@endsection
