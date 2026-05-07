<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title><![CDATA[{{ $title }}]]></title>
        <link>{{ url('/') }}</link>
        <description><![CDATA[{{ $description }}]]></description>
        <language>ko</language>
        <atom:link href="{{ url('/feed') }}" rel="self" type="application/rss+xml"/>
        <lastBuildDate>{{ now()->toRfc1123String() }}</lastBuildDate>
        <generator>Laraboard CMS</generator>

        @foreach($articles as $article)
        <item>
            <title><![CDATA[{{ $article->title }}]]></title>
            <link>{{ url('/news/' . rawurlencode($article->slug)) }}</link>
            <guid isPermaLink="true">{{ url('/news/' . rawurlencode($article->slug)) }}</guid>
            <pubDate>{{ $article->published_at->toRfc1123String() }}</pubDate>
            @if($article->user)
            <dc:creator><![CDATA[{{ $article->user->name }}]]></dc:creator>
            @endif
            @if($article->category)
            <category><![CDATA[{{ $article->category->name }}]]></category>
            @endif
            @if($article->excerpt)
            <description><![CDATA[{{ $article->excerpt }}]]></description>
            @endif
            @if($includeContent && $article->content)
            <content:encoded><![CDATA[{!! $article->content !!}]]></content:encoded>
            @endif
            @if($article->thumbnail)
            <enclosure url="{{ $article->thumbnail }}" type="image/jpeg" length="0"/>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
