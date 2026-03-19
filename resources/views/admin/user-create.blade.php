@extends('admin.layout')
@section('title', '회원 추가')
@section('admin-content')
<h1 class="wp-page-title">회원 추가</h1>

<div class="wp-widget" style="max-width:600px;">
    <div class="wp-widget-body">
        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf

            <div class="wp-form-group">
                <label class="wp-form-label">아이디 *</label>
                <input type="text" name="username" value="{{ old('username') }}" required class="wp-form-input" placeholder="영문, 숫자, _ - 만 사용 가능">
                @error('username') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">이름 *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="wp-form-input">
                @error('name') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">이메일 *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="wp-form-input">
                @error('email') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">비밀번호 *</label>
                <input type="password" name="password" required class="wp-form-input" placeholder="최소 6자">
                @error('password') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">역할 *</label>
                <select name="role" class="wp-form-input wp-form-select">
                    <option value="subscriber" {{ old('role')=='subscriber'?'selected':'' }}>구독자</option>
                    <option value="author"     {{ old('role')=='author'    ?'selected':'' }}>작성자</option>
                    <option value="editor"     {{ old('role')=='editor'    ?'selected':'' }}>편집자</option>
                    <option value="admin"      {{ old('role')=='admin'     ?'selected':'' }}>관리자</option>
                </select>
                <p class="wp-form-help">구독자: 읽기 전용 / 작성자: 기사 작성(승인 필요) / 편집자: 기사 승인 / 관리자: 전체 권한</p>
            </div>

            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    활성화 (즉시 로그인 가능)
                </label>
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;display:flex;gap:8px;">
                <button type="submit" class="wp-btn wp-btn-primary">회원 추가</button>
                <a href="{{ route('admin.users') }}" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>
@endsection
