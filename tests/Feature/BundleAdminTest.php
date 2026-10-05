<?php

namespace Tests\Feature;

use App\Filament\Extensions\OrderItemsTableExtension;
use App\Filament\Resources\BundleResource\Pages\ManageBundles;
use App\Models\Bundle;
use App\Services\BundleService;
use Filament\Facades\Filament;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Livewire;
use Lunar\Admin\Models\Staff;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Tests\TestCase;

class BundleAdminTest extends TestCase
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

    /**
     * Regression test for the exact mistake the shelved feature/bundles
     * branch made: it swapped OrderItemsTableExtension's `extendTable` hook
     * for `extendOrderLinesTableColumns`, which silently deleted the
     * existing barBuilderPreview admin action instead of adding the bundle
     * one alongside it. Both actions must come from the same extendTable().
     *
     * @test
     */
    public function order_items_extension_keeps_the_bar_builder_preview_action_alongside_the_new_bundle_one()
    {
        $livewire = new class extends Component implements HasTable {
            use \Filament\Tables\Concerns\InteractsWithTable;
            use \Filament\Forms\Concerns\InteractsWithForms;

            public function table(Table $table): Table
            {
                return $table;
            }

            public function render()
            {
                return '';
            }
        };

        $table = (new OrderItemsTableExtension())->extendTable(Table::make($livewire));

        $names = collect($table->getActions())->map(fn ($action) => $action->getName());

        $this->assertContains('barBuilderPreview', $names, 'the existing Clickerz Bar admin preview action must still be registered');
        $this->assertContains('bundleContentsPreview', $names, 'the new bundle admin preview action must be registered');
    }

    /** @test */
    public function syncing_tiers_through_the_resource_flow_keeps_a_single_set_of_price_rows()
    {
        $collection = Collection::factory()->create();
        $bundle = Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Bundle'],
            'enabled' => true,
        ]);

        $bundleService = app(BundleService::class);

        // First save, as the CreateAction::using() closure in
        // App\Filament\Resources\BundleResource\Pages\ManageBundles does.
        $bundleService->syncPurchasable($bundle, [
            ['min_quantity' => 1, 'price' => 1.50],
        ]);

        // A later edit, as EditAction::using() does.
        $bundleService->syncPurchasable($bundle, [
            ['min_quantity' => 1, 'price' => 1.50],
            ['min_quantity' => 4, 'price' => 1.25],
        ]);

        $this->assertCount(2, $bundle->tiers());
        $this->assertNotNull($bundle->product_variant_id, 'the hidden pricing variant must only be provisioned once');
    }

    /** @test */
    public function creating_a_bundle_through_the_real_admin_page_provisions_tiers()
    {
        $staff = Staff::factory()->create(['admin' => true]);
        $this->actingAs($staff, 'staff');
        Filament::setCurrentPanel(Filament::getPanel('lunar'));

        $collection = Collection::factory()->create();

        // The repeater's minItems(1) seeds one UUID-keyed blank item on
        // mount, so filling 'tiers' through callAction()'s dot-flattened
        // `data` (or ->fillForm()) leaves that stray empty item behind and
        // fails its own required validation. Replacing the whole array in
        // one ->set() call avoids that.
        Livewire::test(ManageBundles::class)
            ->mountAction('create')
            ->set('mountedActionsData.0.collection_id', $collection->id)
            ->set('mountedActionsData.0.name.en', 'Test bundle')
            ->set('mountedActionsData.0.enabled', true)
            ->set('mountedActionsData.0.tiers', [
                ['min_quantity' => 1, 'price' => 1.50],
                ['min_quantity' => 4, 'price' => 1.25],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $bundle = Bundle::where('collection_id', $collection->id)->firstOrFail();

        $this->assertNotNull($bundle->product_variant_id);
        $this->assertCount(2, $bundle->tiers());
        $this->assertSame(150, $bundle->priceForQuantity(1)->price->value);
        $this->assertSame(125, $bundle->priceForQuantity(4)->price->value);
    }

    /** @test */
    public function editing_a_bundle_through_the_real_admin_page_hydrates_and_resyncs_tiers()
    {
        $staff = Staff::factory()->create(['admin' => true]);
        $this->actingAs($staff, 'staff');
        Filament::setCurrentPanel(Filament::getPanel('lunar'));

        $collection = Collection::factory()->create();
        $bundle = Bundle::create([
            'collection_id' => $collection->id,
            'name' => ['en' => 'Original'],
            'enabled' => true,
        ]);
        app(BundleService::class)->syncPurchasable($bundle, [
            ['min_quantity' => 1, 'price' => 1.50],
        ]);

        $test = Livewire::test(ManageBundles::class)
            ->mountTableAction('edit', $bundle);

        $hydratedTiers = $test->instance()->mountedTableActionsData[0]['tiers'] ?? [];
        $this->assertCount(1, $hydratedTiers, 'editing must hydrate the existing tier(s), not start blank');
        $this->assertSame(1.5, array_values($hydratedTiers)[0]['price']);

        $test->set('mountedTableActionsData.0.tiers', [
                ['min_quantity' => 1, 'price' => 1.50],
                ['min_quantity' => 4, 'price' => 1.25],
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $bundle->refresh();

        $this->assertCount(2, $bundle->tiers());
        $this->assertSame(125, $bundle->priceForQuantity(4)->price->value);
    }
}
