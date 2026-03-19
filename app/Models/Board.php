<?php
// app/Models/Board.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
        'board_id',
        'board_name',
        'skin',
        'posts_per_page',
        'use_comment',
        'is_active',
        'order',
    ];

    protected $casts = [
        'use_comment' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}