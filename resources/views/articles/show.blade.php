@php
    try {
        $layoutSkin = \App\Models\Setting::get('layout_skin', 'basic');
        if (!view()->exists("skin.layout.{$layoutSkin}.main")) $layoutSkin = 'basic';
    } catch (\Exception $e) { $layoutSkin = 'basic'; }
    $isNyt = $layoutSkin === 'newyorktimes-style';
@endphp
@extends("skin.layout.{$layoutSkin}.main")

@section('title', ' — ' . $article->title)
@section('head-meta-override')@endsection

@push('head-meta')
@php
    $_artTitle = $article->meta_title ?: $article->title;
    $_artDesc  = $article->meta_description ?: ($article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 160));
    $_artImg   = $article->og_image ?: $article->thumbnail ?: \App\Models\Setting::get('meta_og_image','');
    $_siteName = \App\Models\Setting::get('site_name','Laraboard');
@endphp
<meta name="description" content="{{ $_artDesc }}">
@if($article->meta_keywords || \App\Models\Setting::get('meta_keywords'))
<meta name="keywords" content="{{ $article->meta_keywords ?: \App\Models\Setting::get('meta_keywords') }}">
@endif
@if(\App\Models\Setting::get('meta_author') || ($article->user->name ?? ''))
<meta name="author" content="{{ $article->user->name ?? \App\Models\Setting::get('meta_author') }}">
@endif
<meta property="og:type"        content="article">
<meta property="og:site_name"   content="{{ $_siteName }}">
<meta property="og:title"       content="{{ $_artTitle }}">
<meta property="og:description" content="{{ $_artDesc }}">
<meta property="og:url"         content="{{ url('/news/' . $article->slug) }}">
@if($_artImg)<meta property="og:image" content="{{ $_artImg }}">@endif
@if($article->published_at)<meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">@endif
@if($article->updated_at)<meta property="article:modified_time"  content="{{ $article->updated_at->toIso8601String() }}">@endif
@if($article->category)<meta property="article:section" content="{{ $article->category->name }}">@endif
<meta name="twitter:card"        content="{{ \App\Models\Setting::get('meta_twitter_card','summary_large_image') }}">
<meta name="twitter:title"       content="{{ $_artTitle }}">
<meta name="twitter:description" content="{{ $_artDesc }}">
@if($_artImg)<meta name="twitter:image" content="{{ $_artImg }}">@endif
@if(\App\Models\Setting::get('rss_enabled','1') === '1')
<link rel="alternate" type="application/rss+xml" title="{{ $_siteName }}" href="{{ url('/feed') }}">
@endif
@endpush

@push('skin-css')
@if(!$isNyt)
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
@endif
<style>
@if(!$isNyt)
/* ═══════════════════════════════════════════════════════
   MAGAZINE ARTICLE — Global Style
   Base accent: #AACDDC
═══════════════════════════════════════════════════════ */
:root {
    --m-accent:       #AACDDC;
    --m-accent-mid:   #6AAEC5;
    --m-accent-dark:  #2C7A96;
    --m-accent-deep:  #1A5266;
    --m-accent-bg:    #EEF7FA;
    --m-accent-light: #F4FAFC;
    --m-text:         #1C2830;
    --m-text-sub:     #5E7880;
    --m-border:       #CCE4ED;
    --m-rule:         #AACDDC;
    --m-white:        #ffffff;
}

