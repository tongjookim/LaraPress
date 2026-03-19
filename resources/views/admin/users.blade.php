@extends('admin.layout')
@section('title', '회원 관리')
@section('admin-content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="wp-page-title" style="margin-bottom:0;">회원 관리 <span style="font-size:13px;color:#646970;font-weight:400;">({{ $users->total() }}명)</span></h1>
    <a href="{{ route('admin.user.create') }}" class="wp-btn wp-btn-primary">+ 회원 추가</a>
</div>

<div class="wp-widget">
    <div class="wp-table-wrap"><table class="wp-list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>아이디</th>
                <th>이름</th>
                <th>이메일</th>
                <th>권한</th>
                <th>상태</th>
                <th>가입일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td style="font-weight:600;">{{ $user->username }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @php
                        $roleStyles = [
                            'subscriber' => 'background:#f0f0f1;color:#50575e;',
                            'author'     => 'background:#dcfce7;color:#166534;',
                            'editor'     => 'background:#dbeafe;color:#1d4ed8;',
                            'admin'      => 'background:#fef3c7;color:#92400e;',
                        ];
                    @endphp
                    <span class="wp-badge" style="{{ $roleStyles[$user->role] ?? 'background:#f0f0f1;color:#50575e;' }}">{{ $user->roleLabel() }}</span>
                </td>
                <td>
                    @if($user->is_active)
                        <span class="wp-badge wp-badge-active">활성</span>
                    @else
                        <span class="wp-badge wp-badge-inactive">비활성</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                <td>
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        <a href="{{ route('admin.user.edit', $user->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">수정</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.user.role', $user->id) }}" method="POST" style="display:inline;gap:0;">@csrf
                            <select name="role" onchange="this.form.submit()" style="font-size:12px;padding:2px 4px;border:1px solid #8c8f94;border-radius:3px;cursor:pointer;background:#fff;">
                                <option value="subscriber" {{ $user->role=='subscriber'?'selected':'' }}>구독자</option>
                                <option value="author"     {{ $user->role=='author'    ?'selected':'' }}>작성자</option>
                                <option value="editor"     {{ $user->role=='editor'    ?'selected':'' }}>편집자</option>
                                <option value="admin"      {{ $user->role=='admin'     ?'selected':'' }}>관리자</option>
                            </select>
                        </form>
                        @endif
                        <form action="{{ route('admin.user.toggle', $user->id) }}" method="POST" style="display:inline;">@csrf
                            <button class="wp-btn wp-btn-sm {{ $user->is_active ? 'wp-btn-warning' : 'wp-btn-primary' }}">{{ $user->is_active ? '비활성' : '활성' }}</button>
                        </form>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">@csrf @method('DELETE')
                            <button class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:30px;color:#8c8f94;">등록된 회원이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>

<div style="margin-top:16px;">{{ $users->links() }}</div>
@endsection
