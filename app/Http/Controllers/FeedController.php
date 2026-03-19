<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Setting;

class FeedController extends Controller
{
    public function index()
    {
        if (Setting::get('rss_enabled', '1') !== '1') {
            abort(404);
        }

        $limit          = max(1, (int) Setting::get('rss_limit', '20'));
        $includeContent = Setting::get('rss_include_content', '0') === '1';

        $articles = Article::with(['category', 'user'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        $title       = Setting::get('rss_title') ?: Setting::get('site_name', 'Laraboard');
        $description = Setting::get('rss_description') ?: Setting::get('site_description', '');

        return response()
            ->view('feed', compact('articles', 'title', 'description', 'includeContent'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
