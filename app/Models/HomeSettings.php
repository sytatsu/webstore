<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasTranslations;

class HomeSettings extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'title',
        'sub_title',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'title' => 'array',
        'sub_title' => 'array',
    ];

    public function homeCollections()
    {
        return $this->hasMany(HomeCollection::class, 'home_setting_id')->orderBy('position');
    }
}
