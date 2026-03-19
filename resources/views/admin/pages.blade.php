@extends('admin.layout')
@section('title', '페이지 관리')
@section('admin-content')
<h1 class="wp-page-title">페이지 관리</h1>

<div style="margin-bottom:16px;">
    <a href="{{ route('admin.page.create') }}" class="wp-btn wp-btn-primary">+ 새 페이지</a>
</div>

<div class="wp-widget">
    <div class="wp-table-wrap"><table class="wp-list-table">
        <thead>
            <tr>
                <th>순서</th>
                <th>제목</th>
                <th>URL</th>
                <th>상태</th>
                <th>등록일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
            <tr>
                <td>{{ $page->order }}</td>
                <td style="font-weight:600;">{{ $page->title }}</td>
                <td><a href="{{ route('page.show', $page->slug) }}" target="_blank" style="color:#2271b1;">/page/{{ $page->slug }}</a></td>
                <td>
                    @if($page->is_active)
                        <span class="wp-badge wp-badge-active">활성</span>
                    @else
                        <span class="wp-badge wp-badge-inactive">비활성</span>
                    @endif
                </td>
                <td>{{ $page->created_at->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('admin.page.edit', $page->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                    <form action="{{ route('admin.page.delete', $page->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:30px;color:#8c8f94;">등록된 페이지가 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endsection
