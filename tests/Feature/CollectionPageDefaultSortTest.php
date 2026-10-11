<?php

namespace Tests\Feature;

use App\Http\Livewire\Sytatsu\Pages\Webstore\CollectionPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\FieldTypes\Text;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Tests\TestCase;

class CollectionPageDefaultSortTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'default' => true]);
        Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    }

    /** @test */
    public function sort_falls_back_to_newest_when_the_collection_has_no_default_sort()
    {
        $collection = Collection::factory()->create([
            'attribute_data' => ['name' => new Text('Shirts')],
        ]);

        Livewire::test(CollectionPage::class, ['collection' => $collection])
            ->assertSet('sort', 'newest')
            ->assertSet('filters.sort', 'newest');
    }

    /** @test */
    public function sort_uses_the_collections_configured_default_sort()
    {
        $collection = Collection::factory()->create([
            'attribute_data' => [
                'name' => new Text('Shirts'),
                'default_sort' => new Text('alphabetical'),
            ],
        ]);

        Livewire::test(CollectionPage::class, ['collection' => $collection])
            ->assertSet('sort', 'alphabetical');
    }
}