/* ── 페이지 배경 ── */
.mag-page-wrapper {
    background: #F2F7F9;
    margin: -2rem -1rem 0;
    padding: 0 1rem;
}
@media (min-width: 640px) {
    .mag-page-wrapper { margin: -2rem -1.5rem 0; padding: 0 1.5rem; }
}
@media (min-width: 1024px) {
    .mag-page-wrapper { margin: -2rem -2rem 0; padding: 0 2rem; }
}
@endif
@if($isNyt)
/* ── NYT Article Page Styles ── */
.nyt-article-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 40px;
    padding-top: 32px;
    padding-bottom: 48px;
}
@media (max-width: 1024px) {
    .nyt-article-wrap { grid-template-columns: 1fr; gap: 24px; }
    .nyt-art-sidebar { display: none; } /* 태블릿: 사이드바 숨김 */
}
@media (max-width: 768px) {
    .nyt-article-wrap { padding: 0 14px; padding-top: 20px; padding-bottom: 32px; }
    .nyt-article-title { font-size: 1.6rem; }
    .nyt-article-body { font-size: 16px; line-height: 1.8; }
    .nyt-article-byline { flex-direction: column; gap: 4px; }
    .nyt-article-byline .nyt-views { margin-left: 0; }
    .nyt-author-box { flex-direction: column; }
    .nyt-comments { padding: 0 14px 32px; }
    .nyt-related-grid { grid-template-columns: 1fr 1fr !important; }
}
@media (max-width: 480px) {
    .nyt-article-wrap { padding: 0 12px; padding-top: 16px; padding-bottom: 24px; }
    .nyt-article-title { font-size: 1.4rem; }
    .nyt-article-body { font-size: 15px; }
    .nyt-article-excerpt { padding-left: 12px; font-size: 16px; }
    .nyt-related-grid { grid-template-columns: 1fr !important; }
    .nyt-comment__content, .nyt-comment__actions { padding-left: 0; }
    .nyt-reply-list, .nyt-reply-form { margin-left: 0; }
}
/* ── 정렬 리셋: article/header 브라우저 기본값 제거 ── */
.nyt-article-wrap > article {
    margin: 0;
    padding: 0;
    min-width: 0; /* grid 컬럼 overflow 방지 */
}
.nyt-article-wrap > article > header {
    margin: 0;
    padding: 0;
}
.nyt-article-header { margin-bottom: 24px; }
.nyt-article-section {
    font-family: var(--nyt-sans);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--nyt-section);
    display: block;
    margin-bottom: 10px;
    text-decoration: none;
}
.nyt-article-section:hover { text-decoration: underline; }
.nyt-article-title {
    font-family: var(--nyt-serif);
    font-size: clamp(1.75rem, 3.5vw, 2.6rem);
    font-weight: 700;
    line-height: 1.2;
    color: var(--nyt-black);
    margin: 0 0 16px;
    letter-spacing: -0.02em;
}
.nyt-article-subtitle {
    font-family: var(--nyt-serif);
    font-size: clamp(1rem, 2vw, 1.25rem);
    font-weight: 400;
    font-style: italic;
    color: #333;
    line-height: 1.5;
    margin: 0 0 20px;
}
.nyt-article-byline {
    font-family: var(--nyt-sans);
    font-size: 13px;
    color: var(--nyt-gray-dark);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px 16px;
    padding: 16px 0;
    border-top: 1px solid var(--nyt-border);
    border-bottom: 1px solid var(--nyt-border);
    margin-bottom: 24px;
}
.nyt-article-byline strong { font-weight: 700; }
.nyt-article-byline time { color: var(--nyt-gray-mid); }
.nyt-article-byline .nyt-views {
    color: var(--nyt-gray-mid); margin-left: auto;
    display: flex; align-items: center; gap: 4px;
}
.nyt-article-hero { margin-bottom: 24px; }
.nyt-article-hero img {
    width: 100%; display: block;
    border: 1px solid var(--nyt-border);
}
.nyt-article-hero figcaption {
    font-family: var(--nyt-sans);
    font-size: 12px;
    color: var(--nyt-gray-mid);
    border-top: 2px solid var(--nyt-black);
    padding-top: 6px;
    margin-top: 4px;
    line-height: 1.5;
}
.nyt-article-excerpt {
    font-family: var(--nyt-serif);
    font-size: 18px;
    font-style: italic;
    color: #444;
    line-height: 1.7;
    border-left: 3px solid var(--nyt-black);
    padding-left: 16px;
    margin: 0 0 24px;
}
.nyt-article-body {
    font-family: var(--nyt-serif);
    font-size: 17px;
    line-height: 1.9;
    color: var(--nyt-black);
    word-break: keep-all;
    width: 100%;
    margin-left: 0;
    padding-left: 0;
}
/* 단락 */
.nyt-article-body p { margin: 0 0 1.5em; }
/* 링크 */
.nyt-article-body a { color: var(--nyt-link); text-decoration: underline; }
.nyt-article-body a:hover { color: var(--nyt-black); }
/* 굵게/이탤릭 */
.nyt-article-body strong, .nyt-article-body b { font-weight: 700; }
.nyt-article-body em, .nyt-article-body i { font-style: italic; }
/* 제목 */
.nyt-article-body h1, .nyt-article-body h2,
.nyt-article-body h3, .nyt-article-body h4,
.nyt-article-body h5, .nyt-article-body h6 {
    font-family: var(--nyt-serif);
    font-weight: 700;
    line-height: 1.25;
    margin: 1.8em 0 0.6em;
    color: var(--nyt-black);
}
.nyt-article-body h2 { font-size: 1.5rem; border-bottom: 1px solid var(--nyt-border); padding-bottom: 6px; }
.nyt-article-body h3 { font-size: 1.25rem; }
.nyt-article-body h4 { font-size: 1.1rem; }
/* 인용 */
.nyt-article-body blockquote {
    margin: 1.5em 0;
    padding: 16px 20px;
    border-left: 4px solid var(--nyt-black);
    background: var(--nyt-gray-bg);
    font-style: italic;
    font-size: 1.05rem;
    color: #333;
}
.nyt-article-body blockquote p { margin: 0; }
/* 이미지 — 전체 너비로 확장 */
.nyt-article-body img {
    max-width: 100%;
    width: 100%;
    height: auto;
    display: block;
    margin: 1.5em 0;
}
.nyt-article-body figure {
    margin: 1.5em 0;
    max-width: 100%;
}
.nyt-article-body figcaption {
    font-family: var(--nyt-sans);
    font-size: 13px;
    color: var(--nyt-gray-mid);
    border-top: 1px solid var(--nyt-border);
    padding-top: 6px;
    margin-top: 6px;
    line-height: 1.5;
}
/* 목록 */
.nyt-article-body ul, .nyt-article-body ol {
    margin: 0 0 1.5em 1.5em;
    padding: 0;
}
.nyt-article-body li { margin-bottom: 0.4em; line-height: 1.75; }
.nyt-article-body ul li { list-style: disc; }
.nyt-article-body ol li { list-style: decimal; }
/* 표 */
.nyt-article-body table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5em 0;
    font-family: var(--nyt-sans);
    font-size: 14px;
    overflow-x: auto;
    display: block;
}
.nyt-article-body th {
    background: var(--nyt-black);
    color: #fff;
    font-weight: 700;
    padding: 10px 14px;
    text-align: left;
}
.nyt-article-body td {
    padding: 9px 14px;
    border-bottom: 1px solid var(--nyt-border);
}
.nyt-article-body tr:nth-child(even) td { background: var(--nyt-gray-bg); }
/* 영상/임베드 */
.nyt-article-body iframe,
.nyt-article-body video {
    max-width: 100%;
    display: block;
    margin: 1.5em 0;
}
/* 코드 */
.nyt-article-body pre {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 16px 20px;
    overflow-x: auto;
    font-size: 14px;
    line-height: 1.6;
    margin: 1.5em 0;
}
.nyt-article-body code {
    font-family: 'Courier New', monospace;
    background: var(--nyt-gray-bg);
    padding: 2px 6px;
    font-size: 0.9em;
    border: 1px solid var(--nyt-border);
}
.nyt-article-body pre code {
    background: none; border: none; padding: 0;
}
/* 수평선 */
.nyt-article-body hr {
    border: none;
    border-top: 2px solid var(--nyt-border);
    margin: 2em 0;
}
.nyt-article-footer {
    margin-top: 40px;
    padding-top: 16px;
    border-top: 1px solid var(--nyt-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-family: var(--nyt-sans);
    font-size: 13px;
    color: var(--nyt-gray-mid);
}
.nyt-back-link {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: var(--nyt-sans); font-size: 13px; font-weight: 600;
    color: var(--nyt-black); text-decoration: none;
    border-bottom: 1px solid var(--nyt-black);
    padding-bottom: 1px;
}
.nyt-back-link:hover { opacity: 0.6; }
/* Author box */
.nyt-author-box {
    margin-top: 32px;
    border: 1px solid var(--nyt-border);
    border-top: 3px solid var(--nyt-black);
}
.nyt-author-box__header {
    display: flex; gap: 16px; align-items: center;
    padding: 20px; background: var(--nyt-gray-bg);
    text-decoration: none;
    transition: background .15s;
}
.nyt-author-box__header:hover { background: #eee; }
.nyt-author-box__avatar {
    width: 56px; height: 56px; border-radius: 50%;
    object-fit: cover; flex-shrink: 0;
    border: 2px solid var(--nyt-border);
}
.nyt-author-box__avatar-placeholder {
    width: 56px; height: 56px; border-radius: 50%;
    background: var(--nyt-black); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 700; flex-shrink: 0;
}
.nyt-author-box__name {
    font-family: var(--nyt-serif); font-size: 16px; font-weight: 700;
    color: var(--nyt-black); display: block;
}
.nyt-author-box__role {
    font-family: var(--nyt-sans); font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--nyt-gray-mid); display: block; margin-top: 2px;
}
.nyt-author-box__bio {
    font-family: var(--nyt-sans); font-size: 13px; color: #555;
    line-height: 1.55; margin-top: 4px;
    overflow: hidden; display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
/* Related */
.nyt-related {
    margin-top: 40px;
    border-top: 3px solid var(--nyt-black);
    padding-top: 20px;
}
.nyt-related-title {
    font-family: var(--nyt-sans); font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--nyt-gray-mid); margin-bottom: 20px;
}
.nyt-related-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;
}
@media (min-width: 768px) { .nyt-related-grid { grid-template-columns: repeat(4, 1fr); } }
.nyt-related-card { text-decoration: none; color: inherit; display: block; }
.nyt-related-card img { width: 100%; aspect-ratio: 3/2; object-fit: cover; display: block; margin-bottom: 8px; }
.nyt-related-card__cat {
    font-family: var(--nyt-sans); font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--nyt-section); display: block; margin-bottom: 4px;
}
.nyt-related-card__title {
    font-family: var(--nyt-serif); font-size: 14px; font-weight: 700;
    color: var(--nyt-black); line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.nyt-related-card:hover .nyt-related-card__title { text-decoration: underline; }
.nyt-related-card__date {
    font-family: var(--nyt-sans); font-size: 11px;
    color: var(--nyt-gray-mid); display: block; margin-top: 5px;
}
/* Sidebar */
.nyt-art-sidebar { font-family: var(--nyt-sans); min-width: 0; }
.nyt-art-sidebar__card { margin-bottom: 32px; }
.nyt-art-sidebar__header {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--nyt-gray-mid);
    border-bottom: 1px solid var(--nyt-border);
    padding-bottom: 8px; margin-bottom: 12px;
}
.nyt-art-sidebar__item {
    padding: 10px 0;
    border-bottom: 1px solid var(--nyt-border);
    display: flex; gap: 8px; align-items: flex-start;
}
.nyt-art-sidebar__item:last-child { border-bottom: none; }
.nyt-art-sidebar__rank {
    font-size: 18px; font-weight: 700; color: var(--nyt-border);
    line-height: 1; flex-shrink: 0; width: 20px; text-align: right;
    padding-top: 2px;
}
.nyt-art-sidebar__link {
    font-family: var(--nyt-serif); font-size: 14px; font-weight: 700;
    color: var(--nyt-black); text-decoration: none; line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.nyt-art-sidebar__link:hover { text-decoration: underline; }
.nyt-art-sidebar__date { font-size: 11px; color: var(--nyt-gray-mid); display: block; margin-top: 3px; }
/* Comments */
.nyt-comments {
    max-width: 1200px; margin: 0 auto;
    padding: 0 20px 48px;
    border-top: 1px solid var(--nyt-border);
}
.nyt-comments-title {
    font-family: var(--nyt-sans); font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em; color: var(--nyt-gray-mid);
    border-bottom: 1px solid var(--nyt-border);
    padding: 20px 0 10px; margin-bottom: 20px;
}
.nyt-comment { padding: 16px 0; border-bottom: 1px solid var(--nyt-border); }
.nyt-comment:last-child { border-bottom: none; }
.nyt-comment__header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.nyt-comment__avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--nyt-black); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.nyt-comment__author { font-family: var(--nyt-sans); font-size: 13px; font-weight: 700; color: var(--nyt-black); }
.nyt-comment__date { font-family: var(--nyt-sans); font-size: 12px; color: var(--nyt-gray-mid); margin-left: auto; }
.nyt-comment__content { font-family: var(--nyt-sans); font-size: 15px; line-height: 1.7; color: #333; padding-left: 38px; word-break: keep-all; }
.nyt-comment__actions { padding-left: 38px; margin-top: 6px; display: flex; gap: 10px; }
.nyt-comment__btn { font-family: var(--nyt-sans); font-size: 12px; color: var(--nyt-gray-mid); background: none; border: none; cursor: pointer; padding: 0; text-decoration: underline; }
.nyt-comment__btn:hover { color: var(--nyt-black); }
.nyt-reply-list { margin-left: 38px; margin-top: 8px; border-left: 2px solid var(--nyt-border); padding-left: 14px; }
.nyt-reply { padding: 10px 0; border-bottom: 1px solid #eee; }
.nyt-reply:last-child { border-bottom: none; }
.nyt-reply-form { margin-left: 38px; margin-top: 10px; display: none; }
.nyt-reply-form.open { display: block; }
.nyt-comment-form { margin-top: 32px; padding-top: 20px; border-top: 2px solid var(--nyt-black); }
.nyt-comment-form textarea {
    width: 100%; padding: 12px 14px; font-size: 14px; line-height: 1.7;
    border: 1px solid var(--nyt-border); resize: vertical; min-height: 100px;
    font-family: var(--nyt-sans); color: var(--nyt-black);
    transition: border-color .15s;
}
.nyt-comment-form textarea:focus { outline: none; border-color: var(--nyt-black); }
.nyt-comment-form__submit {
    margin-top: 10px; background: var(--nyt-black); color: #fff;
    border: none; padding: 9px 22px; font-size: 13px; font-weight: 700;
    font-family: var(--nyt-sans); cursor: pointer; transition: opacity .15s; letter-spacing: 0.04em;
}
.nyt-comment-form__submit:hover { opacity: .75; }
.nyt-login-prompt {
    font-family: var(--nyt-sans); font-size: 14px; color: var(--nyt-gray-mid);
    text-align: center; padding: 20px; border: 1px solid var(--nyt-border); margin-top: 24px;
}
.nyt-login-prompt a { color: var(--nyt-black); font-weight: 700; }
@endif

/* ── 히어로 ── */
.mag-hero {
    position: relative;
    width: 100%;
    background: var(--m-accent-deep);
    overflow: hidden;
    margin-bottom: 0;
}
.mag-hero__img {
    width: 100%;
    height: 420px;
    object-fit: cover;
    display: block;
    opacity: 0.75;
}
@media (min-width: 768px) { .mag-hero__img { height: 520px; } }

.mag-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(26,40,48,0.08) 0%,
        rgba(26,40,48,0.15) 40%,
        rgba(26,40,48,0.82) 100%
    );
}
.mag-hero__body {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 2.5rem 2rem 2rem;
    max-width: 860px;
}
.mag-hero__cat {
    display: inline-block;
    background: var(--m-accent);
    color: var(--m-accent-deep);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 2px;
    margin-bottom: 14px;
}
.mag-hero__title {
    font-family: 'Playfair Display', 'Noto Sans KR', Georgia, serif;
    font-size: clamp(1.6rem, 3.5vw, 2.6rem);
    font-weight: 900;
    color: #fff;
    line-height: 1.25;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.4);
}
.mag-hero__subtitle {
    font-size: clamp(1rem, 2vw, 1.2rem);
    color: rgba(255,255,255,0.88);
    font-weight: 400;
    line-height: 1.5;
    margin-bottom: 14px;
    text-shadow: 0 1px 4px rgba(0,0,0,0.3);
}
.mag-hero__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px 16px;
    font-size: 12px;
    color: rgba(255,255,255,0.75);
}
.mag-hero__meta-dot { opacity: 0.4; }

