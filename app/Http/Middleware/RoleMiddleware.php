<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * 사용법: middleware('role:author') — author 이상만 허용
     */
    public function handle(Request $request, Closure $next, string $minRole = 'admin')
    {
        $user = auth()->user();

        if (!$user || !$user->hasMinRole($minRole)) {
            if ($request->expectsJson()) {
                abort(403, '권한이 없습니다.');
            }
            abort(403, '접근 권한이 없습니다.');
        }

        return $next($request);
    }
}
