<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Lunar\Models\Language;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Make English the only default language. The migrations already seed
     * 'nl' (default) and 'en', so reuse that row instead of inserting a duplicate.
     */
    protected function useEnglishAsDefaultLanguage(): Language
    {
        Language::query()->where('code', '!=', 'en')->update(['default' => false]);

        return Language::query()->updateOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'default' => true],
        );
    }
}
