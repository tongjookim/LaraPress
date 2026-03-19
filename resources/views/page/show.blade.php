@extends('skin.layout.basic.main')
@section('title', $page->title)
@section('content')

<style>
.page-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: #fff;
    padding: 56px 0 48px;
    margin: -2rem -1rem 0;
    position: relative;
    overflow: hidden;
}
.page-hero::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(99,179,237,.12) 0%, transparent 70%);
    pointer-events: none;
}
.page-hero-inner {
    max-width: 860px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
}
.page-breadcrumb {
    font-size: 12px;
    color: rgba(255,255,255,.5);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.page-breadcrumb a {
    color: rgba(255,255,255,.5);
    text-decoration: none;
    transition: color .2s;
}
.page-breadcrumb a:hover { color: rgba(255,255,255,.85); }
.page-hero-title {
    font-size: clamp(1.75rem, 4vw, 2.5rem);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.02em;
    margin: 0 0 16px;
    color: #fff;
}
.page-hero-accent {
    width: 48px;
    height: 4px;
    background: linear-gradient(90deg, #63b3ed, #4299e1);
    border-radius: 2px;
}
.page-body-wrap {
    max-width: 860px;
    margin: 0 auto;
    padding: 0 1rem;
}
.page-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,.07), 0 1px 4px rgba(0,0,0,.04);
    padding: 48px 56px;
    margin-top: -28px;
    position: relative;
}
@media (max-width: 640px) {
    .page-card { padding: 28px 20px; }
    .page-hero { padding: 40px 0 36px; }
}
.page-content-body {
    font-size: 16px;
    line-height: 1.85;
    color: #2d3748;
}
.page-content-body h1,
.page-content-body h2,
.page-content-body h3,
.page-content-body h4 {
    color: #1a202c;
    font-weight: 700;
    margin-top: 2em;
    margin-bottom: .6em;
    line-height: 1.3;
}
.page-content-body h2 {
    font-size: 1.45rem;
    padding-bottom: 8px;
    border-bottom: 2px solid #ebf4ff;
}
.page-content-body h3 { font-size: 1.2rem; }
.page-content-body p { margin-bottom: 1.25em; }
.page-content-body a {
    color: #3182ce;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.page-content-body ul,
.page-content-body ol {
    padding-left: 1.5em;
    margin-bottom: 1.25em;
}
.page-content-body li { margin-bottom: .4em; }
.page-content-body blockquote {
    border-left: 4px solid #4299e1;
    background: #ebf8ff;
    padding: 14px 20px;
    margin: 1.5em 0;
    border-radius: 0 6px 6px 0;
    color: #2b6cb0;
    font-style: italic;
}
.page-content-body img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,.1);
    margin: 1em 0;
}
.page-content-body pre,
.page-content-body code {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-family: 'SFMono-Regular', Consolas, monospace;
    font-size: 14px;
}
.page-content-body pre {
    padding: 16px 20px;
    overflow-x: auto;
    margin-bottom: 1.25em;
}
.page-content-body code { padding: 2px 6px; }
.page-content-body table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.25em;
    font-size: 14px;
}
.page-content-body th,
.page-content-body td {
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    text-align: left;
}
.page-content-body th {
    background: #f7fafc;
    font-weight: 600;
    color: #4a5568;
}
.page-content-body tr:nth-child(even) td { background: #fafafa; }
.page-back-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 36px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}
.page-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #4a5568;
    font-size: 14px;
    text-decoration: none;
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    transition: all .2s;
    background: #fff;
}
.page-back-btn:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
    color: #2d3748;
}
.page-meta-footer {
    font-size: 12px;
    color: #a0aec0;
}
</style>

{{-- Hero header --}}
<div class="page-hero">
    <div class="page-hero-inner">
        <nav class="page-breadcrumb">
            <a href="{{ route('home') }}">홈</a>
            <span>›</span>
            <span>{{ $page->title }}</span>
        </nav>
        <h1 class="page-hero-title">{{ $page->title }}</h1>
        <div class="page-hero-accent"></div>
    </div>
</div>

{{-- Content card --}}
<div class="page-body-wrap">
    <div class="page-card">
        <div class="page-content-body">
            {!! $page->content !!}
        </div>

        <div class="page-back-bar">
            <a href="{{ route('home') }}" class="page-back-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                홈으로 돌아가기
            </a>
            <span class="page-meta-footer">{{ App\Models\Setting::get('site_name', 'Laraboard') }}</span>
        </div>
    </div>
</div>

@endsection
