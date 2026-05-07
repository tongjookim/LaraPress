@extends('admin.layout')
@section('title', '도구')

@section('admin-content')
<h1 class="wp-page-title">도구</h1>

@php $activeTab = session('tab', 'export'); @endphp

{{-- 탭 네비게이션 --}}
<div style="display:flex;gap:0;margin-bottom:24px;border-bottom:2px solid #c3c4c7;flex-wrap:wrap;">
    @foreach([
        'export'    => ['⬇', '내보내기'],
        'json'      => ['📄', 'JSON 가져오기'],
        'rss'       => ['📡', 'RSS 가져오기'],
        'wordpress' => ['🔵', '워드프레스 가져오기'],
        'gnuboard'  => ['🟢', '그누보드5 가져오기'],
    ] as $key => [$icon, $label])
    <button onclick="switchTab('{{ $key }}')" id="tab-btn-{{ $key }}"
            style="padding:10px 18px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;
                   border-bottom:3px solid transparent;margin-bottom:-2px;transition:all .15s;
                   {{ $activeTab === $key ? 'border-bottom-color:#2271b1;color:#2271b1;' : 'color:#646970;' }}">
        {{ $icon }} {{ $label }}
    </button>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════
     탭 1: 내보내기
═══════════════════════════════════════════════════════ --}}
<div id="tab-export" class="tab-panel" style="{{ $activeTab === 'export' ? '' : 'display:none;' }}">
    <div class="wp-widget" style="max-width:600px;">
        <div class="wp-widget-header">⬇ 기사 내보내기 (JSON)</div>
        <div class="wp-widget-body">
            <p style="font-size:13px;color:#646970;margin-bottom:20px;">
                기사를 JSON 파일로 다운로드합니다. 다른 Laraboard 사이트에서 JSON 가져오기로 복원할 수 있습니다.
            </p>
            <form method="GET" action="{{ route('admin.tools.export') }}">
                <div class="wp-form-group">
                    <label class="wp-form-label">상태 필터</label>
                    <select name="status" class="wp-form-input wp-form-select" style="width:auto;">
                        <option value="">전체 ({{ $articleCount }}개)</option>
                        <option value="published">게시됨만</option>
                        <option value="draft">초안만</option>
                        <option value="pending">승인 대기만</option>
                    </select>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">카테고리 필터</label>
                    <select name="category_id" class="wp-form-input wp-form-select" style="width:auto;">
                        <option value="">전체 카테고리</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="wp-btn wp-btn-primary">⬇ JSON 파일 다운로드</button>
            </form>
        </div>
    </div>

    <div class="wp-widget" style="max-width:600px;margin-top:16px;">
        <div class="wp-widget-header">📋 JSON 형식 안내</div>
        <div class="wp-widget-body">
            <pre style="background:#f6f7f7;padding:14px;border-radius:4px;font-size:12px;overflow-x:auto;line-height:1.6;">[
  {
    "title": "기사 제목",
    "slug": "article-slug",
    "category": "카테고리-슬러그",
    "author": "작성자명",
    "content": "기사 본문 HTML",
    "excerpt": "요약",
    "thumbnail": "/uploads/...",
    "status": "published",
    "published_at": "2026-01-01 00:00:00"
  }
]</pre>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     탭 2: JSON 가져오기
═══════════════════════════════════════════════════════ --}}
<div id="tab-json" class="tab-panel" style="{{ $activeTab === 'json' ? '' : 'display:none;' }}">
    <div class="wp-widget" style="max-width:600px;">
        <div class="wp-widget-header">📄 JSON 가져오기</div>
        <div class="wp-widget-body">
            <p style="font-size:13px;color:#646970;margin-bottom:20px;">
                Laraboard 내보내기 JSON 파일을 업로드해 기사를 가져옵니다.<br>
                카테고리는 slug 기준으로 자동 매칭됩니다.
            </p>
            <form method="POST" action="{{ route('admin.tools.import.json') }}" enctype="multipart/form-data">
                @csrf
                <div class="wp-form-group">
                    <label class="wp-form-label">JSON 파일 *</label>
                    <input type="file" name="file" accept=".json" required
                           style="display:block;width:100%;padding:6px;font-size:13px;">
                    <p class="wp-form-help">최대 20MB</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">기본 카테고리</label>
                    <select name="category_id" class="wp-form-input wp-form-select" style="width:auto;">
                        <option value="">미분류 (JSON 내 category 값 우선)</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">기본 상태 *</label>
                    <select name="status" class="wp-form-input wp-form-select" style="width:auto;">
                        <option value="draft">초안 (JSON 내 status 값 우선)</option>
                        <option value="published">게시됨</option>
                        <option value="pending">승인 대기</option>
                    </select>
                </div>
                <button type="submit" class="wp-btn wp-btn-primary"
                        onclick="return confirm('JSON 파일의 기사를 가져오시겠습니까?')">
                    ⬆ 가져오기 시작
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     탭 3: RSS 가져오기
═══════════════════════════════════════════════════════ --}}
<div id="tab-rss" class="tab-panel" style="{{ $activeTab === 'rss' ? '' : 'display:none;' }}">
    <div class="wp-widget" style="max-width:600px;">
        <div class="wp-widget-header">📡 RSS / Atom 피드 가져오기</div>
        <div class="wp-widget-body">
            <p style="font-size:13px;color:#646970;margin-bottom:20px;">
                외부 RSS 또는 Atom 피드 URL에서 기사를 가져옵니다.<br>
                <code style="background:#f0f0f1;padding:1px 5px;border-radius:3px;font-size:11px;">content:encoded</code>가 있으면 전문(full text)을 가져옵니다.
            </p>
            <form method="POST" action="{{ route('admin.tools.import.rss') }}">
                @csrf
                <div class="wp-form-group">
                    <label class="wp-form-label">RSS/Atom URL *</label>
                    <input type="url" name="rss_url" required class="wp-form-input"
                           placeholder="https://example.com/feed" style="max-width:100%;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                    <div class="wp-form-group">
                        <label class="wp-form-label">최대 가져올 수</label>
                        <input type="number" name="limit" value="50" min="1" max="200" class="wp-form-input" style="width:100px;">
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">가져온 기사 상태</label>
                        <select name="status" class="wp-form-input wp-form-select" style="width:auto;">
                            <option value="draft">초안</option>
                            <option value="pending">승인 대기</option>
                            <option value="published">게시됨</option>
                        </select>
                    </div>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">카테고리</label>
                    <select name="category_id" class="wp-form-input wp-form-select" style="width:auto;">
                        <option value="">미분류</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="wp-btn wp-btn-primary"
                        onclick="return confirm('RSS 피드에서 기사를 가져오시겠습니까?')">
                    📡 RSS 가져오기 시작
                </button>
            </form>
        </div>
    </div>
    <div class="wp-widget" style="max-width:600px;margin-top:16px;">
        <div class="wp-widget-header">💡 지원 피드 형식</div>
        <div class="wp-widget-body" style="font-size:13px;color:#646970;">
            <ul style="margin:0;padding-left:18px;line-height:1.9;">
                <li><strong>RSS 2.0</strong> — 일반 RSS 피드 (description 또는 content:encoded)</li>
                <li><strong>Atom 1.0</strong> — Atom 피드 (content 또는 summary)</li>
                <li>WordPress: <code style="background:#f0f0f1;padding:1px 4px;border-radius:2px;">/?feed=rss2</code> 또는 <code style="background:#f0f0f1;padding:1px 4px;border-radius:2px;">/feed</code></li>
            </ul>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     탭 4: 워드프레스 가져오기