/* 히어로 없을 때 (이미지 없는 기사) */
.mag-hero--text {
    background: linear-gradient(135deg, var(--m-accent-deep) 0%, #1a3d4f 100%);
    padding: 3.5rem 2rem;
    position: relative;
}
.mag-hero--text::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: repeating-linear-gradient(
        45deg,
        rgba(170,205,220,0.03) 0px, rgba(170,205,220,0.03) 1px,
        transparent 1px, transparent 40px
    );
}
.mag-hero--text .mag-hero__body {
    position: static;
    max-width: 800px;
}
.mag-hero--text .mag-hero__title { font-size: clamp(1.7rem, 3.5vw, 2.8rem); }
.mag-hero--text .mag-hero__meta { color: rgba(255,255,255,0.65); }

/* ── 브레드크럼 ── */
.mag-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--m-text-sub);
    padding: 14px 0;
    border-bottom: 1px solid var(--m-border);
    margin-bottom: 0;
    background: var(--m-white);
    padding-left: 24px;
    padding-right: 24px;
}
.mag-breadcrumb a { color: var(--m-accent-dark); text-decoration: none; }
.mag-breadcrumb a:hover { text-decoration: underline; }
.mag-breadcrumb__sep { color: var(--m-border); }

/* ── 2단 레이아웃 ── */
.mag-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
    max-width: 1200px;
    margin: 0 auto;
    background: var(--m-white);
}
@media (min-width: 1024px) {
    .mag-layout {
        grid-template-columns: 1fr 300px;
    }
}

