
<?php $__env->startPush('scripts'); ?>
<style>
/* ── 모바일 경량 에디터 ── */
.me-wrap {
    border: 1px solid #8c8f94;
    border-radius: 3px;
    overflow: hidden;
    background: #fff;
}
.me-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 6px 8px;
    background: #f6f7f7;
    border-bottom: 1px solid #c3c4c7;
}
.me-btn {
    min-width: 36px; height: 36px;
    padding: 0 8px;
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 3px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
    color: #2c3338;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: background .12s;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}
.me-btn:active { background: #e0e0e0; }
.me-btn svg { pointer-events: none; }
.me-sep { width: 1px; background: #c3c4c7; margin: 4px 2px; align-self: stretch; }
.me-body {
    min-height: 320px;
    padding: 14px 12px;
    font-size: 16px;
    line-height: 1.8;
    color: #1d2327;
    outline: none;
    word-break: break-word;
    -webkit-user-select: text;
    user-select: text;
}
.me-body:empty::before {
    content: attr(data-placeholder);
    color: #8c8f94;
    pointer-events: none;
}
.me-body img { max-width: 100%; height: auto; display: block; margin: 8px 0; }
.me-body blockquote {
    border-left: 3px solid #c3c4c7;
    margin: 1em 0; padding: 4px 16px;
    color: #50575e;
}
.me-body h2 { font-size: 1.4em; font-weight: 700; margin: 1.2em 0 .4em; }
.me-body h3 { font-size: 1.15em; font-weight: 700; margin: 1em 0 .3em; }
.me-body a { color: #2271b1; text-decoration: underline; }
/* 링크 입력 모달 */
.me-modal-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 9999;
    align-items: flex-end; justify-content: center;
}
.me-modal-backdrop.open { display: flex; }
.me-modal {
    background: #fff; width: 100%; max-width: 480px;
    border-radius: 12px 12px 0 0;
    padding: 20px 16px 32px;
    box-shadow: 0 -4px 24px rgba(0,0,0,.18);
}
.me-modal h3 { font-size: 15px; font-weight: 700; margin: 0 0 14px; }
.me-modal input {
    width: 100%; padding: 10px 12px; font-size: 15px;
    border: 1px solid #8c8f94; border-radius: 6px; margin-bottom: 10px;
    box-sizing: border-box;
}
.me-modal-btns { display: flex; gap: 8px; }
.me-modal-ok  { flex: 1; padding: 11px; background: #2271b1; color: #fff; border: none; border-radius: 6px; font-size: 15px; font-weight: 700; cursor: pointer; }
.me-modal-cancel { padding: 11px 18px; background: #f0f0f1; border: none; border-radius: 6px; font-size: 15px; cursor: pointer; }
</style>

<script>
(function () {
    var editorId  = '<?php echo e($editorId); ?>';
    var skinBase  = '/plugins/smarteditor2/dist/';
    var ta        = document.getElementById(editorId);
    var oEditors  = [];

    if (!ta) return;

    var isMobile = window.innerWidth <= 768;

    /* ════════════════════════════════════════════
       데스크탑: SmartEditor2 동적 로드
    ════════════════════════════════════════════ */
    if (!isMobile) {
        var s = document.createElement('script');
        s.src = '/plugins/smarteditor2/dist/js/service/HuskyEZCreator.js';
        s.charset = 'utf-8';
        s.onload = function () { initSmartEditor(); };
        document.head.appendChild(s);
        return;
    }

    function initSmartEditor() {
        nhn.husky.EZCreator.createInIFrame({
            oAppRef       : oEditors,
            elPlaceHolder : editorId,
            sSkinURI      : skinBase + 'SmartEditor2Skin.html',
            htParams      : {
                bUseToolbar         : true,
                bUseVerticalResizer : true,
                bUseModeChanger     : true,
                I18N_LOCALE         : 'ko_KR'
            },
            fCreator : 'createSEditor2'
        });

        var form = ta.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                try { oEditors.getById[editorId].exec('UPDATE_CONTENTS_FIELD', []); } catch (err) {}
                if (!ta.value || ta.value.trim() === '') {
                    e.preventDefault();
                    alert('내용을 입력해주세요.');
                }
            });
        }

        // 이미지 삽입 버튼 (미디어 라이브러리)
        var wrapper  = document.createElement('div');
        wrapper.style.cssText = 'margin-top:6px;display:flex;align-items:center;gap:8px;';
        var mediaBtn = document.createElement('button');
        mediaBtn.type = 'button';
        mediaBtn.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:4px 12px;background:#f6f7f7;border:1px solid #8c8f94;border-radius:3px;font-size:12px;cursor:pointer;color:#2c3338;white-space:nowrap;';
        mediaBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> 이미지 삽입';
        function seEsc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
        mediaBtn.addEventListener('click', function () {
            if (typeof MediaPicker === 'undefined') return;
            MediaPicker.open({ mode: 'picker', onSelect: function (media) {
                var imgTag = '<img src="' + media.url + '" alt="' + seEsc(media.alt_text || media.title || '') + '" style="max-width:100%;">';
                var html = media.caption
                    ? '<figure style="margin:1em 0;text-align:center;">' + imgTag + '<figcaption style="margin-top:6px;font-size:0.875em;color:#555;">' + seEsc(media.caption) + '</figcaption></figure>'
                    : imgTag;
                try { oEditors.getById[editorId].exec('PASTE_HTML', [html]); } catch (e) {}
            }});
        });
        wrapper.appendChild(mediaBtn);
        var editorIframe = ta.nextElementSibling;
        if (editorIframe) editorIframe.parentNode.insertBefore(wrapper, editorIframe.nextSibling);
        else ta.parentNode.appendChild(wrapper);
    }

    /* ════════════════════════════════════════════
       모바일: 경량 contenteditable 에디터
    ════════════════════════════════════════════ */

    // textarea를 숨기고 모바일 에디터 삽입
    ta.style.display = 'none';

    // ── 링크 모달 ──────────────────────────────
    var modalBackdrop = document.createElement('div');
    modalBackdrop.className = 'me-modal-backdrop';
    modalBackdrop.innerHTML =
        '<div class="me-modal">'
        + '<h3>링크 삽입</h3>'
        + '<input id="me-link-url" type="url" placeholder="https://example.com" autocomplete="off">'
        + '<input id="me-link-text" type="text" placeholder="링크 텍스트 (비워두면 URL 사용)">'
        + '<div class="me-modal-btns">'
        + '<button class="me-modal-ok" id="me-link-ok">삽입</button>'
        + '<button class="me-modal-cancel" id="me-link-cancel">취소</button>'
        + '</div></div>';
    document.body.appendChild(modalBackdrop);

    var savedRange = null;
    function saveRange() {
        var sel = window.getSelection();
        if (sel && sel.rangeCount > 0) savedRange = sel.getRangeAt(0).cloneRange();
    }
    function restoreRange() {
        if (!savedRange) return;
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedRange);
    }
    function openLinkModal() {
        saveRange();
        var selText = window.getSelection().toString();
        document.getElementById('me-link-text').value = selText || '';
        document.getElementById('me-link-url').value = '';
        modalBackdrop.classList.add('open');
        setTimeout(function () { document.getElementById('me-link-url').focus(); }, 100);
    }
    document.getElementById('me-link-ok').addEventListener('click', function () {
        var url  = document.getElementById('me-link-url').value.trim();
        var text = document.getElementById('me-link-text').value.trim() || url;
        if (!url) { modalBackdrop.classList.remove('open'); return; }
        restoreRange();
        document.execCommand('insertHTML', false, '<a href="' + url + '">' + text + '</a>');
        modalBackdrop.classList.remove('open');
    });
    document.getElementById('me-link-cancel').addEventListener('click', function () {
        modalBackdrop.classList.remove('open');
    });
    modalBackdrop.addEventListener('click', function (e) {
        if (e.target === modalBackdrop) modalBackdrop.classList.remove('open');
    });

    // ── 에디터 DOM 구성 ────────────────────────
    var wrap = document.createElement('div');
    wrap.className = 'me-wrap';

    // 툴바
    var toolbar = document.createElement('div');
    toolbar.className = 'me-toolbar';

    function mkBtn(html, title, fn) {
        var b = document.createElement('button');
        b.type = 'button'; b.className = 'me-btn'; b.title = title;
        b.innerHTML = html;
        b.addEventListener('mousedown', function (e) { e.preventDefault(); }); // 포커스 유지
        b.addEventListener('click', fn);
        return b;
    }
    function sep() {
        var s = document.createElement('div'); s.className = 'me-sep'; return s;
    }

    // Bold
    toolbar.appendChild(mkBtn('<b>B</b>', '굵게', function () { document.execCommand('bold'); body.focus(); }));
    // Italic
    toolbar.appendChild(mkBtn('<i>I</i>', '기울임', function () { document.execCommand('italic'); body.focus(); }));
    // Underline
    toolbar.appendChild(mkBtn('<u>U</u>', '밑줄', function () { document.execCommand('underline'); body.focus(); }));
    toolbar.appendChild(sep());
    // H2
    toolbar.appendChild(mkBtn('H2', '소제목', function () { document.execCommand('formatBlock', false, 'h2'); body.focus(); }));
    // H3
    toolbar.appendChild(mkBtn('H3', '소소제목', function () { document.execCommand('formatBlock', false, 'h3'); body.focus(); }));
    // 단락
    toolbar.appendChild(mkBtn('P', '단락', function () { document.execCommand('formatBlock', false, 'p'); body.focus(); }));
    toolbar.appendChild(sep());
    // 인용
    toolbar.appendChild(mkBtn(
        '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10.5h3m-3 3h3m8-6H8m8 3h-4m4 3h-4"/></svg>',
        '인용',
        function () { document.execCommand('formatBlock', false, 'blockquote'); body.focus(); }
    ));
    // 링크
    toolbar.appendChild(mkBtn(
        '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
        '링크',
        function () { openLinkModal(); }
    ));
    toolbar.appendChild(sep());
    // 이미지 (미디어 라이브러리)
    toolbar.appendChild(mkBtn(
        '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
        '이미지',
        function () {
            if (typeof MediaPicker === 'undefined') return;
            saveRange();
            MediaPicker.open({ mode: 'picker', onSelect: function (media) {
                restoreRange();
                var alt = (media.alt_text || media.title || '').replace(/"/g, '&quot;');
                var imgTag = '<img src="' + media.url + '" alt="' + alt + '" style="max-width:100%;">';
                var html = media.caption
                    ? '<figure style="margin:1em 0;text-align:center;">' + imgTag + '<figcaption style="font-size:.875em;color:#555;margin-top:4px;">' + media.caption + '</figcaption></figure>'
                    : imgTag;
                document.execCommand('insertHTML', false, html);
                body.focus();
            }});
        }
    ));
    // 실행취소
    toolbar.appendChild(sep());
    toolbar.appendChild(mkBtn(
        '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>',
        '실행취소',
        function () { document.execCommand('undo'); body.focus(); }
    ));

    // 본문 영역
    var body = document.createElement('div');
    body.className = 'me-body';
    body.contentEditable = 'true';
    body.setAttribute('data-placeholder', '내용을 입력하세요');
    // 기존 내용 복원
    var existingVal = ta.value || '';
    body.innerHTML = existingVal;

    wrap.appendChild(toolbar);
    wrap.appendChild(body);

    // textarea 바로 앞에 삽입
    ta.parentNode.insertBefore(wrap, ta);

    // ── 폼 submit 시 innerHTML → textarea 동기화 ──
    var form = ta.closest('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var html = body.innerHTML.trim();
            // 빈 에디터 체크
            var text = body.innerText.trim();
            if (!text && !body.querySelector('img')) {
                e.preventDefault();
                alert('내용을 입력해주세요.');
                body.focus();
                return;
            }
            ta.value = html;
        });
    }

    // ── 붙여넣기: 이미지 파일 드롭/붙여넣기 처리 ──
    body.addEventListener('paste', function (e) {
        var items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (var i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                e.preventDefault();
                alert('이미지는 위 툴바의 이미지 버튼으로 삽입해주세요.');
                return;
            }
        }
        // 텍스트만 붙여넣기: 서식 제거 옵션 유지
    });

}());
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/laraboard/www/resources/views/partials/smarteditor.blade.php ENDPATH**/ ?>