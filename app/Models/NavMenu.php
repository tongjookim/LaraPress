<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavMenu extends Model
{
    protected $fillable = ['label', 'url', 'order', 'is_active', 'target', 'location', 'group'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * 활성화된 헤더 메뉴 항목을 순서대로 반환.
     * DB가 비어있으면 기본 메뉴(홈 + 뉴스 + 게시판)를 자동 생성.
     */
    public static function activeItems()
    {
        $items = self::where('is_active', true)
                     ->where('location', 'header')
                     ->orderBy('order')->get();

        if ($items->isEmpty()) {
            self::seedDefaults();
            $items = self::where('is_active', true)->orderBy('order')->get();
        }

        return $items;
    }

    public static function seedDefaults(): void
    {
        $order = 1;
        self::create(['label' => '홈',  'url' => '/',     'order' => $order++, 'location' => 'header']);
        self::create(['label' => '뉴스', 'url' => '/news', 'order' => $order++, 'location' => 'header']);

        foreach (Board::where('is_active', true)->orderBy('order')->get() as $board) {
            self::create([
                'label'    => $board->board_name,
                'url'      => '/bbs/' . $board->board_id,
                'order'    => $order++,
                'location' => 'header',
            ]);
        }
    }

    /**
     * 활성화된 푸터 메뉴 항목을 그룹별로 반환.
     * ['그룹명' => [NavMenu, ...], ...]
     */
    public static function footerGroups(): array
    {
        $items = self::where('is_active', true)
                     ->where('location', 'footer')
                     ->orderBy('group')
                     ->orderBy('order')
                     ->get();

        $groups = [];
        foreach ($items as $item) {
            $g = $item->group ?: '기타';
            $groups[$g][] = $item;
        }

        return $groups;
    }
}