/* ── 기사 본문 영역 ── */
.mag-article {
    padding: 36px 28px 40px;
    border-right: 1px solid var(--m-border);
    min-width: 0;
}
@media (min-width: 768px) { .mag-article { padding: 44px 48px 56px; } }

/* 발췌문 */
.mag-excerpt {
    font-size: 18px;
    font-weight: 300;
    color: var(--m-text-sub);
    line-height: 1.7;
    border-left: 4px solid var(--m-accent);
    padding-left: 20px;
    margin: 0 0 28px;
    font-family: 'Noto Sans KR', sans-serif;
}

/* 저자 바 */
.mag-byline {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px 20px;
    padding: 16px 0;
    border-top: 2px solid var(--m-accent);
    border-bottom: 1px solid var(--m-border);
    margin-bottom: 36px;
    font-size: 13px;
}
.mag-byline__author {
    font-weight: 700;
    color: var(--m-text);
    display: flex;
    align-items: center;
    gap: 6px;
}
.mag-byline__author::before {
    content: '';
    display: inline-block;
    width: 28px; height: 28px;
    background: var(--m-accent-bg);
    border: 2px solid var(--m-accent);
    border-radius: 50%;
    font-size: 13px;
    text-align: center;
    line-height: 24px;
}
.mag-byline__date { color: var(--m-text-sub); }
.mag-byline__views {
    display: flex; align-items: center; gap: 4px;
    color: var(--m-text-sub);
    margin-left: auto;
}

/* 본문 텍스트 */
.mag-body {
    font-family: 'Noto Sans KR', -apple-system, sans-serif;
    font-size: 17px;
    line-height: 1.95;
    color: var(--m-text);
    letter-spacing: -0.01em;
    word-break: keep-all;
}
.mag-body p {
    margin: 0 0 1.6em;
}
.mag-body br + br {
    display: block;
    content: '';
    margin-top: 0.6em;
}
.mag-body strong, .mag-body b { font-weight: 700; }
.mag-body a { color: var(--m-accent-dark); text-decoration: underline; }
.mag-body a:hover { color: var(--m-accent-deep); }


/* 하단 푸터 */
.mag-article-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 48px;
    padding-top: 20px;
    border-top: 1px solid var(--m-border);
    font-size: 13px;
    color: var(--m-text-sub);
}
.mag-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--m-accent-dark);
    font-weight: 600;
    text-decoration: none;
    font-size: 13px;
    transition: color 0.15s;
}
.mag-back-link:hover { color: var(--m-accent-deep); }

/* ── 사이드바 ── */
.mag-sidebar {
    padding: 28px 20px;
    background: var(--m-accent-light);
}
@media (min-width: 1024px) {
    .mag-sidebar {
        position: sticky;
        top: 0;
        max-height: 100vh;
        overflow-y: auto;
    }
}
.mag-sidebar-card {
    background: var(--m-white);
    border: 1px solid var(--m-border);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 20px;
}
.mag-sidebar-card:last-child { margin-bottom: 0; }
.mag-sidebar-header {
    background: var(--m-accent);
    color: var(--m-accent-deep);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 8px 14px;
}
.mag-sidebar-item {
    padding: 11px 14px;
    border-bottom: 1px solid #f0f5f7;
    transition: background 0.12s;
}
.mag-sidebar-item:last-child { border-bottom: none; }
.mag-sidebar-item:hover { background: var(--m-accent-bg); }
.mag-sidebar-item a {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--m-text);
    text-decoration: none;
    line-height: 1.45;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.mag-sidebar-item a:hover { color: var(--m-accent-dark); }
