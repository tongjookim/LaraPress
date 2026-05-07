<?php

namespace App\Http\Controllers;

use App\Models\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'role:admin']);
    }

    public function index(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $days = in_array($days, [7, 30, 90]) ? $days : 30;
        $from = now()->subDays($days - 1)->startOfDay();

        // 일별 방문자 수 (고유 IP)
        $dailyVisits = SiteVisit::select(
                'date',
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT ip) as unique_visitors')
            )
            ->where('date', '>=', $from->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy(fn($r) => $r->date->format('Y-m-d'));

        // 날짜 범위 채우기 (방문 없는 날도 0으로)
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $dates[$d] = [
                'date'             => $d,
                'visits'           => $dailyVisits[$d]->visits ?? 0,
                'unique_visitors'  => $dailyVisits[$d]->unique_visitors ?? 0,
            ];
        }

        // 합계
        $totalVisits   = array_sum(array_column($dates, 'visits'));
        $maxVisits     = max(array_column($dates, 'visits') ?: [1]);

        // 기기 통계
        $deviceStats = SiteVisit::select('device_type', DB::raw('COUNT(*) as cnt'))
            ->where('date', '>=', $from->toDateString())
            ->groupBy('device_type')
            ->pluck('cnt', 'device_type')
            ->toArray();

        $deviceTotal = array_sum($deviceStats);

        // 유입 경로 (외부 레퍼러 도메인 TOP 10)
        $referrers = SiteVisit::select('referrer_domain', DB::raw('COUNT(*) as cnt'))
            ->where('date', '>=', $from->toDateString())
            ->whereNotNull('referrer_domain')
            ->groupBy('referrer_domain')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // 인기 페이지 TOP 10
        $topPages = SiteVisit::select('path', DB::raw('COUNT(*) as cnt'))
            ->where('date', '>=', $from->toDateString())
            ->groupBy('path')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // 오늘 / 어제 방문자
        $today     = SiteVisit::whereDate('date', now()->toDateString())->count();
        $yesterday = SiteVisit::whereDate('date', now()->subDay()->toDateString())->count();

        return view('admin.statistics', compact(
            'dates', 'days', 'totalVisits', 'maxVisits',
            'deviceStats', 'deviceTotal',
            'referrers', 'topPages',
            'today', 'yesterday'
        ));
    }
}
