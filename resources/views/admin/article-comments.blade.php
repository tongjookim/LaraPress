@extends('admin.layout')
@section('title', '기사 댓글 관리')

@section('admin-content')
<h1 class="wp-page-title">기사 댓글 관리</h1>

@if(session('success'))
    <div class="wp-notice" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="wp-notice wp-notice-error" style="margin-bottom:16px;">{{ session('error') }}</div>
@endif

{{-- 상태 탭 --}}
<div style="display:flex;gap:0;border-bottom:2px solid #c3c4c7;margin-bottom:20px;flex-wrap:wrap;">
    @php
    $tabs = [
        'all'      => ['전체',       $counts['all']],
        'pending'  => ['승인 대기',  $counts['pending']],
        'approved' => ['승인됨',     $counts['approved']],
        'trashed'  => ['휴지통',     $counts['trashed']],
    ];
    @endphp
    @foreach($tabs as $key => [$label, $count])
    <a href="{{ route('admin.article-comments', array_filter(['tab'=>$key==='all'?null:$key, 'q'=>$search?:null])) }}"
       style="padding:8px 16px;font-size:13px;font-weight:600;text-decoration:none;border-bottom:2px solid {{ $tab===$key ? '#2271b1' : 'transparent' }};margin-bottom:-2px;color:{{ $tab===$key ? '#2271b1' : '#646970' }};">
        {{ $label }}
        @if($count > 0)
        <span style="display:inline-block;min-width:18px;padding:0 5px;font-size:11px;font-weight:700;border-radius:9px;text-align:center;line-height:18px;margin-left:4px;
                     background:{{ $key==='pending' ? '#d63638' : '#c3c4c7' }};color:#fff;">{{ $count }}</span>
        @endif
    </a>
    @endforeach
</div>

{{-- 검색 + 일괄 처리 --}}
<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.article-comments') }}" style="display:flex;gap:6px;flex:1;min-width:200px;">
        @if($tab !== 'all') <input type="hidden" name="tab" value="{{ $tab }}"> @endif
        <input type="text" name="q" value="{{ $search }}" placeholder="내용, 작성자, 기사 제목 검색…"
               class="wp-form-input" style="flex:1;max-width:320px;">
        <button type="submit" class="wp-btn wp-btn-secondary">검색</button>
        @if($search)
        <a href="{{ route('admin.article-comments', $tab!=='all'?['tab'=>$tab]:[]) }}" class="wp-btn wp-btn-secondary">✕ 초기화</a>
        @endif
    </form>

    <form id="bulk-form" method="POST" action="{{ route('admin.article-comment.bulk') }}" style="display:flex;gap:6px;align-items:center;">
        @csrf
        <select name="action" class="wp-form-input wp-form-select" style="width:auto;height:34px;font-size:13px;">
            <option value="">일괄 처리 선택</option>
            @if($tab !== 'trashed')
            <option value="approve">승인</option>
            <option value="unapprove">승인 취소</option>
            <option value="delete">삭제</option>
            @endif
            @if($tab === 'trashed')
            <option value="restore">복원</option>
            <option value="force_delete">영구 삭제</option>
            @endif
        </select>
        <button type="button" onclick="submitBulk()" class="wp-btn wp-btn-secondary">적용</button>
    </form>
</div>

