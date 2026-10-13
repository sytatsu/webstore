<?php

namespace Tests\Feature;

use App\Http\Livewire\Sytatsu\Components\AddToCart;
use App\Http\Livewire\Sytatsu\Components\Cart\Components\CartItems;
use App\Http\Livewire\Sytatsu\Components\Product\ProductTile;
use App\Pipelines\Lunar\Order\Creation\DecreaseStock;
use App\Services\CartService;
use App\Support\PrintOnDemand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\Components\OrderItemsTable;
use Lunar\Admin\Filament\Resources\ProductResource\Pages\ManageProductInventory;
use Lunar\Admin\Models\Staff;
use Lunar\Exceptions\Carts\CartException;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Tests\TestCase;

class PrintOnDemandTest extends TestCase
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

    private function makeVariant(string $purchasable, int $stock): ProductVariant
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'purchasable' => $purchasable,
            'stock' => $stock,
            'backorder' => 0,
        ]);

        Price::factory()->create([
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'currency_id' => Currency::getDefault()->id,
            'price' => 1000,
            'min_quantity' => 1,
        ]);

        return $variant;
    }

    /** @test */
    public function a_print_on_demand_variant_can_be_added_beyond_its_stock()
    {
        $variant = $this->makeVariant(PrintOnDemand::PURCHASABILITY, stock: 2);

        app(CartService::class)->addLine($variant, 5);

        $lines = app(CartService::class)->mapCartLines();
        $this->assertSame(5, $lines[0]['quantity']);
        $this->assertSame(3, $lines[0]['print_on_demand_quantity']);
    }

    /** @test */
    public function an_in_stock_only_variant_still_cannot_be_added_beyond_its_stock()
    {
        $variant = $this->makeVariant('in_stock', stock: 2);

        $this->expectException(CartException::class);

        app(CartService::class)->addLine($variant, 5);
    }

    /** @test */
    public function placing_an_order_only_takes_the_stocked_part_and_records_the_printed_part()
    {
        $variant = $this->makeVariant(PrintOnDemand::PURCHASABILITY, stock: 2);
        app(CartService::class)->addLine($variant, 5);
        $cart = app(CartService::class)->getCurrentCart();

        $order = Order::factory()->create(['cart_id' => $cart->id]);
        $orderLine = OrderLine::factory()->create([
            'order_id' => $order->id,
            'purchasable_type' => $variant->getMorphClass(),
            'purchasable_id' => $variant->id,
            'quantity' => 5,
            'meta' => null,
        ]);

        (new DecreaseStock)->handle($order->fresh(), fn ($order) => $order);

        $this->assertSame(0, $variant->fresh()->stock);
        $this->assertSame(3, $orderLine->fresh()->meta[PrintOnDemand::META_KEY]);

        $html = view('mail.sytatsu.orders.includes.order-table', ['order' => $order->fresh()])->render();
        $this->assertStringContainsString('3 of 5 printed on demand', $html);
    }

    /** @test */
    public function a_fully_stocked_print_on_demand_order_line_is_not_marked()
    {
        $variant = $this->makeVariant(PrintOnDemand::PURCHASABILITY, stock: 10);
        app(CartService::class)->addLine($variant, 4);
        $cart = app(CartService::class)->getCurrentCart();

        $order = Order::factory()->create(['cart_id' => $cart->id]);
        $orderLine = OrderLine::factory()->create([
            'order_id' => $order->id,
            'purchasable_type' => $variant->getMorphClass(),
            'purchasable_id' => $variant->id,
            'quantity' => 4,
            'meta' => null,
        ]);

        (new DecreaseStock)->handle($order->fresh(), fn ($order) => $order);

        $this->assertSame(6, $variant->fresh()->stock);
        $this->assertArrayNotHasKey(PrintOnDemand::META_KEY, (array) $orderLine->fresh()->meta);
    }

    /** @test */
    public function print_on_demand_can_be_chosen_on_the_admin_inventory_page()
    {
        $this->actingAs(Staff::factory()->create(['admin' => true]), 'staff');
        $variant = $this->makeVariant('in_stock', stock: 2);

        Livewire::test(ManageProductInventory::class, ['record' => $variant->product_id])
            ->assertSee('In stock or print on demand')
            ->set('purchasable', PrintOnDemand::PURCHASABILITY)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(PrintOnDemand::PURCHASABILITY, $variant->fresh()->purchasable);
    }

    /** @test */
    public function the_product_page_tile_and_cart_say_when_an_item_is_printed_on_demand()
    {
        $variant = $this->makeVariant(PrintOnDemand::PURCHASABILITY, stock: 0);

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->assertSee('Print on demand')
            ->assertSee('Printed on demand for your order')
            ->assertDontSee('Sold out');

        Livewire::test(ProductTile::class, ['product' => $variant->product])
            ->assertSee('Print on demand');

        app(CartService::class)->addLine($variant, 2);

        Livewire::test(CartItems::class)
            ->assertSee('Print on demand');
    }

    /** @test */
    public function a_stocked_print_on_demand_item_shows_its_stock_and_only_flags_the_excess()
    {
        $variant = $this->makeVariant(PrintOnDemand::PURCHASABILITY, stock: 2);

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->assertSee('2 Available')
            ->assertDontSee('Printed on demand for your order')
            ->set('quantity', 5)
            ->assertSee('3 of 5 will be printed on demand');

        Livewire::test(ProductTile::class, ['product' => $variant->product])
            ->assertDontSee('Print on demand');
    }

    private function makePrintOnDemandOrder(): Order
    {
        $variant = $this->makeVariant(PrintOnDemand::PURCHASABILITY, stock: 1);
        $order = Order::factory()->create();
        OrderLine::factory()->create([
            'order_id' => $order->id,
            'purchasable_type' => $variant->getMorphClass(),
            'purchasable_id' => $variant->id,
            'quantity' => 4,
            'meta' => [PrintOnDemand::META_KEY => 3],
        ]);
        OrderLine::factory()->create([
            'order_id' => $order->id,
            'purchasable_type' => $variant->getMorphClass(),
            'purchasable_id' => $variant->id,
            'quantity' => 2,
            'meta' => [PrintOnDemand::META_KEY => 2],
        ]);

        return $order->fresh();
    }

    /** @test */
    public function the_admin_order_items_table_flags_lines_to_print()
    {
        $this->actingAs(Staff::factory()->create(['admin' => true]), 'staff');
        $order = $this->makePrintOnDemandOrder();

        Livewire::test(OrderItemsTable::class, ['record' => $order])
            ->assertSee('3 of 4 printed on demand')
            ->assertSee('Print on demand');
    }

    /** @test */
    public function the_checkout_success_page_flags_lines_to_print()
    {
        $html = view('sytatsu.webstore.order-success', ['order' => $this->makePrintOnDemandOrder()])->render();

        $this->assertStringContainsString('3 of 4 printed on demand', $html);
        $this->assertStringContainsString('printed especially for your order', $html);
    }
}
