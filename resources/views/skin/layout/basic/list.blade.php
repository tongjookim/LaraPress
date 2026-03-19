{{--
    레이아웃 스킨 list.blade.php — 현재 미사용 (ArticleController는 articles.list 뷰를 직접 렌더링).
    향후 스킨 기반 기사 목록 렌더링으로 전환 시 이 템플릿을 활성화할 수 있음.
--}}
@extends('skin.layout.basic.main')

@section('title', isset($currentCategory) ? " - {$currentCategory->name}" : ' - 기사 목록')

@section('content')
<div class="flex flex-col lg:flex-row gap-8">
    <div class="flex-1 min-w-0">

        {{-- 카테고리 탭 --}}
        @if(isset($categories) && $categories->isNotEmpty())
        <div class="flex gap-2 flex-wrap mb-6">
            <a href="{{ route('news.index') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ !isset($currentCategory) ? 'text-white site-primary-btn' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                전체
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('news.index', ['category' => $cat->slug]) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ isset($currentCategory) && $currentCategory->id === $cat->id ? 'text-white site-primary-btn' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- 기사 목록 --}}
        @if(isset($articles) && $articles->isNotEmpty())
        <div class="space-y-4">
            @foreach($articles as $article)
            <a href="{{ route('news.show', $article->slug) }}"
               class="group flex items-center gap-4 p-5 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                @if($article->thumbnail)
                <div style="flex-shrink:0;width:100px;height:72px;border-radius:8px;overflow:hidden;">
                    <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}"
                         style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    @if($article->category)
                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded mb-1"
                          style="background:var(--site-primary-light);color:var(--site-primary);">
                        {{ $article->category->name }}
                    </span>
                    @endif
                    <h2 class="font-bold text-gray-900 leading-snug mb-1 group-hover:underline site-nav-link">
                        {{ $article->title }}
                    </h2>
                    @if($article->excerpt)
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $article->excerpt }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1.5">
                        {{ $article->user->name ?? '' }} · {{ $article->published_at?->format('Y.m.d') }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $articles->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-20 text-gray-400">등록된 기사가 없습니다.</div>
        @endif

    </div>
</div>
@endsection
