<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\CartService;
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

class BundleCartTest extends TestCase
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

    private function makeBundle(array $tiers = [['min_quantity' => 1, 'price' => 1.50], ['min_quantity' => 4, 'price' => 1.25]]): Bundle
    {
        $collection = Collection::factory()->create();
        $bundle = Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Mini-friends bundle'],
            'enabled' => true,
        ]);

        app(BundleService::class)->syncPurchasable($bundle, $tiers);

        return $bundle;
    }

    private function makeProduct(int $stock = 10): ProductVariant
    {
        $product = Product::factory()->create();

        return ProductVariant::factory()->create([
            'product_id' => $product->id,
            'purchasable' => 'in_stock',
            'stock' => $stock,
        ]);
    }

    /** @test */
    public function adding_a_bundle_creates_exactly_one_cart_line_with_the_matched_tier_price()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeProduct();
        $owl = $this->makeProduct();

        $result = app(BundleService::class)->addToCart($bundle, [
            $fox->id => 2,
            $owl->id => 2,
        ]);

        $this->assertSame([], $result['errors']);

        $lines = app(CartService::class)->mapCartLines();
        $this->assertCount(1, $lines);

        $line = $lines[0];
        $this->assertSame(4, $line['quantity']);
        $this->assertSame(125, $line['purchasable']->pricing()->qty(4)->get()->matched->price->value);
        $this->assertSame($bundle->id, $line['meta']['bundle']['bundle_id']);
        $this->assertCount(2, $line['meta']['bundle']['items']);
    }

    /** @test */
    public function stock_is_clamped_when_a_selection_exceeds_available_stock()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeProduct(stock: 1);

        $result = app(BundleService::class)->addToCart($bundle, [
            $fox->id => 5,
        ]);

        $this->assertNotEmpty($result['errors']);

        $lines = app(CartService::class)->mapCartLines();
        $this->assertSame(1, $lines[0]['quantity']);
    }

    /** @test */
    public function out_of_stock_items_are_dropped_from_the_selection()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeProduct(stock: 0);
        $owl = $this->makeProduct(stock: 5);

        $result = app(BundleService::class)->addToCart($bundle, [
            $fox->id => 2,
            $owl->id => 2,
        ]);

        $this->assertNotEmpty($result['errors']);

        $lines = app(CartService::class)->mapCartLines();
        $this->assertSame(2, $lines[0]['quantity']);
        $this->assertCount(1, $lines[0]['meta']['bundle']['items']);
    }

    /** @test */
    public function editing_a_bundle_line_replaces_it_instead_of_adding_a_second_line()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeProduct();
        $owl = $this->makeProduct();

        app(BundleService::class)->addToCart($bundle, [$fox->id => 2]);
        $lineId = app(CartService::class)->mapCartLines()[0]['id'];

        $result = app(BundleService::class)->updateCartLine($lineId, $bundle, [
            $fox->id => 2,
            $owl->id => 2,
        ]);

        $this->assertSame([], $result['errors']);

        $lines = app(CartService::class)->mapCartLines();
        $this->assertCount(1, $lines, 'editing a bundle line must not leave a duplicate line behind');
        $this->assertSame(4, $lines[0]['quantity']);
        $this->assertCount(2, $lines[0]['meta']['bundle']['items']);
    }

    /** @test */
    public function selection_for_cart_line_seeds_from_an_existing_bundle_line()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeProduct();

        app(BundleService::class)->addToCart($bundle, [$fox->id => 3]);
        $lineId = app(CartService::class)->mapCartLines()[0]['id'];

        $selection = app(BundleService::class)->selectionForCartLine($lineId, $bundle);

        $this->assertSame([$fox->id => 3], $selection);
    }
}
