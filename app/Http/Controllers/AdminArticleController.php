<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // ── 카테고리 ──────────────────────────────────────────

    public function categories()
    {
        $categories = ArticleCategory::with('parent')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('admin.categories', compact('categories'));
    }

    public function categoryCreate()
    {
        $parents = ArticleCategory::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('admin.category-form', compact('parents'));
    }

    public function categoryStore(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|max:100',
            'slug'        => ['nullable', 'max:100', 'unique:article_categories', 'regex:/^[\p{L}\p{N}\-_]+$/u'],
            'parent_id'   => 'nullable|exists:article_categories,id',
            'description' => 'nullable|max:500',
            'order'       => 'nullable|integer',
        ]);

        $validated['slug']      = $validated['slug'] ?: ArticleCategory::makeSlug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['order']     = $validated['order'] ?? 0;

        ArticleCategory::create($validated);

        return redirect()->route('admin.categories')
            ->with('success', '카테고리가 생성되었습니다.');
    }

    public function categoryEdit($id)
    {
        $category = ArticleCategory::findOrFail($id);
        $parents  = ArticleCategory::where('is_active', true)
            ->where('id', '!=', $id)
            ->orderBy('order')
            ->get();

        return view('admin.category-form', compact('category', 'parents'));
    }

    public function categoryUpdate(Request $request, $id)
    {
        $category  = ArticleCategory::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|max:100',
            'slug'        => ['nullable', 'max:100', Rule::unique('article_categories')->ignore($category->id), 'regex:/^[\p{L}\p{N}\-_]+$/u'],
            'parent_id'   => 'nullable|exists:article_categories,id',
            'description' => 'nullable|max:500',
            'order'       => 'nullable|integer',
        ]);

        // 자기 자신을 부모로 설정하는 것을 방지
        if ((int) ($validated['parent_id'] ?? 0) === $category->id) {
            return back()->withErrors(['parent_id' => '자기 자신을 상위 카테고리로 지정할 수 없습니다.']);
        }

        $validated['slug']      = $validated['slug'] ?: ArticleCategory::makeSlug($validated['name'], $category->id);
        $validated['is_active'] = $request->has('is_active');
        $validated['order']     = $validated['order'] ?? 0;

        $category->update($validated);

        return redirect()->route('admin.categories')
            ->with('success', '카테고리가 수정되었습니다.');
    }

    public function categoryDelete($id)
    {
        $category = ArticleCategory::findOrFail($id);

        // 하위 카테고리가 있으면 삭제 불가
        if ($category->children()->exists()) {
            return back()->with('error', '하위 카테고리가 있어 삭제할 수 없습니다. 먼저 하위 카테고리를 삭제하세요.');
        }

        $category->delete();

        return back()->with('success', '카테고리가 삭제되었습니다.');
    }

    // ── 기사 ──────────────────────────────────────────────

    public function articles(Request $request)
    {
        $user    = auth()->user();
        $isTrash = $request->input('status') === 'trash';

        if ($isTrash) {
            $query = Article::onlyTrashed()->with(['user', 'category'])->orderBy('deleted_at', 'desc');
        } else {
            $query = Article::with(['user', 'category'])->orderBy('created_at', 'desc');

            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }
        }

        // 작성자는 자기 기사만 조회
        if ($user->role === 'author') {
            $query->where('user_id', $user->id);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles   = $query->paginate(20)->withQueryString();
        $categories = ArticleCategory::orderBy('order')->get();

        $counts = [
            'all'       => Article::count(),
            'draft'     => Article::where('status', 'draft')->count(),
            'pending'   => Article::where('status', 'pending')->count(),
            'published' => Article::where('status', 'published')->count(),
            'trash'     => Article::onlyTrashed()->count(),
        ];

        return view('admin.articles', compact('articles', 'categories', 'counts', 'isTrash'));
    }

    public function articleCreate()
    {
        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();

        return view('admin.article-form', compact('categories'));
    }

    public function articleStore(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|max:300',
            'subtitle'         => 'nullable|max:300',
            'slug'             => ['nullable', 'max:300', 'unique:articles', 'regex:/^[\p{L}\p{N}\-_]+$/u'],
            'category_id'      => 'nullable|exists:article_categories,id',
            'content'          => 'required',
            'excerpt'          => 'nullable|max:500',
            'thumbnail'        => 'nullable|max:500',
            'status'           => 'required|in:draft,pending,published',
            'published_at'     => 'nullable|date',
            'meta_title'       => 'nullable|max:100',
            'meta_description' => 'nullable|max:300',
            'meta_keywords'    => 'nullable|max:200',
            'og_image'         => 'nullable|url|max:500',
            'focus_keyword'    => 'nullable|max:100',
        ]);

        $validated['slug']    = $validated['slug'] ?: Article::makeSlug($validated['title']);
        $validated['user_id'] = auth()->id();

        // 작성자(author)는 항상 pending — 편집자/관리자만 즉시 게시 가능
        if (auth()->user()->role === 'author') {
            $validated['status'] = 'pending';
        }

        $validated['published_at'] = $validated['status'] === 'published'
            ? ($validated['published_at'] ?? now())
            : ($validated['published_at'] ?? null);

        Article::create($validated);

        return redirect()->route('admin.articles')
            ->with('success', '기사가 등록되었습니다.');
    }

    public function articleEdit($id)
    {
        $article    = Article::findOrFail($id);
        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();

        return view('admin.article-form', compact('article', 'categories'));
    }

    public function articleUpdate(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|max:300',
            'subtitle'         => 'nullable|max:300',
            'slug'             => ['nullable', 'max:300', Rule::unique('articles')->ignore($article->id), 'regex:/^[\p{L}\p{N}\-_]+$/u'],
            'category_id'      => 'nullable|exists:article_categories,id',
            'content'          => 'required',
            'excerpt'          => 'nullable|max:500',
            'thumbnail'        => 'nullable|max:500',
            'status'           => 'required|in:draft,pending,published',
            'published_at'     => 'nullable|date',
            'meta_title'       => 'nullable|max:100',
            'meta_description' => 'nullable|max:300',
            'meta_keywords'    => 'nullable|max:200',
            'og_image'         => 'nullable|url|max:500',
            'focus_keyword'    => 'nullable|max:100',
        ]);

        $validated['slug'] = $validated['slug'] ?: Article::makeSlug($validated['title'], $article->id);

        // 작성자(author)는 다른 사람의 기사 수정 불가
        if (auth()->user()->role === 'author' && $article->user_id !== auth()->id()) {
            abort(403, '자신의 기사만 수정할 수 있습니다.');
        }
        // 작성자는 상태를 pending 이상으로 설정 불가
        if (auth()->user()->role === 'author') {
            $validated['status'] = 'pending';
        }

        // 처음 published로 전환하는 경우 published_at 자동 설정
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = $article->published_at ?? now();
        }

        $article->update($validated);

        return redirect()->route('admin.articles')
            ->with('success', '기사가 수정되었습니다.');
    }

    public function articleStatus(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $request->validate(['status' => 'required|in:draft,pending,published']);

        $article->status = $request->input('status');

        if ($article->status === 'published' && !$article->published_at) {
            $article->published_at = now();
        }

        $article->save();

        $label = $article->statusLabel();

        return back()->with('success', "기사 상태가 [{$label}](으)로 변경되었습니다.");
    }

    public function articleDelete($id)
    {
        Article::findOrFail($id)->delete(); // soft delete → 휴지통

        return back()->with('success', '기사가 휴지통으로 이동되었습니다.');
    }

    public function articleRestore($id)
    {
        Article::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', '기사가 복원되었습니다.');
    }

    public function articleForceDelete($id)
    {
        Article::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', '기사가 영구 삭제되었습니다.');
    }

    public function articleEmptyTrash()
    {
        $count = Article::onlyTrashed()->count();
        Article::onlyTrashed()->forceDelete();

        return back()->with('success', "휴지통을 비웠습니다. ({$count}개 영구 삭제)");
    }

    // ── 일괄 처리 ─────────────────────────────────────────
    public function articleBulk(Request $request)
    {
        $action = $request->input('action');
        $ids    = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', '선택된 기사가 없습니다.');
        }

        $count = count($ids);

        match ($action) {
            'delete'       => Article::whereIn('id', $ids)->delete(),
            'restore'      => Article::onlyTrashed()->whereIn('id', $ids)->restore(),
            'force_delete' => Article::onlyTrashed()->whereIn('id', $ids)->forceDelete(),
            'publish'      => Article::whereIn('id', $ids)->update(['status' => 'published', 'published_at' => now()]),
            'draft'        => Article::whereIn('id', $ids)->update(['status' => 'draft']),
            default        => null,
        };

        $labels = [
            'delete'       => '휴지통으로 이동',
            'restore'      => '복원',
            'force_delete' => '영구 삭제',
            'publish'      => '게시 처리',
            'draft'        => '초안으로 변경',
        ];

        $label = $labels[$action] ?? '처리';
        return back()->with('success', "{$count}개 기사가 {$label}되었습니다.");
    }

    // ── 내보내기 (JSON) ──────────────────────────────────

    public function export(Request $request)
    {
        $query = Article::with(['category', 'user']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $articles = $query->orderBy('published_at', 'desc')->get();

        $data = $articles->map(fn($a) => [
            'title'        => $a->title,
            'slug'         => $a->slug,
            'category'     => $a->category?->slug,
            'author'       => $a->user?->name,
            'content'      => $a->content,
            'excerpt'      => $a->excerpt,
            'thumbnail'    => $a->thumbnail,
            'status'       => $a->status,
            'published_at' => $a->published_at?->toDateTimeString(),
            'created_at'   => $a->created_at->toDateTimeString(),
        ]);

        $filename = 'articles-export-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    // ── 가져오기 (JSON) ──────────────────────────────────

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json,txt|max:10240',
        ]);

        $json = file_get_contents($request->file('file')->getRealPath());
        $rows = json_decode($json, true);

        if (!is_array($rows)) {
            return back()->with('error', '올바른 JSON 파일이 아닙니다.');
        }

        $categoryMap = ArticleCategory::pluck('id', 'slug');
        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            if (empty($row['title']) || empty($row['content'])) {
                $skipped++;
                continue;
            }

            $slug = !empty($row['slug'])
                ? $row['slug']
                : Article::makeSlug($row['title']);

            // slug 중복 시 suffix 추가
            if (Article::where('slug', $slug)->exists()) {
                $slug = Article::makeSlug($row['title']);
            }

            $categoryId = null;
            if (!empty($row['category']) && isset($categoryMap[$row['category']])) {
                $categoryId = $categoryMap[$row['category']];
            }

            Article::create([
                'title'        => $row['title'],
                'slug'         => $slug,
                'category_id'  => $categoryId,
                'user_id'      => auth()->id(),
                'content'      => $row['content'],
                'excerpt'      => $row['excerpt'] ?? null,
                'thumbnail'    => $row['thumbnail'] ?? null,
                'status'       => in_array($row['status'] ?? '', ['draft','pending','published'])
                                    ? $row['status'] : 'draft',
                'published_at' => !empty($row['published_at']) ? $row['published_at'] : null,
            ]);

            $imported++;
        }

        return back()->with('success', "가져오기 완료: {$imported}개 등록, {$skipped}개 건너뜀.");
    }
}