.mag-sidebar-item time {
    display: block;
    font-size: 11px;
    color: var(--m-text-sub);
    margin-top: 4px;
}
.mag-rank {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px; height: 20px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
    margin-right: 8px;
    vertical-align: middle;
}
.mag-rank--top { background: var(--m-accent-dark); color: #fff; }
.mag-rank--rest { background: #e8eff2; color: var(--m-text-sub); }

/* ── 연관 기사 섹션 ── */
.mag-related {
    background: var(--m-white);
    border-top: 3px solid var(--m-accent);
    padding: 36px 28px 44px;
    max-width: 1200px;
    margin: 0 auto;
}
@media (min-width: 768px) { .mag-related { padding: 40px 48px 52px; } }
.mag-related-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 22px;
    font-weight: 700;
    color: var(--m-text);
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--m-border);
    display: flex;
    align-items: center;
    gap: 10px;
}
.mag-related-title::before {
    content: '';
    display: block;
    width: 5px; height: 22px;
    background: var(--m-accent);
    border-radius: 3px;
}
.mag-related-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}
@media (min-width: 640px)  { .mag-related-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .mag-related-grid { grid-template-columns: repeat(4, 1fr); } }
.mag-related-card {
    border: 1px solid var(--m-border);
    border-top: 3px solid var(--m-accent);
    background: var(--m-white);
    transition: box-shadow 0.2s, transform 0.2s;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.mag-related-card:hover {
    box-shadow: 0 6px 24px rgba(42, 120, 150, 0.12);
    transform: translateY(-2px);
}
.mag-related-card__img {
    width: 100%;
    height: 130px;
    object-fit: cover;
    display: block;
}
.mag-related-card__body {
    padding: 14px 16px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.mag-related-card__cat {
    font-size: 10px;
    font-weight: 700;
    color: var(--m-accent-dark);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 6px;
}
.mag-related-card__title {
    font-size: 14px;
    font-weight: 700;
    color: var(--m-text);
    line-height: 1.45;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
    margin-bottom: 10px;
}
.mag-related-card:hover .mag-related-card__title { color: var(--m-accent-dark); }
.mag-related-card__date {
    font-size: 11px;
    color: var(--m-text-sub);
    margin-top: auto;
}

/* ── 댓글 섹션 ── */
.mag-comments {
    background: var(--m-white);
    border-top: 1px solid var(--m-border);
    padding: 36px 28px 44px;
    max-width: 1200px;
    margin: 0 auto;
}
@media (min-width: 768px) { .mag-comments { padding: 40px 48px 52px; } }
.mag-comments-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--m-text);
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--m-border);
    display: flex;
    align-items: center;
    gap: 10px;
}
.mag-comments-title::before {
    content: '';
    display: block;
    width: 5px; height: 20px;
    background: var(--m-accent);
    border-radius: 3px;
}
.mag-comment {
    padding: 16px 0;
    border-bottom: 1px solid var(--m-border);
}
.mag-comment:last-child { border-bottom: none; }
.mag-comment__header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}
.mag-comment__avatar {
    width: 32px; height: 32px;
    background: var(--m-accent-bg);
    border: 2px solid var(--m-accent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: var(--m-accent-dark);
    flex-shrink: 0;
}
.mag-comment__author { font-size: 14px; font-weight: 700; color: var(--m-text); }
.mag-comment__date { font-size: 12px; color: var(--m-text-sub); margin-left: auto; }
.mag-comment__content {
    font-size: 15px; line-height: 1.75; color: var(--m-text);
    padding-left: 42px;
    word-break: keep-all;
}
.mag-comment__actions {
    padding-left: 42px;
    margin-top: 6px;
    display: flex; gap: 10px;
}
.mag-comment__btn {
    font-size: 12px; color: var(--m-text-sub); background: none; border: none;
    cursor: pointer; padding: 0; text-decoration: underline;
}
.mag-comment__btn:hover { color: var(--m-accent-dark); }
.mag-reply-list {
    margin-left: 42px;
    margin-top: 8px;
    border-left: 3px solid var(--m-border);
    padding-left: 16px;
}
.mag-reply { padding: 10px 0; border-bottom: 1px solid #f0f5f7; }
.mag-reply:last-child { border-bottom: none; }
.mag-reply-form {
    margin-left: 42px; margin-top: 10px;
    display: none;
}
.mag-reply-form.open { display: block; }
.mag-comment-form {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid var(--m-accent);
}
.mag-comment-form textarea {
    width: 100%;
    padding: 12px 14px;
    font-size: 14px;
    line-height: 1.7;
    border: 1px solid var(--m-border);
    border-radius: 4px;
    resize: vertical;
    min-height: 100px;
    font-family: 'Noto Sans KR', sans-serif;
    color: var(--m-text);
    transition: border-color 0.15s;
}
.mag-comment-form textarea:focus {
    outline: none;
    border-color: var(--m-accent-mid);
    box-shadow: 0 0 0 3px rgba(170,205,220,0.2);
}
.mag-comment-form__submit {
    margin-top: 10px;
    background: var(--m-accent-dark);
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 9px 22px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
}
.mag-comment-form__submit:hover { background: var(--m-accent-deep); }
.mag-login-prompt {
    background: var(--m-accent-light);
    border: 1px solid var(--m-border);
    border-radius: 4px;
    padding: 16px 20px;
    font-size: 14px;
    color: var(--m-text-sub);
    text-align: center;
    margin-top: 24px;
}
.mag-login-prompt a { color: var(--m-accent-dark); font-weight: 600; text-decoration: none; }
.mag-login-prompt a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')

@php
$authorSocials = [
    'social_facebook'  => ['label'=>'Facebook',   'color'=>'#1877F2', 'icon'=>'<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>'],
    'social_x'         => ['label'=>'X',          'color'=>'#000000', 'icon'=>'<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
    'social_instagram' => ['label'=>'Instagram',  'color'=>'#E1306C', 'icon'=>'<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>'],
    'social_linkedin'  => ['label'=>'LinkedIn',   'color'=>'#0A66C2', 'icon'=>'<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>'],
    'social_website'   => ['label'=>'홈페이지',    'color'=>'#6366f1', 'icon'=>'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>'],
    'social_blog'      => ['label'=>'블로그',      'color'=>'#f59e0b', 'icon'=>'<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>'],
    'social_pixabay'   => ['label'=>'Pixabay',    'color'=>'#2ec66e', 'icon'=>'<circle cx="12" cy="12" r="10"/><text x="5.5" y="16" font-size="8" fill="white" font-weight="bold">PX</text>'],
    'social_wikipedia' => ['label'=>'Wikipedia',  'color'=>'#333',    'icon'=>'<circle cx="12" cy="12" r="10"/><text x="7.5" y="16.5" font-size="11" fill="white" font-weight="bold">W</text>'],
    'social_email'     => ['label'=>'이메일',      'color'=>'#6b7280', 'icon'=>'<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>'],
];
@endphp

@if($isNyt)
{{-- ═══════════════════════════ NYT STYLE ═══════════════════════════ --}}

{{-- 히어로 이미지 (전폭) --}}
@if($article->thumbnail)
<div style="width:100%;max-height:520px;overflow:hidden;border-bottom:1px solid var(--nyt-border);">
    <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}"
         style="width:100%;max-height:520px;object-fit:cover;display:block;">
</div>
@endif

<div class="nyt-article-wrap">

    {{-- 본문 컬럼 --}}
    <article>
        <header class="nyt-article-header">
            @if($article->category)
            <a href="{{ route('news.index', ['category' => $article->category->slug]) }}" class="nyt-article-section">{{ $article->category->name }}</a>
            @endif
            <h1 class="nyt-article-title">{{ $article->title }}</h1>
            @if($article->subtitle)
            <p class="nyt-article-subtitle">{{ $article->subtitle }}</p>
            @endif
            <div class="nyt-article-byline">
                <strong>{{ $article->user->name }}</strong>
                <time datetime="{{ $article->published_at?->toDateString() ?? $article->created_at->toDateString() }}">
                    {{ $article->published_at?->format('Y년 m월 d일 H:i') ?? $article->created_at->format('Y년 m월 d일 H:i') }}
                </time>
                <span class="nyt-views">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ number_format($article->view_count) }}
                </span>
            </div>
        </header>

        @if($article->excerpt)
        <p class="nyt-article-excerpt">{{ $article->excerpt }}</p>
        @endif

        <div class="nyt-article-body">
            {!! $article->content !!}
        </div>

        <div class="nyt-article-footer">
            <span>최종 수정: {{ $article->updated_at->format('Y.m.d H:i') }}</span>
            <a class="nyt-back-link"
               href="{{ route('news.index', $article->category ? ['category' => $article->category->slug] : []) }}">
                ← {{ $article->category?->name ?? '전체' }} 목록
            </a>
        </div>

        {{-- 작성자 박스 --}}
        @if($article->user->author_box_enabled)
        @php
            $author = $article->user;
            $hasSocials = collect($authorSocials)->keys()->some(fn($f) => !empty($author->$f));
        @endphp
        <div class="nyt-author-box">
            <a href="{{ route('journalist.show', $author->username) }}" class="nyt-author-box__header">
                @if($author->profile_image)
                    <img src="{{ $author->profile_image }}" alt="{{ $author->name }}" class="nyt-author-box__avatar">
                @else
                    <div class="nyt-author-box__avatar-placeholder">{{ mb_substr($author->name, 0, 1) }}</div>
                @endif
                <div style="flex:1;min-width:0;">
                    <span class="nyt-author-box__name">{{ $author->name }}</span>
                    <span class="nyt-author-box__role">{{ $author->roleLabel() }}</span>
                    @if($author->bio)
                    <p class="nyt-author-box__bio">{{ $author->bio }}</p>
                    @endif
                </div>
                <span style="font-family:var(--nyt-sans);font-size:12px;color:var(--nyt-gray-mid);flex-shrink:0;">기자 페이지 →</span>
            </a>
            @if($hasSocials)
            <div style="display:flex;flex-wrap:wrap;gap:0;padding:8px 16px;border-top:1px solid var(--nyt-border);">
                @foreach($authorSocials as $field => $meta)
                @if(!empty($author->$field))
                @php
                    $href = $field === 'social_email' ? 'mailto:'.$author->$field : $author->$field;
                    $strokeStyle = in_array($field, ['social_instagram','social_pixabay','social_wikipedia']) ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
                @endphp
                <a href="{{ $href }}" target="{{ $field==='social_email'?'_self':'_blank' }}" rel="noopener noreferrer"
                   title="{{ $meta['label'] }}"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-size:12px;font-family:var(--nyt-sans);font-weight:600;text-decoration:none;color:{{ $meta['color'] }};transition:opacity .12s;margin:2px;"
                   onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                    <svg width="13" height="13" viewBox="0 0 24 24" {!! $strokeStyle !!}>{!! $meta['icon'] !!}</svg>
                    {{ $meta['label'] }}
                </a>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- 연관 기사 --}}
        @if($related->count())
        <div class="nyt-related">
            <div class="nyt-related-title">연관 기사</div>
            <div class="nyt-related-grid">
                @foreach($related as $rel)
                <a href="{{ route('news.show', $rel->slug) }}" class="nyt-related-card">
                    @if($rel->thumbnail)
                        <img src="{{ $rel->thumbnail }}" alt="{{ $rel->title }}">
                    @endif
                    @if($rel->category)
                        <span class="nyt-related-card__cat">{{ $rel->category->name }}</span>
                    @endif
                    <span class="nyt-related-card__title">{{ $rel->title }}</span>
                    <time class="nyt-related-card__date">{{ $rel->published_at?->format('Y.m.d') ?? $rel->created_at->format('Y.m.d') }}</time>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </article>

    {{-- 사이드바 --}}
    <aside class="nyt-art-sidebar">
        @if($article->category)
        @php
            $catLatest = \App\Models\Article::with('user')
                ->where('status', 'published')
                ->where('category_id', $article->category_id)
                ->where('id', '!=', $article->id)
                ->orderBy('published_at', 'desc')
                ->limit(6)->get();
        @endphp
        @if($catLatest->count())
        <div class="nyt-art-sidebar__card">
            <div class="nyt-art-sidebar__header">{{ $article->category->name }}</div>
            @foreach($catLatest as $a)
            <div class="nyt-art-sidebar__item">
                <div>
                    <a href="{{ route('news.show', $a->slug) }}" class="nyt-art-sidebar__link">{{ $a->title }}</a>
                    <time class="nyt-art-sidebar__date">{{ $a->published_at?->format('Y.m.d') ?? $a->created_at->format('Y.m.d') }}</time>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endif

        @php
            $popular = \App\Models\Article::where('status', 'published')
                ->where('id', '!=', $article->id)
                ->orderBy('view_count', 'desc')
                ->limit(7)->get();
        @endphp
        @if($popular->count())
        <div class="nyt-art-sidebar__card">
            <div class="nyt-art-sidebar__header">인기 기사</div>
            @foreach($popular as $i => $a)
            <div class="nyt-art-sidebar__item">
                <span class="nyt-art-sidebar__rank">{{ $i + 1 }}</span>
                <div>
                    <a href="{{ route('news.show', $a->slug) }}" class="nyt-art-sidebar__link">{{ $a->title }}</a>
                    <time class="nyt-art-sidebar__date">조회 {{ number_format($a->view_count) }}</time>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </aside>

