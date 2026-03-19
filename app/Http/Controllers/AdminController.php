<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Board;
use App\Models\Post;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Setting;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // 대시보드
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'boards' => Board::count(),
            'posts' => Post::count(),
            'comments' => Comment::count(),
            'pages' => Page::count(),
            'total_views' => Post::sum('view_count'),
            'articles' => Article::count(),
            'articles_published' => Article::where('status', 'published')->count(),
        ];

        // 게시판별 통계
        $boardStats = Board::withCount('posts')
            ->withSum('posts', 'view_count')
            ->orderBy('order')
            ->get();

        // 최근 게시글
        $recentPosts = Post::with(['user', 'board'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // 최근 기사
        $recentArticles = Article::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // 최근 댓글
        $recentComments = Comment::with(['user', 'post'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 최근 가입 회원
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'boardStats', 'recentPosts', 'recentArticles', 'recentComments', 'recentUsers'
        ));
    }

    // 회원 관리
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    // 회원 추가 폼
    public function userCreate()
    {
        return view('admin.user-create');
    }

    // 회원 추가 처리
    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users|alpha_dash|max:50',
            'email'    => 'required|email|unique:users|max:200',
            'name'     => 'required|max:100',
            'password' => 'required|min:6',
            'role'     => 'required|in:subscriber,author,editor,admin',
        ], [
            'username.unique' => '이미 사용 중인 아이디입니다.',
            'email.unique'    => '이미 사용 중인 이메일입니다.',
        ]);

        User::create([
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'name'      => $validated['name'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.users')
            ->with('success', '회원이 추가되었습니다.');
    }

    // 회원 정보 수정 폼
    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user-edit', compact('user'));
    }

    // 회원 정보 업데이트
    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username'      => ['required', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'name'          => 'required|max:100',
            'password'      => 'nullable|min:6',
            'role'          => 'nullable|in:subscriber,author,editor,admin',
            'is_active'     => 'boolean',
            'bio'                => 'nullable|max:1000',
            'profile_image_file' => 'nullable|file|image|max:2048',
            'social_facebook'    => 'nullable|url|max:300',
            'social_x'           => 'nullable|url|max:300',
            'social_instagram'   => 'nullable|url|max:300',
            'social_linkedin'    => 'nullable|url|max:300',
            'social_website'     => 'nullable|url|max:300',
            'social_blog'        => 'nullable|url|max:300',
            'social_pixabay'     => 'nullable|url|max:300',
            'social_wikipedia'   => 'nullable|url|max:300',
            'social_email'       => 'nullable|email|max:200',
        ]);

        $user->username  = $validated['username'];
        $user->email     = $validated['email'];
        $user->name      = $validated['name'];
        $user->role      = $request->input('role', $user->role);
        $user->is_active = $request->has('is_active');
        $user->bio       = $validated['bio'] ?? '';
        $user->author_box_enabled = $request->has('author_box_enabled');

        $socialFields = ['social_facebook','social_x','social_instagram','social_linkedin',
                         'social_website','social_blog','social_pixabay','social_wikipedia','social_email'];
        foreach ($socialFields as $field) {
            $user->$field = $validated[$field] ?? null;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // 프로필 이미지 업로드
        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $path = 'profiles/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('uploads')->put($path, file_get_contents($file->getRealPath()));
            $user->profile_image = '/uploads/' . $path;
        } elseif ($request->input('clear_profile_image')) {
            $user->profile_image = null;
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', '회원 정보가 수정되었습니다.');
    }

    // 역할 변경
    public function userRoleUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', '자신의 역할은 변경할 수 없습니다.');
        }

        $request->validate(['role' => 'required|in:subscriber,author,editor,admin']);
        $user->role = $request->input('role');
        $user->save();

        return back()->with('success', '역할이 변경되었습니다.');
    }

    // 관리자 권한 토글 (하위 호환)
    public function userToggleAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->role = $user->isAdmin() ? 'subscriber' : 'admin';
        $user->save();

        return back()->with('success', '역할이 변경되었습니다.');
    }

    // 회원 활성화 토글
    public function userToggle($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', '회원 상태가 변경되었습니다.');
    }

    // 회원 삭제
    public function userDelete($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', '자기 자신은 삭제할 수 없습니다.');
        }

        $user->delete();
        return back()->with('success', '회원이 삭제되었습니다.');
    }

    // 사이트 설정
    public function settings()
    {
        $settings = [
            'logo_text'       => Setting::get('logo_text', ''),
            'logo_tagline'    => Setting::get('logo_tagline', ''),
            'logo_image'      => Setting::get('logo_image', ''),
            'favicon'         => Setting::get('favicon', ''),
            'admin_logo_image'=> Setting::get('admin_logo_image', ''),
            'admin_logo_text' => Setting::get('admin_logo_text', ''),
            'site_name' => Setting::get('site_name', 'Laraboard'),
            'site_description' => Setting::get('site_description', ''),
            'site_keywords' => Setting::get('site_keywords', ''),
            'meta_title' => Setting::get('meta_title', ''),
            'meta_description' => Setting::get('meta_description', ''),
            'meta_keywords' => Setting::get('meta_keywords', ''),
            'meta_author' => Setting::get('meta_author', ''),
            'meta_og_image' => Setting::get('meta_og_image', ''),
            'custom_head_script' => Setting::get('custom_head_script', ''),
            'custom_body_script' => Setting::get('custom_body_script', ''),
            'layout_skin' => Setting::get('layout_skin', 'basic'),
            'board_skin' => Setting::get('board_skin', 'basic'),
            'member_skin' => Setting::get('member_skin', 'default'),
            // 토론 설정
            'comments_enabled'            => Setting::get('comments_enabled', '1'),
            'comments_require_login'      => Setting::get('comments_require_login', '1'),
            'comments_moderation'         => Setting::get('comments_moderation', '0'),
            // 언론사 정보
            'press_masthead'              => Setting::get('press_masthead', ''),
            'press_registration_number'   => Setting::get('press_registration_number', ''),
            'press_publisher'             => Setting::get('press_publisher', ''),
            'press_editor'            => Setting::get('press_editor', ''),
            'press_address'           => Setting::get('press_address', ''),
            'press_postal_code'       => Setting::get('press_postal_code', ''),
            'press_fax'               => Setting::get('press_fax', ''),
            'press_phone'             => Setting::get('press_phone', ''),
            'press_email'             => Setting::get('press_email', ''),
            'press_youth_manager'     => Setting::get('press_youth_manager', ''),
            'press_privacy_manager'   => Setting::get('press_privacy_manager', ''),
            'press_grievance_manager' => Setting::get('press_grievance_manager', ''),
        ];

        $layoutSkins = $this->getAvailableSkins('layout');
        $boardSkins = $this->getAvailableSkins('board');
        $memberSkins = $this->getAvailableSkins('member');

        return view('admin.settings', compact('settings', 'layoutSkins', 'boardSkins', 'memberSkins'));
    }

    // 사이트 설정 업데이트
    public function settingsUpdate(Request $request)
    {
        $request->validate([
            'logo_image_file'       => 'nullable|file|image|max:2048',
            'favicon_file'          => 'nullable|file|mimes:ico,png,svg,gif|max:512',
            'admin_logo_image_file' => 'nullable|file|image|max:2048',
        ]);

        // 관리자 패널 로고 업로드
        if ($request->hasFile('admin_logo_image_file')) {
            $file = $request->file('admin_logo_image_file');
            $path = 'brand/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('uploads')->put($path, file_get_contents($file->getRealPath()));
            Setting::set('admin_logo_image', '/uploads/' . $path);
        } elseif ($request->input('clear_admin_logo_image')) {
            Setting::set('admin_logo_image', '');
        }

        // 로고 이미지 업로드
        if ($request->hasFile('logo_image_file')) {
            $file = $request->file('logo_image_file');
            $path = 'brand/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('uploads')->put($path, file_get_contents($file->getRealPath()));
            Setting::set('logo_image', '/uploads/' . $path);
        } elseif ($request->input('clear_logo_image')) {
            Setting::set('logo_image', '');
        }

        // 파비콘 업로드
        if ($request->hasFile('favicon_file')) {
            $file = $request->file('favicon_file');
            $path = 'brand/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('uploads')->put($path, file_get_contents($file->getRealPath()));
            Setting::set('favicon', '/uploads/' . $path);
        } elseif ($request->input('clear_favicon')) {
            Setting::set('favicon', '');
        }

        $validated = $request->validate([
            'logo_text'       => 'nullable|max:100',
            'logo_tagline'    => 'nullable|max:200',
            'admin_logo_text' => 'nullable|max:100',
            'site_name' => 'required|max:100',
            'site_description' => 'nullable|max:500',
            'site_keywords' => 'nullable|max:500',
            'meta_title' => 'nullable|max:100',
            'meta_description' => 'nullable|max:200',
            'meta_keywords' => 'nullable|max:200',
            'meta_author' => 'nullable|max:100',
            'meta_og_image' => 'nullable|url|max:500',
            'custom_head_script' => 'nullable',
            'custom_body_script' => 'nullable',
            'layout_skin' => 'required|max:50',
            'board_skin' => 'required|max:50',
            'member_skin' => 'required|max:50',
            // 언론사 정보
            // 토론 설정
            'comments_enabled'            => 'nullable|in:0,1',
            'comments_require_login'      => 'nullable|in:0,1',
            'comments_moderation'         => 'nullable|in:0,1',
            // 언론사 정보
            'press_masthead'              => 'nullable|max:100',
            'press_registration_number'   => 'nullable|max:50',
            'press_publisher'             => 'nullable|max:50',
            'press_editor'            => 'nullable|max:50',
            'press_address'           => 'nullable|max:200',
            'press_postal_code'       => 'nullable|max:10',
            'press_fax'               => 'nullable|max:30',
            'press_phone'             => 'nullable|max:30',
            'press_email'             => 'nullable|email|max:100',
            'press_youth_manager'     => 'nullable|max:50',
            'press_privacy_manager'   => 'nullable|max:50',
            'press_grievance_manager' => 'nullable|max:50',
        ]);

        // 체크박스 항목: 미전송 시 0으로 처리
        $checkboxKeys = ['comments_enabled', 'comments_require_login', 'comments_moderation'];
        foreach ($checkboxKeys as $k) {
            $validated[$k] = $request->has($k) ? '1' : '0';
        }

        foreach ($validated as $key => $value) {
            // 파일 업로드로 이미 처리된 항목 제외
            if (in_array($key, ['logo_image', 'favicon', 'admin_logo_image'])) continue;
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', '설정이 저장되었습니다.');
    }

    private function getAvailableSkins($type)
    {
        $skinPath = resource_path("views/skin/{$type}");
        
        if (!is_dir($skinPath)) {
            return ['basic' => 'Basic'];
        }

        $skins = [];
        $directories = glob($skinPath . '/*', GLOB_ONLYDIR);

        foreach ($directories as $dir) {
            $skinName = basename($dir);
            $skins[$skinName] = ucfirst(str_replace(['-', '_'], ' ', $skinName));
        }

        return $skins ?: ['basic' => 'Basic'];
    }

    // 페이지 관리
    public function pages()
    {
        $pages = Page::orderBy('order')->get();
        return view('admin.pages', compact('pages'));
    }

    public function pageCreate()
    {
        return view('admin.page-form');
    }

    public function pageStore(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:pages|alpha_dash|max:100',
            'title' => 'required|max:200',
            'content' => 'required',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        Page::create($validated);

        return redirect()->route('admin.pages')
            ->with('success', '페이지가 생성되었습니다.');
    }

    public function pageEdit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.page-form', compact('page'));
    }

    public function pageUpdate(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('pages')->ignore($page->id)],
            'title' => 'required|max:200',
            'content' => 'required',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        $page->update($validated);

        return redirect()->route('admin.pages')
            ->with('success', '페이지가 수정되었습니다.');
    }

    public function pageDelete($id)
    {
        Page::findOrFail($id)->delete();
        return back()->with('success', '페이지가 삭제되었습니다.');
    }

    // 내 프로필
    public function myProfile()
    {
        $user = auth()->user();
        return view('admin.my-profile', compact('user'));
    }

    public function myProfileUpdate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'               => 'required|max:100',
            'password'           => 'nullable|min:6|confirmed',
            'bio'                => 'nullable|max:1000',
            'profile_image_file' => 'nullable|file|image|max:2048',
            'social_facebook'    => 'nullable|url|max:300',
            'social_x'           => 'nullable|url|max:300',
            'social_instagram'   => 'nullable|url|max:300',
            'social_linkedin'    => 'nullable|url|max:300',
            'social_website'     => 'nullable|url|max:300',
            'social_blog'        => 'nullable|url|max:300',
            'social_pixabay'     => 'nullable|url|max:300',
            'social_wikipedia'   => 'nullable|url|max:300',
            'social_email'       => 'nullable|email|max:200',
        ]);

        $user->name = $validated['name'];
        $user->bio  = $validated['bio'] ?? '';
        $user->author_box_enabled = $request->has('author_box_enabled');

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $socialFields = ['social_facebook','social_x','social_instagram','social_linkedin',
                         'social_website','social_blog','social_pixabay','social_wikipedia','social_email'];
        foreach ($socialFields as $field) {
            $user->$field = $validated[$field] ?? null;
        }

        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $path = 'profiles/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('uploads')->put($path, file_get_contents($file->getRealPath()));
            $user->profile_image = '/uploads/' . $path;
        } elseif ($request->input('clear_profile_image')) {
            $user->profile_image = null;
        }

        $user->save();

        return back()->with('success', '프로필이 저장되었습니다.');
    }

    // 게시판 관리
    public function boards()
    {
        $boards = Board::orderBy('order')->get();
        return view('admin.boards', compact('boards'));
    }

    public function boardCreate()
    {
        return view('admin.board-form');
    }

    public function boardStore(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|unique:boards|alpha_dash|max:50',
            'board_name' => 'required|max:100',
            'skin' => 'required',
            'posts_per_page' => 'required|integer|min:5|max:100',
        ]);

        Board::create($validated);

        return redirect()->route('admin.boards')
            ->with('success', '게시판이 생성되었습니다.');
    }

    public function boardEdit($id)
    {
        $board = Board::findOrFail($id);
        return view('admin.board-form', compact('board'));
    }

    public function boardUpdate(Request $request, $id)
    {
        $board = Board::findOrFail($id);

        $validated = $request->validate([
            'board_name' => 'required|max:100',
            'skin' => 'required',
            'posts_per_page' => 'required|integer|min:5|max:100',
        ]);

        $board->update($validated);

        return redirect()->route('admin.boards')
            ->with('success', '게시판이 수정되었습니다.');
    }

    public function boardDelete($id)
    {
        Board::findOrFail($id)->delete();
        return back()->with('success', '게시판이 삭제되었습니다.');
    }
}
