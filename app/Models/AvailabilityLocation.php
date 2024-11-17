<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $label
 */
class AvailabilityLocation extends BaseModel
{
    use HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'label'
    ];

    public function availability(): HasOne
    {
        return $this->hasOne(Availability::class);
    }
}
