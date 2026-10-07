<?php

namespace Tests\Feature;

use App\Http\Livewire\Sytatsu\Components\AddToCart;
use App\Http\Livewire\Sytatsu\Components\Bundle\BundleBuilder;
use App\Http\Livewire\Sytatsu\Components\Product\ProductTile;
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

    private function makeVariant(int $stock = 10, ?Bundle $inBundle = null): ProductVariant
    {
        $product = Product::factory()->create();

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'purchasable' => 'in_stock',
            'stock' => $stock,
        ]);

        if ($inBundle) {
            $inBundle->collection->products()->attach($product->id);
        }

        return $variant;
    }

    /** @test */
    public function picking_items_via_the_cross_component_event_and_adding_to_cart_resets_the_tray()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant();

        // This is exactly the event AddToCart dispatches when adding to a
        // bundle — see App\Http\Livewire\Sytatsu\Components\AddToCart::addToBundle().
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
    public function add_to_cart_becomes_add_to_bundle_for_a_bundle_eligible_product_and_caps_at_stock()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(stock: 2, inBundle: $bundle);

        Livewire::test(AddToCart::class, ['purchasable' => $fox])
            ->assertSet('activeBundle.id', $bundle->id)
            ->assertSet('bundleAvailable', 2)
            ->call('addToBundle')
            ->assertSet('bundleQuantity', 1)
            ->assertDispatched('bundle-item-picked', variantId: $fox->id, quantity: 1)
            // The tray's own "pulse" animation (bundle-builder.blade.php)
            // listens for this, separate from bundle-item-picked above
            // since that one also fires on a decrement/removal.
            ->assertDispatched('bundle-item-added')
            ->call('addToBundle')
            ->assertSet('bundleQuantity', 2)
            ->assertDispatched('bundle-item-added')
            ->call('addToBundle')
            ->assertSet('bundleQuantity', 2, 'must not exceed available stock')
            ->assertNotDispatched('bundle-item-added', 'the stock-capped call added nothing, so no pulse');

        $this->assertSame([$fox->id => 2], app(BundleService::class)->getSessionSelection($bundle));
    }

    /** @test */
    public function removing_from_the_bundle_does_not_trigger_the_trays_added_pulse()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);

        Livewire::test(AddToCart::class, ['purchasable' => $fox])
            ->call('addToBundle')
            ->assertDispatched('bundle-item-added')
            ->call('removeFromBundleOne')
            ->assertNotDispatched('bundle-item-added')
            ->call('addToBundle')
            ->assertDispatched('bundle-item-added')
            ->call('removeFromBundle')
            ->assertNotDispatched('bundle-item-added');
    }

    /** @test */
    public function removing_from_the_add_to_cart_toggle_clears_the_session_selection()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);

        Livewire::test(AddToCart::class, ['purchasable' => $fox])
            ->call('addToBundle')
            ->call('addToBundle')
            ->assertSet('bundleQuantity', 2)
            ->call('removeFromBundle')
            ->assertSet('bundleQuantity', 0);

        $this->assertSame([], app(BundleService::class)->getSessionSelection($bundle));
    }

    /** @test */
    public function add_to_cart_stays_in_sync_when_the_tray_broadcasts_a_new_selection()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);

        Livewire::test(AddToCart::class, ['purchasable' => $fox])
            ->assertSet('bundleQuantity', 0)
            ->dispatch('bundle-selection-updated', selection: [$fox->id => 3])
            ->assertSet('bundleQuantity', 3)
            ->dispatch('bundle-selection-updated', selection: [])
            ->assertSet('bundleQuantity', 0);
    }

    /** @test */
    public function a_bundle_locked_product_cannot_be_added_to_the_cart_normally_even_if_the_action_is_called_directly()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);

        // The button for this is gone from the Blade, but the server
        // action itself must also refuse — defence in depth against a
        // stale client still firing the old "addToCart" call.
        Livewire::test(AddToCart::class, ['purchasable' => $fox])
            ->call('addToCart');

        $this->assertCount(0, app(CartService::class)->mapCartLines());
    }

    /** @test */
    public function a_product_outside_any_bundle_keeps_the_normal_add_to_cart_behaviour()
    {
        $fox = $this->makeVariant();
        $fox->prices()->create([
            'currency_id' => \Lunar\Models\Currency::getDefault()->id,
            'customer_group_id' => null,
            'min_quantity' => 1,
            'price' => 495,
        ]);

        Livewire::test(AddToCart::class, ['purchasable' => $fox])
            ->assertSet('activeBundle', null)
            ->call('addToCart');

        $this->assertCount(1, app(CartService::class)->mapCartLines());
    }

    /** @test */
    public function the_collection_page_always_shows_the_tray_and_an_add_to_bundle_button_never_a_plain_add_to_cart_one()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);
        $fox->product->urls()->create([
            'slug' => 'fox',
            'default' => true,
            'language_id' => \Lunar\Models\Language::getDefault()->id,
        ]);

        \Livewire\Livewire::test(\App\Http\Livewire\Sytatsu\Pages\Webstore\CollectionPage::class, ['collection' => $bundle->collection])
            // The tray's own empty-selection copy — present with no toggle
            // needed to reveal it, and no "Start building" button exists.
            ->assertSee('Pick products below to start your bundle.')
            ->assertDontSee('Start building')
            ->assertDontSee('Add to shopping cart')
            ->assertSee('Add to bundle');
    }

    /** @test */
    public function editing_an_existing_line_from_the_collection_page_seeds_that_products_add_to_cart_stepper()
    {
        // This is the one thing nothing else exercises: BundleBuilder::mount()
        // writes the edited line's selection into the session, and on the
        // very same request every eligible tile's own AddToCart::mount()
        // reads that session to seed its stepper — all within one page
        // render, relying on BundleBuilder being rendered (and thus mounted)
        // before the product grid in collection.blade.php.
        $bundle = $this->makeBundle();
        $bundle->collection->urls()->create([
            'slug' => 'mini-friends',
            'default' => true,
            'language_id' => \Lunar\Models\Language::getDefault()->id,
        ]);
        $fox = $this->makeVariant(inBundle: $bundle);
        $fox->product->urls()->create([
            'slug' => 'fox',
            'default' => true,
            'language_id' => \Lunar\Models\Language::getDefault()->id,
        ]);

        app(BundleService::class)->addToCart($bundle, [$fox->id => 2]);
        $lineId = app(CartService::class)->mapCartLines()[0]['id'];

        // BundleBuilder::mount() clears the session once a line is added to
        // cart, so without the edit link this product's tile would start
        // back at "Add to bundle" — proving the seed below really comes from
        // the edit flow, not a leftover session value.
        $this->assertSame([], app(BundleService::class)->getSessionSelection($bundle));

        // A real HTTP request (not Livewire::test's own request mocking) so
        // CollectionPage::render()'s request()->integer('edit_bundle_line')
        // reads the actual query string, exactly like the "Edit" link from
        // the cart produces in production.
        $response = $this->get('/collections/mini-friends?edit_bundle_line=' . $lineId);

        // Collection-grid tiles render AddToCart in minimalistic mode, which
        // drops the "Remove" text link (but not the +/- stepper) — so the
        // seeded quantity is what proves this, not that link.
        $response->assertOk();
        $response->assertDontSee('Add to bundle');
        $response->assertSeeHtml('>2<');

        $this->assertSame([$fox->id => 2], app(BundleService::class)->getSessionSelection($bundle));
    }

    /** @test */
    public function a_bundle_eligible_tiles_own_price_is_hidden_since_the_bundle_decides_what_it_costs()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);
        $fox->prices()->create([
            'currency_id' => \Lunar\Models\Currency::getDefault()->id,
            'customer_group_id' => null,
            'min_quantity' => 1,
            'price' => 495,
        ]);

        Livewire::test(ProductTile::class, ['product' => $fox->product])
            ->assertDontSee('4.95');
    }

    /** @test */
    public function a_tile_outside_any_bundle_still_shows_its_own_price()
    {
        $fox = $this->makeVariant();
        $fox->prices()->create([
            'currency_id' => \Lunar\Models\Currency::getDefault()->id,
            'customer_group_id' => null,
            'min_quantity' => 1,
            'price' => 495,
        ]);

        Livewire::test(ProductTile::class, ['product' => $fox->product])
            ->assertSee('4.95');
    }

    /** @test */
    public function a_bundle_eligible_tiles_whole_card_is_an_add_to_bundle_click_target()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);

        $html = Livewire::test(ProductTile::class, ['product' => $fox->product])->html();

        // The forwarding click handler (product-tile.blade.php) and its
        // target (add-to-cart.blade.php's own addToBundle() button/stepper)
        // — can't drive an actual click from a server-rendered-HTML
        // assertion, but this at least catches the markup regressing
        // (e.g. the ref name drifting out of sync between the two files).
        $this->assertStringContainsString('x-ref="bundleAddTrigger"', $html);
        $this->assertStringContainsString("querySelector('[x-ref=bundleAddTrigger]')", $html);
        $this->assertStringContainsString('cursor-pointer', $html);
    }

    /** @test */
    public function a_tile_outside_any_bundle_is_not_a_click_target_and_its_image_still_links_to_the_product()
    {
        $fox = $this->makeVariant();

        $html = Livewire::test(ProductTile::class, ['product' => $fox->product])->html();

        $this->assertStringNotContainsString('bundleAddTrigger', $html);
        $this->assertStringContainsString(
            \App\Services\WebstoreHelperService::getProductRoute($fox->product),
            $html
        );
    }

    /** @test */
    public function the_products_own_detail_page_also_shows_the_tray_and_the_add_to_bundle_button()
    {
        $bundle = $this->makeBundle();
        $bundle->collection->urls()->create([
            'slug' => 'mini-friends',
            'default' => true,
            'language_id' => \Lunar\Models\Language::getDefault()->id,
        ]);
        $fox = $this->makeVariant(inBundle: $bundle);
        $fox->product->urls()->create([
            'slug' => 'fox',
            'default' => true,
            'language_id' => \Lunar\Models\Language::getDefault()->id,
        ]);

        \Livewire\Livewire::test(\App\Http\Livewire\Sytatsu\Pages\Webstore\ProductPage::class, ['product' => $fox->product])
            ->assertSee('Pick products below to start your bundle.')
            ->assertDontSee('Add to shopping cart')
            ->assertSee('Add to bundle');
    }

    /** @test */
    public function the_products_own_detail_page_has_a_button_back_to_the_bundles_collection()
    {
        $bundle = $this->makeBundle();
        $bundle->collection->urls()->create([
            'slug' => 'mini-friends',
            'default' => true,
            'language_id' => \Lunar\Models\Language::getDefault()->id,
        ]);
        $fox = $this->makeVariant(inBundle: $bundle);
        $fox->product->urls()->create([
            'slug' => 'fox',
            'default' => true,
            'language_id' => \Lunar\Models\Language::getDefault()->id,
        ]);

        // The tray mounted on this page only shows what's already picked —
        // getting back to the rest of the collection's own pickable
        // products needs this, not just the tray.
        \Livewire\Livewire::test(\App\Http\Livewire\Sytatsu\Pages\Webstore\ProductPage::class, ['product' => $fox->product])
            ->assertSee(__('Back to :name', ['name' => $bundle->getTranslatedName()]))
            ->assertSeeHtml(\App\Services\WebstoreHelperService::getCollectionRoute($bundle->collection));
    }

    /** @test */
    public function a_bundle_collection_gets_a_thin_cta_instead_of_its_product_grid_on_the_homepage()
    {
        $bundle = $this->makeBundle();
        $fox = $this->makeVariant(inBundle: $bundle);

        $dto = new \App\DTOs\ProductCollectionDTO($bundle->collection, collect([$fox->product]));

        \Livewire\Livewire::test(\App\Http\Livewire\Sytatsu\Components\Collection\CollectionCards::class, ['collections' => $dto])
            ->assertSee(__('Bundle deal'))
            ->assertSee(__('Create your bundle'))
            ->assertSeeHtml(\App\Services\WebstoreHelperService::getCollectionRoute($bundle->collection))
            // The product tile itself (its own "add to bundle" button) must
            // not render here — there's no bundle tray on this page to add
            // to, only a link onward to the collection's own page.
            ->assertDontSee(__('Add to bundle'));
    }

    /** @test */
    public function a_non_bundle_collection_still_gets_its_normal_product_grid_on_the_homepage()
    {
        $collection = Collection::factory()->create();
        $product = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $product->id, 'purchasable' => 'in_stock', 'stock' => 10]);
        $collection->products()->attach($product->id);

        $dto = new \App\DTOs\ProductCollectionDTO($collection, collect([$product]));

        \Livewire\Livewire::test(\App\Http\Livewire\Sytatsu\Components\Collection\CollectionCards::class, ['collections' => $dto])
            ->assertDontSee(__('Bundle deal'))
            ->assertDontSee(__('Create your bundle'));
    }
}
