<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSettings extends Model
{
    protected $fillable = [
        'name',
        'title',
        'sub_title',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function homeCollections()
    {
        return $this->hasMany(HomeCollection::class, 'home_setting_id')->orderBy('position');
    }
}
