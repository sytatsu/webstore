<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Services\BundleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Tests\TestCase;

class BundleEligibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Currency::factory()->create(['code' => 'EUR', 'default' => true, 'decimal_places' => 2]);
        Channel::factory()->create(['default' => true]);
        CustomerGroup::factory()->create(['default' => true]);
        TaxClass::factory()->create(['default' => true]);
        ProductType::factory()->create();
    }

    private function createProductIn(Collection $collection, array $variantAttributes = []): Product
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->create(array_merge([
            'product_id' => $product->id,
            'purchasable' => 'always',
        ], $variantAttributes));
        $collection->products()->attach($product);

        return $product;
    }

    /** @test */
    public function eligible_products_include_items_from_descendant_sub_collections()
    {
        // A collection tree lives inside one collection_group_id (NodeTrait's
        // nested-set scope) — parent/child collections must share it, and
        // parent_id has to be set via a separate save() after creation, not
        // inline in the factory array (matches SeedMiniFriendsCollectionCommand).
        $group = \Lunar\Models\CollectionGroup::factory()->create();
        $miniFriends = Collection::factory()->create(['collection_group_id' => $group->id]);
        $safari = Collection::factory()->create(['collection_group_id' => $group->id]);
        $safari->parent_id = $miniFriends->id;
        $safari->save();
        $ocean = Collection::factory()->create(['collection_group_id' => $group->id]);
        $ocean->parent_id = $miniFriends->id;
        $ocean->save();

        $fox = $this->createProductIn($safari);
        $whale = $this->createProductIn($ocean);
        $elsewhere = $this->createProductIn(Collection::factory()->create());

        $bundle = Bundle::create([
            'collection_id' => $miniFriends->id,
            'name' => ['en' => 'Mini-friends bundle'],
            'enabled' => true,
        ]);

        $eligible = app(BundleService::class)->eligibleProducts($bundle)->pluck('id');

        $this->assertTrue($eligible->contains($fox->id));
        $this->assertTrue($eligible->contains($whale->id));
        $this->assertFalse($eligible->contains($elsewhere->id));
    }

    /** @test */
    public function out_of_stock_and_unpublished_products_are_not_eligible()
    {
        $collection = Collection::factory()->create();

        $outOfStock = $this->createProductIn($collection, ['purchasable' => 'in_stock', 'stock' => 0]);
        $unpublished = Product::factory()->create(['status' => 'draft']);
        ProductVariant::factory()->create(['product_id' => $unpublished->id, 'purchasable' => 'always']);
        $collection->products()->attach($unpublished);
        $inStock = $this->createProductIn($collection, ['purchasable' => 'in_stock', 'stock' => 5]);

        $eligible = app(BundleService::class)->eligibleProducts($bundle = Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Bundle'],
            'enabled' => true,
        ]))->pluck('id');

        $this->assertFalse($eligible->contains($outOfStock->id));
        $this->assertFalse($eligible->contains($unpublished->id));
        $this->assertTrue($eligible->contains($inStock->id));
    }

    /** @test */
    public function sub_collection_page_surfaces_the_bundle_configured_on_its_parent()
    {
        $group = \Lunar\Models\CollectionGroup::factory()->create();
        $miniFriends = Collection::factory()->create(['collection_group_id' => $group->id]);
        $safari = Collection::factory()->create(['collection_group_id' => $group->id]);
        $safari->parent_id = $miniFriends->id;
        $safari->save();

        $bundle = Bundle::create([
            'collection_id' => $miniFriends->id,
            'name' => ['en' => 'Mini-friends bundle'],
            'enabled' => true,
        ]);

        $found = app(BundleService::class)->findActiveForCollection($safari->fresh());

        $this->assertNotNull($found);
        $this->assertSame($bundle->id, $found->id);
    }

    /** @test */
    public function disabled_bundles_are_not_found()
    {
        $collection = Collection::factory()->create();

        Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Bundle'],
            'enabled' => false,
        ]);

        $this->assertNull(app(BundleService::class)->findActiveForCollection($collection));
    }
}