═══════════════════════════════════════════════════════ --}}
<div id="tab-wordpress" class="tab-panel" style="{{ $activeTab === 'wordpress' ? '' : 'display:none;' }}">
    <div class="wp-widget" style="max-width:680px;">
        <div class="wp-widget-header">🔵 워드프레스 WXR 가져오기</div>
        <div class="wp-widget-body">
            <div style="background:#e8f0fb;border:1px solid #c5d8f7;border-radius:4px;padding:10px 14px;font-size:12px;color:#1a4f9a;margin-bottom:20px;line-height:1.7;">
                <strong>워드프레스 내보내기 방법:</strong><br>
                WordPress 관리자 → 도구 → 내보내기 → "모든 콘텐츠" 또는 "포스트" 선택 → 내보내기 파일 다운로드<br>
                생성된 <code>.xml</code> (WXR 형식) 파일을 업로드하세요.
            </div>
            <form method="POST" action="{{ route('admin.tools.import.wordpress') }}" enctype="multipart/form-data">
                @csrf
                <div class="wp-form-group">
                    <label class="wp-form-label">WXR XML 파일 *</label>
                    <input type="file" name="file" accept=".xml,.txt" required
                           style="display:block;width:100%;padding:6px;font-size:13px;">
                    <p class="wp-form-help">WordPress 내보내기 .xml 파일, 최대 100MB</p>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                    <div class="wp-form-group">
                        <label class="wp-form-label">가져올 포스트 유형</label>
                        <select name="post_type" class="wp-form-input wp-form-select" style="width:auto;">
                            <option value="post">포스트만</option>
                            <option value="page">페이지만</option>
                            <option value="both">포스트 + 페이지</option>
                        </select>
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">상태 처리</label>
                        <select name="status" class="wp-form-input wp-form-select" style="width:auto;">
                            <option value="keep">원본 상태 유지 (publish→게시됨)</option>
                            <option value="draft">모두 초안으로</option>
                            <option value="pending">모두 승인 대기로</option>
                            <option value="published">모두 게시됨으로</option>
                        </select>
                    </div>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">기본 카테고리</label>
                    <select name="category_id" class="wp-form-input wp-form-select" style="width:auto;">
                        <option value="">WP 카테고리 slug 자동 매칭 (없으면 미분류)</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="wp-btn wp-btn-primary"
                        onclick="return confirm('WordPress XML 파일에서 기사를 가져오시겠습니까?\n대용량 파일은 시간이 걸릴 수 있습니다.')">
                    🔵 워드프레스 가져오기 시작
                </button>
            </form>
        </div>
    </div>
    <div class="wp-widget" style="max-width:680px;margin-top:16px;">
        <div class="wp-widget-header">💡 참고 사항</div>
        <div class="wp-widget-body" style="font-size:13px;color:#646970;line-height:1.9;">
            <ul style="margin:0;padding-left:18px;">
                <li>썸네일: WXR에 포함된 이미지 URL은 가져오지 않고, 본문 첫 번째 <code>&lt;img&gt;</code> 태그를 썸네일로 자동 설정합니다.</li>
                <li>카테고리 자동 매칭: WP 카테고리 nicename과 Laraboard 카테고리 slug가 동일하면 자동 매칭됩니다.</li>
                <li>미디어(이미지) 파일은 별도로 업로드해야 합니다.</li>
                <li>댓글, 첨부파일, 커스텀 필드는 가져오지 않습니다.</li>
            </ul>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     탭 5: 그누보드5 가져오기
