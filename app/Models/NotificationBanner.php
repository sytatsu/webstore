<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasTranslations;

class NotificationBanner extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'is_active',
        'banner_text',
        'banner_icon',
        'banner_url',
        'banner_start_at',
        'banner_end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'banner_text' => 'array',
        'banner_start_at' => 'datetime',
        'banner_end_at' => 'datetime',
    ];
}
