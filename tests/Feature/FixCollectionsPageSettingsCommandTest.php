<?php

namespace Tests\Feature;

use App\Filament\Pages\CollectionsPageSettingsPage;
use App\Models\WebstoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Collection;
use Lunar\Models\Language;
use Tests\TestCase;

class FixCollectionsPageSettingsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $pokeballs;

    protected Collection $miniFriends;

    protected function setUp(): void
    {
        parent::setUp();

        $language = Language::factory()->create(['code' => 'en', 'default' => true]);

        $this->pokeballs = Collection::factory()->create();
        $this->pokeballs->urls()->create(['slug' => 'pokeballs', 'default' => true, 'language_id' => $language->id]);

        $this->miniFriends = Collection::factory()->create();
        $this->miniFriends->urls()->create(['slug' => 'mini-friends', 'default' => true, 'language_id' => $language->id]);
    }

    /**
     * The shape the old WebstoreSettingResource Repeater left behind:
     * ids and `{"collection_id": ...}` rows instead of slugs, plus a
     * plain-string title from before the per-locale shape.
     */
    public function test_it_normalizes_malformed_stored_settings()
    {
        WebstoreSetting::setByKey(CollectionsPageSettingsPage::COLLECTIONS_KEY, [
            (string) $this->miniFriends->id,
            ['collection_id' => (string) $this->pokeballs->id],
            'mini-friends',
            'does-not-exist',
        ]);
        WebstoreSetting::setByKey(CollectionsPageSettingsPage::TITLE_KEY, 'Our collections');

        $this->artisan('webstore:fix-collections-page-settings', ['--force' => true])->assertSuccessful();

        $this->assertSame(['mini-friends', 'pokeballs'], WebstoreSetting::getByKey(CollectionsPageSettingsPage::COLLECTIONS_KEY));
        $this->assertSame(['en' => 'Our collections'], WebstoreSetting::getByKey(CollectionsPageSettingsPage::TITLE_KEY));
        $this->assertSame([], WebstoreSetting::getByKey(CollectionsPageSettingsPage::DESCRIPTION_KEY));
    }

    public function test_passed_options_overwrite_the_stored_settings()
    {
        WebstoreSetting::setByKey(CollectionsPageSettingsPage::COLLECTIONS_KEY, ['mini-friends']);
        WebstoreSetting::setByKey(CollectionsPageSettingsPage::TITLE_KEY, ['en' => 'Old', 'nl' => 'Oud']);

        $this->artisan('webstore:fix-collections-page-settings', [
            '--collections' => ['pokeballs', 'mini-friends'],
            '--title-en' => 'Collections',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertSame(['pokeballs', 'mini-friends'], WebstoreSetting::getByKey(CollectionsPageSettingsPage::COLLECTIONS_KEY));
        $this->assertSame(['en' => 'Collections', 'nl' => 'Oud'], WebstoreSetting::getByKey(CollectionsPageSettingsPage::TITLE_KEY));
    }

    public function test_dry_run_saves_nothing()
    {
        WebstoreSetting::setByKey(CollectionsPageSettingsPage::COLLECTIONS_KEY, [(string) $this->pokeballs->id]);

        $this->artisan('webstore:fix-collections-page-settings', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame([(string) $this->pokeballs->id], WebstoreSetting::getByKey(CollectionsPageSettingsPage::COLLECTIONS_KEY));
    }
}
