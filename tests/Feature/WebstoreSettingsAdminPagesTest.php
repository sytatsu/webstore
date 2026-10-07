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
            collect($component->get('data.elements'))
                ->where('type', HomeFeaturedCollectionsSettingsPage::TYPE_COLLECTION)
                ->pluck('collection_id')
                ->values()
                ->all()
        );
        $component->call('save');

        $this->assertSame(
            [
                ['type' => 'collection', 'collection_id' => $collectionA->id],
                ['type' => 'collection', 'collection_id' => $collectionB->id],
            ],
            WebstoreSetting::getByKey(HomeFeaturedCollectionsSettingsPage::SETTING_KEY)
        );
    }

    /**
     * The Clickerz Bar CTA is one more row an admin can drag into the
     * same ordered list as the featured collections (not a separate
     * fixed-position toggle) — confirms a `clickerz` row saved between
     * two collection rows round-trips in that exact position.
     */
    public function test_the_homepage_elements_page_saves_a_clickerz_row_in_its_chosen_position()
    {
        $collectionA = Collection::factory()->create();
        $collectionB = Collection::factory()->create();

        $staff = Staff::factory()->create(['admin' => true]);
        $this->actingAs($staff, 'staff');
        Filament::setCurrentPanel(Filament::getPanel('lunar'));

        $component = Livewire::test(HomeFeaturedCollectionsSettingsPage::class)
            ->set('data.elements', [
                ['type' => 'collection', 'collection_id' => $collectionA->id],
                ['type' => 'clickerz'],
                ['type' => 'collection', 'collection_id' => $collectionB->id],
            ])
            ->call('save');

        $this->assertSame(
            [
                ['type' => 'collection', 'collection_id' => $collectionA->id],
                ['type' => 'clickerz'],
                ['type' => 'collection', 'collection_id' => $collectionB->id],
            ],
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

    /**
     * The Clickerz Bar link used to always render between the Collections
     * and FDM Printing dropdowns, hardcoded in navigation.blade.php. It's
     * now one more entry in NavigationSettingsPage::TOP_LEVEL_ORDER_KEY —
     * confirms the storefront nav actually follows that stored order,
     * same class of bug this settings area kept hitting before (a
     * "reorder" control nothing downstream honored).
     */
    public function test_reordering_top_level_nav_items_moves_the_clickerz_link()
    {
        $group = CollectionGroup::factory()->create(['handle' => 'printed', 'name' => 'Printed']);
        Collection::factory()->create([
            'collection_group_id' => $group->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('Printed Collection')]),
        ]);
        WebstoreSetting::setByKey(NavigationSettingsPage::GROUPS_KEY, ['printed']);

        WebstoreSetting::setByKey(NavigationSettingsPage::TOP_LEVEL_ORDER_KEY, ['clickerz', 'collections', 'services']);
        $html = Livewire::test(Navigation::class)->html();
        $this->assertTrue(
            strpos($html, 'Clickerz Bar') < strpos($html, 'Printed Collection'),
            'expected the Clickerz Bar link before the Collections dropdown when listed first'
        );

        WebstoreSetting::setByKey(NavigationSettingsPage::TOP_LEVEL_ORDER_KEY, ['collections', 'services', 'clickerz']);
        $html = Livewire::test(Navigation::class)->html();
        $this->assertTrue(
            strpos($html, 'Printed Collection') < strpos($html, 'Clickerz Bar'),
            'expected the Clickerz Bar link after the Collections dropdown once listed last'
        );
    }

    /**
     * Removing an entry from the top-level order hides it regardless of
     * its own gate — proves the stored order is an actual whitelist, not
     * just a sort key on top of the old always-on checks.
     */
    public function test_removing_clickerz_from_the_top_level_order_hides_it_even_though_bar_builder_is_enabled()
    {
        $this->assertTrue(\App\Filament\Pages\BarBuilderSettingsPage::isEnabled());

        WebstoreSetting::setByKey(NavigationSettingsPage::TOP_LEVEL_ORDER_KEY, ['collections', 'services']);

        $html = Livewire::test(Navigation::class)->html();
        $this->assertStringNotContainsString('Clickerz Bar', $html);
    }
}
