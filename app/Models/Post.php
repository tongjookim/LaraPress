<?php
// app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'board_id',
        'user_id',
        'title',
        'content',
    ];

    protected $guarded = [
        'id',
        'view_count',
        'is_notice',
    ];

    protected $casts = [
        'is_notice' => 'boolean',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function incrementViewCount()
    {
        $key = 'viewed_posts';
        $viewed = session($key, []);

        if (!in_array($this->id, $viewed)) {
            $this->increment('view_count');
            $viewed[] = $this->id;
            session([$key => $viewed]);
        }
    }
}