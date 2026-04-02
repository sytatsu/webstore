<?php

namespace Tests\Feature;

use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\Price;
use Tests\TestCase;

class CollectionFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create([
            'code' => 'en',
            'default' => true,
        ]);

        Currency::factory()->create([
            'code' => 'EUR',
            'default' => true,
            'decimal_places' => 2,
        ]);

        ProductType::factory()->create();
    }

    /** @test */
    public function products_are_filtered_by_price_and_stock_on_the_same_variant()
    {
        $product = Product::factory()->create();

        // Variant 1: Correct price, out of stock
        $variant1 = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'purchasable' => 'out_of_stock',
            'stock' => 0,
        ]);
        Price::factory()->create([
            'price' => 2000, // 20.00
            'priceable_id' => $variant1->id,
            'priceable_type' => ProductVariant::class,
        ]);

        // Variant 2: In stock, wrong price (too expensive)
        $variant2 = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'purchasable' => 'in_stock',
            'stock' => 10,
        ]);
        Price::factory()->create([
            'price' => 10000, // 100.00
            'priceable_id' => $variant2->id,
            'priceable_type' => ProductVariant::class,
        ]);

        $repo = new ProductRepository();
        $collectionIds = [0]; // Not used for now as we are testing filtering logic

        // Mock a collection join for the test
        // Actually, getOrderedByName joins collection_product.
        // We need to create a collection and attach the product.
        $collection = \Lunar\Models\Collection::factory()->create();
        $collection->products()->attach($product);

        // Filter: price 10-50, in stock only.
        // Product should NOT show because no SINGLE variant satisfies both.
        $filters = [
            'minPrice' => 10,
            'maxPrice' => 50,
            'inStock' => true,
        ];

        $results = $repo->getOrderedByName([$collection->id], null, $filters);

        $this->assertCount(0, $results, 'Product should not be returned if no single variant matches both price and stock filters');

        // Variant 3: Correct price and in stock
        $variant3 = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'purchasable' => 'in_stock',
            'stock' => 5,
        ]);
        Price::factory()->create([
            'price' => 3000, // 30.00
            'priceable_id' => $variant3->id,
            'priceable_type' => ProductVariant::class,
        ]);

        $results = $repo->getOrderedByName([$collection->id], null, $filters);
        $this->assertCount(1, $results, 'Product should be returned if a single variant matches both price and stock filters');
    }
}
