<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'user_id', 'title', 'subtitle', 'slug',
        'content', 'excerpt', 'thumbnail', 'status', 'published_at',
        'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'focus_keyword',
    ];

    protected $guarded = ['id', 'view_count'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'     => '초안',
            'pending'   => '승인 대기',
            'published' => '게시됨',
            default     => $this->status,
        };
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'draft'     => 'wp-badge-inactive',
            'pending'   => 'wp-badge' . ' ' . 'wp-badge-pending',
            'published' => 'wp-badge-active',
            default     => 'wp-badge',
        };
    }

    /**
     * 제목으로부터 slug를 생성한다. 한글은 romanize 없이 시간 기반 suffix를 붙인다.
     */
    public static function makeSlug(string $title, ?int $exceptId = null): string
    {
        $base = Str::slug($title);

        // 한글 등 비ASCII 제목은 Str::slug()가 빈 문자열을 반환하므로
        // 유니코드 문자를 그대로 살려 슬래그를 만든다
        if (empty($base)) {
            $base = preg_replace('/[^\p{L}\p{N}]+/u', '-', mb_strtolower(trim($title)));
            $base = trim($base, '-');
        }

        if (empty($base)) {
            $base = 'article';
        }

        $slug = $base;
        $counter = 1;
        while (
            static::where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
