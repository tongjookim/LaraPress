@extends('admin.layout')

@section('title', '통계')

@section('admin-content')
<h1 class="wp-page-title">접속 통계</h1>

{{-- 기간 선택 --}}
<div style="margin-bottom:16px;display:flex;align-items:center;gap:8px;">
    @foreach([7 => '7일', 30 => '30일', 90 => '90일'] as $d => $label)
    <a href="{{ route('admin.statistics', ['days' => $d]) }}"
       class="wp-btn {{ $days == $d ? 'wp-btn-primary' : 'wp-btn-secondary' }}">
        {{ $label }}
    </a>
    @endforeach
    <span style="font-size:13px;color:#646970;margin-left:8px;">
        최근 {{ $days }}일 기준
    </span>
</div>

{{-- 요약 카드 --}}
<div class="admin-grid-4">
    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:#2271b1;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div>
                <div style="font-size:26px;font-weight:900;color:#1d2327;line-height:1;">{{ number_format($totalVisits) }}</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">{{ $days }}일 총 방문</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:#00a32a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size:26px;font-weight:900;color:#1d2327;line-height:1;">{{ number_format($today) }}</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">오늘 방문</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:#8c5ae3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div style="font-size:26px;font-weight:900;color:#1d2327;line-height:1;">{{ number_format($yesterday) }}</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">어제 방문</div>
            </div>
        </div>
    </div>

    @php
        $desktopPct = $deviceTotal > 0 ? round(($deviceStats['desktop'] ?? 0) / $deviceTotal * 100) : 0;
        $mobilePct  = $deviceTotal > 0 ? round(($deviceStats['mobile']  ?? 0) / $deviceTotal * 100) : 0;
        $tabletPct  = $deviceTotal > 0 ? round(($deviceStats['tablet']  ?? 0) / $deviceTotal * 100) : 0;
    @endphp
    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:#dba617;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div style="font-size:26px;font-weight:900;color:#1d2327;line-height:1;">{{ $mobilePct }}%</div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">모바일 비율</div>
            </div>
        </div>
    </div>
</div>

