<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as BaseTestCase;
use Tests\Traits\FactoryTrait;

class FeatureTestCase extends BaseTestCase
{
    use RefreshDatabase, FactoryTrait;

    public Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }
}
