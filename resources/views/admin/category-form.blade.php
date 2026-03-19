@extends('admin.layout')

@section('title', isset($category) ? '카테고리 수정' : '카테고리 추가')

@section('admin-content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">
        {{ isset($category) ? '카테고리 수정' : '새 카테고리' }}
    </h1>
    <a href="{{ route('admin.categories') }}" class="wp-btn wp-btn-secondary">← 목록으로</a>
</div>

<div class="wp-widget" style="max-width:640px;">
    <div class="wp-widget-header">카테고리 정보</div>
    <div class="wp-widget-body">
        <form method="POST"
              action="{{ isset($category) ? route('admin.category.update', $category->id) : route('admin.category.store') }}">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            @if($errors->any())
                <div class="wp-notice wp-notice-error">
                    <ul style="margin:0;padding-left:16px;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="wp-form-group">
                <label class="wp-form-label">카테고리 이름 <span style="color:#d63638;">*</span></label>
                <input type="text" name="name" class="wp-form-input"
                       value="{{ old('name', $category->name ?? '') }}" required maxlength="100">
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">슬러그</label>
                <input type="text" name="slug" id="slug" class="wp-form-input"
                       value="{{ old('slug', $category->slug ?? '') }}" maxlength="100"
                       placeholder="비워두면 이름으로 자동 생성">
                <p class="wp-form-help">URL에 사용됩니다. 한글, 영문, 숫자, 하이픈(-) 사용 가능합니다.</p>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">상위 카테고리</label>
                <select name="parent_id" class="wp-form-input wp-form-select">
                    <option value="">없음 (최상위)</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}"
                            {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">설명</label>
                <input type="text" name="description" class="wp-form-input"
                       value="{{ old('description', $category->description ?? '') }}" maxlength="500">
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">정렬 순서</label>
                <input type="number" name="order" class="wp-form-input" style="width:120px;"
                       value="{{ old('order', $category->order ?? 0) }}" min="0">
            </div>

            <div class="wp-form-group" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" style="width:auto;"
                       {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                <label for="is_active" style="font-size:13px;font-weight:600;cursor:pointer;">활성화</label>
            </div>

            <div style="display:flex;gap:8px;margin-top:24px;padding-top:16px;border-top:1px solid #f0f0f1;">
                <button type="submit" class="wp-btn wp-btn-primary">
                    {{ isset($category) ? '수정 저장' : '카테고리 추가' }}
                </button>
                <a href="{{ route('admin.categories') }}" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>

<script>
// 이름 입력 시 슬러그 자동 생성 (신규 등록일 때만)
@if(!isset($category))
document.querySelector('[name="name"]').addEventListener('input', function () {
    const slugField = document.getElementById('slug');
    if (slugField._manually_edited) return;
    slugField.value = this.value
        .toLowerCase()
        .replace(/[^\p{L}\p{N}\s\-]/gu, '')
        .replace(/[\s]+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
});
document.getElementById('slug').addEventListener('input', function () {
    this._manually_edited = true;
});
@endif
</script>
@endsection
