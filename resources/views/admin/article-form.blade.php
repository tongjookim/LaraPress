@extends('admin.layout')

@section('title', isset($article) ? '기사 수정' : '기사 작성')

@section('admin-content')

<style>
/* ── 기사 작성 레이아웃 ── */
.af-header {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 20px; gap: 12px; flex-wrap: wrap;
}
/* 본문(좌 70%) | 사이드(우 30%) */
.af-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}
.af-main-col { min-width: 0; }
.af-side-col  { min-width: 0; }

/* 모바일 고정 하단 저장 바 */
.af-sticky-bar {
    display: none;
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 250;
    background: #fff; border-top: 2px solid #c3c4c7;
    padding: 10px 14px; gap: 8px;
    box-shadow: 0 -2px 8px rgba(0,0,0,.1);
}
/* 사이드 패널 아코디언 토글 (모바일 전용) */
.af-panel-toggle {
    display: none;
    width: 100%; background: none; border: none; cursor: pointer;
    text-align: left; padding: 0;
}
.af-panel-toggle-icon { transition: transform .2s; }

/* ── 태블릿 (1024px 미만): 1열 스택 ── */
@media (max-width: 1024px) {
    .af-grid { grid-template-columns: 1fr; }
    /* 본문 먼저, 사이드 아래 */
    .af-main-col { order: 1; }
    .af-side-col  { order: 2; }
    /* 사이드 패널 가로 2열 배치 */
    .af-side-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .af-side-col > .wp-widget { margin-bottom: 0 !important; }
    .af-seo-widget { grid-column: 1 / -1; }
    /* 데스크탑 저장버튼 숨김 (하단 바 사용) */
    .af-desktop-save { display: none !important; }
}
/* ── 모바일 (768px 미만) ── */
@media (max-width: 768px) {
    .af-header .wp-btn { font-size: 12px; padding: 3px 10px; }
    .af-sticky-bar { display: flex; }
    .wp-main { padding-bottom: 68px; }
    /* 사이드 1열 */
    .af-side-col { grid-template-columns: 1fr; }
    .af-side-col > .wp-widget { margin-bottom: 12px !important; }
    /* 아코디언 */
    .af-panel-toggle { display: flex; align-items: center; justify-content: space-between; }
    .af-panel-body { display: none; }
    .af-panel-body.open { display: block; }
    #title { font-size: 16px !important; }
}
@media (max-width: 480px) {
    .af-header { margin-bottom: 14px; }
    .wp-content { padding: 10px; }
    .wp-widget-body { padding: 10px; }
}
</style>

{{-- 헤더 --}}
<div class="af-header">
    <h1 class="wp-page-title" style="margin-bottom:0;">
        {{ isset($article) ? '기사 수정' : '새 기사 작성' }}
    </h1>
    <a href="{{ route('admin.articles') }}" class="wp-btn wp-btn-secondary">← 목록으로</a>
</div>

