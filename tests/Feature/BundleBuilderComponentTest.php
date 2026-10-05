<?php

namespace Tests\Feature;

use App\Http\Livewire\Sytatsu\Components\Bundle\BundleBuilder;
use App\Http\Livewire\Sytatsu\Components\Bundle\BundlePickControl;
use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Tests\TestCase;

class BundleBuilderComponentTest extends TestCase
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

    private function makeBundle(): Bundle
    {
        $collection = Collection::factory()->create();
        $bundle = Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Mini-friends bundle'],
            'enabled' => true,
        ]);

        app(BundleService::class)->syncPurchasable($bundle, [
            ['min_quantity' => 1, 'price' => 1.50],
            ['min_quantity' => 4, 'price' => 1.25],
        ]);

        return $bundle;
    }

    private function makeVariant(int $stock = 10): ProductVariant
    {
        $product = Product::factory()->create();

        return ProductVariant::factory()->create([
            'product_id' => $product->id,
            'purchasable' => 'in_stock',
            'stock' => $stock,
        ]);
    }

    /** @test */
    public function picking_items_via_the_cross_component_event_and_adding_to_cart_resets_the_tray()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant();

        // This is exactly the event a BundlePickControl tile dispatches —
        // see App\Http\Livewire\Sytatsu\Components\Bundle\BundlePickControl::emitPick().
        Livewire::test(BundleBuilder::class, ['bundle' => $bundle])
            ->dispatch('bundle-item-picked', variantId: $fox->id, quantity: 2)
            ->assertSet('selection', [$fox->id => 2])
            ->call('addToCart')
            ->assertSet('added', true)
            ->assertSet('addedWasEdit', false)
            ->assertSet('selection', []);

        $this->assertCount(1, app(CartService::class)->mapCartLines());
    }

    /** @test */
    public function a_stock_clamp_still_counts_as_added_so_the_tray_does_not_resubmit()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(stock: 1);

        $component = Livewire::test(BundleBuilder::class, ['bundle' => $bundle])
            ->dispatch('bundle-item-picked', variantId: $fox->id, quantity: 5)
            ->call('addToCart');

        $component->assertSet('added', true);
        $this->assertNotEmpty($component->get('bundleErrors'), 'a clamp still produces a warning message');
        $this->assertCount(1, app(CartService::class)->mapCartLines());
        $this->assertSame(1, app(CartService::class)->mapCartLines()[0]['quantity']);
    }

    /** @test */
    public function editing_seeds_the_selection_and_updating_replaces_the_original_line()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant();
        $owl = $this->makeVariant();

        app(BundleService::class)->addToCart($bundle, [$fox->id => 2]);
        $lineId = app(CartService::class)->mapCartLines()[0]['id'];

        Livewire::test(BundleBuilder::class, ['bundle' => $bundle, 'editLineId' => $lineId])
            ->assertSet('selection', [$fox->id => 2])
            ->assertSet('editingLineId', $lineId)
            ->dispatch('bundle-item-picked', variantId: $owl->id, quantity: 2)
            ->call('addToCart')
            ->assertSet('added', true)
            ->assertSet('addedWasEdit', true);

        $lines = app(CartService::class)->mapCartLines();
        $this->assertCount(1, $lines, 'editing must not leave the original line behind');
        $this->assertSame(4, $lines[0]['quantity']);
    }

    /** @test */
    public function pick_control_stops_incrementing_at_available_stock_and_emits_the_pick_event()
    {
        $fox = $this->makeVariant(stock: 2);

        Livewire::test(BundlePickControl::class, ['product' => $fox->product])
            ->assertSet('available', 2)
            ->call('increment')
            ->assertSet('quantity', 1)
            ->assertDispatched('bundle-item-picked', variantId: $fox->id, quantity: 1)
            ->call('increment')
            ->assertSet('quantity', 2)
            ->call('increment')
            ->assertSet('quantity', 2, 'must not exceed available stock');
    }

    /** @test */
    public function pick_control_stays_in_sync_when_the_builder_broadcasts_a_new_selection()
    {
        $fox = $this->makeVariant();

        Livewire::test(BundlePickControl::class, ['product' => $fox->product])
            ->assertSet('quantity', 0)
            ->dispatch('bundle-selection-updated', selection: [$fox->id => 3])
            ->assertSet('quantity', 3)
            ->dispatch('bundle-selection-updated', selection: [])
            ->assertSet('quantity', 0);
    }
}
