<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ url('/news') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>

    @foreach($categories as $cat)
    <url>
        <loc>{{ url('/news?category=' . $cat->slug) }}</loc>
        <lastmod>{{ $cat->updated_at->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    @foreach($articles as $article)
    <url>
        <loc>{{ url('/news/' . rawurlencode($article->slug)) }}</loc>
        <lastmod>{{ ($article->updated_at ?? $article->published_at)->toAtomString() }}</lastmod>
        <changefreq>{{ $freq }}</changefreq>
        <priority>{{ $priority }}</priority>
    </url>
    @endforeach

    @foreach($pages as $page)
    <url>
        <loc>{{ url('/page/' . $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

</urlset>