{{-- 댓글 테이블 --}}
<div class="wp-widget">
    <div class="wp-widget-body" style="padding:0;">
        @if($comments->isEmpty())
        <p style="padding:32px;text-align:center;color:#8c8f94;font-size:13px;">
            @if($search) 검색 결과가 없습니다.
            @elseif($tab==='pending') 승인 대기 중인 댓글이 없습니다.
            @elseif($tab==='trashed') 휴지통이 비어 있습니다.
            @else 등록된 댓글이 없습니다. @endif
        </p>
        @else
        <table class="wp-list-table" style="table-layout:fixed;">
            <colgroup>
                <col style="width:36px;">
                <col style="width:130px;">
                <col>
                <col style="width:180px;">
                <col style="width:76px;">
                <col style="width:100px;">
                <col style="width:120px;">
            </colgroup>
            <thead>
                <tr>
                    <th style="padding:10px 12px;">
                        <input type="checkbox" id="check-all" onchange="toggleAll(this)">
                    </th>
                    <th>작성자</th>
                    <th>댓글 내용</th>
                    <th>기사</th>
                    <th>상태</th>
                    <th>작성일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                <tr id="row-{{ $comment->id }}" style="{{ $comment->trashed() ? 'opacity:.6;' : '' }}">
                    {{-- 체크박스 --}}
                    <td>
                        <input type="checkbox" class="row-check" name="ids[]"
                               form="bulk-form" value="{{ $comment->id }}">
                    </td>

                    {{-- 작성자 --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            @if($comment->user?->profile_image)
                                <img src="{{ $comment->user->profile_image }}" style="width:24px;height:24px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                            @else
                                <span style="width:24px;height:24px;border-radius:50%;background:#e0e0e0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#555;flex-shrink:0;">
                                    {{ mb_substr($comment->user?->name ?? '?', 0, 1) }}
                                </span>
                            @endif
                            <div style="min-width:0;">
                                <p style="font-size:12px;font-weight:600;color:#1d2327;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $comment->user?->name ?? '(탈퇴)' }}</p>
                                <p style="font-size:11px;color:#8c8f94;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $comment->user?->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- 댓글 내용 --}}
                    <td>
                        @if($comment->parent_id)
                        <span style="font-size:10px;color:#8c8f94;display:block;margin-bottom:2px;">↳ 답글</span>
                        @endif
                        <p class="comment-text-{{ $comment->id }}"
                           style="font-size:13px;color:#1d2327;line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $comment->content }}</p>
                        {{-- 인라인 수정 폼 (기본 숨김) --}}
                        <div id="edit-area-{{ $comment->id }}" style="display:none;margin-top:6px;">
                            <form method="POST" action="{{ route('admin.article-comment.update', $comment->id) }}">
                                @csrf @method('PUT')
                                <textarea name="content" rows="3"
                                    style="width:100%;padding:6px 8px;font-size:12px;border:1px solid #2271b1;border-radius:3px;resize:vertical;font-family:inherit;">{{ $comment->content }}</textarea>
                                <div style="display:flex;gap:6px;margin-top:4px;">
                                    <button type="submit" class="wp-btn wp-btn-primary wp-btn-sm">저장</button>
                                    <button type="button" onclick="cancelEdit({{ $comment->id }})" class="wp-btn wp-btn-secondary wp-btn-sm">취소</button>
                                </div>
                            </form>
                        </div>
                    </td>

                    {{-- 기사 --}}
                    <td>
                        @if($comment->article)
                        <a href="{{ route('news.show', $comment->article->slug) }}" target="_blank"
                           style="font-size:12px;color:#2271b1;text-decoration:none;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $comment->article->title }}
                        </a>
                        @else
                        <span style="font-size:12px;color:#8c8f94;">(삭제된 기사)</span>
                        @endif
                    </td>

                    {{-- 상태 --}}
                    <td>
                        @if($comment->trashed())
                            <span class="wp-badge wp-badge-inactive">삭제됨</span>
                        @elseif($comment->is_approved)
                            <span class="wp-badge wp-badge-active">승인</span>
                        @else
                            <span class="wp-badge wp-badge-pending">대기</span>
                        @endif
                    </td>

                    {{-- 작성일 --}}
                    <td>
                        <span style="font-size:12px;color:#646970;">{{ $comment->created_at->format('Y-m-d') }}</span>
                        <br>
                        <span style="font-size:11px;color:#8c8f94;">{{ $comment->created_at->format('H:i') }}</span>
                    </td>

                    {{-- 관리 버튼 --}}
                    <td>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            @if($comment->trashed())
                                {{-- 복원 --}}
                                <form method="POST" action="{{ route('admin.article-comment.restore', $comment->id) }}">
                                    @csrf
                                    <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" style="width:100%;">복원</button>
                                </form>
                                {{-- 영구 삭제 --}}
                                <form method="POST" action="{{ route('admin.article-comment.force-delete', $comment->id) }}"
                                      onsubmit="return confirm('영구 삭제하시겠습니까? 복원이 불가능합니다.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" style="width:100%;">영구삭제</button>
                                </form>
                            @else
                                {{-- 승인 토글 --}}
                                <form method="POST" action="{{ route('admin.article-comment.approve', $comment->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="wp-btn wp-btn-sm {{ $comment->is_approved ? 'wp-btn-warning' : 'wp-btn-primary' }}"
                                            style="width:100%;">
                                        {{ $comment->is_approved ? '승인 취소' : '승인' }}
                                    </button>
                                </form>
                                {{-- 수정 --}}
                                <button type="button"
                                        onclick="toggleEdit({{ $comment->id }})"
                                        class="wp-btn wp-btn-secondary wp-btn-sm" style="width:100%;">수정</button>
                                {{-- 삭제 --}}
                                <form method="POST" action="{{ route('admin.article-comment.delete', $comment->id) }}"
                                      onsubmit="return confirm('댓글을 삭제하시겠습니까?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" style="width:100%;">삭제</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
        @endif
    </div>
</div>

{{-- 페이지네이션 --}}
@if($comments->hasPages())
<div style="margin-top:16px;display:flex;justify-content:center;">
    {{ $comments->links() }}
</div>
@endif

@push('scripts')
<script>
// ── 전체 선택/해제 ───────────────────────────────────────────
function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(function(cb) { cb.checked = master.checked; });
}
document.querySelectorAll('.row-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var all  = document.querySelectorAll('.row-check');
        var chk  = document.querySelectorAll('.row-check:checked');
        document.getElementById('check-all').indeterminate = chk.length > 0 && chk.length < all.length;
        document.getElementById('check-all').checked = chk.length === all.length;
    });
});

// ── 일괄 처리 실행 ───────────────────────────────────────────
function submitBulk() {
    var action = document.querySelector('[name=action]').value;
    if (!action) { alert('처리 방식을 선택하세요.'); return; }
    var checked = document.querySelectorAll('.row-check:checked');
    if (!checked.length) { alert('댓글을 선택하세요.'); return; }
    if (action === 'force_delete' && !confirm(checked.length + '개 댓글을 영구 삭제하시겠습니까?')) return;
    document.getElementById('bulk-form').submit();
}

// ── 인라인 수정 토글 ─────────────────────────────────────────
function toggleEdit(id) {
    var area = document.getElementById('edit-area-' + id);
    var text = document.querySelector('.comment-text-' + id);
    var open = area.style.display === 'none';
    area.style.display = open ? 'block' : 'none';
    text.style.display = open ? 'none'  : '';
}
function cancelEdit(id) {
    document.getElementById('edit-area-' + id).style.display = 'none';
    document.querySelector('.comment-text-' + id).style.display = '';
}
</script>
@endpush
@endsection