</div>{{-- /.nyt-article-wrap --}}

{{-- 댓글 --}}
@if($commentsEnabled)
<div class="nyt-comments">
    <div class="nyt-comments-title">댓글 {{ $comments->count() }}</div>

    @forelse($comments as $comment)
    <div class="nyt-comment" id="comment-{{ $comment->id }}">
        <div class="nyt-comment__header">
            <div class="nyt-comment__avatar">{{ mb_substr($comment->user->name, 0, 1) }}</div>
            <span class="nyt-comment__author">{{ $comment->user->name }}</span>
            <span class="nyt-comment__date">{{ $comment->created_at->format('Y.m.d H:i') }}</span>
        </div>
        <div class="nyt-comment__content">{{ $comment->content }}</div>
        <div class="nyt-comment__actions">
            @auth
            <button class="nyt-comment__btn" onclick="toggleReplyForm({{ $comment->id }})">답글</button>
            @if(auth()->id() === $comment->user_id || auth()->user()->canApproveArticle())
            <form action="{{ route('news.comment.delete', [$article->slug, $comment->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('댓글을 삭제하시겠습니까?')">
                @csrf @method('DELETE')
                <button type="submit" class="nyt-comment__btn" style="color:#c0392b;">삭제</button>
            </form>
            @endif
            @endauth
        </div>
        @auth
        <div class="nyt-reply-form" id="reply-form-{{ $comment->id }}">
            <form action="{{ route('news.comment.store', $article->slug) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <textarea name="content" rows="3" placeholder="답글을 입력하세요..." required style="width:100%;padding:10px 12px;font-size:13px;border:1px solid var(--nyt-border);resize:vertical;font-family:var(--nyt-sans);"></textarea>
                <div style="display:flex;gap:8px;margin-top:6px;">
                    <button type="submit" class="nyt-comment-form__submit" style="padding:7px 16px;font-size:13px;">답글 등록</button>
                    <button type="button" class="nyt-comment__btn" onclick="toggleReplyForm({{ $comment->id }})" style="font-size:13px;text-decoration:none;padding:7px 0;">취소</button>
                </div>
            </form>
        </div>
        @endauth
        @if($comment->replies->count())
        <div class="nyt-reply-list">
            @foreach($comment->replies as $reply)
            <div class="nyt-reply" id="comment-{{ $reply->id }}">
                <div class="nyt-comment__header">
                    <div class="nyt-comment__avatar" style="width:24px;height:24px;font-size:10px;">{{ mb_substr($reply->user->name, 0, 1) }}</div>
                    <span class="nyt-comment__author">{{ $reply->user->name }}</span>
                    <span class="nyt-comment__date">{{ $reply->created_at->format('Y.m.d H:i') }}</span>
                </div>
                <div class="nyt-comment__content" style="padding-left:34px;">{{ $reply->content }}</div>
                @auth
                @if(auth()->id() === $reply->user_id || auth()->user()->canApproveArticle())
                <div class="nyt-comment__actions" style="padding-left:34px;">
                    <form action="{{ route('news.comment.delete', [$article->slug, $reply->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('댓글을 삭제하시겠습니까?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="nyt-comment__btn" style="color:#c0392b;">삭제</button>
                    </form>
                </div>
                @endif
                @endauth
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @empty
    <p style="font-family:var(--nyt-sans);font-size:14px;color:var(--nyt-gray-mid);padding:20px 0;">첫 댓글을 남겨보세요.</p>
    @endforelse

    @auth
    <div class="nyt-comment-form">
        <h3 style="font-family:var(--nyt-sans);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--nyt-gray-mid);margin-bottom:12px;">댓글 작성</h3>
        @if(session('success'))
            <div style="background:#f0fdf4;border:1px solid #86efac;padding:10px 14px;font-size:13px;color:#166534;margin-bottom:12px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fca5a5;padding:10px 14px;font-size:13px;color:#991b1b;margin-bottom:12px;">{{ session('error') }}</div>
        @endif
        <form action="{{ route('news.comment.store', $article->slug) }}" method="POST">
            @csrf
            <textarea name="content" rows="4" placeholder="댓글을 입력하세요..." required maxlength="2000">{{ old('content') }}</textarea>
            @error('content')<p style="color:#c0392b;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            <button type="submit" class="nyt-comment-form__submit">댓글 등록</button>
        </form>
    </div>
    @else
    <div class="nyt-login-prompt">
        댓글을 작성하려면 <a href="{{ route('login') }}">로그인</a>이 필요합니다.
    </div>
    @endauth
