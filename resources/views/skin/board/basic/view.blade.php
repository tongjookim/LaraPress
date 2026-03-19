@extends('skin.layout.basic.main')
@push('skin-css')
    @vite(['resources/views/skin/board/' . $board->skin . '/style.css'])
@endpush
@section('title', " - {$post->title}")

@section('content')
<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex text-sm text-gray-500">
        <a href="/" class="site-nav-link">홈</a>
        <span class="mx-2">/</span>
        <a href="{{ route('bbs.index', $board->board_id) }}" class="site-nav-link">{{ $board->board_name }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-700">{{ Str::limit($post->title, 30) }}</span>
    </nav>
</div>

<!-- Post Content -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="p-8 border-b" style="background:var(--site-primary-light);">
        @if($post->is_notice)
        <span class="inline-block px-3 py-1 text-white text-xs font-semibold rounded-full mb-3"
              style="background:var(--site-primary);">
            공지사항
        </span>
        @endif

        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

        <div class="flex items-center text-sm text-gray-600 space-x-4">
            <span class="flex items-center font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ $post->user->name }}
            </span>
            <span>{{ $post->created_at->format('Y년 m월 d일 H:i') }}</span>
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ number_format($post->view_count) }}
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="p-8">
        <div class="prose max-w-none text-gray-700 leading-relaxed">
            {!! $post->content !!}
        </div>
    </div>

    <!-- Actions -->
    <div class="px-8 py-4 bg-gray-50 border-t flex justify-between items-center">
        <a href="{{ route('bbs.index', $board->board_id) }}"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            목록
        </a>

        @if($post->user_id === auth()->id() || auth()->user()->isAdmin())
        <div class="space-x-2">
            <a href="{{ route('bbs.edit', [$board->board_id, $post->id]) }}"
               class="px-4 py-2 text-white rounded-lg transition"
               style="background:var(--site-accent);display:inline-block;">
                수정
            </a>
            <form method="POST" action="{{ route('bbs.delete', [$board->board_id, $post->id]) }}" class="inline-block"
                  onsubmit="return confirm('정말 삭제하시겠습니까?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    삭제
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- Comments Section -->
@if($board->use_comment)
<div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <h3 class="text-xl font-bold mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2 site-primary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        댓글 <span class="site-primary-text ml-2">{{ $post->comments->count() }}</span>
    </h3>

    <!-- Comment Form -->
    <form method="POST" action="{{ route('bbs.comment.store', [$board->board_id, $post->id]) }}" class="mb-8">
        @csrf
        <textarea name="content" rows="3"
                  class="w-full px-4 py-3 rounded-lg resize-none site-input"
                  placeholder="댓글을 입력하세요..." required></textarea>
        <div class="mt-3 flex justify-end">
            <button type="submit"
                    class="px-6 py-2 rounded-lg text-white site-primary-btn">
                댓글 등록
            </button>
        </div>
    </form>

    <!-- Comments List -->
    <div class="space-y-4">
        @forelse($post->comments as $comment)
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $comment->created_at->format('Y.m.d H:i') }}</span>
                    @if($comment->user_id === auth()->id() || auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('bbs.comment.delete', [$board->board_id, $post->id, $comment->id]) }}"
                          class="inline-block" onsubmit="return confirm('댓글을 삭제하시겠습니까?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">삭제</button>
                    </form>
                    @endif
                </div>
            </div>
            <p class="text-gray-700">{!! nl2br(e($comment->content)) !!}</p>
        </div>
        @empty
        <p class="text-center text-gray-400 py-8">첫 댓글을 작성해보세요!</p>
        @endforelse
    </div>
</div>
@endif
@endsection