═══════════════════════════════════════════════════════ --}}
<div id="tab-gnuboard" class="tab-panel" style="{{ $activeTab === 'gnuboard' ? '' : 'display:none;' }}">
    <div class="wp-widget" style="max-width:680px;">
        <div class="wp-widget-header">🟢 그누보드5 게시판 가져오기</div>
        <div class="wp-widget-body">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:4px;padding:10px 14px;font-size:12px;color:#166534;margin-bottom:20px;line-height:1.7;">
                <strong>그누보드5 데이터 내보내기 방법 (CSV):</strong><br>
                phpMyAdmin → 그누보드5 DB 선택 → <code>g5_write_{게시판테이블}</code> 선택 → 내보내기 → CSV 형식
            </div>

            {{-- 방식 선택 --}}
            <div style="display:flex;gap:12px;margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border:2px solid #2271b1;border-radius:4px;font-weight:600;font-size:13px;color:#2271b1;background:#f0f6fc;">
                    <input type="radio" name="gnuboard_method" value="csv" checked onchange="toggleGnuMethod('csv')"> CSV 파일 업로드
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border:2px solid #c3c4c7;border-radius:4px;font-weight:600;font-size:13px;color:#646970;background:#fff;">
                    <input type="radio" name="gnuboard_method" value="db" onchange="toggleGnuMethod('db')"> DB 직접 연결
                </label>
            </div>

            {{-- CSV 폼 --}}
            <form id="gnu-csv-form" method="POST" action="{{ route('admin.tools.import.gnuboard') }}"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="method" value="csv">
                <div class="wp-form-group">
                    <label class="wp-form-label">CSV 파일 *</label>
                    <input type="file" name="file" accept=".csv,.txt" required
                           style="display:block;width:100%;padding:6px;font-size:13px;">
                    <p class="wp-form-help">phpMyAdmin에서 내보낸 g5_write_{테이블} CSV 파일, 최대 50MB</p>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                    <div class="wp-form-group">
                        <label class="wp-form-label">카테고리</label>
                        <select name="category_id" class="wp-form-input wp-form-select" style="width:auto;">
                            <option value="">미분류</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">가져온 기사 상태</label>
                        <select name="status" class="wp-form-input wp-form-select" style="width:auto;">
                            <option value="draft">초안</option>
                            <option value="pending">승인 대기</option>
                            <option value="published">게시됨</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="wp-btn wp-btn-primary"
                        onclick="return confirm('그누보드5 CSV 파일에서 게시글을 가져오시겠습니까?')">
                    🟢 CSV 가져오기 시작
                </button>
            </form>

            {{-- DB 연결 폼 --}}
            <form id="gnu-db-form" method="POST" action="{{ route('admin.tools.import.gnuboard') }}"
                  style="display:none;">
                @csrf
                <input type="hidden" name="method" value="db">
                <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:4px;padding:10px 14px;font-size:12px;color:#795548;margin-bottom:16px;line-height:1.7;">
                    ⚠️ 그누보드5 DB에 직접 접근합니다. 동일 서버 또는 외부 접속이 허용된 DB만 사용 가능합니다.
                </div>
                <div style="display:grid;grid-template-columns:3fr 1fr;gap:0 16px;">
                    <div class="wp-form-group">
                        <label class="wp-form-label">DB 호스트 *</label>
                        <input type="text" name="db_host" value="localhost" class="wp-form-input" placeholder="localhost">
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">포트 *</label>
                        <input type="number" name="db_port" value="3306" class="wp-form-input">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
                    <div class="wp-form-group">
                        <label class="wp-form-label">DB 이름 *</label>
                        <input type="text" name="db_name" class="wp-form-input" placeholder="gnuboard5">
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">사용자명 *</label>
                        <input type="text" name="db_user" class="wp-form-input" placeholder="root">
                    </div>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label">비밀번호</label>
                    <input type="password" name="db_pass" class="wp-form-input">
                </div>
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:0 16px;">
                    <div class="wp-form-group">
                        <label class="wp-form-label">테이블 prefix</label>
                        <input type="text" name="db_prefix" value="g5_" class="wp-form-input" placeholder="g5_">
                        <p class="wp-form-help">기본: g5_</p>
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">게시판 테이블명 *</label>
                        <input type="text" name="db_table" class="wp-form-input" placeholder="board_name">
                        <p class="wp-form-help">예: free → g5_write_free 조회</p>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                    <div class="wp-form-group">
                        <label class="wp-form-label">카테고리</label>
                        <select name="category_id" class="wp-form-input wp-form-select" style="width:auto;">
                            <option value="">미분류</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">가져온 기사 상태</label>
                        <select name="status" class="wp-form-input wp-form-select" style="width:auto;">
                            <option value="draft">초안</option>
                            <option value="pending">승인 대기</option>
                            <option value="published">게시됨</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="wp-btn wp-btn-primary"
                        onclick="return confirm('그누보드5 DB에서 게시글을 가져오시겠습니까?')">
                    🟢 DB 가져오기 시작
                </button>
            </form>
        </div>
    </div>

    <div class="wp-widget" style="max-width:680px;margin-top:16px;">
        <div class="wp-widget-header">💡 참고 사항</div>
        <div class="wp-widget-body" style="font-size:13px;color:#646970;line-height:1.9;">
            <ul style="margin:0;padding-left:18px;">
                <li>댓글(wr_is_comment=1)은 자동으로 제외됩니다.</li>
                <li>조회수(wr_hit)가 있으면 함께 가져옵니다.</li>
                <li>작성일(wr_datetime)이 발행일로 설정됩니다.</li>
                <li>첨부파일, 이미지는 별도로 업로드해야 합니다.</li>
                <li>그누보드5 ca_name(카테고리명)은 Laraboard 카테고리와 별도로 매칭되지 않습니다.</li>
            </ul>
        </div>
    </div>
</div>

<script>
function switchTab(name) {
    document.querySelectorAll('.tab-panel').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = '';

    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.style.borderBottomColor = 'transparent';
        btn.style.color = '#646970';
    });
    const active = document.getElementById('tab-btn-' + name);
    active.style.borderBottomColor = '#2271b1';
    active.style.color = '#2271b1';
}

function toggleGnuMethod(method) {
    const csvForm = document.getElementById('gnu-csv-form');
    const dbForm  = document.getElementById('gnu-db-form');
    const labels  = document.querySelectorAll('[name="gnuboard_method"]');

    csvForm.style.display = method === 'csv' ? '' : 'none';
    dbForm.style.display  = method === 'db'  ? '' : 'none';

    labels.forEach(r => {
        const label = r.parentElement;
        if (r.value === method) {
            label.style.borderColor = '#2271b1';
            label.style.color = '#2271b1';
            label.style.background = '#f0f6fc';
        } else {
            label.style.borderColor = '#c3c4c7';
            label.style.color = '#646970';
            label.style.background = '#fff';
        }
    });
}
</script>
@endsection
