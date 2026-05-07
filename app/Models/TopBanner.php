<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TopBanner extends Model
{
    protected $fillable = [
        'text', 'link_url', 'text_color', 'bg_color',
        'font_size', 'font_weight', 'start_at', 'end_at',
        'reshow_hours', 'is_active', 'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
    ];

    /**
     * 현재 시각 기준으로 노출 가능한 활성 배너 목록
     */
    public static function activeNow()
    {
        $now = Carbon::now();

        return self::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->orderBy('order')
            ->orderBy('id')
            ->get();
    }
}
