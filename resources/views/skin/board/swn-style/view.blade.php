@extends('skin.layout.basic.main')
@section('title', " - {$post->title}")

@section('content')
<!-- Breadcrumb -->
<div class="mb-4">
    <nav class="flex text-xs text-gray-500">
        <a href="/" class="hover:text-blue-600">홈</a>
        <span class="mx-1.5">/</span>
        <a href="{{ route('bbs.index', $board->board_id) }}" class="hover:text-blue-600">{{ $board->board_name }}</a>
        <span class="mx-1.5">/</span>
        <span class="text-gray-700">{{ Str::limit($post->title, 40) }}</span>
    </nav>
</div>

<!-- Post Content -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
        @if($post->is_notice)
        <span class="inline-block px-2 py-0.5 bg-blue-700 text-white text-xs font-semibold rounded mr-2">
            공지
        </span>
        @endif

        <h1 class="text-2xl font-bold text-gray-900 mb-3 leading-tight">{{ $post->title }}</h1>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
            <span class="font-medium text-gray-700">{{ $post->user->name }}</span>
            <span>{{ $post->created_at->format('Y.m.d H:i') }}</span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ number_format($post->view_count) }}
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-8 min-h-48">
        <div class="prose max-w-none text-gray-800 leading-relaxed">
            {!! $post->content !!}
        </div>
    </div>

    <!-- Actions -->
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
        <a href="{{ route('bbs.index', $board->board_id) }}"
           class="px-4 py-1.5 text-sm bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            목록
        </a>

        @if(auth()->check() && ($post->user_id === auth()->id() || auth()->user()->isAdmin()))
        <div class="flex gap-2">
            <a href="{{ route('bbs.edit', [$board->board_id, $post->id]) }}"
               class="px-4 py-1.5 text-sm bg-blue-600 text-white hover:bg-blue-700 transition">
                수정
            </a>
            <form method="POST" action="{{ route('bbs.delete', [$board->board_id, $post->id]) }}" class="inline-block" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-1.5 text-sm bg-red-500 text-white hover:bg-red-600 transition">
                    삭제
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- Comments Section -->
@if($board->use_comment)
<div class="mt-6 bg-white border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-base font-bold text-gray-800">
            댓글 <span class="text-blue-600">{{ $post->comments->count() }}</span>
        </h3>
    </div>

    <!-- Comment Form -->
    <div class="px-6 py-5 border-b border-gray-100">
        <form method="POST" action="{{ route('bbs.comment.store', [$board->board_id, $post->id]) }}">
            @csrf
            <textarea name="content" rows="3"
                      class="w-full px-3 py-2 text-sm border border-gray-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none"
                      placeholder="댓글을 입력하세요..." required></textarea>
            <div class="mt-2 flex justify-end">
                <button type="submit"
                        class="px-5 py-1.5 text-sm bg-blue-700 text-white hover:bg-blue-800 transition">
                    등록
                </button>
            </div>
        </form>
    </div>

    <!-- Comments List -->
    <div>
        @forelse($post->comments as $comment)
        <div class="px-6 py-4 border-b border-gray-100 last:border-0">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</span>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">{{ $comment->created_at->format('Y.m.d H:i') }}</span>
                    @if(auth()->check() && ($comment->user_id === auth()->id() || auth()->user()->isAdmin()))
                    <form method="POST" action="{{ route('bbs.comment.delete', [$board->board_id, $post->id, $comment->id]) }}" class="inline-block" onsubmit="return confirm('댓글을 삭제하시겠습니까?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">삭제</button>
                    </form>
                    @endif
                </div>
            </div>
            <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($comment->content)) !!}</p>
        </div>
        @empty
        <p class="text-center text-sm text-gray-400 py-8">첫 댓글을 작성해보세요!</p>
        @endforelse
    </div>
</div>
@endif
@endsection
