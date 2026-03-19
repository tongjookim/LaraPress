<?php

namespace App\Http\Controllers;

use App\Models\ArticleComment;
use Illuminate\Http\Request;

class AdminArticleCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'role:editor']);
    }

    // ── 목록 ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $tab    = $request->input('tab', 'all');   // all | pending | approved | trashed
        $search = $request->input('q', '');

        $query = ArticleComment::with(['user', 'article'])
            ->withTrashed();

        match ($tab) {
            'pending'  => $query->whereNull('deleted_at')->where('is_approved', false),
            'approved' => $query->whereNull('deleted_at')->where('is_approved', true),
            'trashed'  => $query->whereNotNull('deleted_at'),
            default    => $query->whereNull('deleted_at'),
        };

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user',    fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('article', fn($a) => $a->where('title', 'like', "%{$search}%"));
            });
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        // 탭별 카운트
        $counts = [
            'all'      => ArticleComment::whereNull('deleted_at')->count(),
            'pending'  => ArticleComment::whereNull('deleted_at')->where('is_approved', false)->count(),
            'approved' => ArticleComment::whereNull('deleted_at')->where('is_approved', true)->count(),
            'trashed'  => ArticleComment::onlyTrashed()->count(),
        ];

        return view('admin.article-comments', compact('comments', 'tab', 'search', 'counts'));
    }

    // ── 승인 토글 ─────────────────────────────────────────────
    public function approve($id)
    {
        $comment = ArticleComment::findOrFail($id);
        $comment->update(['is_approved' => !$comment->is_approved]);
        $msg = $comment->is_approved ? '댓글이 승인되었습니다.' : '댓글 승인이 취소되었습니다.';
        return back()->with('success', $msg);
    }

    // ── 수정 ──────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $comment = ArticleComment::findOrFail($id);
        $data    = $request->validate(['content' => 'required|max:2000']);
        $comment->update($data);
        return back()->with('success', '댓글이 수정되었습니다.');
    }

    // ── 삭제 (soft) ───────────────────────────────────────────
    public function destroy($id)
    {
        ArticleComment::findOrFail($id)->delete();
        return back()->with('success', '댓글이 삭제되었습니다.');
    }

    // ── 복원 ──────────────────────────────────────────────────
    public function restore($id)
    {
        ArticleComment::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', '댓글이 복원되었습니다.');
    }

    // ── 영구 삭제 ─────────────────────────────────────────────
    public function forceDestroy($id)
    {
        ArticleComment::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', '댓글이 영구 삭제되었습니다.');
    }

    // ── 일괄 처리 ─────────────────────────────────────────────
    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids    = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', '선택된 댓글이 없습니다.');
        }

        match ($action) {
            'approve' => ArticleComment::whereIn('id', $ids)->update(['is_approved' => true]),
            'unapprove' => ArticleComment::whereIn('id', $ids)->update(['is_approved' => false]),
            'delete'  => ArticleComment::whereIn('id', $ids)->delete(),
            'restore' => ArticleComment::withTrashed()->whereIn('id', $ids)->restore(),
            'force_delete' => ArticleComment::withTrashed()->whereIn('id', $ids)->forceDelete(),
            default   => null,
        };

        $count = count($ids);
        return back()->with('success', "{$count}개 댓글에 작업이 적용되었습니다.");
    }
}
