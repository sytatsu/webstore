<?php

namespace Tests\Feature;

use Lunar\FieldTypes\Text;
use App\Http\Livewire\Sytatsu\Components\Navigation;
use App\Filament\Resources\WebstoreSettingResource\Pages\ManageWebstoreSettings;
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

        $this->useEnglishAsDefaultLanguage();

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
            'attribute_data' => collect(['name' => new Text('Printed Collection')]),
        ]);

        $collectionOther = Collection::factory()->create([
            'collection_group_id' => $groupOther->id,
            'attribute_data' => collect(['name' => new Text('Other Collection')]),
        ]);

        // Default setting (should show "printed" only)
        Livewire::test(Navigation::class)
            ->assertSee('Printed Collection')
            ->assertDontSee('Other Collection');

        // Configure to show "other" instead
        WebstoreSetting::setByKey('navigation_collection_groups', ['other']);

        Livewire::test(Navigation::class)
            ->assertSee('Other Collection')
            ->assertDontSee('Printed Collection');

        // Configure to show both
        WebstoreSetting::setByKey('navigation_collection_groups', ['printed', 'other']);

        Livewire::test(Navigation::class)
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
            'attribute_data' => collect(['name' => new Text('Polymaker Collection')]),
        ]);
        $collectionPolymaker->urls()->create([
            'slug' => 'polymaker',
            'default' => true,
            'language_id' => Language::whereDefault(true)->first()->id,
        ]);

        $collectionEsun = Collection::factory()->create([
            'collection_group_id' => $group->id,
            'attribute_data' => collect(['name' => new Text('eSun Collection')]),
        ]);
        $collectionEsun->urls()->create([
            'slug' => 'esun',
            'default' => true,
            'language_id' => Language::whereDefault(true)->first()->id,
        ]);

        // Default should show "Polymaker Collection" if it exists (slugs default to ['polymaker'])
        Livewire::test(Navigation::class)
            ->assertSee('FDM Printing')
            ->assertSee('Polymaker Collection')
            ->assertDontSee('eSun Collection');

        // Configure to show both
        WebstoreSetting::setByKey('navigation_fdm_printing_handles', ['polymaker', 'esun']);

        Livewire::test(Navigation::class)
            ->assertSee('FDM Printing')
            ->assertSee('Polymaker Collection')
            ->assertSee('eSun Collection');

        // Configure to show none (empty array)
        WebstoreSetting::setByKey('navigation_fdm_printing_handles', []);

        Livewire::test(Navigation::class)
            ->assertDontSee('FDM Printing');
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
        Livewire::test(ManageWebstoreSettings::class)
            ->assertSuccessful();
    }
}
