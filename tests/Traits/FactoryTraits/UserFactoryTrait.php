<?php

namespace Tests\Traits\FactoryTraits;

use App\Models\User;

trait UserFactoryTrait
{
    public function createUser(): User
    {
        return User::factory()->create();
    }
}
