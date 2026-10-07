<?php

namespace Tests\Feature;

use App\Http\Livewire\Sytatsu\Components\Cart\Components\CartItems;
use App\Models\Bundle;
use App\Services\BundleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Tests\TestCase;

/**
 * Renders the two bundle-aware Blade surfaces that were only ever reviewed
 * by eye in this session — the order confirmation email's table partial
 * and the admin "bundle contents" preview modal — with a real OrderLine
 * carrying the exact meta['bundle'] shape BundleService::buildMeta()
 * produces. This is the category of bug this feature kept surfacing only
 * on execution (a draft-status product crashing getThumbnail(), a
 * Repeater's UUID-keyed default item): "looks correct on read" isn't
 * enough for Blade/mail templates in this codebase.
 */
class BundleOrderRenderingTest extends TestCase
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

    private function makeBundleOrderLine(): OrderLine
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

        $fox = Product::factory()->create();
        $foxVariant = ProductVariant::factory()->create(['product_id' => $fox->id, 'purchasable' => 'in_stock', 'stock' => 10]);
        $owl = Product::factory()->create();
        $owlVariant = ProductVariant::factory()->create(['product_id' => $owl->id, 'purchasable' => 'in_stock', 'stock' => 10]);

        $selection = [$foxVariant->id => 2, $owlVariant->id => 2];
        $bundleMeta = app(BundleService::class)->buildMeta($bundle, $selection);

        $order = Order::factory()->create();

        return OrderLine::factory()->create([
            'order_id' => $order->id,
            'purchasable_type' => $bundle->variant->getMorphClass(),
            'purchasable_id' => $bundle->variant->id,
            'description' => 'Bundle: Mini-friends bundle',
            'quantity' => 4,
            'meta' => ['bundle' => $bundleMeta],
        ]);
    }

    /** @test */
    public function order_confirmation_table_renders_the_bundle_line_without_error()
    {
        $line = $this->makeBundleOrderLine();
        $order = $line->order()->with('lines')->first();

        $html = view('mail.sytatsu.orders.includes.order-table', ['order' => $order])->render();

        $this->assertStringContainsString('Mini-friends bundle', $html);
        $this->assertStringContainsString((string) $line->meta['bundle']['items'][0]['quantity'], $html);
    }

    /** @test */
    public function admin_bundle_preview_modal_renders_the_picked_items_without_error()
    {
        $line = $this->makeBundleOrderLine();

        $html = view('filament.orders.bundle-preview', ['bundle' => $line->meta['bundle']])->render();

        $this->assertStringContainsString('Mini-friends bundle', $html);
        $this->assertStringContainsString((string) $line->meta['bundle']['items'][0]['quantity'], $html);
    }

    /** @test */
    public function the_checkout_success_page_renders_the_bundle_line_with_its_picked_items()
    {
        // This page has its own hand-rolled line loop (not the
        // order-table.blade.php partial the email and admin preview
        // share), so a bundle branch was missed here entirely until this
        // was actually looked at — same class of bug as the search-box
        // dropdown's own separate price-display code path.
        $line = $this->makeBundleOrderLine();
        $order = $line->order()->with('lines')->first();

        $html = view('sytatsu.webstore.order-success', ['order' => $order])->render();

        $this->assertStringContainsString('Mini-friends bundle', $html);
        $this->assertStringContainsString($line->meta['bundle']['items'][0]['name'], $html);
        $this->assertStringContainsString($line->meta['bundle']['items'][1]['name'], $html);
        $this->assertStringContainsString((string) $line->meta['bundle']['items'][0]['quantity'], $html);
    }

    /** @test */
    public function the_cart_page_renders_a_bundle_line_with_its_thumbnail_strip_and_edit_link()
    {
        $collection = Collection::factory()->create();
        $collection->urls()->create([
            'slug' => 'mini-friends',
            'default' => true,
            'language_id' => Language::getDefault()->id,
        ]);

        $bundle = Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Mini-friends bundle'],
            'enabled' => true,
        ]);
        app(BundleService::class)->syncPurchasable($bundle, [
            ['min_quantity' => 1, 'price' => 1.50],
            ['min_quantity' => 4, 'price' => 1.25],
        ]);

        $fox = Product::factory()->create();
        $foxVariant = ProductVariant::factory()->create(['product_id' => $fox->id, 'purchasable' => 'in_stock', 'stock' => 10]);

        app(BundleService::class)->addToCart($bundle, [$foxVariant->id => 2]);

        Livewire::test(CartItems::class)
            ->assertSee('Mini-friends bundle')
            ->assertSee(__('Edit'));
    }
}
