<?php

namespace Tests\Feature;

use App\Filament\Pages\BulkInventoryPage;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Admin\Models\Staff;
use Lunar\Models\Brand;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Tests\TestCase;

class BulkInventoryTest extends TestCase
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

        $this->actingAs(Staff::factory()->create(['admin' => true]), 'staff');
        Filament::setCurrentPanel(Filament::getPanel('lunar'));
    }

    protected function variantFor(Brand $brand, array $attributes = []): ProductVariant
    {
        $product = Product::factory()->create(['brand_id' => $brand->id]);

        return ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 5, ...$attributes]);
    }

    public function test_update_by_brand_only_touches_variants_of_that_brand()
    {
        $brandA = Brand::factory()->create();
        $brandB = Brand::factory()->create();
        $a1 = $this->variantFor($brandA, ['stock' => 3]);
        $a2 = $this->variantFor($brandA, ['stock' => 7]);
        $b1 = $this->variantFor($brandB, ['stock' => 4]);

        Livewire::test(BulkInventoryPage::class)
            ->callAction('updateByBrand', [
                'brands' => [$brandA->id],
                'field' => 'stock',
                'mode' => 'add',
                'quantity' => 10,
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(13, $a1->fresh()->stock);
        $this->assertSame(17, $a2->fresh()->stock);
        $this->assertSame(4, $b1->fresh()->stock);
    }

    public function test_update_by_brand_can_set_purchasability()
    {
        $brand = Brand::factory()->create();
        $variant = $this->variantFor($brand, ['purchasable' => 'in_stock']);

        Livewire::test(BulkInventoryPage::class)
            ->callAction('updateByBrand', [
                'brands' => [$brand->id],
                'field' => 'purchasable',
                'purchasable' => 'always',
            ])
            ->assertHasNoActionErrors();

        $this->assertSame('always', $variant->fresh()->purchasable);
    }

    public function test_decreasing_stock_on_selected_rows_never_goes_below_zero()
    {
        $brand = Brand::factory()->create();
        $low = $this->variantFor($brand, ['stock' => 2]);
        $high = $this->variantFor($brand, ['stock' => 9]);

        Livewire::test(BulkInventoryPage::class)
            ->callTableBulkAction('adjustStock', [$low, $high], [
                'mode' => 'subtract',
                'quantity' => 5,
            ])
            ->assertHasNoTableBulkActionErrors();

        $this->assertSame(0, $low->fresh()->stock);
        $this->assertSame(4, $high->fresh()->stock);
    }

    public function test_the_brand_filter_limits_the_table_to_that_brand()
    {
        $brandA = Brand::factory()->create();
        $brandB = Brand::factory()->create();
        $a = $this->variantFor($brandA);
        $b = $this->variantFor($brandB);

        Livewire::test(BulkInventoryPage::class)
            ->filterTable('brand', [$brandA->id])
            ->assertCanSeeTableRecords([$a])
            ->assertCanNotSeeTableRecords([$b]);
    }
}
