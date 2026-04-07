<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Collection as LunarCollection;

class Collection extends LunarCollection
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lunar_collections';

    /**
     * Create a new instance of the Model.
     */
    public function __construct(array $attributes = [])
    {
        // Skip Lunar\Base\BaseModel's constructor and go to Eloquent\Model
        Model::__construct($attributes);

        if ($connection = config('lunar.database.connection')) {
            $this->setConnection($connection);
        }
    }
}
