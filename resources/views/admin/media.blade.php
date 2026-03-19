@extends('admin.layout')
@section('title', '미디어 라이브러리')

@section('admin-content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">미디어 라이브러리</h1>
    <button type="button" class="wp-btn wp-btn-primary" id="upload-toggle">+ 파일 업로드</button>
</div>

@if(session('success'))
    <div class="wp-notice" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

{{-- 업로드 영역 --}}
<div id="upload-area" style="display:none;margin-bottom:20px;">
    <div class="wp-widget">
        <div class="wp-widget-header">파일 업로드</div>
        <div class="wp-widget-body">
            <div id="drop-zone" style="border:2px dashed #c3c4c7;border-radius:4px;padding:40px;text-align:center;cursor:pointer;transition:border-color .2s;background:#fafafa;position:relative;">
                <svg style="width:40px;height:40px;color:#8c8f94;margin:0 auto 12px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p style="font-size:14px;color:#2c3338;margin-bottom:6px;">파일을 드래그하거나 클릭하여 업로드</p>
                <p style="font-size:12px;color:#8c8f94;">이미지(JPG, PNG, GIF, WebP), PDF, Word, Excel · 최대 10MB</p>
                <input type="file" id="file-input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                       style="position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;">
            </div>
            <div id="upload-progress" style="display:none;margin-top:12px;">
                <div style="background:#e5e7eb;border-radius:4px;height:6px;">
                    <div id="progress-bar" style="background:#2271b1;height:6px;border-radius:4px;width:0;transition:width .3s;"></div>
                </div>
                <p id="upload-status" style="font-size:12px;color:#646970;margin-top:6px;"></p>
            </div>
            <div id="upload-results" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;margin-top:12px;"></div>
        </div>
    </div>
</div>

{{-- 필터 --}}
<div style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.media') }}" style="display:flex;gap:8px;flex:1;min-width:0;">
        <select name="type" class="wp-form-input wp-form-select" style="width:120px;height:32px;padding:4px 8px;">
            <option value="">전체</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>이미지</option>
            <option value="file"  {{ request('type') === 'file'  ? 'selected' : '' }}>파일</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}"
               class="wp-form-input" style="height:32px;max-width:240px;"
               placeholder="파일명 검색">
        <button type="submit" class="wp-btn wp-btn-secondary" style="height:32px;line-height:1;">검색</button>
        @if(request('type') || request('search'))
            <a href="{{ route('admin.media') }}" class="wp-btn wp-btn-secondary" style="height:32px;line-height:1;">초기화</a>
        @endif
    </form>
    <span style="font-size:13px;color:#646970;">총 {{ $media->total() }}개</span>
</div>

{{-- 미디어 그리드 --}}
@if($media->isEmpty())
    <div class="wp-widget">
        <div class="wp-widget-body" style="text-align:center;padding:60px;color:#8c8f94;">
            <svg style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-size:14px;">업로드된 미디어가 없습니다.</p>
        </div>
    </div>
@else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:20px;">
        @foreach($media as $item)
        <a href="{{ route('admin.media.show', $item->id) }}"
           style="background:#fff;border:1px solid #c3c4c7;border-radius:3px;overflow:hidden;display:block;text-decoration:none;transition:border-color .15s,box-shadow .15s;"
           onmouseenter="this.style.borderColor='#2271b1';this.style.boxShadow='0 2px 8px rgba(0,0,0,.1)'"
           onmouseleave="this.style.borderColor='#c3c4c7';this.style.boxShadow='none'">
            {{-- 썸네일 --}}
            <div style="height:120px;background:#f0f0f1;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                @if($item->isImage())
                    <img src="{{ $item->url }}" alt="{{ $item->alt_text ?: $item->original_name }}"
                         style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="text-align:center;padding:12px;">
                        <svg style="width:36px;height:36px;color:#8c8f94;margin:0 auto 6px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span style="font-size:11px;color:#646970;text-transform:uppercase;">{{ pathinfo($item->original_name, PATHINFO_EXTENSION) }}</span>
                    </div>
                @endif
            </div>
            {{-- 정보 --}}
            <div style="padding:8px 10px;">
                <p style="font-size:12px;color:#2c3338;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;" title="{{ $item->original_name }}">
                    {{ $item->original_name }}
                </p>
                <p style="font-size:11px;color:#8c8f94;">{{ $item->formattedSize() }} · {{ $item->created_at->format('Y.m.d') }}</p>
            </div>
        </a>
        @endforeach
    </div>

    {{-- 페이지네이션 --}}
    {{ $media->links() }}
@endif

<script>
// 업로드 영역 토글
document.getElementById('upload-toggle').addEventListener('click', function () {
    var area = document.getElementById('upload-area');
    var isOpen = area.style.display !== 'none';
    area.style.display = isOpen ? 'none' : 'block';
    this.textContent = isOpen ? '+ 파일 업로드' : '× 닫기';
});

// 드롭존
var dropZone = document.getElementById('drop-zone');
var fileInput = document.getElementById('file-input');

dropZone.addEventListener('dragover', function (e) {
    e.preventDefault();
    this.style.borderColor = '#2271b1';
    this.style.background  = '#f0f5fb';
});
dropZone.addEventListener('dragleave', function () {
    this.style.borderColor = '#c3c4c7';
    this.style.background  = '#fafafa';
});
dropZone.addEventListener('drop', function (e) {
    e.preventDefault();
    this.style.borderColor = '#c3c4c7';
    this.style.background  = '#fafafa';
    uploadFiles(e.dataTransfer.files);
});
fileInput.addEventListener('change', function () { uploadFiles(this.files); });

function uploadFiles(files) {
    if (!files.length) return;
    var total = files.length, done = 0;
    var lastId = null;
    document.getElementById('upload-progress').style.display = 'block';
    document.getElementById('progress-bar').style.width = '0%';
    document.getElementById('upload-results').innerHTML = '';

    Array.from(files).forEach(function (file) {
        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/upload/image', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                done++;
                if (data.id) lastId = data.id;
                document.getElementById('progress-bar').style.width = Math.round(done / total * 100) + '%';
                document.getElementById('upload-status').textContent = done + '/' + total + ' 업로드 완료';

                // 업로드된 파일 미리보기 추가
                var results = document.getElementById('upload-results');
                var d = document.createElement('div');
                d.style.cssText = 'border:1px solid #c3c4c7;border-radius:3px;overflow:hidden;';
                if (data.is_image) {
                    d.innerHTML = '<img src="' + data.url + '" style="width:100%;height:90px;object-fit:cover;display:block;">';
                }
                d.innerHTML += '<div style="padding:4px 6px;font-size:10px;color:#2c3338;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (data.original_name || file.name) + '</div>';
                results.appendChild(d);

                // 마지막 파일까지 완료되면 편집 페이지로 이동
                if (done === total && lastId) {
                    setTimeout(function () {
                        window.location.href = '/admin/media/' + lastId;
                    }, 800);
                }
            })
            .catch(function () {
                done++;
                document.getElementById('upload-status').textContent = file.name + ' 업로드 실패';
            });
    });
}
</script>
@endsection
