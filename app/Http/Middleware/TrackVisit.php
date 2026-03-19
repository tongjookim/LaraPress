<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;

class TrackVisit
{
    // 추적 제외 패턴
    private const SKIP_PATHS = [
        '/admin',
        '/login',
        '/logout',
        '/register',
        '/upload',
        '/sitemap',
        '/feed',
    ];

    private const BOT_PATTERNS = [
        'bot', 'crawler', 'spider', 'slurp', 'facebookexternalhit',
        'Googlebot', 'bingbot', 'YandexBot', 'DuckDuckBot', 'ia_archiver',
        'curl', 'wget', 'python', 'Go-http-client', 'okhttp',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // GET 요청만 추적
        if (!$request->isMethod('GET')) {
            return $response;
        }

        // 관리자/인증 경로 제외
        $path = $request->path();
        foreach (self::SKIP_PATHS as $skip) {
            if (str_starts_with('/' . $path, $skip)) {
                return $response;
            }
        }

        // 정적 파일 요청 제외 (.js, .css, .png 등)
        if (preg_match('/\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|map|webp)$/i', $path)) {
            return $response;
        }

        // 봇 제외
        $ua = $request->userAgent() ?? '';
        foreach (self::BOT_PATTERNS as $bot) {
            if (stripos($ua, $bot) !== false) {
                return $response;
            }
        }

        try {
            $ip   = $request->ip();
            $date = now()->toDateString();

            // 같은 IP + 날짜 + 경로 중복 방지
            $exists = SiteVisit::where('ip', $ip)
                ->where('date', $date)
                ->where('path', '/' . $path)
                ->exists();

            if (!$exists) {
                $referrer = $request->headers->get('referer');
                $referrerDomain = null;
                if ($referrer) {
                    $parsed = parse_url($referrer);
                    $referrerDomain = $parsed['host'] ?? null;
                    // 자체 도메인 레퍼러 제외
                    if ($referrerDomain === $request->getHost()) {
                        $referrer = null;
                        $referrerDomain = null;
                    }
                }

                SiteVisit::create([
                    'ip'             => $ip,
                    'date'           => $date,
                    'device_type'    => $this->detectDevice($ua),
                    'referrer'       => $referrer ? substr($referrer, 0, 500) : null,
                    'referrer_domain'=> $referrerDomain,
                    'path'           => '/' . $path,
                ]);
            }
        } catch (\Throwable) {
            // 추적 실패가 서비스에 영향 없도록 무시
        }

        return $response;
    }

    private function detectDevice(string $ua): string
    {
        if (preg_match('/tablet|ipad|playbook|silk/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|windows phone/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }
}
