<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeCollection extends Model
{
    protected $fillable = [
        'home_setting_id',
        'collection_id',
        'position',
    ];

    public function homeSetting()
    {
        return $this->belongsTo(HomeSettings::class, 'home_setting_id');
    }

    public function collection()
    {
        return $this->belongsTo(\Lunar\Models\Collection::class, 'collection_id');
    }
}
