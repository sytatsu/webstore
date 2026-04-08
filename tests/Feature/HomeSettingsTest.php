<?php

namespace Tests\Feature;

use App\Models\HomeSettings;
use App\Models\NotificationBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Channel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeSettingsTest extends TestCase
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
    public function welcome_page_uses_active_settings()
    {
        $group = CollectionGroup::factory()->create();
        $collection = Collection::factory()->create([
            'collection_group_id' => $group->id,
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Featured Collection'),
            ]),
        ]);

        $settings = HomeSettings::create([
            'name' => 'Test Settings',
            'title' => 'Custom Welcome Title',
            'sub_title' => 'Custom Subtitle Text',
            'is_active' => true,
        ]);

        $settings->homeCollections()->create([
            'collection_id' => $collection->id,
            'position' => 1,
        ]);

        $this->get(route('sytatsu.welcome'))
            ->assertStatus(200)
            ->assertSee('Custom Welcome Title')
            ->assertSee('Custom Subtitle Text');
    }

    #[Test]
    public function welcome_page_uses_default_when_no_active_settings()
    {
        $this->get(route('sytatsu.welcome'))
            ->assertStatus(200)
            ->assertSee('Print & Shop'); // Default title in Welcome component
    }

    #[Test]
    public function notification_banner_shows_when_active_and_within_time()
    {
        NotificationBanner::create([
            'name' => 'Banner Settings',
            'is_active' => true,
            'banner_text' => ['en' => 'Flash Sale!'],
            'banner_icon' => 'heroicon-o-sparkles',
            'banner_start_at' => now()->subDay(),
            'banner_end_at' => now()->addDay(),
        ]);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\NotificationBanner::class)
            ->assertSee('Flash Sale!')
            ->assertSee('<svg', false);
    }

    #[Test]
    public function notification_banner_does_not_show_when_inactive()
    {
        NotificationBanner::create([
            'name' => 'Banner Settings',
            'is_active' => false,
            'banner_text' => ['en' => 'Flash Sale!'],
        ]);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\NotificationBanner::class)
            ->assertDontSee('Flash Sale!');
    }

    #[Test]
    public function notification_banner_does_not_show_outside_time_range()
    {
        NotificationBanner::create([
            'name' => 'Banner Settings',
            'is_active' => true,
            'banner_text' => ['en' => 'Future Sale!'],
            'banner_start_at' => now()->addDay(),
            'banner_end_at' => now()->addWeeks(2),
        ]);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\NotificationBanner::class)
            ->assertDontSee('Future Sale!');

        NotificationBanner::query()->delete();

        NotificationBanner::create([
            'name' => 'Past Settings',
            'is_active' => true,
            'banner_text' => ['en' => 'Past Sale!'],
            'banner_start_at' => now()->subWeeks(2),
            'banner_end_at' => now()->subDay(),
        ]);

        Livewire::test(\App\Http\Livewire\Sytatsu\Components\NotificationBanner::class)
            ->assertDontSee('Past Sale!');
    }

    #[Test]
    public function notification_banner_shows_translated_text()
    {
        NotificationBanner::create([
            'name' => 'Banner Settings',
            'is_active' => true,
            'banner_text' => [
                'en' => 'English Text',
                'nl' => 'Nederlandse Tekst',
            ],
            'banner_start_at' => now()->subDay(),
            'banner_end_at' => now()->addDay(),
        ]);

        app()->setLocale('en');
        Livewire::test(\App\Http\Livewire\Sytatsu\Components\NotificationBanner::class)
            ->assertSee('English Text')
            ->assertDontSee('Nederlandse Tekst');

        app()->setLocale('nl');
        Livewire::test(\App\Http\Livewire\Sytatsu\Components\NotificationBanner::class)
            ->assertSee('Nederlandse Tekst')
            ->assertDontSee('English Text');
    }
}
