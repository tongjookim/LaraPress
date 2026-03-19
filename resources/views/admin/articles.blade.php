@extends('admin.layout')

@section('title', '기사 관리')

@section('admin-content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">기사</h1>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        @if(!$isTrash)
        <a href="{{ route('admin.article.export', request()->only('status','category_id')) }}"
           class="wp-btn wp-btn-secondary">⬇ 내보내기 (JSON)</a>
        <button type="button" class="wp-btn wp-btn-secondary"
                onclick="document.getElementById('import-modal').style.display='flex'">
            ⬆ 가져오기 (JSON)
        </button>
        <a href="{{ route('admin.article.create') }}" class="wp-btn wp-btn-primary">+ 새 기사</a>
        @else
        {{-- 휴지통 비우기 --}}
        @if($counts['trash'] > 0)
        <form method="POST" action="{{ route('admin.article.empty-trash') }}" style="display:inline;"
              onsubmit="return confirm('휴지통의 기사 {{ $counts['trash'] }}개를 모두 영구 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.')">
            @csrf
            <button class="wp-btn wp-btn-danger">🗑 휴지통 비우기 ({{ $counts['trash'] }}개)</button>
        </form>
        @endif
        @endif
    </div>
</div>

{{-- 가져오기 모달 --}}
<div id="import-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:6px;padding:28px 32px;width:100%;max-width:480px;box-shadow:0 8px 30px rgba(0,0,0,.2);">
        <h3 style="margin:0 0 8px;font-size:16px;font-weight:700;">기사 가져오기 (JSON)</h3>
        <p style="font-size:12px;color:#646970;margin:0 0 16px;">
            내보내기로 생성된 JSON 파일을 업로드하면 기사가 자동으로 등록됩니다.<br>
            카테고리는 slug 기준으로 매칭됩니다.
        </p>
        <form method="POST" action="{{ route('admin.article.import') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">JSON 파일 선택</label>
                <input type="file" name="file" accept=".json" required
                       style="display:block;width:100%;font-size:13px;padding:6px;">
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" class="wp-btn wp-btn-secondary"
                        onclick="document.getElementById('import-modal').style.display='none'">취소</button>
                <button type="submit" class="wp-btn wp-btn-primary">가져오기 시작</button>
            </div>
        </form>
    </div>
</div>

{{-- 상태 탭 --}}
<div style="display:flex;gap:0;margin-bottom:16px;border-bottom:1px solid #c3c4c7;flex-wrap:wrap;">
    @php $curStatus = request('status', ''); @endphp
    @foreach(['' => '전체', 'published' => '게시됨', 'pending' => '승인 대기', 'draft' => '초안'] as $val => $label)
    <a href="{{ route('admin.articles', array_merge(request()->except('status','page'), $val ? ['status'=>$val] : [])) }}"
       style="padding:8px 14px;font-size:13px;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-1px;
              {{ (!$isTrash && $curStatus === $val) ? 'border-bottom-color:#2271b1;color:#2271b1;font-weight:700;' : 'color:#646970;' }}">
        {{ $label }}
        <span style="font-size:11px;background:#f0f0f1;padding:1px 6px;border-radius:10px;margin-left:4px;">
            {{ $counts[$val === '' ? 'all' : $val] }}
        </span>
    </a>
    @endforeach
    {{-- 휴지통 탭 --}}
    <a href="{{ route('admin.articles', ['status' => 'trash']) }}"
       style="padding:8px 14px;font-size:13px;text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-1px;
              {{ $isTrash ? 'border-bottom-color:#d63638;color:#d63638;font-weight:700;' : 'color:#646970;' }}">
        🗑 휴지통
        @if($counts['trash'] > 0)
        <span style="font-size:11px;background:#fce8e8;color:#d63638;padding:1px 6px;border-radius:10px;margin-left:4px;">
            {{ $counts['trash'] }}
        </span>
        @endif
    </a>
</div>

{{-- 검색 / 카테고리 필터 + 일괄 처리 --}}
<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center;">
    @if(!$isTrash)
    <form method="GET" action="{{ route('admin.articles') }}" style="display:flex;gap:6px;flex-wrap:wrap;">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <select name="category_id" class="wp-form-input wp-form-select" style="width:auto;">
            <option value="">모든 카테고리</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <input type="text" name="search" class="wp-form-input" style="max-width:240px;"
               placeholder="제목 또는 내용 검색..." value="{{ request('search') }}">
        <button type="submit" class="wp-btn wp-btn-secondary">검색</button>
        @if(request()->hasAny(['search','category_id']))
            <a href="{{ route('admin.articles', request()->only('status')) }}" class="wp-btn wp-btn-secondary">초기화</a>
        @endif
    </form>
    @endif

    {{-- 일괄 처리 폼 --}}
    <form id="bulk-form" method="POST" action="{{ route('admin.article.bulk') }}" style="display:flex;gap:6px;align-items:center;margin-left:auto;">
        @csrf
        <span id="selected-count" style="font-size:12px;color:#646970;display:none;"></span>
        <select name="action" id="bulk-action" class="wp-form-input wp-form-select" style="width:auto;height:34px;font-size:13px;">
            <option value="">일괄 처리 선택</option>
            @if(!$isTrash)
            <option value="publish">게시 승인</option>
            <option value="draft">초안으로 변경</option>
            <option value="delete">휴지통으로 이동</option>
            @else
            <option value="restore">복원</option>
            <option value="force_delete">영구 삭제</option>
            @endif
        </select>
        <button type="button" onclick="submitBulk()" class="wp-btn wp-btn-secondary">적용</button>
    </form>
</div>

<div class="wp-widget">
    <div class="wp-widget-body" style="padding:0;">
        <div class="wp-table-wrap">
        <table class="wp-list-table">
            <thead>
                <tr>
                    <th style="width:36px;padding:10px 12px;">
                        <input type="checkbox" id="check-all" onchange="toggleAll(this)" title="전체 선택">
                    </th>
                    <th>제목</th>
                    <th style="width:110px;">카테고리</th>
                    <th style="width:90px;">작성자</th>
                    @if($isTrash)
                    <th style="width:130px;">삭제일</th>
                    @else
                    <th style="width:80px;">상태</th>
                    <th style="width:110px;">등록일</th>
                    @endif
                    <th style="width:190px;">관리</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                <tr id="article-row-{{ $article->id }}" style="{{ $isTrash ? 'opacity:.8;' : '' }}">
                    <td style="padding:10px 12px;">
                        <input type="checkbox" class="row-check" name="ids[]"
                               form="bulk-form" value="{{ $article->id }}"
                               onchange="updateSelectedCount()">
                    </td>
                    <td>
                        <strong style="font-size:13px;">{{ $article->title }}</strong>
                        @if($article->excerpt && !$isTrash)
                            <div style="font-size:12px;color:#8c8f94;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:360px;">
                                {{ $article->excerpt }}
                            </div>
                        @endif
                        @if($isTrash)
                        <div style="font-size:11px;color:#d63638;margin-top:2px;">🗑 휴지통</div>
                        @endif
                    </td>
                    <td style="color:#646970;font-size:12px;">{{ $article->category?->name ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $article->user->name }}</td>

                    @if($isTrash)
                    <td style="font-size:12px;color:#d63638;">{{ $article->deleted_at->format('Y-m-d H:i') }}</td>
                    @else
                    <td><span class="wp-badge {{ $article->statusClass() }}">{{ $article->statusLabel() }}</span></td>
                    <td style="font-size:12px;color:#646970;">
                        {{ $article->created_at->format('Y-m-d') }}
                        @if($article->published_at)
                            <div style="color:#00a32a;">공개 {{ $article->published_at->format('m-d') }}</div>
                        @endif
                    </td>
                    @endif

                    <td>
                        @if($isTrash)
                        <form method="POST" action="{{ route('admin.article.restore', $article->id) }}" style="display:inline;">
                            @csrf
                            <button class="wp-btn wp-btn-secondary wp-btn-sm">↩ 복원</button>
                        </form>
                        <form method="POST" action="{{ route('admin.article.force-delete', $article->id) }}" style="display:inline;"
                              onsubmit="return confirm('「{{ addslashes($article->title) }}」을(를) 영구 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.')">
                            @csrf @method('DELETE')
                            <button class="wp-btn wp-btn-danger wp-btn-sm">영구 삭제</button>
                        </form>
                        @else
                        @if($article->status !== 'published')
                        <form method="POST" action="{{ route('admin.article.status', $article->id) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="status" value="published">
                            <button class="wp-btn wp-btn-primary wp-btn-sm">승인</button>
                        </form>
                        @endif
                        @if($article->status === 'published')
                        <form method="POST" action="{{ route('admin.article.status', $article->id) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="status" value="draft">
                            <button class="wp-btn wp-btn-secondary wp-btn-sm">내리기</button>
                        </form>
                        @endif
                        <a href="{{ route('admin.article.edit', $article->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                        <form method="POST" action="{{ route('admin.article.delete', $article->id) }}" style="display:inline;"
                              onsubmit="return confirm('「{{ addslashes($article->title) }}」을(를) 휴지통으로 이동하시겠습니까?')">
                            @csrf @method('DELETE')
                            <button class="wp-btn wp-btn-danger wp-btn-sm">🗑</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#8c8f94;">
                        @if($isTrash) 휴지통이 비어있습니다.
                        @elseif(request()->hasAny(['search','category_id','status'])) 검색 결과가 없습니다.
                        @else 등록된 기사가 없습니다.
                            <a href="{{ route('admin.article.create') }}" style="color:#2271b1;">첫 기사를 작성해보세요.</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($articles->hasPages())
<div style="margin-top:16px;">
    {{ $articles->links() }}
</div>
@endif

@push('scripts')
<script>
function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(function(cb) { cb.checked = master.checked; });
    updateSelectedCount();
}

