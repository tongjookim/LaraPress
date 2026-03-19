# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laraboard is a Laravel 10 BBS (bulletin board system) with multi-board support, a pluggable skin/theme system, and an admin panel. The app is localized in Korean (locale: `ko`, timezone: `Asia/Seoul`).

## Common Commands

```bash
# Initial setup
composer setup          # copies .env.example, dumps autoload, generates key

# Development
npm run dev             # Vite dev server (CSS/JS hot reload)
npm run build           # Production asset build

# Database
php artisan migrate
php artisan tinker

# Code style
./vendor/bin/pint       # Laravel Pint (PSR-12 formatter/linter)

# Tests
./vendor/bin/phpunit    # PHPUnit (no tests exist yet)
```

## Build & Asset Notes

- Document root is the **project root** (not `/public` as in standard Laravel).
- `build/` is a **symlink** pointing to the Vite output directory. Do not delete or recreate it as a real directory.
- `@vite(...)` directives in Blade templates resolve against this symlink.

## Architecture

### Request Flow
`index.php` → Laravel bootstrap → `routes/web.php` → Controller → Blade view via skin path

### Skin System
Views are resolved dynamically based on database settings, not hardcoded paths.

- **Layout skins**: `resources/views/skin/layout/{skin}/` — controls the site-wide chrome. Each skin contains `main.blade.php` (base layout), `head.blade.php`, `navigation.blade.php`, `footer.blade.php`, `home.blade.php`, `list.blade.php`. `main.blade.php` dynamically `@include`s the skin's own head/navigation/footer and `@yield('content')`.
- **Board skins**: `resources/views/skin/board/{skin}/` — per-board rendering (list, view, write, edit). Each `Board` model has a `skin` column.

The active layout skin is read via `Setting::get('layout_skin', 'basic')` and the home route resolves `skin.layout.{$layoutSkin}.home` with a fallback to `basic`.

Available skins: `basic` (both layout and board), `swn-style` (layout only).

### Admin Panel Layout
Admin views use a WordPress-style layout at `resources/views/admin/layout.blade.php`. Child views extend it with `@extends('admin.layout')` and put content in `@section('admin-content')`. The layout includes a fixed top bar (32px), fixed left sidebar (220px), and inline CSS (no separate admin Tailwind file — all styles are in the layout `<style>` block). CSS class naming follows WP conventions: `wp-topbar`, `wp-sidebar`, `wp-main`, `wp-btn`, `wp-list-table`, etc.

### Key Models
- **Board**: identified by `board_id` (slug, used in URLs), has `skin`, `posts_per_page`, and `allow_comments` fields.
- **Post**: belongs to Board and User; has `is_notice` (pinned) and `view_count`.
- **Setting**: key-value store (`Setting::get('key', 'default')`); used for site-wide config including `layout_skin`.
- **User**: `is_admin` boolean; authorization handled via `AdminMiddleware` and `PostPolicy`/`CommentPolicy`.

### Route Structure
- `GET /bbs/{boardId}/...` — board routes (all require `auth`)
- `GET /admin/...` — admin routes (require `auth` + `admin` middleware)
- `GET /page/{slug}` — static pages (must be last route, after all others)
- `GET /debug-info` — JSON status endpoint

### Admin Panel
`AdminController` manages users, boards, posts, pages, and site settings. Protected by `app/Http/Middleware/AdminMiddleware.php` which checks `$user->is_admin`.
