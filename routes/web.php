<?php
// routes/web.php

use App\Http\Controllers\BbsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminSeoController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminThemeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AdminToolsController;
use App\Http\Controllers\AdminTopBannerController;
use App\Http\Controllers\AdminArticleCommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\AdminPluginController;
use App\Http\Controllers\AdminStatisticsController;
use App\Http\Controllers\AdminMailController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

// 랜딩 페이지
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// 데모 홈 (레이아웃 스킨에 따라 동적 뷰)
Route::get('/demo', function () {
    try {
        $layoutSkin = Setting::get('layout_skin', 'basic');
        $homeView = "skin.layout.{$layoutSkin}.home";

        if (!view()->exists($homeView)) {
            $homeView = 'skin.layout.basic.home';
        }

        return view($homeView);
    } catch (\Exception $e) {
        return response('
            <!DOCTYPE html>
            <html>
            <head><meta charset="utf-8"><title>Error</title></head>
            <body>
                <h1>에러 발생</h1>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
            </body>
            </html>
        ', 500);
    }
})->name('home');

// 인증
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');

// 이메일 인증
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/')->with('success', '이메일 인증이 완료되었습니다!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 사이트맵 & RSS 피드 (공개)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/feed', [FeedController::class, 'index'])->name('feed');

// 기사 (뉴스)
Route::get('/search', [ArticleController::class, 'search'])->name('news.search');
Route::get('/news', [ArticleController::class, 'index'])->name('news.index');
Route::get('/journalist/{username}', [ArticleController::class, 'author'])->name('journalist.show');
Route::get('/news/{slug}', [ArticleController::class, 'show'])->name('news.show')->where('slug', '[^/]+');
// 기사 댓글 (로그인 필요, 구독자 이상)
Route::post('/news/{slug}/comments', [ArticleController::class, 'commentStore'])->middleware('auth')->name('news.comment.store');
Route::delete('/news/{slug}/comments/{commentId}', [ArticleController::class, 'commentDelete'])->middleware('auth')->name('news.comment.delete');

// 미디어 업로드 (로그인 필요)
Route::post('/upload/image', [MediaController::class, 'upload'])->middleware('auth')->name('media.upload');

// 프로필 (로그인 필요, 모든 회원)
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');

// 게시판
Route::prefix('bbs/{boardId}')->name('bbs.')->middleware('auth')->group(function () {
    Route::get('/', [BbsController::class, 'index'])->name('index');
    // 글쓰기: 구독자 차단 (author 이상만)
    Route::get('/write', [BbsController::class, 'create'])->name('create')->middleware('role:author');
    Route::post('/write', [BbsController::class, 'store'])->name('store')->middleware('role:author');
    Route::get('/{postId}', [BbsController::class, 'show'])->name('show');
    Route::get('/{postId}/edit', [BbsController::class, 'edit'])->name('edit');
    Route::put('/{postId}', [BbsController::class, 'update'])->name('update');
    Route::delete('/{postId}', [BbsController::class, 'destroy'])->name('delete');
    Route::post('/{postId}/comment', [BbsController::class, 'storeComment'])->name('comment.store');
    Route::delete('/{postId}/comment/{commentId}', [BbsController::class, 'deleteComment'])->name('comment.delete');
});

// 관리자 패널 (auth + admin 미들웨어 — admin은 canAccessAdmin 기반)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // 내 프로필 (모든 관리 패널 접근 가능 회원)
    Route::get('/my-profile', [AdminController::class, 'myProfile'])->name('my-profile');
    Route::put('/my-profile', [AdminController::class, 'myProfileUpdate'])->name('my-profile.update');

    // 미디어 피커 AJAX — 기사 작성자도 사용 가능 (role:editor 밖)
    Route::get('/media-picker', [MediaController::class, 'pickerData'])->name('media.picker');

    // ── author 이상 접근 가능 ──────────────────────────────
    // 기사 관리 (작성자: 자기 기사만, 편집자/관리자: 전체)
    Route::get('/articles', [AdminArticleController::class, 'articles'])->name('articles');
    Route::get('/articles/create', [AdminArticleController::class, 'articleCreate'])->name('article.create');
    Route::post('/articles', [AdminArticleController::class, 'articleStore'])->name('article.store');
    Route::get('/articles/{id}/edit', [AdminArticleController::class, 'articleEdit'])->name('article.edit');
    Route::put('/articles/{id}', [AdminArticleController::class, 'articleUpdate'])->name('article.update');
    Route::delete('/articles/{id}', [AdminArticleController::class, 'articleDelete'])->name('article.delete');

    // ── editor 이상 접근 가능 ─────────────────────────────
    Route::middleware('role:editor')->group(function () {
        // 기사 댓글 관리
        Route::get('/article-comments', [AdminArticleCommentController::class, 'index'])->name('article-comments');
        Route::post('/article-comments/bulk', [AdminArticleCommentController::class, 'bulk'])->name('article-comment.bulk');
        Route::post('/article-comments/{id}/approve', [AdminArticleCommentController::class, 'approve'])->name('article-comment.approve');
        Route::put('/article-comments/{id}', [AdminArticleCommentController::class, 'update'])->name('article-comment.update');
        Route::delete('/article-comments/{id}', [AdminArticleCommentController::class, 'destroy'])->name('article-comment.delete');
        Route::post('/article-comments/{id}/restore', [AdminArticleCommentController::class, 'restore'])->name('article-comment.restore');
        Route::delete('/article-comments/{id}/force', [AdminArticleCommentController::class, 'forceDestroy'])->name('article-comment.force-delete');

        // 기사 일괄 처리
        Route::post('/articles/bulk', [AdminArticleController::class, 'articleBulk'])->name('article.bulk');

        // 기사 승인·복원·영구삭제
        Route::post('/articles/{id}/status', [AdminArticleController::class, 'articleStatus'])->name('article.status');
        Route::post('/articles/{id}/restore', [AdminArticleController::class, 'articleRestore'])->name('article.restore');
        Route::delete('/articles/{id}/force', [AdminArticleController::class, 'articleForceDelete'])->name('article.force-delete');
        Route::post('/articles/empty-trash', [AdminArticleController::class, 'articleEmptyTrash'])->name('article.empty-trash');
        Route::get('/articles/export', [AdminArticleController::class, 'export'])->name('article.export');
        Route::post('/articles/import', [AdminArticleController::class, 'import'])->name('article.import');

        // 기사 카테고리 관리
        Route::get('/categories', [AdminArticleController::class, 'categories'])->name('categories');
        Route::get('/categories/create', [AdminArticleController::class, 'categoryCreate'])->name('category.create');
        Route::post('/categories', [AdminArticleController::class, 'categoryStore'])->name('category.store');
        Route::get('/categories/{id}/edit', [AdminArticleController::class, 'categoryEdit'])->name('category.edit');
        Route::put('/categories/{id}', [AdminArticleController::class, 'categoryUpdate'])->name('category.update');
        Route::delete('/categories/{id}', [AdminArticleController::class, 'categoryDelete'])->name('category.delete');

        // 미디어 라이브러리
        Route::get('/media', [MediaController::class, 'index'])->name('media');
        Route::get('/media/{id}', [MediaController::class, 'show'])->name('media.show');
        Route::put('/media/{id}', [MediaController::class, 'update'])->name('media.update');
        Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.delete');
    });

    // ── admin 전용 ────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        // 회원 관리
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'userCreate'])->name('user.create');
        Route::post('/users', [AdminController::class, 'userStore'])->name('user.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'userEdit'])->name('user.edit');
        Route::put('/users/{id}', [AdminController::class, 'userUpdate'])->name('user.update');
        Route::post('/users/{id}/toggle', [AdminController::class, 'userToggle'])->name('user.toggle');
        Route::post('/users/{id}/toggle-admin', [AdminController::class, 'userToggleAdmin'])->name('user.toggle-admin');
        Route::delete('/users/{id}', [AdminController::class, 'userDelete'])->name('user.delete');
        Route::post('/users/{id}/role', [AdminController::class, 'userRoleUpdate'])->name('user.role');

        // 사이트 설정
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('settings.update');

        // 게시판 관리
        Route::get('/boards', [AdminController::class, 'boards'])->name('boards');
        Route::get('/boards/create', [AdminController::class, 'boardCreate'])->name('board.create');
        Route::post('/boards', [AdminController::class, 'boardStore'])->name('board.store');
        Route::get('/boards/{id}/edit', [AdminController::class, 'boardEdit'])->name('board.edit');
        Route::put('/boards/{id}', [AdminController::class, 'boardUpdate'])->name('board.update');
        Route::delete('/boards/{id}', [AdminController::class, 'boardDelete'])->name('board.delete');

        // 도구
        Route::get('/tools', [AdminToolsController::class, 'index'])->name('tools');
        Route::get('/tools/export', [AdminToolsController::class, 'exportJson'])->name('tools.export');
        Route::post('/tools/import/json', [AdminToolsController::class, 'importJson'])->name('tools.import.json');
        Route::post('/tools/import/rss', [AdminToolsController::class, 'importRss'])->name('tools.import.rss');
        Route::post('/tools/import/wordpress', [AdminToolsController::class, 'importWordpress'])->name('tools.import.wordpress');
        Route::post('/tools/import/gnuboard', [AdminToolsController::class, 'importGnuboard'])->name('tools.import.gnuboard');

        // 테마 설정
        Route::get('/theme', [AdminThemeController::class, 'index'])->name('theme');
        Route::post('/theme/menu', [AdminThemeController::class, 'menuStore'])->name('theme.menu.store');
        Route::put('/theme/menu/{id}', [AdminThemeController::class, 'menuUpdate'])->name('theme.menu.update');
        Route::delete('/theme/menu/{id}', [AdminThemeController::class, 'menuDelete'])->name('theme.menu.delete');
        Route::post('/theme/menu/reorder', [AdminThemeController::class, 'menuReorder'])->name('theme.menu.reorder');
        Route::post('/theme/footer-menu', [AdminThemeController::class, 'footerMenuStore'])->name('theme.footer-menu.store');
        Route::put('/theme/footer-menu/{id}', [AdminThemeController::class, 'footerMenuUpdate'])->name('theme.footer-menu.update');
        Route::delete('/theme/footer-menu/{id}', [AdminThemeController::class, 'footerMenuDelete'])->name('theme.footer-menu.delete');
        Route::post('/theme/colors', [AdminThemeController::class, 'colorUpdate'])->name('theme.color.update');
        Route::post('/theme/widgets', [AdminThemeController::class, 'widgetUpdate'])->name('theme.widget.update');

        // 페이지 관리
        Route::get('/pages', [AdminController::class, 'pages'])->name('pages');
        Route::get('/pages/create', [AdminController::class, 'pageCreate'])->name('page.create');
        Route::post('/pages', [AdminController::class, 'pageStore'])->name('page.store');
        Route::get('/pages/{id}/edit', [AdminController::class, 'pageEdit'])->name('page.edit');
        Route::put('/pages/{id}', [AdminController::class, 'pageUpdate'])->name('page.update');
        Route::delete('/pages/{id}', [AdminController::class, 'pageDelete'])->name('page.delete');

        // 탑배너 관리
        Route::get('/top-banners', [AdminTopBannerController::class, 'index'])->name('top-banners');
        Route::post('/top-banners', [AdminTopBannerController::class, 'store'])->name('top-banner.store');
        Route::put('/top-banners/{id}', [AdminTopBannerController::class, 'update'])->name('top-banner.update');
        Route::delete('/top-banners/{id}', [AdminTopBannerController::class, 'destroy'])->name('top-banner.delete');
        Route::post('/top-banners/reorder', [AdminTopBannerController::class, 'reorder'])->name('top-banner.reorder');

        // SEO 설정
        Route::get('/seo', [AdminSeoController::class, 'index'])->name('seo');
        Route::post('/seo', [AdminSeoController::class, 'update'])->name('seo.update');
        Route::post('/seo/ping', [AdminSeoController::class, 'ping'])->name('seo.ping');

        // 플러그인 관리
        Route::get('/plugins', [AdminPluginController::class, 'index'])->name('plugins');
        Route::post('/plugins/{name}/activate', [AdminPluginController::class, 'activate'])->name('plugin.activate');
        Route::post('/plugins/{name}/deactivate', [AdminPluginController::class, 'deactivate'])->name('plugin.deactivate');
        Route::delete('/plugins/{name}', [AdminPluginController::class, 'destroy'])->name('plugin.delete');
        Route::get('/plugins/{name}/settings', [AdminPluginController::class, 'settings'])->name('plugin.settings');
        Route::post('/plugins/{name}/settings', [AdminPluginController::class, 'settingsUpdate'])->name('plugin.settings.update');

        // 통계
        Route::get('/statistics', [AdminStatisticsController::class, 'index'])->name('statistics');

        // 메일 관리
        Route::get('/mail', [AdminMailController::class, 'index'])->name('mail');
        Route::post('/mail/test', [AdminMailController::class, 'test'])->name('mail.test');
        Route::post('/mail/send', [AdminMailController::class, 'send'])->name('mail.send');
        Route::get('/mail/diagnose', [AdminMailController::class, 'diagnose'])->name('mail.diagnose');
    });
});

// 디버깅 라우트
Route::get('/debug-info', function () {
    return response()->json([
        'status' => 'ok',
        'laravel_version' => app()->version(),
        'php_version' => phpversion(),
        'environment' => app()->environment(),
        'routes_loaded' => count(Route::getRoutes()),
        'layout_skin' => Setting::get('layout_skin', 'basic'),
    ]);
});

// 커스텀 페이지 (맨 마지막에 위치)
Route::get('/page/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();
    
    return view('page.show', compact('page'));
})->name('page.show');