</div>
@endif

@else
{{-- ═══════════════════════════ BASIC/DEFAULT STYLE ═══════════════════════════ --}}

<div class="mag-page-wrapper">

{{-- ── 히어로 ── --}}
@if($article->thumbnail)
<div class="mag-hero">
    <img class="mag-hero__img" src="{{ $article->thumbnail }}" alt="{{ $article->title }}">
    <div class="mag-hero__overlay"></div>
    <div class="mag-hero__body">
        @if($article->category)
            <span class="mag-hero__cat">{{ $article->category->name }}</span>
        @endif
        <h1 class="mag-hero__title">{{ $article->title }}</h1>
        @if($article->subtitle)
            <p class="mag-hero__subtitle">{{ $article->subtitle }}</p>
        @endif
        <div class="mag-hero__meta">
            <span>{{ $article->user->name }}</span>
            <span class="mag-hero__meta-dot">|</span>
            <span>{{ $article->published_at?->format('Y년 m월 d일') ?? $article->created_at->format('Y년 m월 d일') }}</span>
            <span class="mag-hero__meta-dot">|</span>
            <span>조회 {{ number_format($article->view_count) }}</span>
        </div>
    </div>
</div>
@else
<div class="mag-hero mag-hero--text">
    <div class="mag-hero__body">
        @if($article->category)
            <span class="mag-hero__cat">{{ $article->category->name }}</span>
        @endif
        <h1 class="mag-hero__title">{{ $article->title }}</h1>
        @if($article->subtitle)
            <p class="mag-hero__subtitle">{{ $article->subtitle }}</p>
        @endif
        <div class="mag-hero__meta">
            <span>{{ $article->user->name }}</span>
            <span class="mag-hero__meta-dot">|</span>
            <span>{{ $article->published_at?->format('Y년 m월 d일') ?? $article->created_at->format('Y년 m월 d일') }}</span>
            <span class="mag-hero__meta-dot">|</span>
            <span>조회 {{ number_format($article->view_count) }}</span>
        </div>
    </div>
</div>
@endif

{{-- ── 브레드크럼 ── --}}
<nav class="mag-breadcrumb">
    <a href="{{ route('news.index') }}">뉴스</a>
    @if($article->category)
        <span class="mag-breadcrumb__sep">›</span>
        <a href="{{ route('news.index', ['category' => $article->category->slug]) }}">{{ $article->category->name }}</a>
    @endif
    <span class="mag-breadcrumb__sep">›</span>
    <span style="color:#8a9ea6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:320px;">{{ $article->title }}</span>
</nav>

{{-- ── 2단 레이아웃 ── --}}
<div class="mag-layout">

    {{-- 기사 본문 --}}
    <article class="mag-article">

        @if($article->excerpt)
        <p class="mag-excerpt">{{ $article->excerpt }}</p>
        @endif

        <div class="mag-byline">
            <span class="mag-byline__author">{{ $article->user->name }}</span>
            <time class="mag-byline__date" datetime="{{ $article->published_at?->toDateString() ?? $article->created_at->toDateString() }}">
                {{ $article->published_at?->format('Y년 m월 d일 H:i') ?? $article->created_at->format('Y년 m월 d일 H:i') }}
            </time>
            <span class="mag-byline__views">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ number_format($article->view_count) }}
            </span>
        </div>

        <div class="mag-body">
            {!! $article->content !!}
        </div>

        <div class="mag-article-footer">
            <span>최종 수정: {{ $article->updated_at->format('Y.m.d H:i') }}</span>
            <a class="mag-back-link"
               href="{{ route('news.index', $article->category ? ['category' => $article->category->slug] : []) }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ $article->category?->name ?? '전체' }} 목록
            </a>
        </div>

        @if($article->user->author_box_enabled)
        @php
            $author = $article->user;
            $hasSocials = collect($authorSocials)->keys()->some(fn($f) => !empty($author->$f));
        @endphp
        <div style="margin-top:32px;border:1px solid var(--m-border);border-radius:6px;overflow:hidden;">
            <a href="{{ route('journalist.show', $author->username) }}"
               style="display:flex;gap:20px;align-items:center;padding:20px 24px;background:linear-gradient(135deg,#1a5276 0%,#154360 100%);text-decoration:none;transition:opacity .15s;"
               onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                @if($author->profile_image)
                    <img src="{{ $author->profile_image }}" alt="{{ $author->name }}"
                         style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.5);flex-shrink:0;">
                @else
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.15);border:3px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#fff;flex-shrink:0;">
                        {{ mb_substr($author->name, 0, 1) }}
                    </div>
                @endif
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:4px;">
                        <span style="font-size:16px;font-weight:900;color:#fff;">{{ $author->name }}</span>
                        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:rgba(255,255,255,.2);color:rgba(255,255,255,.9);">{{ $author->roleLabel() }}</span>
                    </div>
                    @if($author->bio)
                    <p style="font-size:13px;line-height:1.6;color:rgba(255,255,255,.7);margin:0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $author->bio }}</p>
                    @endif
                </div>
                <span style="color:rgba(255,255,255,.5);font-size:12px;flex-shrink:0;white-space:nowrap;">기자 페이지 ›</span>
            </a>
            @if($hasSocials)
            <div style="display:flex;flex-wrap:wrap;gap:0;background:var(--m-accent-light);border-top:1px solid var(--m-border);padding:10px 18px;">
                @foreach($authorSocials as $field => $meta)
                @if(!empty($author->$field))
                @php
                    $href = $field === 'social_email' ? 'mailto:'.$author->$field : $author->$field;
                    $strokeStyle = in_array($field, ['social_instagram','social_pixabay','social_wikipedia']) ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
                @endphp
                <a href="{{ $href }}" target="{{ $field==='social_email'?'_self':'_blank' }}" rel="noopener noreferrer"
                   title="{{ $meta['label'] }}"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:99px;font-size:12px;font-weight:600;text-decoration:none;color:{{ $meta['color'] }};transition:background .12s;margin:2px;"
                   onmouseover="this.style.background='rgba(0,0,0,.06)'"
                   onmouseout="this.style.background='transparent'">
                    <svg width="13" height="13" viewBox="0 0 24 24" {!! $strokeStyle !!}>{!! $meta['icon'] !!}</svg>
                    {{ $meta['label'] }}
                </a>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endif

    </article>

    {{-- 사이드바 --}}
    <aside class="mag-sidebar">

        @if($article->category)
        @php
            $catLatest = \App\Models\Article::with('user')
                ->where('status', 'published')
                ->where('category_id', $article->category_id)
                ->where('id', '!=', $article->id)
                ->orderBy('published_at', 'desc')
                ->limit(6)->get();
        @endphp
        @if($catLatest->count())
        <div class="mag-sidebar-card">
            <div class="mag-sidebar-header">{{ $article->category->name }}</div>
            @foreach($catLatest as $a)
            <div class="mag-sidebar-item">
                <a href="{{ route('news.show', $a->slug) }}">{{ $a->title }}</a>
                <time>{{ $a->published_at?->format('Y.m.d') ?? $a->created_at->format('Y.m.d') }}</time>
            </div>
            @endforeach
        </div>
        @endif
        @endif

        @php
            $popular = \App\Models\Article::where('status', 'published')
                ->where('id', '!=', $article->id)
                ->orderBy('view_count', 'desc')
                ->limit(7)->get();
        @endphp
        @if($popular->count())
        <div class="mag-sidebar-card">
            <div class="mag-sidebar-header">인기 기사</div>
            @foreach($popular as $i => $a)
            <div class="mag-sidebar-item" style="display:flex;align-items:flex-start;">
                <span class="mag-rank {{ $i < 3 ? 'mag-rank--top' : 'mag-rank--rest' }}">{{ $i + 1 }}</span>
                <div style="flex:1;min-width:0;">
                    <a href="{{ route('news.show', $a->slug) }}">{{ $a->title }}</a>
                    <time>조회 {{ number_format($a->view_count) }}</time>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </aside>

