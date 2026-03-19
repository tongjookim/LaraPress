<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArticleCategory extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(ArticleCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ArticleCategory::class, 'parent_id')->orderBy('order');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    public static function makeSlug(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name);

        if (empty($base)) {
            $base = preg_replace('/[^\p{L}\p{N}]+/u', '-', mb_strtolower(trim($name)));
            $base = trim($base, '-');
        }

        if (empty($base)) {
            $base = 'category';
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
