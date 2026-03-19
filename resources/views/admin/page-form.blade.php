@extends('admin.layout')
@section('title', isset($page) ? '페이지 수정' : '페이지 생성')
@section('admin-content')
<h1 class="wp-page-title">{{ isset($page) ? '페이지 수정' : '새 페이지 생성' }}</h1>

<div class="wp-widget" style="max-width:800px;">
    <div class="wp-widget-body">
        <form action="{{ isset($page) ? route('admin.page.update', $page->id) : route('admin.page.store') }}" method="POST">
            @csrf
            @if(isset($page)) @method('PUT') @endif

            <div class="wp-form-group">
                <label class="wp-form-label">페이지 제목 *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $page->title ?? '') }}" required class="wp-form-input">
                @error('title') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">URL 슬러그 *</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug ?? '') }}" required class="wp-form-input">
                <p class="wp-form-help">영문, 숫자, 하이픈만 사용 · 접속: /page/{{ old('slug', $page->slug ?? 'your-slug') }}</p>
                @error('slug') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">페이지 내용 *</label>
                <textarea name="content" id="se2_content" style="width:100%;height:480px;display:none;">{{ old('content', $page->content ?? '') }}</textarea>
                @error('content') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">정렬 순서</label>
                <input type="number" name="order" value="{{ old('order', $page->order ?? 0) }}" min="0" class="wp-form-input" style="max-width:120px;">
            </div>

            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }}>
                    활성화 (페이지 공개)
                </label>
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;">
                <button type="submit" class="wp-btn wp-btn-primary">{{ isset($page) ? '수정 저장' : '페이지 생성' }}</button>
                <a href="{{ route('admin.pages') }}" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>

@include('partials.smarteditor', ['editorId' => 'se2_content', 'editorHeight' => 480])

<script>
document.getElementById('title').addEventListener('input', function() {
    if (!document.getElementById('slug').value || {{ isset($page) ? 'false' : 'true' }}) {
        document.getElementById('slug').value = this.value.toLowerCase()
            .replace(/[^a-z0-9가-힣\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    }
});
</script>
@endsection