{{-- 일별 방문 차트 --}}
<div class="wp-widget" style="margin-bottom:16px;">
    <div class="wp-widget-header">📈 일별 방문자 추이 (최근 {{ $days }}일)</div>
    <div class="wp-widget-body">
        @if($totalVisits === 0)
        <div style="text-align:center;padding:40px;color:#8c8f94;font-size:13px;">아직 수집된 데이터가 없습니다.</div>
        @else
        <div style="display:flex;align-items:flex-end;gap:2px;height:160px;padding-bottom:24px;position:relative;border-bottom:1px solid #e0e0e0;">
            @foreach($dates as $d => $row)
            @php
                $h = $maxVisits > 0 ? max(4, round($row['visits'] / $maxVisits * 140)) : 4;
                $isToday = $d === now()->format('Y-m-d');
            @endphp
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;group;"
                 title="{{ $d }}: {{ $row['visits'] }}회 방문 (고유 {{ $row['unique_visitors'] }}명)">
                <div style="width:100%;background:{{ $isToday ? '#2271b1' : '#c3d9f3' }};height:{{ $h }}px;border-radius:2px 2px 0 0;transition:background .15s;"
                     onmouseover="this.style.background='#2271b1'" onmouseout="this.style.background='{{ $isToday ? '#2271b1' : '#c3d9f3' }}'"></div>
                @if($days <= 30)
                <div style="position:absolute;bottom:-20px;font-size:9px;color:#8c8f94;white-space:nowrap;transform:rotate(-45deg);transform-origin:top left;">
                    {{ \Carbon\Carbon::parse($d)->format('m/d') }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @if($days > 30)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#8c8f94;margin-top:4px;">
            <span>{{ array_key_first($dates) }}</span>
            <span>{{ array_key_last($dates) }}</span>
        </div>
        @else
        <div style="height:20px;"></div>
        @endif
        @endif
    </div>
</div>

{{-- 2열 하단 --}}
<div class="admin-grid-2" style="margin-bottom:16px;">

    {{-- 기기 통계 --}}
    <div class="wp-widget">
        <div class="wp-widget-header">📱 접속 기기</div>
        <div class="wp-widget-body">
            @if($deviceTotal === 0)
            <div style="text-align:center;padding:20px;color:#8c8f94;font-size:13px;">데이터 없음</div>
            @else
            @php
                $devices = [
                    'desktop' => ['label' => 'PC / 데스크톱', 'color' => '#2271b1', 'pct' => $desktopPct],
                    'mobile'  => ['label' => '모바일',        'color' => '#00a32a', 'pct' => $mobilePct],
                    'tablet'  => ['label' => '태블릿',        'color' => '#dba617', 'pct' => $tabletPct],
                ];
            @endphp
            {{-- 도넛형 비율 바 --}}
            <div style="display:flex;border-radius:4px;overflow:hidden;height:20px;margin-bottom:16px;">
                @foreach($devices as $key => $dev)
                @if(($deviceStats[$key] ?? 0) > 0)
                <div style="width:{{ $dev['pct'] }}%;background:{{ $dev['color'] }};transition:width .3s;" title="{{ $dev['label'] }}: {{ $dev['pct'] }}%"></div>
                @endif
                @endforeach
            </div>
            @foreach($devices as $key => $dev)
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;font-size:13px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:12px;height:12px;border-radius:2px;background:{{ $dev['color'] }};flex-shrink:0;"></div>
                    <span>{{ $dev['label'] }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <span style="color:#646970;">{{ number_format($deviceStats[$key] ?? 0) }}회</span>
                    <span style="font-weight:700;min-width:36px;text-align:right;">{{ $dev['pct'] }}%</span>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>

    {{-- 유입 경로 --}}
    <div class="wp-widget">
        <div class="wp-widget-header">🔗 유입 경로 TOP 10</div>
        <div class="wp-widget-body" style="padding:0;">
            @if($referrers->isEmpty())
            <div style="text-align:center;padding:20px;color:#8c8f94;font-size:13px;">외부 유입 데이터 없음</div>
            @else
            @php $maxRef = $referrers->first()->cnt; @endphp
            <table class="wp-list-table">
                <thead>
                    <tr>
                        <th>도메인</th>
                        <th style="text-align:right;">방문</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referrers as $ref)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="flex:1;background:#f0f0f1;border-radius:2px;height:6px;overflow:hidden;">
                                    <div style="width:{{ round($ref->cnt / $maxRef * 100) }}%;height:100%;background:#2271b1;border-radius:2px;"></div>
                                </div>
                                <span style="font-size:12px;color:#2c3338;flex-shrink:0;">{{ $ref->referrer_domain }}</span>
                            </div>
                        </td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($ref->cnt) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

{{-- 인기 페이지 --}}
<div class="wp-widget">
    <div class="wp-widget-header">📄 인기 페이지 TOP 10</div>
    <div class="wp-widget-body" style="padding:0;">
        @if($topPages->isEmpty())
        <div style="text-align:center;padding:20px;color:#8c8f94;font-size:13px;">데이터 없음</div>
        @else
        @php $maxPage = $topPages->first()->cnt; @endphp
        <table class="wp-list-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>페이지 경로</th>
                    <th style="text-align:right;">방문</th>
                    <th style="text-align:right;">비율</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topPages as $i => $page)
                <tr>
                    <td style="color:#8c8f94;font-size:12px;width:28px;">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;max-width:300px;background:#f0f0f1;border-radius:2px;height:6px;overflow:hidden;">
                                <div style="width:{{ round($page->cnt / $maxPage * 100) }}%;height:100%;background:#00a32a;border-radius:2px;"></div>
                            </div>
                            <a href="{{ $page->path }}" target="_blank" style="color:#2271b1;text-decoration:none;font-size:13px;font-family:monospace;">
                                {{ Str::limit($page->path, 60) }}
                            </a>
                        </div>
                    </td>
                    <td style="text-align:right;font-weight:600;">{{ number_format($page->cnt) }}</td>
                    <td style="text-align:right;color:#646970;font-size:12px;">
                        {{ $totalVisits > 0 ? round($page->cnt / $totalVisits * 100, 1) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

@endsection
