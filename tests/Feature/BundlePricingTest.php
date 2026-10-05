<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Services\BundleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Tests\TestCase;

/**
 * Proves the hidden-variant + Lunar price-break approach actually does
 * "every item in the bundle is priced at the highest tier reached" with
 * zero custom pricing code — see docs/bundles/README.md.
 */
class BundlePricingTest extends TestCase
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

    private function makeBundle(array $tiers): Bundle
    {
        $collection = Collection::factory()->create();
        $bundle = Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Test bundle'],
            'enabled' => true,
        ]);

        app(BundleService::class)->syncPurchasable($bundle, $tiers);

        return $bundle;
    }

    /** @test */
    public function tier_price_applies_at_and_above_its_threshold_and_not_below_it()
    {
        $bundle = $this->makeBundle([
            ['min_quantity' => 1, 'price' => 1.50],
            ['min_quantity' => 4, 'price' => 1.25],
        ]);

        $this->assertSame(150, $bundle->priceForQuantity(1)->price->value);
        $this->assertSame(150, $bundle->priceForQuantity(2)->price->value);
        $this->assertSame(150, $bundle->priceForQuantity(3)->price->value);
        $this->assertSame(125, $bundle->priceForQuantity(4)->price->value);
        $this->assertSame(125, $bundle->priceForQuantity(5)->price->value);
    }

    /** @test */
    public function three_tiers_resolve_to_the_highest_reached_threshold()
    {
        $bundle = $this->makeBundle([
            ['min_quantity' => 1, 'price' => 2.00],
            ['min_quantity' => 2, 'price' => 1.50],
            ['min_quantity' => 4, 'price' => 1.25],
        ]);

        $this->assertSame(200, $bundle->priceForQuantity(1)->price->value);
        $this->assertSame(150, $bundle->priceForQuantity(2)->price->value);
        $this->assertSame(150, $bundle->priceForQuantity(3)->price->value);
        $this->assertSame(125, $bundle->priceForQuantity(4)->price->value);
        $this->assertSame(125, $bundle->priceForQuantity(10)->price->value);
    }

    /** @test */
    public function next_tier_reports_the_next_unlockable_threshold()
    {
        $bundle = $this->makeBundle([
            ['min_quantity' => 1, 'price' => 1.50],
            ['min_quantity' => 4, 'price' => 1.25],
        ]);

        $this->assertSame(4, $bundle->nextTier(2)->min_quantity);
        $this->assertNull($bundle->nextTier(4));
    }

    /** @test */
    public function saving_tiers_again_replaces_rather_than_duplicates_them()
    {
        $bundle = $this->makeBundle([
            ['min_quantity' => 1, 'price' => 1.50],
            ['min_quantity' => 4, 'price' => 1.25],
        ]);

        app(BundleService::class)->syncPurchasable($bundle, [
            ['min_quantity' => 1, 'price' => 1.00],
        ]);

        $this->assertCount(1, $bundle->tiers());
        $this->assertSame(100, $bundle->priceForQuantity(10)->price->value);
    }
}
