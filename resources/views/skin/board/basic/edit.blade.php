@extends('skin.layout.basic.main')
@push('skin-css')
    @vite(['resources/views/skin/board/' . $board->skin . '/style.css'])
@endpush

@section('title', " - 글수정")

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">글수정</h1>
        <p class="text-gray-500 mt-2">{{ $board->board_name }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('bbs.update', [$board->board_id, $post->id]) }}">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                <input type="text" name="title"
                       class="w-full px-4 py-3 site-input"
                       placeholder="제목을 입력하세요"
                       value="{{ old('title', $post->title) }}"
                       required>
                @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">내용</label>
                <textarea name="content" id="se2_content" style="width:100%;height:450px;display:none;">{{ old('content', $post->content) }}</textarea>
                @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center pt-4 border-t">
                <a href="{{ route('bbs.show', [$board->board_id, $post->id]) }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    취소
                </a>
                <button type="submit"
                        class="px-8 py-3 rounded-lg font-medium text-white site-primary-btn">
                    수정하기
                </button>
            </div>
        </form>
    </div>
</div>
@include('partials.smarteditor', ['editorId' => 'se2_content', 'editorHeight' => 450])
@endsection