</div>{{-- /.mag-layout --}}

{{-- ── 연관 기사 ── --}}
@if($related->count())
<div class="mag-related">
    <h2 class="mag-related-title">연관 기사</h2>
    <div class="mag-related-grid">
        @foreach($related as $rel)
        <a href="{{ route('news.show', $rel->slug) }}" class="mag-related-card">
            @if($rel->thumbnail)
                <img class="mag-related-card__img" src="{{ $rel->thumbnail }}" alt="{{ $rel->title }}">
            @endif
            <div class="mag-related-card__body">
                @if($rel->category)
                    <span class="mag-related-card__cat">{{ $rel->category->name }}</span>
                @endif
                <div class="mag-related-card__title">{{ $rel->title }}</div>
                <time class="mag-related-card__date">
                    {{ $rel->published_at?->format('Y.m.d') ?? $rel->created_at->format('Y.m.d') }}
                </time>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ── 댓글 섹션 ── --}}
@if($commentsEnabled)
<div class="mag-comments">
    <h2 class="mag-comments-title">댓글 <span style="font-size:15px;font-weight:400;color:var(--m-text-sub);">{{ $comments->count() }}</span></h2>

    @forelse($comments as $comment)
    <div class="mag-comment" id="comment-{{ $comment->id }}">
        <div class="mag-comment__header">
            <div class="mag-comment__avatar">{{ mb_substr($comment->user->name, 0, 1) }}</div>
            <span class="mag-comment__author">{{ $comment->user->name }}</span>
            <span class="mag-comment__date">{{ $comment->created_at->format('Y.m.d H:i') }}</span>
        </div>
        <div class="mag-comment__content">{{ $comment->content }}</div>
        <div class="mag-comment__actions">
            @auth
            <button class="mag-comment__btn" onclick="toggleReplyForm({{ $comment->id }})">답글</button>
            @if(auth()->id() === $comment->user_id || auth()->user()->canApproveArticle())
            <form action="{{ route('news.comment.delete', [$article->slug, $comment->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('댓글을 삭제하시겠습니까?')">
                @csrf @method('DELETE')
                <button type="submit" class="mag-comment__btn" style="color:#c0392b;">삭제</button>
            </form>
            @endif
            @endauth
        </div>

        @auth
        <div class="mag-reply-form" id="reply-form-{{ $comment->id }}">
            <form action="{{ route('news.comment.store', $article->slug) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <textarea name="content" rows="3" placeholder="답글을 입력하세요..." required class="" style="width:100%;padding:10px 12px;font-size:13px;border:1px solid var(--m-border);border-radius:4px;resize:vertical;font-family:'Noto Sans KR',sans-serif;"></textarea>
                <div style="display:flex;gap:8px;margin-top:6px;">
                    <button type="submit" class="mag-comment-form__submit" style="padding:7px 16px;font-size:13px;">답글 등록</button>
                    <button type="button" class="mag-comment__btn" onclick="toggleReplyForm({{ $comment->id }})" style="font-size:13px;text-decoration:none;padding:7px 0;">취소</button>
                </div>
            </form>
        </div>
        @endauth

        @if($comment->replies->count())
        <div class="mag-reply-list">
            @foreach($comment->replies as $reply)
            <div class="mag-reply" id="comment-{{ $reply->id }}">
                <div class="mag-comment__header">
                    <div class="mag-comment__avatar" style="width:26px;height:26px;font-size:11px;">{{ mb_substr($reply->user->name, 0, 1) }}</div>
                    <span class="mag-comment__author">{{ $reply->user->name }}</span>
                    <span class="mag-comment__date">{{ $reply->created_at->format('Y.m.d H:i') }}</span>
                </div>
                <div class="mag-comment__content" style="padding-left:36px;">{{ $reply->content }}</div>
                @auth
                @if(auth()->id() === $reply->user_id || auth()->user()->canApproveArticle())
                <div class="mag-comment__actions" style="padding-left:36px;">
                    <form action="{{ route('news.comment.delete', [$article->slug, $reply->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('댓글을 삭제하시겠습니까?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="mag-comment__btn" style="color:#c0392b;">삭제</button>
                    </form>
                </div>
                @endif
                @endauth
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @empty
    <p style="color:var(--m-text-sub);font-size:14px;padding:20px 0;">첫 댓글을 남겨보세요.</p>
    @endforelse

    @auth
    <div class="mag-comment-form">
        <h3 style="font-size:16px;font-weight:700;color:var(--m-text);margin-bottom:12px;">댓글 작성</h3>
        @if(session('success'))
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:4px;padding:10px 14px;font-size:13px;color:#166534;margin-bottom:12px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:4px;padding:10px 14px;font-size:13px;color:#991b1b;margin-bottom:12px;">{{ session('error') }}</div>
        @endif
        <form action="{{ route('news.comment.store', $article->slug) }}" method="POST">
            @csrf
            <textarea name="content" rows="4" placeholder="댓글을 입력하세요..." required maxlength="2000">{{ old('content') }}</textarea>
            @error('content')<p style="color:#c0392b;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            <button type="submit" class="mag-comment-form__submit">댓글 등록</button>
        </form>
    </div>
    @else
    <div class="mag-login-prompt">
        댓글을 작성하려면 <a href="{{ route('login') }}">로그인</a>이 필요합니다.
    </div>
    @endauth
</div>
@endif

</div>{{-- /.mag-page-wrapper --}}
@endif

@push('scripts')
<script>
function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    if (form) form.classList.toggle('open');
}
</script>
@endpush

@endsection