function updateSelectedCount() {
    var checked = document.querySelectorAll('.row-check:checked');
    var all     = document.querySelectorAll('.row-check');
    var counter = document.getElementById('selected-count');
    var master  = document.getElementById('check-all');

    if (checked.length > 0) {
        counter.style.display = 'inline';
        counter.textContent   = checked.length + '개 선택됨';
    } else {
        counter.style.display = 'none';
    }
    master.indeterminate = checked.length > 0 && checked.length < all.length;
    master.checked       = all.length > 0 && checked.length === all.length;
}

function submitBulk() {
    var action  = document.getElementById('bulk-action').value;
    var checked = document.querySelectorAll('.row-check:checked');

    if (!action)          { alert('처리 방식을 선택하세요.'); return; }
    if (!checked.length)  { alert('기사를 선택하세요.'); return; }

    var msg = {
        'delete':       checked.length + '개 기사를 휴지통으로 이동하시겠습니까?',
        'force_delete': checked.length + '개 기사를 영구 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.',
        'publish':      checked.length + '개 기사를 게시 승인하시겠습니까?',
        'draft':        checked.length + '개 기사를 초안으로 변경하시겠습니까?',
        'restore':      checked.length + '개 기사를 복원하시겠습니까?',
    }[action] || checked.length + '개 기사에 작업을 적용하시겠습니까?';

    if (confirm(msg)) {
        document.getElementById('bulk-form').submit();
    }
}
</script>
@endpush
@endsection
