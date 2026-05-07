<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'ip',
        'date',
        'device_type',
        'referrer',
        'referrer_domain',
        'path',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
