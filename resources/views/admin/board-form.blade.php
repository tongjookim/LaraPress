@extends('admin.layout')
@section('title', isset($board) ? '게시판 수정' : '게시판 생성')
@section('admin-content')
<h1 class="wp-page-title">{{ isset($board) ? '게시판 수정' : '게시판 생성' }}</h1>

<div class="wp-widget" style="max-width:600px;">
    <div class="wp-widget-body">
        <form action="{{ isset($board) ? route('admin.board.update', $board->id) : route('admin.board.store') }}" method="POST">
            @csrf
            @if(isset($board)) @method('PUT') @endif

            @unless(isset($board))
            <div class="wp-form-group">
                <label class="wp-form-label">게시판 ID *</label>
                <input type="text" name="board_id" value="{{ old('board_id') }}" required class="wp-form-input">
                <p class="wp-form-help">영문, 숫자, 하이픈만 사용 (예: free, notice, qna)</p>
                @error('board_id') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>
            @endunless

            <div class="wp-form-group">
                <label class="wp-form-label">게시판 이름 *</label>
                <input type="text" name="board_name" value="{{ old('board_name', $board->board_name ?? '') }}" required class="wp-form-input">
                @error('board_name') <p style="color:#d63638;font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">스킨 *</label>
                <select name="skin" class="wp-form-input wp-form-select">
                    @php $skins = ['basic' => 'Basic']; @endphp
                    @foreach($skins ?? ['basic' => 'Basic'] as $key => $name)
                        <option value="{{ $key }}" {{ old('skin', $board->skin ?? 'basic') == $key ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="wp-form-group">
                <label class="wp-form-label">페이지당 글 수 *</label>
                <input type="number" name="posts_per_page" value="{{ old('posts_per_page', $board->posts_per_page ?? 15) }}" min="5" max="100" required class="wp-form-input" style="max-width:120px;">
            </div>

            <div style="padding-top:12px;border-top:1px solid #c3c4c7;">
                <button type="submit" class="wp-btn wp-btn-primary">{{ isset($board) ? '수정 저장' : '게시판 생성' }}</button>
                <a href="{{ route('admin.boards') }}" class="wp-btn wp-btn-secondary">취소</a>
            </div>
        </form>
    </div>
</div>
@endsection