<form method="POST"
      action="{{ isset($article) ? route('admin.article.update', $article->id) : route('admin.article.store') }}"
      id="article-form">
    @csrf
    @if(isset($article)) @method('PUT') @endif

    @if($errors->any())
        <div class="wp-notice wp-notice-error" style="margin-bottom:16px;">
            <ul style="margin:0;padding-left:16px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="af-grid">

        {{-- ── 본문 영역 (좌측 70~75%) ── --}}
        <div class="af-main-col">
            <div class="wp-widget" style="margin-bottom:16px;">
                <div class="wp-widget-body">
                    <div class="wp-form-group">
                        <input type="text" name="title" id="title" class="wp-form-input"
                               style="font-size:20px;font-weight:700;padding:10px 12px;"
                               placeholder="제목을 입력하세요" required maxlength="300"
                               value="{{ old('title', $article->title ?? '') }}">
                    </div>
                    <div class="wp-form-group">
                        <input type="text" name="subtitle" id="subtitle" class="wp-form-input"
                               style="font-size:15px;color:#646970;padding:8px 12px;"
                               placeholder="부제목 (선택사항)" maxlength="300"
                               value="{{ old('subtitle', $article->subtitle ?? '') }}">
                    </div>
                    <div class="wp-form-group" style="margin-bottom:0;">
                        <label class="wp-form-label">슬러그</label>
                        <input type="text" name="slug" id="slug" class="wp-form-input"
                               placeholder="비워두면 제목으로 자동 생성됩니다"
                               value="{{ old('slug', $article->slug ?? '') }}" maxlength="300">
                        <p class="wp-form-help">URL 경로에 사용됩니다.</p>
                    </div>
                </div>
            </div>

            <div class="wp-widget" style="margin-bottom:16px;">
                <div class="wp-widget-header">본문</div>
                <div class="wp-widget-body">
                    <textarea name="content" id="se2_content" style="width:100%;height:520px;display:none;">{{ old('content', $article->content ?? '') }}</textarea>
                </div>
            </div>

            <div class="wp-widget">
                <div class="wp-widget-header">발췌문 (Excerpt)</div>
                <div class="wp-widget-body">
                    <textarea name="excerpt" class="wp-form-input wp-form-textarea"
                              style="min-height:80px;"
                              placeholder="목록에 표시될 요약 문장 (비워두면 본문에서 자동 생성 가능)">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                    <p class="wp-form-help">최대 500자</p>
                </div>
            </div>
        </div>

        {{-- ── 설정 패널 (우측 25~30%) ── --}}
        <div class="af-side-col">

            {{-- 게시 옵션 --}}
            <div class="wp-widget" style="margin-bottom:16px;">
                <div class="wp-widget-header">
                    <button type="button" class="af-panel-toggle" onclick="afTogglePanel(this)">
                        <span>게시</span>
                        <svg class="af-panel-toggle-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <span class="af-desktop-label">게시</span>
                </div>
                <div class="wp-widget-body af-panel-body open">
                    <div class="wp-form-group">
                        <label class="wp-form-label">상태</label>
                        <select name="status" id="status" class="wp-form-input wp-form-select">
                            <option value="draft"     {{ old('status', $article->status ?? 'draft') === 'draft'     ? 'selected' : '' }}>초안</option>
                            <option value="pending"   {{ old('status', $article->status ?? '') === 'pending'   ? 'selected' : '' }}>승인 대기</option>
                            <option value="published" {{ old('status', $article->status ?? '') === 'published' ? 'selected' : '' }}>게시됨</option>
                        </select>
                    </div>
                    <div class="wp-form-group" id="published-at-group"
                         style="{{ in_array(old('status', $article->status ?? 'draft'), ['published']) ? '' : 'display:none;' }}">
                        <label class="wp-form-label">게시 일시</label>
                        <input type="datetime-local" name="published_at" class="wp-form-input"
                               value="{{ old('published_at', isset($article->published_at) ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
                        <p class="wp-form-help">비워두면 저장 시각으로 자동 설정됩니다.</p>
                    </div>
                    <div class="af-desktop-save" style="display:flex;gap:8px;margin-top:16px;">
                        <button type="submit" class="wp-btn wp-btn-primary" style="flex:1;">
                            {{ isset($article) ? '저장' : '등록' }}
                        </button>
                        <a href="{{ route('admin.articles') }}" class="wp-btn wp-btn-secondary">취소</a>
                    </div>
                </div>
            </div>

            {{-- 카테고리 --}}
            <div class="wp-widget" style="margin-bottom:16px;">
                <div class="wp-widget-header">
                    <button type="button" class="af-panel-toggle" onclick="afTogglePanel(this)">
                        <span>카테고리</span>
                        <svg class="af-panel-toggle-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <span class="af-desktop-label">카테고리</span>
                </div>
                <div class="wp-widget-body af-panel-body open">
                    @if($categories->isEmpty())
                        <p style="font-size:13px;color:#8c8f94;">
                            등록된 카테고리가 없습니다.
                            <a href="{{ route('admin.category.create') }}" style="color:#2271b1;">추가하기</a>
                        </p>
                    @else
                        <select name="category_id" class="wp-form-input wp-form-select">
                            <option value="">카테고리 없음</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $article->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            {{-- 대표 이미지 --}}
            <div class="wp-widget" style="margin-bottom:16px;">
                <div class="wp-widget-header">
                    <button type="button" class="af-panel-toggle" onclick="afTogglePanel(this)">
                        <span>대표 이미지</span>
                        <svg class="af-panel-toggle-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <span class="af-desktop-label">대표 이미지</span>
                </div>
                <div class="wp-widget-body af-panel-body open">
                    <div id="thumbnail-preview" style="margin-bottom:10px;display:{{ old('thumbnail', $article->thumbnail ?? '') ? 'block' : 'none' }};">
                        <img id="thumbnail-img"
                             src="{{ old('thumbnail', $article->thumbnail ?? '') }}"
                             style="width:100%;border:1px solid #c3c4c7;border-radius:2px;">
                        <button type="button" id="thumbnail-remove"
                                style="margin-top:6px;font-size:12px;color:#d63638;background:none;border:none;cursor:pointer;padding:0;">
                            × 이미지 제거
                        </button>
                    </div>
                    <input type="hidden" name="thumbnail" id="thumbnail-url"
                           value="{{ old('thumbnail', $article->thumbnail ?? '') }}">
                    <button type="button" class="wp-btn wp-btn-secondary" style="width:100%;margin-bottom:8px;"
                            onclick="MediaPicker.open({mode:'picker', onSelect: function(m){ setThumbnail(m.url); }})">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        미디어 라이브러리
                    </button>
                    <input type="text" id="thumbnail-url-input" class="wp-form-input"
                           style="margin-top:4px;"
                           placeholder="이미지 URL 직접 입력"
                           value="{{ old('thumbnail', $article->thumbnail ?? '') }}">
                </div>
            </div>

            {{-- SEO 패널 --}}
            <div class="wp-widget af-seo-widget" style="margin-bottom:16px;">
                <div class="wp-widget-header" style="cursor:pointer;user-select:none;"
                     onclick="toggleSeoPanel()">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span>🔍 SEO 설정</span>
                        <svg id="seo-toggle-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transition:transform .2s;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div id="seo-panel" style="display:none;">
                    <div class="wp-widget-body" style="border-top:1px solid #f0f0f1;">
                        <div class="wp-form-group">
                            <label class="wp-form-label">포커스 키워드</label>
                            <input type="text" name="focus_keyword" id="focus-keyword" class="wp-form-input"
                                   placeholder="예: 라라벨 CMS"
                                   value="{{ old('focus_keyword', $article->focus_keyword ?? '') }}">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">검색 결과 미리보기</label>
                            <div id="serp-preview" style="border:1px solid #e0e0e0;border-radius:8px;padding:12px 14px;background:#fff;font-family:arial,sans-serif;">
                                <div style="font-size:11px;color:#202124;margin-bottom:3px;">
                                    <span style="color:#0f9d58;">{{ parse_url(url('/'), PHP_URL_HOST) }}</span> › news › ...
                                </div>
                                <div id="serp-title" style="font-size:16px;color:#1a0dab;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                                <div id="serp-desc" style="font-size:12px;color:#4d5156;line-height:1.5;margin-top:2px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"></div>
                            </div>
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">SEO 제목 <span style="font-weight:400;color:#8c8f94;">(meta title)</span></label>
                            <input type="text" name="meta_title" id="seo-title" class="wp-form-input"
                                   placeholder="비워두면 기사 제목 사용" maxlength="100"
                                   value="{{ old('meta_title', $article->meta_title ?? '') }}">
                            <div style="display:flex;justify-content:space-between;margin-top:3px;">
                                <p class="wp-form-help" style="margin:0;"><strong>50–60자</strong> 권장</p>
                                <span id="seo-title-count" style="font-size:11px;color:#646970;">0 / 60</span>
                            </div>
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">SEO 설명 <span style="font-weight:400;color:#8c8f94;">(meta description)</span></label>
                            <textarea name="meta_description" id="seo-desc" class="wp-form-input wp-form-textarea"
                                      rows="3" maxlength="300"
                                      placeholder="비워두면 발췌문 또는 본문 앞부분 사용">{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
                            <div style="display:flex;justify-content:space-between;margin-top:3px;">
                                <p class="wp-form-help" style="margin:0;"><strong>150–160자</strong> 권장</p>
                                <span id="seo-desc-count" style="font-size:11px;color:#646970;">0 / 160</span>
                            </div>
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">소셜 공유 이미지 (OG Image)</label>
                            <input type="url" name="og_image" id="og-image-url" class="wp-form-input"
                                   placeholder="비워두면 대표 이미지 사용"
                                   value="{{ old('og_image', $article->og_image ?? '') }}">
                            <p class="wp-form-help">1200×630px 권장</p>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:#646970;margin-bottom:8px;text-transform:uppercase;letter-spacing:.04em;">SEO 체크리스트</div>
                            <div id="seo-checklist" style="font-size:12px;display:flex;flex-direction:column;gap:5px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /.af-grid --}}
</form>

{{-- 모바일 고정 하단 저장 바 --}}
<div class="af-sticky-bar">
    <select name="status" id="status-mobile" class="wp-form-input wp-form-select" style="flex:1;max-width:120px;height:40px;font-size:13px;">
        <option value="draft"     {{ old('status', $article->status ?? 'draft') === 'draft'     ? 'selected' : '' }}>초안</option>
        <option value="pending"   {{ old('status', $article->status ?? '') === 'pending'   ? 'selected' : '' }}>승인 대기</option>
        <option value="published" {{ old('status', $article->status ?? '') === 'published' ? 'selected' : '' }}>게시됨</option>
    </select>
    <button type="submit" form="article-form" class="wp-btn wp-btn-primary" style="flex:1;height:40px;font-size:14px;font-weight:700;">
        {{ isset($article) ? '💾 저장' : '✅ 등록' }}
    </button>
    <a href="{{ route('admin.articles') }}" class="wp-btn wp-btn-secondary" style="height:40px;line-height:28px;">취소</a>
</div>

@include('partials.smarteditor', ['editorId' => 'se2_content', 'editorHeight' => 520])
@include('admin.partials.media-picker')

<script>
// 데스크탑 저장버튼 / 모바일 아코디언 라벨 분기
(function () {
    var isMobile = window.innerWidth <= 768;
    // 데스크탑 레이블 숨기기 (모바일에서만 아코디언 사용)
    document.querySelectorAll('.af-desktop-label').forEach(function (el) {
        el.style.display = isMobile ? 'none' : 'block';
    });
    document.querySelectorAll('.af-panel-toggle').forEach(function (el) {
        el.style.display = isMobile ? 'flex' : 'none';
    });
    if (isMobile) {
        document.querySelectorAll('.af-panel-body').forEach(function (el) {
            el.classList.remove('open');
        });
    }
    // 모바일 저장 바의 상태값을 메인 폼과 동기화
    var mobileStatus = document.getElementById('status-mobile');
    var mainStatus   = document.getElementById('status');
    if (mobileStatus && mainStatus) {
        mobileStatus.value = mainStatus.value;
        mobileStatus.addEventListener('change', function () {
            mainStatus.value = this.value;
            mainStatus.dispatchEvent(new Event('change'));
        });
        mainStatus.addEventListener('change', function () {
            mobileStatus.value = this.value;
        });
    }
}());

// 아코디언 토글
function afTogglePanel(btn) {
    var body = btn.closest('.wp-widget').querySelector('.af-panel-body');
    var icon = btn.querySelector('.af-panel-toggle-icon');
    var open = body.classList.toggle('open');
    if (icon) icon.style.transform = open ? 'rotate(180deg)' : '';
}

// 게시 상태에 따라 게시일시 필드 토글
document.getElementById('status').addEventListener('change', function () {
    document.getElementById('published-at-group').style.display =
        this.value === 'published' ? 'block' : 'none';
});

// 썸네일 설정
function setThumbnail(url) {
    document.getElementById('thumbnail-url').value = url;
    document.getElementById('thumbnail-url-input').value = url;
    if (url) {
        document.getElementById('thumbnail-img').src = url;
        document.getElementById('thumbnail-preview').style.display = 'block';
    } else {
        document.getElementById('thumbnail-preview').style.display = 'none';
    }
    updateChecklist();
}
document.getElementById('thumbnail-url-input').addEventListener('input', function () {
    setThumbnail(this.value);
});
document.getElementById('thumbnail-remove').addEventListener('click', function () {
    setThumbnail('');
});

// 슬러그 자동 생성
@if(!isset($article))
document.getElementById('title').addEventListener('input', function () {
    var slugField = document.getElementById('slug');
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

// SEO 패널 토글
function toggleSeoPanel() {
    var panel = document.getElementById('seo-panel');
    var icon  = document.getElementById('seo-toggle-icon');
    var open  = panel.style.display === 'none';
    panel.style.display = open ? 'block' : 'none';
    icon.style.transform = open ? 'rotate(180deg)' : '';
    if (open) updateSerpPreview();
}

// 글자 수 카운터
function seoCounter(inputId, countId, max) {
    var el  = document.getElementById(inputId);
    var cnt = document.getElementById(countId);
    if (!el || !cnt) return;
    function update() {
        var len = el.value.length;
        cnt.textContent = len + ' / ' + max;
        cnt.style.color = len > max ? '#d63638' : (len >= max * 0.85 ? '#d97706' : '#646970');
    }
    el.addEventListener('input', function () { update(); updateSerpPreview(); updateChecklist(); });
    update();
}
seoCounter('seo-title', 'seo-title-count', 60);
seoCounter('seo-desc',  'seo-desc-count',  160);

// SERP 미리보기
function updateSerpPreview() {
    var title   = (document.getElementById('seo-title')?.value || document.getElementById('title')?.value || '');
    var excerpt = document.querySelector('textarea[name="excerpt"]')?.value || '';
    var desc    = document.getElementById('seo-desc')?.value || excerpt;
    var titleEl = document.getElementById('serp-title');
    var descEl  = document.getElementById('serp-desc');
    if (titleEl) titleEl.textContent = title || '(제목 없음)';
    if (descEl)  descEl.textContent  = desc  || '(설명 없음)';
}
document.getElementById('title')?.addEventListener('input', function () { updateSerpPreview(); updateChecklist(); });
document.querySelector('textarea[name="excerpt"]')?.addEventListener('input', updateSerpPreview);
document.getElementById('focus-keyword')?.addEventListener('input', updateChecklist);

// SEO 체크리스트
var SEO_CHECKS = [
    { id:'c-kw',     label:'포커스 키워드가 설정되었습니다.',         check: function() { return (document.getElementById('focus-keyword')?.value||'').trim().length > 0; } },
    { id:'c-title',  label:'SEO 제목 길이가 적절합니다 (50–60자).',  check: function() { var l=(document.getElementById('seo-title')?.value||document.getElementById('title')?.value||'').length; return l>=50&&l<=60; } },
    { id:'c-kw-t',   label:'포커스 키워드가 제목에 포함됩니다.',      check: function() { var kw=(document.getElementById('focus-keyword')?.value||'').toLowerCase(); var t=(document.getElementById('seo-title')?.value||document.getElementById('title')?.value||'').toLowerCase(); return kw.length>0&&t.includes(kw); } },
    { id:'c-desc',   label:'SEO 설명 길이가 적절합니다 (150–160자).', check: function() { var l=(document.getElementById('seo-desc')?.value||'').length; return l>=150&&l<=160; } },
    { id:'c-kw-d',   label:'포커스 키워드가 설명에 포함됩니다.',      check: function() { var kw=(document.getElementById('focus-keyword')?.value||'').toLowerCase(); var d=(document.getElementById('seo-desc')?.value||'').toLowerCase(); return kw.length>0&&d.includes(kw); } },
    { id:'c-thumb',  label:'대표 이미지(썸네일)가 설정되었습니다.',   check: function() { return (document.getElementById('thumbnail-url')?.value||'').length>0; } },
    { id:'c-slug',   label:'슬러그가 설정되었습니다.',                check: function() { return (document.getElementById('slug')?.value||'').length>0; } },
    { id:'c-excerpt',label:'발췌문이 입력되었습니다.',                 check: function() { return (document.querySelector('textarea[name="excerpt"]')?.value||'').length>20; } },
];
function updateChecklist() {
    var container = document.getElementById('seo-checklist');
    if (!container) return;
    container.innerHTML = '';
    var score = 0;
    SEO_CHECKS.forEach(function (item) {
        var pass = item.check();
        if (pass) score++;
        var div = document.createElement('div');
        div.style.cssText = 'display:flex;align-items:center;gap:6px;padding:4px 8px;border-radius:3px;background:' + (pass?'#d7edda':'#f6f7f7');
        div.innerHTML = '<span style="color:'+(pass?'#2d7a3a':'#8c8f94')+';font-size:14px;">'+(pass?'✓':'○')+'</span>'
            + '<span style="color:'+(pass?'#1e4620':'#50575e')+';">'+item.label+'</span>';
        container.appendChild(div);
    });
    var scoreDiv = document.createElement('div');
    var pct = Math.round(score / SEO_CHECKS.length * 100);
    var color = pct>=80?'#2d7a3a':pct>=50?'#92400e':'#6d1010';
    scoreDiv.style.cssText = 'margin-top:8px;padding:6px 10px;border-radius:3px;background:'+(pct>=80?'#d7edda':pct>=50?'#fef3c7':'#fce8e6')+';font-weight:700;font-size:12px;color:'+color+';text-align:center;';
    scoreDiv.textContent = 'SEO 점수: ' + score + ' / ' + SEO_CHECKS.length + ' (' + pct + '%)';
    container.appendChild(scoreDiv);
}
updateSerpPreview();
updateChecklist();
</script>
@endsection
