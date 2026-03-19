<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSeoController extends Controller
{
    public function index()
    {
        $settings = [
            'meta_title'        => Setting::get('meta_title', ''),
            'meta_description'  => Setting::get('meta_description', ''),
            'meta_keywords'     => Setting::get('meta_keywords', ''),
            'meta_author'       => Setting::get('meta_author', ''),
            'meta_og_image'     => Setting::get('meta_og_image', ''),
            'meta_og_type'      => Setting::get('meta_og_type', 'website'),
            'meta_twitter_card' => Setting::get('meta_twitter_card', 'summary_large_image'),
            'sitemap_enabled'          => Setting::get('sitemap_enabled', '1'),
            'sitemap_articles_priority'=> Setting::get('sitemap_articles_priority', '0.8'),
            'sitemap_articles_freq'    => Setting::get('sitemap_articles_freq', 'daily'),
            'rss_enabled'         => Setting::get('rss_enabled', '1'),
            'rss_title'           => Setting::get('rss_title', ''),
            'rss_description'     => Setting::get('rss_description', ''),
            'rss_limit'           => Setting::get('rss_limit', '20'),
            'rss_include_content' => Setting::get('rss_include_content', '0'),
        ];

        return view('admin.seo', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'meta_title'        => 'nullable|max:100',
            'meta_description'  => 'nullable|max:300',
            'meta_keywords'     => 'nullable|max:200',
            'meta_author'       => 'nullable|max:100',
            'meta_og_image'     => 'nullable|url|max:500',
            'meta_og_type'      => 'nullable|max:50',
            'meta_twitter_card' => 'nullable|max:50',
            'sitemap_enabled'           => 'nullable',
            'sitemap_articles_priority' => 'nullable|numeric|between:0,1',
            'sitemap_articles_freq'     => 'nullable|in:always,hourly,daily,weekly,monthly,yearly,never',
            'rss_enabled'         => 'nullable',
            'rss_title'           => 'nullable|max:200',
            'rss_description'     => 'nullable|max:500',
            'rss_limit'           => 'nullable|integer|between:1,100',
            'rss_include_content' => 'nullable',
        ]);

        foreach (['sitemap_enabled', 'rss_enabled', 'rss_include_content'] as $k) {
            $validated[$k] = $request->has($k) ? '1' : '0';
        }

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'SEO 설정이 저장되었습니다.');
    }

    public function ping(Request $request)
    {
        $engine = $request->input('engine', 'google');
        $sitemapUrl = url('/sitemap.xml');

        $pingUrls = [
            'google' => 'https://www.google.com/ping?sitemap=' . urlencode($sitemapUrl),
            'bing'   => 'https://www.bing.com/ping?sitemap='   . urlencode($sitemapUrl),
        ];

        if (!isset($pingUrls[$engine])) {
            return response()->json(['success' => false, 'message' => '지원하지 않는 검색엔진입니다.'], 400);
        }

        try {
            $ctx = stream_context_create(['http' => ['timeout' => 5]]);
            file_get_contents($pingUrls[$engine], false, $ctx);
            return response()->json(['success' => true, 'message' => strtoupper($engine) . ' 핑 전송 완료!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => '핑 전송 실패: ' . $e->getMessage()]);
        }
    }
}
