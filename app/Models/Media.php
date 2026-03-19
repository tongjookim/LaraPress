<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'user_id', 'original_name', 'filename', 'path', 'url', 'mime_type', 'size',
        'alt_text', 'title', 'caption', 'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function formattedSize(): string
    {
        if ($this->size >= 1048576) {
            return round($this->size / 1048576, 1) . ' MB';
        }
        return round($this->size / 1024, 1) . ' KB';
    }
}
