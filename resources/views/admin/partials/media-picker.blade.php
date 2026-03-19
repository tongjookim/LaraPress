{{--
    미디어 라이브러리 모달 파셜
    사용: @include('admin.partials.media-picker')
    JS:  MediaPicker.open({ mode: 'library'|'picker', onSelect: fn(media){} })
--}}
<div id="media-picker-overlay" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.6);animation:mpFadeIn .15s ease;">
<div id="media-picker-modal" style="position:absolute;inset:20px;background:#f0f0f1;border-radius:6px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.4);">

    {{-- 헤더 --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:50px;background:#1d2327;color:#fff;flex-shrink:0;">
        <span style="font-size:15px;font-weight:700;" id="mp-modal-title">미디어 라이브러리</span>
        <div style="display:flex;align-items:center;gap:10px;">
            <button id="mp-select-btn" type="button"
                    style="display:none;padding:6px 16px;background:#2271b1;color:#fff;border:none;border-radius:3px;font-size:13px;font-weight:700;cursor:pointer;">
                선택
            </button>
            <button type="button" onclick="MediaPicker.close()"
                    style="background:none;border:none;color:#c3c4c7;font-size:22px;line-height:1;cursor:pointer;padding:4px;">×</button>
        </div>
    </div>

    {{-- 탭 --}}
    <div style="display:flex;border-bottom:1px solid #c3c4c7;background:#fff;flex-shrink:0;">
        <button type="button" class="mp-tab active" data-tab="library"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid #2271b1;color:#2271b1;">
            미디어 목록
        </button>
        <button type="button" class="mp-tab" data-tab="upload"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:#646970;">
            파일 업로드
        </button>
    </div>

    {{-- 본문 --}}
    <div style="flex:1;display:flex;overflow:hidden;">

        {{-- 업로드 탭 --}}
        <div id="mp-tab-upload" style="display:none;flex:1;padding:24px;overflow-y:auto;">
            <div id="mp-drop-zone"
                 style="border:2px dashed #c3c4c7;border-radius:6px;padding:60px 40px;text-align:center;cursor:pointer;background:#fafafa;transition:all .2s;position:relative;">
                <svg style="width:48px;height:48px;color:#8c8f94;margin:0 auto 14px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p style="font-size:15px;color:#2c3338;font-weight:600;margin-bottom:6px;">파일을 드래그하거나 클릭하여 업로드</p>
                <p style="font-size:12px;color:#8c8f94;">이미지(JPG, PNG, GIF, WebP), PDF, Word, Excel · 최대 10MB</p>
                <input type="file" id="mp-file-input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                       style="position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;">
            </div>
            <div id="mp-upload-progress" style="display:none;margin-top:16px;">
                <div style="background:#e5e7eb;border-radius:4px;height:8px;">
                    <div id="mp-progress-bar" style="background:#2271b1;height:8px;border-radius:4px;width:0;transition:width .3s;"></div>
                </div>
                <p id="mp-upload-status" style="font-size:12px;color:#646970;margin-top:6px;"></p>
            </div>
            <div id="mp-upload-results" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;margin-top:16px;"></div>
        </div>

        {{-- 라이브러리 탭 --}}
        <div id="mp-tab-library" style="display:flex;flex:1;overflow:hidden;">

            {{-- 좌: 그리드 --}}
            <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;border-right:1px solid #dcdcde;">

                {{-- 검색/필터 바 --}}
                <div style="padding:10px 14px;background:#fff;border-bottom:1px solid #dcdcde;display:flex;gap:8px;align-items:center;flex-shrink:0;">
                    <select id="mp-type-filter" style="height:30px;padding:0 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;background:#fff;">
                        <option value="">전체</option>
                        <option value="image">이미지</option>
                        <option value="file">파일</option>
                    </select>
                    <input type="text" id="mp-search" placeholder="파일명 검색..."
                           style="flex:1;height:30px;padding:0 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;">
                    <button type="button" id="mp-search-btn"
                            style="height:30px;padding:0 12px;background:#2271b1;color:#fff;border:none;border-radius:3px;font-size:12px;cursor:pointer;">검색</button>
                    <span id="mp-total-count" style="font-size:12px;color:#646970;white-space:nowrap;"></span>
                </div>

                {{-- 로딩 인디케이터 (그리드 밖에 위치 — grid.innerHTML='' 에 영향받지 않도록) --}}
                <div id="mp-grid-loading" style="display:none;text-align:center;padding:40px;color:#8c8f94;flex-shrink:0;">로딩 중...</div>

                {{-- 그리드 --}}
                <div id="mp-grid" style="flex:1;overflow-y:auto;padding:12px;display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;align-content:start;"></div>

                {{-- 페이지네이션 --}}
                <div id="mp-pagination" style="padding:10px 14px;background:#fff;border-top:1px solid #dcdcde;display:flex;align-items:center;justify-content:center;gap:6px;flex-shrink:0;"></div>
            </div>

            {{-- 우: 상세 패널 --}}
            <div id="mp-detail" style="width:280px;flex-shrink:0;overflow-y:auto;background:#fff;display:flex;flex-direction:column;">
                <div id="mp-detail-empty" style="flex:1;display:flex;align-items:center;justify-content:center;padding:24px;text-align:center;">
                    <div>
                        <svg style="width:40px;height:40px;color:#c3c4c7;margin:0 auto 10px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p style="font-size:13px;color:#8c8f94;">미디어를 클릭하면<br>상세 정보를 볼 수 있습니다.</p>
                    </div>
                </div>
                <div id="mp-detail-content" style="display:none;padding:16px;flex:1;">
                    {{-- 미리보기 --}}
                    <div id="mp-preview" style="margin-bottom:14px;background:#f0f0f1;border-radius:4px;overflow:hidden;min-height:140px;display:flex;align-items:center;justify-content:center;">
                        <img id="mp-preview-img" src="" alt="" style="max-width:100%;max-height:200px;display:none;">
                        <div id="mp-preview-icon" style="text-align:center;padding:24px;display:none;">
                            <svg style="width:48px;height:48px;color:#8c8f94;margin:0 auto 8px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span id="mp-preview-ext" style="font-size:14px;color:#646970;font-weight:700;text-transform:uppercase;"></span>
                        </div>
                    </div>

                    {{-- 메타 정보 --}}
                    <div style="margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f0f0f1;font-size:12px;color:#646970;display:flex;flex-direction:column;gap:4px;">
                        <div><strong style="color:#1d2327;" id="mp-info-name"></strong></div>
                        <div id="mp-info-meta"></div>
                    </div>

                    {{-- 편집 필드 --}}
                    <form id="mp-meta-form" onsubmit="return false;">
                        <div style="margin-bottom:10px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#646970;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">대체 텍스트 (Alt)</label>
                            <input type="text" id="mp-alt" style="width:100%;padding:5px 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;">
                            <p style="font-size:11px;color:#8c8f94;margin-top:3px;">스크린 리더, SEO에 사용됩니다.</p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#646970;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">제목</label>
                            <input type="text" id="mp-title" style="width:100%;padding:5px 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;">
                        </div>
                        <div style="margin-bottom:10px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#646970;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">캡션</label>
                            <textarea id="mp-caption" rows="2" style="width:100%;padding:5px 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;resize:vertical;"></textarea>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#646970;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">설명</label>
                            <textarea id="mp-desc" rows="3" style="width:100%;padding:5px 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;resize:vertical;"></textarea>
                        </div>
                        <button type="button" id="mp-save-meta"
                                style="width:100%;padding:6px;background:#2271b1;color:#fff;border:none;border-radius:3px;font-size:13px;font-weight:700;cursor:pointer;margin-bottom:10px;">
                            저장
                        </button>
                        <div id="mp-save-msg" style="font-size:12px;text-align:center;min-height:16px;"></div>
                    </form>

                    {{-- URL 복사 / 다운로드 --}}
                    <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f0f0f1;display:flex;flex-direction:column;gap:6px;">
                        <div style="display:flex;gap:6px;">
                            <input type="text" id="mp-url-display" readonly
                                   style="flex:1;padding:5px 8px;border:1px solid #c3c4c7;border-radius:3px;font-size:11px;background:#f6f7f7;color:#2c3338;min-width:0;cursor:text;">
                            <button type="button" id="mp-copy-url"
                                    style="padding:5px 10px;background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;cursor:pointer;white-space:nowrap;">복사</button>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <a id="mp-download-link" href="#" download
                               style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:5px 10px;background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;text-decoration:none;color:#2c3338;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                다운로드
                            </a>
                            <button type="button" id="mp-delete-btn"
                                    style="flex:1;padding:5px 10px;background:#fce8e6;border:1px solid #f5aca6;border-radius:3px;font-size:12px;font-weight:700;color:#d63638;cursor:pointer;">
                                영구 삭제
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /라이브러리 탭 --}}
    </div>{{-- /본문 --}}

