<?php

namespace App\Http\Controllers;

use App\Models\TopBanner;
use Illuminate\Http\Request;

class AdminTopBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'role:admin']);
    }

    public function index()
    {
        $banners = TopBanner::orderBy('order')->orderBy('id')->get();
        return view('admin.top-banners', compact('banners'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text'         => 'required|max:500',
            'link_url'     => 'nullable|max:500',
            'text_color'   => 'required|max:20',
            'bg_color'     => 'required|max:20',
            'font_size'    => 'required|integer|min:10|max:32',
            'font_weight'  => 'required|in:400,600,700,800',
            'start_at'     => 'nullable|date',
            'end_at'       => 'nullable|date|after_or_equal:start_at',
            'reshow_hours' => 'required|integer|min:0|max:8760',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order']     = TopBanner::max('order') + 1;

        TopBanner::create($data);

        return back()->with('success', '탑배너가 추가되었습니다.');
    }

    public function update(Request $request, $id)
    {
        $banner = TopBanner::findOrFail($id);

        $data = $request->validate([
            'text'         => 'required|max:500',
            'link_url'     => 'nullable|max:500',
            'text_color'   => 'required|max:20',
            'bg_color'     => 'required|max:20',
            'font_size'    => 'required|integer|min:10|max:32',
            'font_weight'  => 'required|in:400,600,700,800',
            'start_at'     => 'nullable|date',
            'end_at'       => 'nullable|date|after_or_equal:start_at',
            'reshow_hours' => 'required|integer|min:0|max:8760',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $banner->update($data);

        return back()->with('success', '탑배너가 수정되었습니다.');
    }

    public function destroy($id)
    {
        TopBanner::findOrFail($id)->delete();
        return back()->with('success', '탑배너가 삭제되었습니다.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('ids', []) as $order => $id) {
            TopBanner::where('id', $id)->update(['order' => $order + 1]);
        }
        return response()->json(['ok' => true]);
    }
}
