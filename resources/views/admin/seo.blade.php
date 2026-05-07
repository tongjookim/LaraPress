@extends('admin.layout')
@section('title', 'SEO 설정')
@section('admin-content')
<h1 class="wp-page-title">SEO 설정</h1>

@php
    $tab = request('tab', 'meta');
@endphp

{{-- 탭 --}}
<div style="display:flex;gap:0;border-bottom:1px solid #c3c4c7;margin-bottom:20px;">
    @foreach(['meta'=>'🔍 메타태그','sitemap'=>'🗺️ 사이트맵','rss'=>'📡 RSS 피드'] as $key=>$label)
    <a href="?tab={{ $key }}"
       style="padding:8px 18px;font-size:13px;font-weight:{{ $tab===$key?'700':'400' }};text-decoration:none;border:1px solid {{ $tab===$key?'#c3c4c7':'transparent' }};border-bottom:{{ $tab===$key?'1px solid #f0f0f1':'none' }};margin-bottom:-1px;background:{{ $tab===$key?'#f0f0f1':'transparent' }};color:{{ $tab===$key?'#1d2327':'#646970' }};border-radius:3px 3px 0 0;">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- 메타태그 탭 --}}
@if($tab === 'meta')
<form action="{{ route('admin.seo.update') }}" method="POST">
    @csrf

    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">기본 메타 태그</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label class="wp-form-label">사이트 메타 타이틀</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $settings['meta_title']) }}" class="wp-form-input" maxlength="100" id="site-meta-title">
                <div style="display:flex;justify-content:space-between;margin-top:3px;">
                    <p class="wp-form-help" style="margin:0;">비워두면 사이트 이름 사용 · <strong>50–60자</strong> 권장</p>
                    <span id="site-meta-title-count" style="font-size:11px;color:#646970;">0 / 60</span>
                </div>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">사이트 메타 설명</label>
                <textarea name="meta_description" rows="3" class="wp-form-input wp-form-textarea" maxlength="300" id="site-meta-desc">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                <div style="display:flex;justify-content:space-between;margin-top:3px;">
                    <p class="wp-form-help" style="margin:0;"><strong>150–160자</strong> 권장</p>
                    <span id="site-meta-desc-count" style="font-size:11px;color:#646970;">0 / 160</span>
                </div>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">메타 키워드</label>
                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords']) }}" class="wp-form-input" placeholder="뉴스, 미디어, 언론">
                <p class="wp-form-help">쉼표로 구분 (현대 검색엔진에서는 참고용)</p>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">메타 작성자</label>
                <input type="text" name="meta_author" value="{{ old('meta_author', $settings['meta_author']) }}" class="wp-form-input" placeholder="편집부">
            </div>
        </div>
    </div>

    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">소셜 미디어 (Open Graph / Twitter Card)</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label class="wp-form-label">OG 이미지 URL</label>
                <input type="url" name="meta_og_image" value="{{ old('meta_og_image', $settings['meta_og_image']) }}" class="wp-form-input" placeholder="https://example.com/og-image.jpg">
                <p class="wp-form-help">소셜 미디어 공유 시 표시될 이미지 · 1200×630px 권장</p>
                @if($settings['meta_og_image'])
                <img src="{{ $settings['meta_og_image'] }}" style="margin-top:8px;max-width:300px;border:1px solid #c3c4c7;border-radius:3px;">
                @endif
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">OG 타입</label>
                    <select name="meta_og_type" class="wp-form-input wp-form-select">
                        @foreach(['website'=>'website','article'=>'article','blog'=>'blog'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('meta_og_type',$settings['meta_og_type'])===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">Twitter Card 타입</label>
                    <select name="meta_twitter_card" class="wp-form-input wp-form-select">
                        @foreach(['summary'=>'summary','summary_large_image'=>'summary_large_image','app'=>'app','player'=>'player'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('meta_twitter_card',$settings['meta_twitter_card'])===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- 검색엔진 미리보기 --}}
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">구글 검색 미리보기</div>
        <div class="wp-widget-body">
            <div id="serp-preview" style="border:1px solid #e0e0e0;border-radius:8px;padding:16px 20px;max-width:600px;background:#fff;font-family:arial,sans-serif;">
                <div style="font-size:12px;color:#202124;line-height:1.3;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                    <span style="width:18px;height:18px;background:#eee;border-radius:50%;display:inline-block;"></span>
                    <span id="serp-domain" style="font-size:14px;color:#202124;">{{ parse_url(url('/'), PHP_URL_HOST) }}</span>
                </div>
                <div id="serp-title" style="font-size:20px;color:#1a0dab;line-height:1.3;cursor:pointer;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                    {{ $settings['meta_title'] ?: \App\Models\Setting::get('site_name','Laraboard') }}
                </div>
                <div id="serp-desc" style="font-size:14px;color:#4d5156;line-height:1.58;margin-top:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $settings['meta_description'] ?: \App\Models\Setting::get('site_description','') }}
                </div>
            </div>
            <p class="wp-form-help" style="margin-top:8px;">* 입력값에 따라 실시간으로 미리보기가 업데이트됩니다.</p>
        </div>
    </div>

    <button type="submit" class="wp-btn wp-btn-primary">메타태그 저장</button>
</form>

<script>
function countUpdate(inputId, countId, max) {
    const el = document.getElementById(inputId);
    const cnt = document.getElementById(countId);
    if (!el || !cnt) return;
    function update() {
        const len = el.value.length;
        cnt.textContent = len + ' / ' + max;
        cnt.style.color = len > max ? '#d63638' : len >= max * 0.8 ? '#d97706' : '#646970';
    }
    el.addEventListener('input', update);
    update();
}
countUpdate('site-meta-title', 'site-meta-title-count', 60);
countUpdate('site-meta-desc', 'site-meta-desc-count', 160);

// SERP 미리보기
document.getElementById('site-meta-title').addEventListener('input', function() {
    document.getElementById('serp-title').textContent = this.value || '{{ \App\Models\Setting::get('site_name','Laraboard') }}';
});
document.getElementById('site-meta-desc').addEventListener('input', function() {
    document.getElementById('serp-desc').textContent = this.value;
});
</script>
@endif

{{-- 사이트맵 탭 --}}
@if($tab === 'sitemap')
<form action="{{ route('admin.seo.update') }}" method="POST">
    @csrf

    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">사이트맵 설정</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" name="sitemap_enabled" value="1" {{ $settings['sitemap_enabled']==='1'?'checked':'' }}>
                    사이트맵 자동 생성 활성화
                </label>
                <p class="wp-form-help">활성화 시 <code>/sitemap.xml</code>에서 사이트맵에 접근할 수 있습니다.</p>
            </div>

            @if($settings['sitemap_enabled'] === '1')
            <div style="background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:10px;">
                <svg width="16" height="16" fill="none" stroke="#2271b1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <a href="{{ url('/sitemap.xml') }}" target="_blank" style="color:#2271b1;font-size:13px;font-weight:600;">{{ url('/sitemap.xml') }}</a>
            </div>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">기사 우선순위 (Priority)</label>
                    <select name="sitemap_articles_priority" class="wp-form-input wp-form-select">
                        @foreach(['1.0','0.9','0.8','0.7','0.6','0.5'] as $p)
                        <option value="{{ $p }}" {{ $settings['sitemap_articles_priority']===$p?'selected':'' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    <p class="wp-form-help">0.0–1.0 범위 · 기본값 0.8</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">변경 빈도 (Changefreq)</label>
                    <select name="sitemap_articles_freq" class="wp-form-input wp-form-select">
                        @foreach(['always'=>'항상','hourly'=>'매시간','daily'=>'매일','weekly'=>'매주','monthly'=>'매월','yearly'=>'매년','never'=>'없음'] as $v=>$l)
                        <option value="{{ $v }}" {{ $settings['sitemap_articles_freq']===$v?'selected':'' }}>{{ $l }} ({{ $v }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">검색엔진 핑 (Ping)</div>
        <div class="wp-widget-body">
            <p style="font-size:13px;color:#646970;margin-bottom:12px;">사이트맵을 검색엔진에 즉시 알립니다. 사이트맵이 활성화되어 있어야 합니다.</p>
            <div style="display:flex;gap:8px;">
                <button type="button" id="ping-google" class="wp-btn wp-btn-secondary" style="display:flex;align-items:center;gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Google에 핑 전송
                </button>
                <button type="button" id="ping-bing" class="wp-btn wp-btn-secondary" style="display:flex;align-items:center;gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#0078d4"><path d="M5 3l4 1.5v13l6-3.5-3-1.5 3.5-5.5L21 9v9l-12 6L5 21V3z"/></svg>
                    Bing에 핑 전송
                </button>
            </div>
            <div id="ping-result" style="margin-top:10px;font-size:13px;display:none;padding:8px 12px;border-radius:3px;"></div>
        </div>
    </div>

    <button type="submit" class="wp-btn wp-btn-primary">사이트맵 설정 저장</button>
</form>

<script>
function sendPing(engine) {
    const btn = document.getElementById('ping-' + engine);
    const result = document.getElementById('ping-result');
    btn.disabled = true;
    btn.textContent = '전송 중...';
    result.style.display = 'none';

    fetch('{{ route('admin.seo.ping') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ engine })
    })
    .then(r => r.json())
    .then(data => {
        result.style.display = 'block';
        result.style.background = data.success ? '#d7edda' : '#fce8e6';
        result.style.border = '1px solid ' + (data.success ? '#a8d5b0' : '#f5aca6');
        result.style.color = data.success ? '#0a4b1a' : '#6d1010';
        result.textContent = data.message;
    })
    .catch(() => {
        result.style.display = 'block';
        result.style.background = '#fce8e6';
        result.textContent = '네트워크 오류가 발생했습니다.';
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = engine === 'google' ? 'Google에 핑 전송' : 'Bing에 핑 전송';
    });
}
document.getElementById('ping-google').addEventListener('click', () => sendPing('google'));
document.getElementById('ping-bing').addEventListener('click', () => sendPing('bing'));
</script>
@endif

{{-- RSS 피드 탭 --}}
@if($tab === 'rss')
<form action="{{ route('admin.seo.update') }}" method="POST">
    @csrf

    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">RSS 피드 설정</div>
        <div class="wp-widget-body">
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" name="rss_enabled" value="1" {{ $settings['rss_enabled']==='1'?'checked':'' }}>
                    RSS 피드 활성화
                </label>
                <p class="wp-form-help">활성화 시 <code>/feed</code>에서 RSS 피드에 접근할 수 있습니다.</p>
            </div>

            @if($settings['rss_enabled'] === '1')
            <div style="background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#f26522"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/></svg>
                <a href="{{ url('/feed') }}" target="_blank" style="color:#2271b1;font-size:13px;font-weight:600;">{{ url('/feed') }}</a>
            </div>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">
                <div class="wp-form-group">
                    <label class="wp-form-label">피드 제목</label>
                    <input type="text" name="rss_title" value="{{ old('rss_title', $settings['rss_title']) }}" class="wp-form-input" placeholder="{{ \App\Models\Setting::get('site_name','Laraboard') }}">
                    <p class="wp-form-help">비워두면 사이트 이름 사용</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">최대 항목 수</label>
                    <input type="number" name="rss_limit" value="{{ old('rss_limit', $settings['rss_limit']) }}" class="wp-form-input" min="1" max="100" placeholder="20">
                    <p class="wp-form-help">기본값 20 · 최대 100</p>
                </div>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">피드 설명</label>
                <textarea name="rss_description" rows="2" class="wp-form-input wp-form-textarea" placeholder="{{ \App\Models\Setting::get('site_description','') }}">{{ old('rss_description', $settings['rss_description']) }}</textarea>
                <p class="wp-form-help">비워두면 사이트 설명 사용</p>
            </div>
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" name="rss_include_content" value="1" {{ $settings['rss_include_content']==='1'?'checked':'' }}>
                    피드에 전체 본문 포함
                </label>
                <p class="wp-form-help">체크 해제 시 발췌문(excerpt)만 포함됩니다. 전체 본문 포함 시 피드 구독자가 사이트를 방문하지 않을 수 있습니다.</p>
            </div>
        </div>
    </div>

    {{-- RSS 미리보기 --}}
    <div class="wp-widget" style="margin-bottom:16px;">
        <div class="wp-widget-header">피드 정보</div>
        <div class="wp-widget-body">
            <table style="font-size:13px;border-collapse:collapse;width:100%;">
                <tr style="border-bottom:1px solid #f0f0f1;">
                    <td style="padding:8px 0;color:#646970;width:120px;">피드 URL</td>
                    <td><a href="{{ url('/feed') }}" target="_blank" style="color:#2271b1;">{{ url('/feed') }}</a></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f0f1;">
                    <td style="padding:8px 0;color:#646970;">형식</td>
                    <td>RSS 2.0 (application/rss+xml)</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#646970;">네임스페이스</td>
                    <td>atom, content (전문 포함 시)</td>
                </tr>
            </table>
        </div>
    </div>

    <button type="submit" class="wp-btn wp-btn-primary">RSS 설정 저장</button>
</form>
@endif

@endsection
