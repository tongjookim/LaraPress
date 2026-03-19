@extends('admin.layout')
@section('title', '미디어 정보 편집')

@section('admin-content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">미디어 정보 편집</h1>
    <a href="{{ route('admin.media') }}" class="wp-btn wp-btn-secondary">← 미디어 라이브러리</a>
</div>

<div id="save-notice" style="display:none;margin-bottom:16px;" class="wp-notice"></div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    {{-- 미리보기 --}}
    <div class="wp-widget">
        <div class="wp-widget-header">미리보기</div>
        <div class="wp-widget-body" style="text-align:center;padding:24px;">
            @if($media->isImage())
                <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: $media->original_name }}"
                     style="max-width:100%;max-height:500px;border:1px solid #c3c4c7;border-radius:3px;">
            @else
                <div style="padding:40px;background:#f0f0f1;border-radius:4px;display:inline-block;">
                    <svg style="width:64px;height:64px;color:#8c8f94;display:block;margin:0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p style="font-size:24px;font-weight:700;color:#646970;text-transform:uppercase;">
                        {{ pathinfo($media->original_name, PATHINFO_EXTENSION) }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- 사이드 패널 --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- 파일 정보 --}}
        <div class="wp-widget">
            <div class="wp-widget-header">파일 정보</div>
            <div class="wp-widget-body" style="font-size:13px;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;gap:8px;">
                    <span style="color:#8c8f94;width:70px;flex-shrink:0;">파일명</span>
                    <span style="color:#2c3338;word-break:break-all;">{{ $media->original_name }}</span>
                </div>
                <div style="display:flex;gap:8px;">
                    <span style="color:#8c8f94;width:70px;flex-shrink:0;">형식</span>
                    <span style="color:#2c3338;">{{ $media->mime_type }}</span>
                </div>
                <div style="display:flex;gap:8px;">
                    <span style="color:#8c8f94;width:70px;flex-shrink:0;">크기</span>
                    <span style="color:#2c3338;">{{ $media->formattedSize() }}</span>
                </div>
                <div style="display:flex;gap:8px;">
                    <span style="color:#8c8f94;width:70px;flex-shrink:0;">업로드</span>
                    <span style="color:#2c3338;">{{ $media->created_at->format('Y년 m월 d일 H:i') }}</span>
                </div>
                <div style="display:flex;gap:8px;">
                    <span style="color:#8c8f94;width:70px;flex-shrink:0;">업로더</span>
                    <span style="color:#2c3338;">{{ $media->user->name ?? '알 수 없음' }}</span>
                </div>
            </div>
        </div>

        {{-- URL 복사 / 다운로드 --}}
        <div class="wp-widget">
            <div class="wp-widget-header">파일 URL</div>
            <div class="wp-widget-body" style="display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;gap:6px;">
                    <input type="text" id="url-display" readonly
                           value="{{ url($media->url) }}"
                           style="flex:1;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;background:#f6f7f7;color:#2c3338;min-width:0;">
                    <button type="button" id="copy-url-btn"
                            style="padding:6px 12px;background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;cursor:pointer;white-space:nowrap;">복사</button>
                </div>
                <a href="{{ $media->url }}" download="{{ $media->original_name }}"
                   style="display:flex;align-items:center;justify-content:center;gap:6px;padding:6px;background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;text-decoration:none;color:#2c3338;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    다운로드
                </a>
            </div>
        </div>

        {{-- 메타 정보 편집 --}}
        <div class="wp-widget">
            <div class="wp-widget-header">메타 정보</div>
            <div class="wp-widget-body">
                <div class="wp-form-group">
                    <label class="wp-form-label">대체 텍스트 (Alt)</label>
                    <input type="text" id="f-alt" class="wp-form-input"
                           placeholder="스크린 리더 및 SEO에 사용"
                           value="{{ $media->alt_text }}">
                    <p class="wp-form-help">이미지를 설명하는 짧은 텍스트</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">제목</label>
                    <input type="text" id="f-title" class="wp-form-input"
                           value="{{ $media->title }}">
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">캡션</label>
                    <textarea id="f-caption" class="wp-form-input wp-form-textarea"
                              rows="2">{{ $media->caption }}</textarea>
                </div>
                <div class="wp-form-group" style="margin-bottom:0;">
                    <label class="wp-form-label">설명</label>
                    <textarea id="f-desc" class="wp-form-input wp-form-textarea"
                              rows="3">{{ $media->description }}</textarea>
                </div>
            </div>
        </div>

        {{-- 저장 / 삭제 --}}
        <div style="display:flex;flex-direction:column;gap:8px;">
            <button type="button" id="save-btn" class="wp-btn wp-btn-primary" style="width:100%;">
                변경사항 저장
            </button>
            <button type="button" id="delete-btn"
                    style="width:100%;padding:7px;background:#fce8e6;border:1px solid #f5aca6;border-radius:3px;font-size:13px;font-weight:700;color:#d63638;cursor:pointer;">
                영구 삭제
            </button>
        </div>

    </div>
</div>

<script>
var mediaId  = {{ $media->id }};
var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// URL 복사
document.getElementById('copy-url-btn').addEventListener('click', function () {
    var input = document.getElementById('url-display');
    var btn   = this;
    navigator.clipboard.writeText(input.value).then(function () {
        btn.textContent = '복사됨!';
        setTimeout(function () { btn.textContent = '복사'; }, 1500);
    }).catch(function () {
        input.select();
        document.execCommand('copy');
        btn.textContent = '복사됨!';
        setTimeout(function () { btn.textContent = '복사'; }, 1500);
    });
});

// 메타 저장
document.getElementById('save-btn').addEventListener('click', function () {
    var btn = this;
    btn.disabled = true;
    btn.textContent = '저장 중...';

    fetch('/admin/media/' + mediaId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            alt_text:    document.getElementById('f-alt').value,
            title:       document.getElementById('f-title').value,
            caption:     document.getElementById('f-caption').value,
            description: document.getElementById('f-desc').value,
        })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        var notice = document.getElementById('save-notice');
        if (data.ok) {
            notice.className = 'wp-notice';
            notice.textContent = '저장되었습니다.';
        } else {
            notice.className = 'wp-notice wp-notice-error';
            notice.textContent = '저장 실패: ' + JSON.stringify(data);
        }
        notice.style.display = 'block';
        setTimeout(function () { notice.style.display = 'none'; }, 3000);
    })
    .catch(function () {
        var notice = document.getElementById('save-notice');
        notice.className = 'wp-notice wp-notice-error';
        notice.textContent = '저장 중 오류가 발생했습니다.';
        notice.style.display = 'block';
    })
    .finally(function () { btn.disabled = false; btn.textContent = '변경사항 저장'; });
});

// 영구 삭제
document.getElementById('delete-btn').addEventListener('click', function () {
    if (!confirm('"{{ $media->original_name }}"을(를) 영구 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.')) return;

    fetch('/admin/media/' + mediaId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.ok) {
            window.location.href = '{{ route('admin.media') }}';
        }
    });
});
</script>
@endsection
