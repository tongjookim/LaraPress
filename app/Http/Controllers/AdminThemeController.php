<?php

namespace App\Http\Controllers;

use App\Models\NavMenu;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminThemeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // ── 색상 기본값 ───────────────────────────────────────────
    public static array $colorDefaults = [
        'theme_primary'      => '#1a6fb5',
        'theme_accent'       => '#e8524a',
        'theme_topbar_bg'    => '#e8524a',
        'theme_topbar_text'  => '#ffffff',
        'theme_nav_bg'       => '#e8f4fd',
        'theme_nav_text'     => '#1e3a5f',
        'theme_site_bg'      => '#f5f5f5',
        'theme_text'         => '#1a1a1a',
    ];

    // ── 테마 설정 메인 ────────────────────────────────────────
    public function index()
    {
        $menus        = NavMenu::where('location', 'header')->orderBy('order')->get();
        $footerMenus  = NavMenu::where('location', 'footer')->orderBy('group')->orderBy('order')->get();

        $mainWidgets    = json_decode(Setting::get('home_main_widgets',    '["hero_articles","category_articles","board_sections","stats"]'), true) ?? [];
        $sidebarWidgets = json_decode(Setting::get('home_sidebar_widgets', '["login","notice","popular_articles","boards"]'), true) ?? [];

        $colors = [];
        foreach (self::$colorDefaults as $key => $default) {
            $colors[$key] = Setting::get($key, $default);
        }

        return view('admin.theme', compact('menus', 'footerMenus', 'mainWidgets', 'sidebarWidgets', 'colors'));
    }

    // ── 메뉴 추가 ─────────────────────────────────────────────
    public function menuStore(Request $request)
    {
        $data = $request->validate([
            'label'  => 'required|max:60',
            'url'    => 'required|max:300',
            'target' => 'in:_self,_blank',
        ]);

        $data['order']     = NavMenu::where('location', 'header')->max('order') + 1;
        $data['is_active'] = true;
        $data['target']    = $data['target'] ?? '_self';
        $data['location']  = 'header';

        NavMenu::create($data);

        return back()->with('success', '메뉴가 추가되었습니다.');
    }

    // ── 메뉴 수정 ─────────────────────────────────────────────
    public function menuUpdate(Request $request, $id)
    {
        $menu = NavMenu::findOrFail($id);

        $data = $request->validate([
            'label'     => 'required|max:60',
            'url'       => 'required|max:300',
            'target'    => 'in:_self,_blank',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['target']    = $data['target'] ?? '_self';

        $menu->update($data);

        return back()->with('success', '메뉴가 수정되었습니다.');
    }

    // ── 메뉴 삭제 ─────────────────────────────────────────────
    public function menuDelete($id)
    {
        NavMenu::findOrFail($id)->delete();

        return back()->with('success', '메뉴가 삭제되었습니다.');
    }

    // ── 메뉴 순서 저장 (AJAX) ─────────────────────────────────
    public function menuReorder(Request $request)
    {
        $ids = $request->input('ids', []);

        foreach ($ids as $order => $id) {
            NavMenu::where('id', $id)->update(['order' => $order + 1]);
        }

        return response()->json(['ok' => true]);
    }

    // ── 푸터 메뉴 추가 ────────────────────────────────────────
    public function footerMenuStore(Request $request)
    {
        $data = $request->validate([
            'label'  => 'required|max:60',
            'url'    => 'required|max:300',
            'group'  => 'required|max:60',
            'target' => 'in:_self,_blank',
        ]);

        $data['order']     = NavMenu::where('location', 'footer')->where('group', $data['group'])->max('order') + 1;
        $data['is_active'] = true;
        $data['target']    = $data['target'] ?? '_self';
        $data['location']  = 'footer';

        NavMenu::create($data);

        return back()->with('success', '푸터 메뉴가 추가되었습니다.');
    }

    // ── 푸터 메뉴 수정 ────────────────────────────────────────
    public function footerMenuUpdate(Request $request, $id)
    {
        $menu = NavMenu::findOrFail($id);

        $data = $request->validate([
            'label'     => 'required|max:60',
            'url'       => 'required|max:300',
            'group'     => 'required|max:60',
            'target'    => 'in:_self,_blank',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['target']    = $data['target'] ?? '_self';

        $menu->update($data);

        return back()->with('success', '푸터 메뉴가 수정되었습니다.');
    }

    // ── 푸터 메뉴 삭제 ────────────────────────────────────────
    public function footerMenuDelete($id)
    {
        NavMenu::findOrFail($id)->delete();

        return back()->with('success', '푸터 메뉴가 삭제되었습니다.');
    }

    // ── 색상 설정 저장 ────────────────────────────────────────
    public function colorUpdate(Request $request)
    {
        $keys = array_keys(self::$colorDefaults);
        $rules = array_fill_keys($keys, 'required|max:20');
        $data  = $request->validate($rules);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', '색상 설정이 저장되었습니다.');
    }

    // ── 위젯 설정 저장 ────────────────────────────────────────
    public function widgetUpdate(Request $request)
    {
        $mainWidgets    = array_values(array_filter($request->input('main_widgets',    [])));
        $sidebarWidgets = array_values(array_filter($request->input('sidebar_widgets', [])));

        Setting::set('home_main_widgets',    json_encode($mainWidgets));
        Setting::set('home_sidebar_widgets', json_encode($sidebarWidgets));

        return back()->with('success', '위젯 설정이 저장되었습니다.');
    }
}
