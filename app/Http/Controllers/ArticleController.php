<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();

        $currentCategory = null;
        $query = Article::with(['category', 'user'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

        if ($slug = $request->input('category')) {
            $currentCategory = $categories->firstWhere('slug', $slug);
            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }
        }

        $articles = $query->paginate(20)->withQueryString();

        // 피처드 기사 (썸네일 있는 최신 3개) — 뷰에서 중복 제거에 사용
        $featuredQuery = Article::where('status', 'published')
            ->whereNotNull('thumbnail')
            ->where('thumbnail', '!=', '');
        if ($currentCategory) {
            $featuredQuery->where('category_id', $currentCategory->id);
        }
        $featuredArticles = $featuredQuery->orderBy('published_at', 'desc')->limit(3)->get();

        return view('articles.list', compact('articles', 'categories', 'currentCategory', 'featuredArticles'));
    }

    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));

        $articles = Article::with(['category', 'user'])
            ->where('status', 'published')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sq) use ($q) {
                    $sq->where('title', 'like', '%' . $q . '%')
                       ->orWhere('subtitle', 'like', '%' . $q . '%')
                       ->orWhere('excerpt', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $popular = Article::with('user')
            ->where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->limit(6)
            ->get();

        return view('articles.search', compact('articles', 'q', 'popular'));
    }

    public function show(string $slug)
    {
        // URL 디코딩된 슬래그와 원본 모두 시도 (한글 슬래그 지원)
        $decoded = rawurldecode($slug);
        $article = Article::with(['category', 'user'])
            ->where(function ($q) use ($slug, $decoded) {
                $q->where('slug', $slug);
                if ($decoded !== $slug) {
                    $q->orWhere('slug', $decoded);
                }
            })
            ->where('status', 'published')
            ->firstOrFail();

        $key    = 'viewed_articles';
        $viewed = session($key, []);
        if (!in_array($article->id, $viewed)) {
            $article->increment('view_count');
            session([$key => array_merge($viewed, [$article->id])]);
        }

        $related = Article::with(['category', 'user'])
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->when($article->category_id, fn($q) => $q->where('category_id', $article->category_id))
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        $commentsEnabled = Setting::get('comments_enabled', '1') === '1';
        $comments = [];
        if ($commentsEnabled) {
            $requireLogin = Setting::get('comments_require_login', '1') === '1';
            $comments = ArticleComment::with(['user', 'replies.user'])
                ->where('article_id', $article->id)
                ->whereNull('parent_id')
                ->where('is_approved', true)
                ->orderBy('created_at')
                ->get();
        }

        return view('articles.show', compact('article', 'related', 'commentsEnabled', 'comments'));
    }

    public function author(string $username)
    {
        $author = User::where('username', $username)->firstOrFail();

        // 카테고리별 최근 기사 (각 3개, 썸네일 있는 것 우선)
        $categoriesWithArticles = ArticleCategory::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($cat) use ($author) {
                $articles = Article::with('category')
                    ->where('status', 'published')
                    ->where('user_id', $author->id)
                    ->where('category_id', $cat->id)
                    ->orderByRaw('thumbnail IS NULL ASC')
                    ->orderBy('published_at', 'desc')
                    ->limit(5)
                    ->get();
                return $articles->count() ? $cat->setRelation('recentArticles', $articles) : null;
            })
            ->filter();

        // 전체 기사 (페이지네이션)
        $articles = Article::with('category')
            ->where('status', 'published')
            ->where('user_id', $author->id)
            ->orderBy('published_at', 'desc')
            ->paginate(16);

        $totalArticles = Article::where('status', 'published')->where('user_id', $author->id)->count();
        $totalViews    = Article::where('status', 'published')->where('user_id', $author->id)->sum('view_count');

        return view('articles.author', compact('author', 'categoriesWithArticles', 'articles', 'totalArticles', 'totalViews'));
    }

    public function commentStore(Request $request, string $slug)
    {
        if (Setting::get('comments_enabled', '1') !== '1') {
            return back()->with('error', '댓글이 비활성화되어 있습니다.');
        }

        $article = Article::where('slug', $slug)->where('status', 'published')->firstOrFail();

        $request->validate([
            'content'   => 'required|max:2000',
            'parent_id' => 'nullable|exists:article_comments,id',
        ]);

        $moderation = Setting::get('comments_moderation', '0') === '1';

        ArticleComment::create([
            'article_id'  => $article->id,
            'user_id'     => auth()->id(),
            'parent_id'   => $request->input('parent_id'),
            'content'     => $request->input('content'),
            'is_approved' => !$moderation,
        ]);

        $msg = $moderation ? '댓글이 등록되었습니다. 승인 후 표시됩니다.' : '댓글이 등록되었습니다.';
        return back()->with('success', $msg);
    }

    public function commentDelete(Request $request, string $slug, int $commentId)
    {
        $comment = ArticleComment::findOrFail($commentId);
        $user    = auth()->user();

        // 작성자 본인 또는 관리자/편집자만 삭제 가능
        if ($comment->user_id !== $user->id && !$user->canApproveArticle()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', '댓글이 삭제되었습니다.');
    }
}
