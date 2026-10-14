<?php

namespace Tests\Feature;

use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Tests\TestCase;

class HomepageNewestProductsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'default' => true]);
        Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
        ProductType::factory()->create();
    }

    /** @test */
    public function newest_products_only_include_products_with_an_image()
    {
        $collection = Collection::factory()->create();

        $withImage = Product::factory()->create(['status' => 'published', 'created_at' => now()->subDay()]);
        $withoutImage = Product::factory()->create(['status' => 'published', 'created_at' => now()]);

        $collection->products()->attach([$withImage->id => ['position' => 1], $withoutImage->id => ['position' => 2]]);

        $withImage->media()->create([
            'uuid' => (string) Str::uuid(),
            'collection_name' => config('lunar.media.collection'),
            'name' => 'image',
            'file_name' => 'image.jpg',
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'size' => 1,
            'manipulations' => [],
            'custom_properties' => ['primary' => true],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);

        $products = app(ProductRepository::class)->getOrderedByCreatedAt([$collection->id], 4);

        $this->assertSame([$withImage->id], $products->pluck('id')->all());
    }
}
