@extends('admin.layout')

@section('title', '알림판')

@section('admin-content')
<h1 class="wp-page-title">알림판</h1>

{{-- 한 눈에 보기 --}}
<div class="admin-grid-4">
    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#2271b1;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;">{{ number_format($stats['posts']) }}</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">게시글</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#00a32a;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;">{{ number_format($stats['comments']) }}</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">댓글</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#8c5ae3;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;">{{ number_format($stats['users']) }}</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">회원</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#dba617;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;">{{ number_format($stats['total_views']) }}</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">총 조회수</div>
            </div>
        </div>
    </div>
</div>

{{-- 위젯 2열 --}}
<div class="admin-grid-2">

    {{-- 좌측 칼럼 --}}
    <div>
        {{-- 사이트 현황 --}}
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">📊 사이트 현황</div>
            <div class="wp-widget-body">
                <table style="width:100%;font-size:13px;">
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">게시판</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ $stats['boards'] }}개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">게시글</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ number_format($stats['posts']) }}개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">댓글</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ number_format($stats['comments']) }}개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">회원</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ number_format($stats['users']) }}명</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">기사 (전체)</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ number_format($stats['articles']) }}개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">기사 (발행)</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ number_format($stats['articles_published']) }}개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">페이지</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ $stats['pages'] }}개</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#646970;">총 조회수</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;">{{ number_format($stats['total_views']) }}회</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 게시판별 통계 --}}
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">📋 게시판별 현황</div>
            <div class="wp-widget-body" style="padding:0;">
                <table class="wp-list-table">
                    <thead>
                        <tr>
                            <th>게시판</th>
                            <th style="text-align:right;">게시글</th>
                            <th style="text-align:right;">조회수</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boardStats as $board)
                        <tr>
                            <td>
                                <a href="{{ route('bbs.index', $board->board_id) }}" style="color:#2271b1;text-decoration:none;">{{ $board->board_name }}</a>
                            </td>
                            <td style="text-align:right;">{{ number_format($board->posts_count) }}</td>
                            <td style="text-align:right;">{{ number_format($board->posts_sum_view_count ?? 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 우측 칼럼 --}}
    <div>
        {{-- 최근 기사 --}}
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header" style="display:flex;justify-content:space-between;align-items:center;">
                <span>📰 최근 기사</span>
                <a href="{{ route('admin.articles') }}" style="font-size:12px;font-weight:400;color:#2271b1;text-decoration:none;">전체 보기 →</a>
            </div>
            <div class="wp-widget-body" style="padding:0;">
                @forelse($recentArticles as $article)
                <div style="padding:10px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <a href="{{ route('admin.article.edit', $article->id) }}" style="color:#2271b1;text-decoration:none;font-weight:500;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $article->title }}
                        </a>
                        <div style="font-size:11px;color:#8c8f94;margin-top:3px;display:flex;align-items:center;gap:6px;">
                            <span>{{ $article->user->name ?? '알 수 없음' }}</span>
                            @if($article->category)
                            <span>· {{ $article->category->name }}</span>
                            @endif
                            <span style="display:inline-block;padding:0 5px;border-radius:2px;font-size:10px;font-weight:600;
                                {{ $article->status === 'published' ? 'background:#dcfce7;color:#166534;' : ($article->status === 'draft' ? 'background:#f3f4f6;color:#374151;' : 'background:#fef9c3;color:#854d0e;') }}">
                                {{ $article->status === 'published' ? '발행' : ($article->status === 'draft' ? '임시저장' : $article->status) }}
                            </span>
                        </div>
                    </div>
                    <span style="font-size:11px;color:#8c8f94;white-space:nowrap;">{{ $article->created_at->format('m.d H:i') }}</span>
                </div>
                @empty
                <div style="padding:24px;text-align:center;color:#8c8f94;font-size:13px;">기사가 없습니다.</div>
                @endforelse
            </div>
        </div>

        {{-- 최근 게시글 --}}
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">🕐 최근 게시글</div>
            <div class="wp-widget-body" style="padding:0;">
                @forelse($recentPosts as $post)
                <div style="padding:10px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <a href="{{ route('bbs.show', [$post->board->board_id ?? 'free', $post->id]) }}" style="color:#2271b1;text-decoration:none;font-weight:500;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $post->title }}
                        </a>
                        <div style="font-size:11px;color:#8c8f94;margin-top:3px;">
                            {{ $post->user->name ?? '알 수 없음' }} · {{ $post->board->board_name ?? '' }}
                        </div>
                    </div>
                    <span style="font-size:11px;color:#8c8f94;white-space:nowrap;">{{ $post->created_at->format('m.d H:i') }}</span>
                </div>
                @empty
                <div style="padding:24px;text-align:center;color:#8c8f94;font-size:13px;">게시글이 없습니다.</div>
                @endforelse
            </div>
        </div>

        {{-- 최근 댓글 --}}
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">💬 최근 댓글</div>
            <div class="wp-widget-body" style="padding:0;">
                @forelse($recentComments as $comment)
                <div style="padding:10px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <div style="width:28px;height:28px;border-radius:50%;background:#ddd;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#666;flex-shrink:0;">
                            {{ mb_substr($comment->user->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <span style="font-weight:600;color:#1d2327;">{{ $comment->user->name ?? '알 수 없음' }}</span>
                            <span style="color:#8c8f94;font-size:11px;margin-left:6px;">{{ $comment->created_at->format('m.d H:i') }}</span>
                        </div>
                    </div>
                    <div style="color:#646970;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-left:36px;">
                        {{ Str::limit($comment->content, 80) }}
                    </div>
                </div>
                @empty
                <div style="padding:24px;text-align:center;color:#8c8f94;font-size:13px;">댓글이 없습니다.</div>
                @endforelse
            </div>
        </div>

        {{-- 최근 가입 회원 --}}
        <div class="wp-widget">
            <div class="wp-widget-header">👤 최근 가입 회원</div>
            <div class="wp-widget-body" style="padding:0;">
                @foreach($recentUsers as $user)
                <div style="padding:8px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;display:flex;justify-content:space-between;align-items:center;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:28px;height:28px;border-radius:50%;background:{{ $user->is_admin ? '#dbeafe' : '#f3f4f6' }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:{{ $user->is_admin ? '#2271b1' : '#666' }};">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <span style="font-weight:500;">{{ $user->name }}</span>
                            @if($user->is_admin)
                                <span class="wp-badge wp-badge-admin" style="margin-left:4px;">관리자</span>
                            @endif
                        </div>
                    </div>
                    <span style="font-size:11px;color:#8c8f94;">{{ $user->created_at->format('Y.m.d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
