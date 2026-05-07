<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'article_id', 'user_id', 'parent_id', 'content', 'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id')->with('user')->orderBy('created_at');
    }
}
