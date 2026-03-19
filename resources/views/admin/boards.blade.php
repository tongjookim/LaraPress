@extends('admin.layout')
@section('title', '게시판 관리')
@section('admin-content')
<h1 class="wp-page-title">게시판 관리 <span style="font-size:13px;color:#646970;font-weight:400;">({{ $boards->count() }}개)</span></h1>

<div style="margin-bottom:16px;">
    <a href="{{ route('admin.board.create') }}" class="wp-btn wp-btn-primary">+ 게시판 생성</a>
</div>

<div class="wp-widget">
    <div class="wp-table-wrap"><table class="wp-list-table">
        <thead>
            <tr>
                <th>순서</th>
                <th>게시판 ID</th>
                <th>게시판명</th>
                <th>스킨</th>
                <th>페이지당 글 수</th>
                <th>게시글</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            @forelse($boards as $board)
            <tr>
                <td>{{ $board->order }}</td>
                <td style="font-family:monospace;font-size:12px;">{{ $board->board_id }}</td>
                <td style="font-weight:600;">
                    <a href="{{ route('bbs.index', $board->board_id) }}" style="color:#2271b1;text-decoration:none;">{{ $board->board_name }}</a>
                </td>
                <td>{{ $board->skin }}</td>
                <td>{{ $board->posts_per_page }}</td>
                <td>{{ $board->posts->count() }}개</td>
                <td>
                    <a href="{{ route('admin.board.edit', $board->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                    <form action="{{ route('admin.board.delete', $board->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">@csrf @method('DELETE')
                        <button class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:30px;color:#8c8f94;">등록된 게시판이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endsection
