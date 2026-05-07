<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Page;
use App\Models\Setting;

class SitemapController extends Controller
{
    public function index()
    {
        if (Setting::get('sitemap_enabled', '1') !== '1') {
            abort(404);
        }

        $articles = Article::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->select('slug', 'updated_at', 'published_at')
            ->get();

        $categories = ArticleCategory::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();

        $pages = Page::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();

        $priority = Setting::get('sitemap_articles_priority', '0.8');
        $freq     = Setting::get('sitemap_articles_freq', 'daily');

        return response()
            ->view('sitemap', compact('articles', 'categories', 'pages', 'priority', 'freq'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
