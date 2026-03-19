<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->canAccessAdmin()) {
            abort(403, '접근 권한이 없습니다.');
        }

        return $next($request);
    }
}

// app/Http/Kernel.php에 등록:
// protected $middlewareAliases = [
//     'admin' => \App\Http\Middleware\AdminMiddleware::class,
// ];