</div>{{-- /modal --}}
</div>{{-- /overlay --}}

<style>
@keyframes mpFadeIn { from { opacity:0 } to { opacity:1 } }
.mp-grid-item {
    position:relative;background:#fff;border:2px solid #c3c4c7;border-radius:3px;overflow:hidden;cursor:pointer;transition:border-color .15s;
}
.mp-grid-item:hover { border-color:#2271b1; }
.mp-grid-item.selected { border-color:#2271b1;box-shadow:0 0 0 2px #2271b1; }
.mp-grid-item .thumb {
    height:110px;background:#f0f0f1;display:flex;align-items:center;justify-content:center;overflow:hidden;
}
.mp-grid-item .thumb img { width:100%;height:100%;object-fit:cover; }
.mp-grid-item .label {
    padding:5px 7px;border-top:1px solid #f0f0f1;
}
.mp-grid-item .label p { font-size:11px;color:#2c3338;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.mp-tab { transition:color .15s; }
.mp-tab.active { color:#2271b1 !important; border-bottom-color:#2271b1 !important; }
</style>

<script>
var MediaPicker = (function() {
    var _currentMedia = null;
    var _currentPage  = 1;
    var _mode         = 'library'; // 'library' | 'picker'
    var _onSelect     = null;
    var _loading      = false;

    var _selectId = null;

    function open(opts) {
        opts = opts || {};
        _mode     = opts.mode     || 'library';
        _onSelect = opts.onSelect || null;
        _selectId = opts.selectId || null;

        document.getElementById('mp-modal-title').textContent =
            _mode === 'picker' ? '이미지 선택' : '미디어 라이브러리';
        document.getElementById('mp-select-btn').style.display =
            _mode === 'picker' ? 'inline-block' : 'none';

        document.getElementById('media-picker-overlay').style.display = 'block';
        switchTab(opts.tab || 'library');
        loadMedia(1);

        document.getElementById('media-picker-overlay').onclick = function(e) {
            if (e.target === this) close();
        };
    }

    function close() {
        document.getElementById('media-picker-overlay').style.display = 'none';
        _currentMedia = null;
        clearDetail();
    }

    function switchTab(tab) {
        document.querySelectorAll('.mp-tab').forEach(function(btn) {
            var isActive = btn.dataset.tab === tab;
            btn.classList.toggle('active', isActive);
            btn.style.borderBottomColor = isActive ? '#2271b1' : 'transparent';
            btn.style.color = isActive ? '#2271b1' : '#646970';
        });
        document.getElementById('mp-tab-library').style.display = tab === 'library' ? 'flex' : 'none';
        document.getElementById('mp-tab-upload').style.display  = tab === 'upload'  ? 'block' : 'none';
    }

    function loadMedia(page) {
        if (_loading) return;
        _loading = true;
        _currentPage = page;

        var type   = document.getElementById('mp-type-filter').value;
        var search = document.getElementById('mp-search').value;
        var url    = '{{ route('admin.media.picker') }}?page=' + page +
                     (type   ? '&type='   + encodeURIComponent(type)   : '') +
                     (search ? '&search=' + encodeURIComponent(search) : '');

        document.getElementById('mp-grid-loading').style.display = 'block';

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderGrid(data);
                renderPagination(data);
                document.getElementById('mp-total-count').textContent = '총 ' + data.total + '개';
            })
            .catch(function(err) {
                var el = document.getElementById('mp-grid-loading');
                if (el) { el.textContent = '로드 실패'; el.style.display = 'block'; }
            })
            .finally(function() { _loading = false; });
    }

    function renderGrid(data) {
        var grid = document.getElementById('mp-grid');
        grid.innerHTML = '';
        document.getElementById('mp-grid-loading').style.display = 'none';

        if (!data.data.length) {
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#8c8f94;font-size:13px;">미디어가 없습니다.</div>';
            _selectId = null;
            return;
        }

        data.data.forEach(function(m) {
            var div = document.createElement('div');
            div.className = 'mp-grid-item';
            div.dataset.id = m.id;

            var thumb = '<div class="thumb">';
            if (m.is_image) {
                thumb += '<img src="' + m.url + '" alt="' + escHtml(m.alt_text || m.original_name) + '" loading="lazy">';
            } else {
                var ext = m.original_name.split('.').pop().toUpperCase();
                thumb += '<div style="text-align:center;padding:10px;"><svg style="width:32px;height:32px;color:#8c8f94;margin:0 auto 6px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span style="font-size:10px;color:#646970;">' + ext + '</span></div>';
            }
            thumb += '</div>';

            div.innerHTML = thumb
                + '<div class="label"><p title="' + escHtml(m.original_name) + '">' + escHtml(m.original_name) + '</p>'
                + '<p style="font-size:10px;color:#8c8f94;">' + m.size + '</p></div>';

            div.addEventListener('click', function() { selectMedia(m, div); });
            grid.appendChild(div);
        });

        // auto-select if requested
        if (_selectId) {
            var target = data.data.find(function(m) { return m.id == _selectId; });
            if (target) {
                var el = grid.querySelector('[data-id="' + _selectId + '"]');
                selectMedia(target, el);
            }
            _selectId = null;
        }
    }

    function renderPagination(data) {
        var pg = document.getElementById('mp-pagination');
        pg.innerHTML = '';
        if (data.last_page <= 1) return;

        var make = function(page, label, disabled) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = label || page;
            btn.style.cssText = 'padding:4px 10px;border:1px solid #c3c4c7;border-radius:3px;font-size:12px;background:' +
                (page === data.current_page ? '#2271b1' : '#fff') + ';color:' +
                (page === data.current_page ? '#fff' : '#2c3338') + ';cursor:' + (disabled ? 'default' : 'pointer') + ';';
            if (!disabled) btn.addEventListener('click', function() { loadMedia(page); });
            return btn;
        };

        if (data.current_page > 1) pg.appendChild(make(data.current_page - 1, '‹'));
        var start = Math.max(1, data.current_page - 2);
        var end   = Math.min(data.last_page, start + 4);
        for (var i = start; i <= end; i++) pg.appendChild(make(i, i, i === data.current_page));
        if (data.current_page < data.last_page) pg.appendChild(make(data.current_page + 1, '›'));
    }

    function selectMedia(m, el) {
        _currentMedia = m;
        // 그리드 선택 표시
        document.querySelectorAll('.mp-grid-item').forEach(function(it) { it.classList.remove('selected'); });
        if (el) el.classList.add('selected');
        // 선택 버튼 활성화
        if (_mode === 'picker') {
            document.getElementById('mp-select-btn').disabled = false;
        }
        showDetail(m);
    }

    function showDetail(m) {
        document.getElementById('mp-detail-empty').style.display   = 'none';
        document.getElementById('mp-detail-content').style.display = 'block';

        // 미리보기
        if (m.is_image) {
            document.getElementById('mp-preview-img').src           = m.url;
            document.getElementById('mp-preview-img').style.display = 'block';
            document.getElementById('mp-preview-icon').style.display = 'none';
        } else {
            document.getElementById('mp-preview-img').style.display  = 'none';
            document.getElementById('mp-preview-icon').style.display = 'block';
            document.getElementById('mp-preview-ext').textContent    = m.original_name.split('.').pop().toUpperCase();
        }

        // 메타 정보
        document.getElementById('mp-info-name').textContent = m.original_name;
        document.getElementById('mp-info-meta').innerHTML   =
            m.mime_type + '<br>' + m.size + '<br>' + m.created_at + '<br>업로드: ' + m.uploaded_by;

        // 편집 필드
        document.getElementById('mp-alt').value     = m.alt_text    || '';
        document.getElementById('mp-title').value   = m.title       || '';
        document.getElementById('mp-caption').value = m.caption     || '';
        document.getElementById('mp-desc').value    = m.description || '';
        document.getElementById('mp-save-msg').textContent = '';

        // URL / 다운로드
        var fullUrl = location.origin + m.url;
        document.getElementById('mp-url-display').value     = fullUrl;
        document.getElementById('mp-download-link').href    = m.url;
        document.getElementById('mp-download-link').download = m.original_name;
    }

    function clearDetail() {
        document.getElementById('mp-detail-empty').style.display   = 'block';
        document.getElementById('mp-detail-content').style.display = 'none';
    }

    // 탭 버튼
    document.querySelectorAll('.mp-tab').forEach(function(btn) {
        btn.addEventListener('click', function() { switchTab(this.dataset.tab); });
    });

    // 검색
    document.getElementById('mp-search-btn').addEventListener('click', function() { loadMedia(1); });
    document.getElementById('mp-search').addEventListener('keydown', function(e) { if (e.key === 'Enter') loadMedia(1); });
    document.getElementById('mp-type-filter').addEventListener('change', function() { loadMedia(1); });

    // 선택 버튼
    document.getElementById('mp-select-btn').addEventListener('click', function() {
        if (_currentMedia && _onSelect) {
            _onSelect(_currentMedia);
            close();
        }
    });

    // 메타 저장
    document.getElementById('mp-save-meta').addEventListener('click', function() {
        if (!_currentMedia) return;
        var btn = this;
        btn.disabled = true;
        btn.textContent = '저장 중...';

        fetch('{{ route('admin.media.update', '__ID__') }}'.replace('__ID__', _currentMedia.id), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                alt_text:    document.getElementById('mp-alt').value,
                title:       document.getElementById('mp-title').value,
                caption:     document.getElementById('mp-caption').value,
                description: document.getElementById('mp-desc').value,
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.ok) {
                _currentMedia = data.media;
                var msg = document.getElementById('mp-save-msg');
                msg.style.color = '#2d7a3a';
                msg.textContent = '저장되었습니다.';
                setTimeout(function() { msg.textContent = ''; }, 2000);
                // 그리드 아이템 alt 업데이트
                var img = document.querySelector('.mp-grid-item.selected img');
                if (img) img.alt = data.media.alt_text || '';
            }
        })
        .catch(function() {
            var msg = document.getElementById('mp-save-msg');
            msg.style.color = '#d63638';
            msg.textContent = '저장 실패';
        })
        .finally(function() { btn.disabled = false; btn.textContent = '저장'; });
    });

    // URL 복사
    document.getElementById('mp-copy-url').addEventListener('click', function() {
        var input = document.getElementById('mp-url-display');
        var btn   = this;
        navigator.clipboard.writeText(input.value).then(function() {
            btn.textContent = '복사됨!';
            setTimeout(function() { btn.textContent = '복사'; }, 1500);
        }).catch(function() {
            input.select();
            document.execCommand('copy');
            btn.textContent = '복사됨!';
            setTimeout(function() { btn.textContent = '복사'; }, 1500);
        });
    });

    // 삭제
    document.getElementById('mp-delete-btn').addEventListener('click', function() {
        if (!_currentMedia) return;
        if (!confirm('"' + _currentMedia.original_name + '"을(를) 영구 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.')) return;

        fetch('{{ route('admin.media.delete', '__ID__') }}'.replace('__ID__', _currentMedia.id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.ok) {
                // 그리드에서 제거
                var item = document.querySelector('.mp-grid-item[data-id="' + _currentMedia.id + '"]');
                if (item) item.remove();
                clearDetail();
                _currentMedia = null;
                // 페이지 전체 미디어 그리드도 새로고침 (미디어 페이지에서 열었을 때)
                if (typeof refreshPageGrid === 'function') refreshPageGrid();
                document.getElementById('mp-total-count').textContent =
                    parseInt(document.getElementById('mp-total-count').textContent) - 1 + '개';
            }
        });
    });

    // 업로드 드롭존
    var dz = document.getElementById('mp-drop-zone');
    var fi = document.getElementById('mp-file-input');

    dz.addEventListener('dragover', function(e) { e.preventDefault(); this.style.borderColor='#2271b1'; this.style.background='#f0f5fb'; });
    dz.addEventListener('dragleave', function() { this.style.borderColor='#c3c4c7'; this.style.background='#fafafa'; });
    dz.addEventListener('drop', function(e) { e.preventDefault(); this.style.borderColor='#c3c4c7'; this.style.background='#fafafa'; uploadFiles(e.dataTransfer.files); });
    fi.addEventListener('change', function() { uploadFiles(this.files); });

    function uploadFiles(files) {
        if (!files.length) return;
        var total = files.length, done = 0, succeeded = 0;
        document.getElementById('mp-upload-progress').style.display = 'block';
        document.getElementById('mp-progress-bar').style.width = '0%';
        document.getElementById('mp-upload-results').innerHTML = '';

        Array.from(files).forEach(function(file) {
            var fd = new FormData();
            fd.append('file', file);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('/upload/image', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    done++;
                    if (data.url) {
                        succeeded++;
                        // 업로드 결과 썸네일 추가
                        var res = document.getElementById('mp-upload-results');
                        var d = document.createElement('div');
                        d.style.cssText = 'border:1px solid #c3c4c7;border-radius:3px;overflow:hidden;';
                        if (data.is_image || (data.mime_type||'').startsWith('image/')) {
                            d.innerHTML = '<img src="' + data.url + '" style="width:100%;height:90px;object-fit:cover;display:block;">';
                        }
                        d.innerHTML += '<div style="padding:4px 6px;font-size:10px;color:#2c3338;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escHtml(data.original_name) + '</div>';
                        res.appendChild(d);
                    }
                    document.getElementById('mp-progress-bar').style.width = Math.round(done/total*100) + '%';
                    document.getElementById('mp-upload-status').textContent = done + '/' + total + ' 완료';
                    if (done === total) {
                        setTimeout(function() {
                            switchTab('library');
                            loadMedia(1);
                        }, 800);
                        if (typeof refreshPageGrid === 'function') setTimeout(refreshPageGrid, 1200);
                    }
                })
                .catch(function() { done++; });
        });
    }

    function escHtml(s) {
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    return { open: open, close: close };
})();
</script>
