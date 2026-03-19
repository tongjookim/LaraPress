@extends('admin.layout')

@section('title', '카테고리 관리')

@section('admin-content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">카테고리</h1>
    <a href="{{ route('admin.category.create') }}" class="wp-btn wp-btn-primary">+ 새 카테고리</a>
</div>

<div class="wp-widget">
    <div class="wp-widget-header">전체 카테고리 ({{ $categories->count() }})</div>
    <div class="wp-widget-body" style="padding:0;">
        <div class="wp-table-wrap"><table class="wp-list-table">
            <thead>
                <tr>
                    <th style="width:40px;">순서</th>
                    <th>이름</th>
                    <th>슬러그</th>
                    <th>상위 카테고리</th>
                    <th>기사 수</th>
                    <th>상태</th>
                    <th style="width:160px;">관리</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td style="color:#8c8f94;">{{ $cat->order }}</td>
                    <td>
                        <strong>{{ $cat->name }}</strong>
                        @if($cat->description)
                            <div style="font-size:12px;color:#8c8f94;margin-top:2px;">{{ $cat->description }}</div>
                        @endif
                    </td>
                    <td style="font-family:monospace;color:#646970;">{{ $cat->slug }}</td>
                    <td>{{ $cat->parent?->name ?? '—' }}</td>
                    <td>{{ $cat->articles()->count() }}</td>
                    <td>
                        <span class="wp-badge {{ $cat->is_active ? 'wp-badge-active' : 'wp-badge-inactive' }}">
                            {{ $cat->is_active ? '활성' : '비활성' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.category.edit', $cat->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                        <form method="POST" action="{{ route('admin.category.delete', $cat->id) }}" style="display:inline;"
                              onsubmit="return confirm('카테고리를 삭제하시겠습니까?')">
                            @csrf @method('DELETE')
                            <button class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:32px;color:#8c8f94;">
                        등록된 카테고리가 없습니다.
                        <a href="{{ route('admin.category.create') }}" style="color:#2271b1;">첫 카테고리를 만들어보세요.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
