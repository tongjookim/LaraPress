<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $categories = ArticleCategory::where('is_active', true)->orderBy('order')->get();
        $articleCount = Article::count();
        return view('admin.tools', compact('categories', 'articleCount'));
    }

    // ── 내보내기 (JSON) ──────────────────────────────────

    public function exportJson(Request $request)
    {
        $request->validate([
            'status'      => 'nullable|in:draft,pending,published',
            'category_id' => 'nullable|exists:article_categories,id',
        ]);

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

    // ── JSON 가져오기 ─────────────────────────────────────

    public function importJson(Request $request)
    {
        $request->validate([
            'file'        => 'required|file|mimes:json,txt|max:20480',
            'category_id' => 'nullable|exists:article_categories,id',
            'status'      => 'required|in:draft,pending,published',
        ]);

        $json = file_get_contents($request->file('file')->getRealPath());
        $rows = json_decode($json, true);

        if (!is_array($rows)) {
            return back()->with('error', '올바른 JSON 파일이 아닙니다.')->with('tab', 'json');
        }

        $categoryMap   = ArticleCategory::pluck('id', 'slug');
        $defaultCatId  = $request->input('category_id');
        $defaultStatus = $request->input('status', 'draft');
        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            if (empty($row['title']) || empty($row['content'])) {
                $skipped++;
                continue;
            }

            $slug = !empty($row['slug']) ? $row['slug'] : Article::makeSlug($row['title']);
            if (Article::where('slug', $slug)->exists()) {
                $slug = Article::makeSlug($row['title']);
            }

            $catId = $defaultCatId;
            if (!empty($row['category']) && isset($categoryMap[$row['category']])) {
                $catId = $categoryMap[$row['category']];
            }

            $status = in_array($row['status'] ?? '', ['draft', 'pending', 'published'])
                ? $row['status'] : $defaultStatus;

            $pubAt = null;
            if (!empty($row['published_at'])) {
                try { $pubAt = Carbon::parse($row['published_at']); } catch (\Exception $e) {}
            }

            Article::create([
                'title'        => $row['title'],
                'slug'         => $slug,
                'category_id'  => $catId,
                'user_id'      => auth()->id(),
                'content'      => $row['content'],
                'excerpt'      => $row['excerpt'] ?? null,
                'thumbnail'    => $row['thumbnail'] ?? null,
                'status'       => $status,
                'published_at' => $status === 'published' ? ($pubAt ?? now()) : $pubAt,
            ]);

            $imported++;
        }

        return back()->with('success', "JSON 가져오기 완료: {$imported}개 등록, {$skipped}개 건너뜀.")->with('tab', 'json');
    }

    // ── RSS / Atom 가져오기 ───────────────────────────────

    public function importRss(Request $request)
    {
        $request->validate([
            'rss_url'     => 'required|url|max:500',
            'category_id' => 'nullable|exists:article_categories,id',
            'status'      => 'required|in:draft,pending,published',
            'limit'       => 'nullable|integer|min:1|max:200',
        ]);

        $url    = $request->input('rss_url');
        $limit  = (int)$request->input('limit', 50);
        $catId  = $request->input('category_id');
        $status = $request->input('status', 'draft');

        $context = stream_context_create(['http' => [
            'timeout'    => 15,
            'user_agent' => 'Mozilla/5.0 (compatible; Laraboard RSS Importer/1.0)',
        ]]);

        $xml = @file_get_contents($url, false, $context);
        if ($xml === false) {
            return back()->with('error', 'RSS URL에 접근할 수 없습니다. URL을 확인하세요.')->with('tab', 'rss');
        }

        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        if ($feed === false) {
            return back()->with('error', 'RSS/XML 파싱에 실패했습니다. 올바른 RSS/Atom 피드인지 확인하세요.')->with('tab', 'rss');
        }

        $ns    = $feed->getNamespaces(true);
        $items = [];

        // Atom 피드
        if ($feed->getName() === 'feed' || (isset($ns['']) && str_contains($ns[''] ?? '', 'atom'))) {
            foreach ($feed->entry as $entry) {
                $content = '';
                foreach (['content', 'summary'] as $field) {
                    if (isset($entry->$field) && (string)$entry->$field !== '') {
                        $content = (string)$entry->$field;
                        break;
                    }
                }
                $items[] = [
                    'title'   => (string)$entry->title,
                    'content' => $content,
                    'date'    => (string)($entry->published ?? $entry->updated ?? ''),
                    'excerpt' => '',
                ];
                if (count($items) >= $limit) break;
            }
        } else {
            // RSS 2.0
            $contentNs = $ns['content'] ?? null;
            foreach ($feed->channel->item as $item) {
                $content = '';
                if ($contentNs) {
                    $c = $item->children($contentNs);
                    $content = (string)($c->encoded ?? '');
                }
                if (!$content) {
                    $content = (string)($item->description ?? '');
                }
                $items[] = [
                    'title'   => (string)$item->title,
                    'content' => $content,
                    'date'    => (string)($item->pubDate ?? ''),
                    'excerpt' => '',
                ];
                if (count($items) >= $limit) break;
            }
        }

        $imported = 0;
        $skipped  = 0;

        foreach ($items as $item) {
            if (empty(trim($item['title'])) || empty(trim($item['content']))) {
                $skipped++;
                continue;
            }

            $slug  = Article::makeSlug($item['title']);
            $pubAt = null;
            if (!empty($item['date'])) {
                try { $pubAt = Carbon::parse($item['date']); } catch (\Exception $e) {}
            }

            Article::create([
                'title'        => trim($item['title']),
                'slug'         => $slug,
                'category_id'  => $catId,
                'user_id'      => auth()->id(),
                'content'      => trim($item['content']),
                'excerpt'      => null,
                'status'       => $status,
                'published_at' => $status === 'published' ? ($pubAt ?? now()) : $pubAt,
            ]);

            $imported++;
        }

        return back()->with('success', "RSS 가져오기 완료: {$imported}개 등록, {$skipped}개 건너뜀.")->with('tab', 'rss');
    }

    // ── 워드프레스 WXR 가져오기 ───────────────────────────

    public function importWordpress(Request $request)
    {
        $request->validate([
            'file'        => 'required|file|mimes:xml,txt|max:102400',
            'category_id' => 'nullable|exists:article_categories,id',
            'status'      => 'required|in:draft,pending,published,keep',
            'post_type'   => 'nullable|in:post,page,both',
        ]);

        $xmlPath = $request->file('file')->getRealPath();

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml === false) {
            return back()->with('error', 'WordPress XML(WXR) 파일 파싱에 실패했습니다.')->with('tab', 'wordpress');
        }

        // 네임스페이스 감지
        $ns        = $xml->channel->getNamespaces(true);
        $wpNs      = $ns['wp']      ?? 'http://wordpress.org/export/1.2/';
        $contentNs = $ns['content'] ?? 'http://purl.org/rss/1.0/modules/content/';
        $excerptNs = $ns['excerpt'] ?? 'http://wordpress.org/export/1.2/excerpt/';

        $categoryMap   = ArticleCategory::pluck('id', 'slug');
        $defaultCatId  = $request->input('category_id');
        $defaultStatus = $request->input('status', 'draft');
        $allowedTypes  = $request->input('post_type', 'post') === 'both'
            ? ['post', 'page'] : [$request->input('post_type', 'post')];

        $imported = 0;
        $skipped  = 0;

        foreach ($xml->channel->item as $item) {
            $wp       = $item->children($wpNs);
            $postType = (string)$wp->post_type;

            if (!in_array($postType, $allowedTypes)) continue;

            $title = trim((string)$item->title);
            if (empty($title)) { $skipped++; continue; }

            // 콘텐츠
            $contentEl = $item->children($contentNs);
            $content   = (string)($contentEl->encoded ?? '');
            if (!$content) $content = (string)$item->description;

            // 요약
            $excerptEl = $item->children($excerptNs);
            $excerpt   = trim((string)($excerptEl->encoded ?? ''));

            // 상태
            $wpStatus = (string)$wp->status;
            if ($defaultStatus === 'keep') {
                $status = match ($wpStatus) {
                    'publish' => 'published',
                    'pending' => 'pending',
                    default   => 'draft',
                };
            } else {
                $status = $defaultStatus;
            }

            // Slug — WXR의 post_name은 퍼센트 인코딩된 한글일 수 있으므로 디코딩
            $wpSlug = rawurldecode(trim((string)$wp->post_name));
            $slug   = !empty($wpSlug) ? $wpSlug : Article::makeSlug($title);
            if (Article::where('slug', $slug)->exists()) {
                $slug = Article::makeSlug($title);
            }

            // 카테고리 (WP category nicename → slug 매칭)
            $catId = $defaultCatId;
            foreach ($item->category as $cat) {
                $attrs    = $cat->attributes();
                $nicename = (string)($attrs['nicename'] ?? '');
                if ($nicename && isset($categoryMap[$nicename])) {
                    $catId = $categoryMap[$nicename];
                    break;
                }
            }

            // 날짜
            $wpDate = (string)($wp->post_date ?? '');
            $pubAt  = null;
            if ($wpDate && $wpDate !== '0000-00-00 00:00:00') {
                try { $pubAt = Carbon::parse($wpDate); } catch (\Exception $e) {}
            }

            // 썸네일 URL (attachment url이 없으므로 콘텐츠 첫 이미지 추출)
            $thumbnail = null;
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $m)) {
                $thumbnail = $m[1];
            }

            Article::create([
                'title'        => $title,
                'slug'         => $slug,
                'category_id'  => $catId,
                'user_id'      => auth()->id(),
                'content'      => $content,
                'excerpt'      => $excerpt ?: null,
                'thumbnail'    => $thumbnail,
                'status'       => $status,
                'published_at' => $status === 'published' ? ($pubAt ?? now()) : $pubAt,
            ]);

            $imported++;
        }

        return back()->with('success', "워드프레스 가져오기 완료: {$imported}개 등록, {$skipped}개 건너뜀.")->with('tab', 'wordpress');
    }

    // ── 그누보드5 가져오기 ────────────────────────────────

    public function importGnuboard(Request $request)
    {
        $request->validate([
            'method'      => 'required|in:csv,db',
            'category_id' => 'nullable|exists:article_categories,id',
            'status'      => 'required|in:draft,pending,published',
        ]);

        return $request->input('method') === 'csv'
            ? $this->importGnuboardCsv($request)
            : $this->importGnuboardDb($request);
    }

    private function importGnuboardCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:51200',
        ]);

        $path   = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'CSV 파일을 열 수 없습니다.')->with('tab', 'gnuboard');
        }

        // UTF-8 BOM 제거
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return back()->with('error', 'CSV 헤더를 읽을 수 없습니다.')->with('tab', 'gnuboard');
        }
        $headers = array_map('trim', $headers);

        // 필수 컬럼 확인
        if (!in_array('wr_subject', $headers) || !in_array('wr_content', $headers)) {
            fclose($handle);
            return back()->with('error', 'wr_subject, wr_content 컬럼이 없습니다. 그누보드5 게시판 테이블을 내보냈는지 확인하세요.')->with('tab', 'gnuboard');
        }

        $catId  = $request->input('category_id');
        $status = $request->input('status', 'draft');
        [$imported, $skipped] = $this->processGnuboardRows(
            $this->csvIterator($handle, $headers),
            $catId, $status
        );

        fclose($handle);

        return back()->with('success', "그누보드5 CSV 가져오기 완료: {$imported}개 등록, {$skipped}개 건너뜀.")->with('tab', 'gnuboard');
    }

    private function importGnuboardDb(Request $request)
    {
        $request->validate([
            'db_host'   => 'required|max:255',
            'db_port'   => 'required|integer|min:1|max:65535',
            'db_name'   => 'required|max:100',
            'db_user'   => 'required|max:100',
            'db_pass'   => 'nullable|max:200',
            'db_table'  => 'required|max:100|regex:/^[a-zA-Z0-9_]+$/',
            'db_prefix' => 'nullable|max:20',
        ]);

        $host      = $request->input('db_host');
        $port      = $request->input('db_port', 3306);
        $dbName    = $request->input('db_name');
        $user      = $request->input('db_user');
        $pass      = $request->input('db_pass', '');
        $table     = $request->input('db_table');
        $prefix    = $request->input('db_prefix', 'g5_');
        $fullTable = $prefix . 'write_' . $table;

        try {
            $pdo = new \PDO(
                "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4",
                $user, $pass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 10]
            );
        } catch (\PDOException $e) {
            return back()->with('error', 'DB 연결 실패: ' . $e->getMessage())->with('tab', 'gnuboard');
        }

        try {
            $stmt = $pdo->query(
                "SELECT * FROM `{$fullTable}` WHERE wr_is_comment = 0 ORDER BY wr_datetime DESC"
            );
        } catch (\PDOException $e) {
            return back()->with('error', "테이블 조회 실패: {$e->getMessage()}")->with('tab', 'gnuboard');
        }

        $catId  = $request->input('category_id');
        $status = $request->input('status', 'draft');

        $rows = (function () use ($stmt) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) yield $row;
        })();

        [$imported, $skipped] = $this->processGnuboardRows($rows, $catId, $status);

        return back()->with('success', "그누보드5 DB 가져오기 완료: {$imported}개 등록, {$skipped}개 건너뜀.")->with('tab', 'gnuboard');
    }

    private function csvIterator($handle, array $headers): \Generator
    {
        while (($row = fgetcsv($handle)) !== false) {
            $data = [];
            foreach ($headers as $i => $col) {
                $data[$col] = $row[$i] ?? '';
            }
            yield $data;
        }
    }

    private function processGnuboardRows(iterable $rows, ?int $catId, string $status): array
    {
        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            // 댓글 제외
            if (isset($row['wr_is_comment']) && $row['wr_is_comment'] != 0) continue;

            $title   = trim($row['wr_subject'] ?? '');
            $content = trim($row['wr_content'] ?? '');

            if (empty($title) || empty($content)) { $skipped++; continue; }

            $slug  = Article::makeSlug($title);
            $pubAt = null;
            if (!empty($row['wr_datetime'])) {
                try { $pubAt = Carbon::parse($row['wr_datetime']); } catch (\Exception $e) {}
            }

            $article = Article::create([
                'title'        => $title,
                'slug'         => $slug,
                'category_id'  => $catId,
                'user_id'      => auth()->id(),
                'content'      => $content,
                'excerpt'      => null,
                'status'       => $status,
                'published_at' => $status === 'published' ? ($pubAt ?? now()) : $pubAt,
            ]);

            // view_count는 guarded라 별도 처리
            $vc = (int)($row['wr_hit'] ?? 0);
            if ($vc > 0) {
                $article->view_count = $vc;
                $article->save();
            }

            $imported++;
        }

        return [$imported, $skipped];
    }
}
