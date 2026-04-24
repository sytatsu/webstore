<?php

namespace Tests\Feature;

use App\Models\WebstoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Language;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebstoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create([
            'code' => 'en',
            'default' => true,
        ]);

        Currency::factory()->create([
            'code' => 'EUR',
            'default' => true,
            'decimal_places' => 2,
        ]);

        Channel::factory()->create([
            'handle' => 'default',
            'default' => true,
            'name' => 'Default Channel',
        ]);
    }

    #[Test]
    public function navigation_filters_collections_by_configured_group_handles()
    {
        $groupPrinted = CollectionGroup::factory()->create(['handle' => 'printed', 'name' => 'Printed']);
        $groupOther = CollectionGroup::factory()->create(['handle' => 'other', 'name' => 'Other']);

        $collectionPrinted = Collection::factory()->create([
            'collection_group_id' => $groupPrinted->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('Printed Collection')]),
        ]);

        $collectionOther = Collection::factory()->create([
            'collection_group_id' => $groupOther->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('Other Collection')]),
        ]);

        // Default setting (should show "printed" only)
        Livewire::test(\App\Http\Livewire\Sytatsu\Components\Navigation::class)
            ->assertSee('Printed Collection')
            ->assertDontSee('Other Collection');

        // Configure to show "other" instead
        WebstoreSetting::setByKey('navigation_collection_groups', ['other']);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\Navigation::class)
            ->assertSee('Other Collection')
            ->assertDontSee('Printed Collection');

        // Configure to show both
        WebstoreSetting::setByKey('navigation_collection_groups', ['printed', 'other']);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\Navigation::class)
            ->assertSee('Printed Collection')
            ->assertSee('Other Collection')
            ->assertDontSee('Printed</span>', false)
            ->assertDontSee('Other</span>', false);
    }

    #[Test]
    public function navigation_shows_fdm_printing_collections_from_settings()
    {
        $group = CollectionGroup::factory()->create(['handle' => 'fdm', 'name' => 'FDM']);
        $collectionPolymaker = Collection::factory()->create([
            'collection_group_id' => $group->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('Polymaker Collection')]),
        ]);
        $collectionPolymaker->urls()->create([
            'slug' => 'polymaker',
            'default' => true,
            'language_id' => \Lunar\Models\Language::whereDefault(true)->first()->id,
        ]);

        $collectionEsun = Collection::factory()->create([
            'collection_group_id' => $group->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('eSun Collection')]),
        ]);
        $collectionEsun->urls()->create([
            'slug' => 'esun',
            'default' => true,
            'language_id' => \Lunar\Models\Language::whereDefault(true)->first()->id,
        ]);

        // Default should show "Polymaker Collection" if it exists (slugs default to ['polymaker'])
        Livewire::test(\App\Http\Livewire\Sytatsu\Components\Navigation::class)
            ->assertSee('FDM Printing')
            ->assertSee('Polymaker Collection')
            ->assertDontSee('eSun Collection');

        // Configure to show both
        WebstoreSetting::setByKey('navigation_fdm_printing_handles', ['polymaker', 'esun']);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\Navigation::class)
            ->assertSee('FDM Printing')
            ->assertSee('Polymaker Collection')
            ->assertSee('eSun Collection');

        // Configure to show none (empty array)
        WebstoreSetting::setByKey('navigation_fdm_printing_handles', []);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\Navigation::class)
            ->assertDontSee('FDM Printing');
    }

    #[Test]
    public function navigation_does_not_have_border_on_the_last_group()
    {
        $groupA = CollectionGroup::factory()->create(['handle' => 'a', 'name' => 'A']);
        $groupB = CollectionGroup::factory()->create(['handle' => 'b', 'name' => 'B']);

        Collection::factory()->create([
            'collection_group_id' => $groupA->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('Collection A')]),
        ]);

        Collection::factory()->create([
            'collection_group_id' => $groupB->id,
            'attribute_data' => collect(['name' => new \Lunar\FieldTypes\Text('Collection B')]),
        ]);

        WebstoreSetting::setByKey('navigation_collection_groups', ['a', 'b']);

        $response = Livewire::test(\App\Http\Livewire\Sytatsu\Components\Navigation::class);
        $html = $response->html();

        // Group A should have the border classes
        $this->assertStringContainsString('data-group-handle="a"', $html);
        $this->assertStringContainsString('md:border-r', $html);
        $this->assertTrue((bool) preg_match('/md:border-r([^>]*?)data-group-handle="a"/s', $html) || (bool) preg_match('/data-group-handle="a"([^>]*?)md:border-r/s', $html), 'Group A missing md:border-r');

        // Group B (last) should NOT have the border classes
        $this->assertStringContainsString('data-group-handle="b"', $html);
        $this->assertFalse((bool) preg_match('/data-group-handle="b"([^>]*?)md:border-r/s', $html), 'Group B should not have md:border-r');
    }

    #[Test]
    public function protected_settings_cannot_be_deleted()
    {
        $protectedKey = 'navigation_collection_groups';
        $setting = WebstoreSetting::where('key', $protectedKey)->first();

        if (!$setting) {
             $setting = WebstoreSetting::create(['key' => $protectedKey, 'value' => ['printed']]);
        }

        $this->assertFalse($setting->delete());
        $this->assertDatabaseHas('webstore_settings', ['key' => $protectedKey]);

        $nonProtectedKey = 'some_random_setting';
        $nonProtectedSetting = WebstoreSetting::create(['key' => $nonProtectedKey, 'value' => 'test']);

        $this->assertTrue($nonProtectedSetting->delete());
        $this->assertDatabaseMissing('webstore_settings', ['key' => $nonProtectedKey]);
    }

    #[Test]
    public function setting_resource_table_does_not_crash_with_array_values()
    {
        // Add a setting with a nested array (e.g. translation)
        WebstoreSetting::updateOrCreate(
            ['key' => 'navigation_collection_groups'],
            ['value' => ['printed']]
        );

        // Add a setting with a nested array within a list (e.g. if someone manually corrupted the data or it's a list of translations)
        WebstoreSetting::updateOrCreate(
            ['key' => 'corrupted_setting'],
            ['value' => [['en' => 'Sub-array']]]
        );

        // Add a setting with a list of IDs
        WebstoreSetting::updateOrCreate(
            ['key' => 'home_featured_collections'],
            ['value' => [1, 2, 3]]
        );

        // Test the Filament resource table
        Livewire::test(\App\Filament\Resources\WebstoreSettingResource\Pages\ManageWebstoreSettings::class)
            ->assertSuccessful();
    }
}
