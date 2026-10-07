<?php

namespace Tests\Feature;

use App\Filament\Pages\HomeFeaturedCollectionsSettingsPage;
use App\Filament\Pages\NavigationSettingsPage;
use App\Http\Livewire\Sytatsu\Components\Navigation;
use App\Models\WebstoreSetting;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Admin\Models\Staff;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\TaxClass;
use Tests\TestCase;

class WebstoreSettingsAdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Currency::factory()->create(['code' => 'EUR', 'default' => true, 'decimal_places' => 2]);
        Channel::factory()->create(['default' => true]);
        CustomerGroup::factory()->create(['default' => true]);
        TaxClass::factory()->create(['default' => true]);
    }

    /**
     * The malformed shape home_featured_collections had actually rotted
     * into live: a mix of numeric-string ids and `{"collection_id": "..."}`
     * rows left behind by the old resource's hydrate/dehydrate mismatch.
     * The settings page must clean this up on load, not just display it
     * nicely, or the first admin to open it re-saves the same mess.
     */
    public function test_the_homepage_collections_page_normalizes_malformed_stored_data()
    {
        $collectionA = Collection::factory()->create();
        $collectionB = Collection::factory()->create();

        WebstoreSetting::setByKey(HomeFeaturedCollectionsSettingsPage::SETTING_KEY, [
            (string) $collectionA->id,
            ['collection_id' => (string) $collectionB->id],
            (string) $collectionA->id, // duplicate, from a past partial save
        ]);

        $staff = Staff::factory()->create(['admin' => true]);
        $this->actingAs($staff, 'staff');
        Filament::setCurrentPanel(Filament::getPanel('lunar'));

        // Repeater state is keyed by a generated UUID per row, not a
        // plain 0/1 index, so assert on the values only.
        $component = Livewire::test(HomeFeaturedCollectionsSettingsPage::class);
        $this->assertSame(
            [$collectionA->id, $collectionB->id],
            collect($component->get('data.collections'))->pluck('collection_id')->values()->all()
        );
        $component->call('save');

        $this->assertSame(
            [$collectionA->id, $collectionB->id],
            WebstoreSetting::getByKey(HomeFeaturedCollectionsSettingsPage::SETTING_KEY)
        );
    }

    /**
     * NavigationSettingsPage's groups Repeater is reorderable, but that's
     * only meaningful if the storefront actually renders in that order —
     * the underlying query (whereIn('handle', ...)) only filters, it
     * doesn't honor the array's order. Confirms Navigation.php's own
     * re-sort makes the admin-chosen order the one customers see.
     */
    public function test_reordering_navigation_groups_changes_the_order_collections_actually_appear_in()
    {
        $groupPrinted = CollectionGroup::factory()->create(['handle' => 'printed', 'name' => 'Printed']);
        $groupFdm = CollectionGroup::factory()->create(['handle' => 'fdm-printing', 'name' => 'FDM Printing']);

        Collection::factory()->create([
            'collection_group_id' => $groupPrinted->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('Printed Collection')]),
        ]);

        Collection::factory()->create([
            'collection_group_id' => $groupFdm->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('FDM Collection')]),
        ]);

        WebstoreSetting::setByKey(NavigationSettingsPage::GROUPS_KEY, ['printed', 'fdm-printing']);
        $html = Livewire::test(Navigation::class)->html();
        $this->assertTrue(
            strpos($html, 'Printed Collection') < strpos($html, 'FDM Collection'),
            'expected Printed Collection before FDM Collection when printed is listed first'
        );

        WebstoreSetting::setByKey(NavigationSettingsPage::GROUPS_KEY, ['fdm-printing', 'printed']);
        $html = Livewire::test(Navigation::class)->html();
        $this->assertTrue(
            strpos($html, 'FDM Collection') < strpos($html, 'Printed Collection'),
            'expected FDM Collection before Printed Collection once fdm-printing is listed first'
        );
    }
}
