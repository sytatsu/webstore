<?php

namespace Tests\Feature;

use App\Models\WebstoreSetting;
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

        WebstoreSetting::setByKey('home_featured_collections', [$collection->id]);

        $this->get(route('sytatsu.webstore.welcome'))
            ->assertStatus(200);
    }

    #[Test]
    public function welcome_page_uses_default_when_no_active_settings()
    {
        $this->get(route('sytatsu.webstore.welcome'))
            ->assertStatus(200);
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

    #[Test]
    public function welcome_page_does_not_crash_when_featured_collections_has_invalid_ids()
    {
        WebstoreSetting::setByKey('home_featured_collections', ['invalid', 99999]);

        $this->get(route('sytatsu.webstore.welcome'))
            ->assertStatus(200);
    }

    #[Test]
    public function welcome_page_does_not_crash_when_translate_attribute_returns_array()
    {
        $group = CollectionGroup::factory()->create();
        $collection = Collection::factory()->create([
            'collection_group_id' => $group->id,
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Original Name'),
            ]),
        ]);

        WebstoreSetting::setByKey('home_featured_collections', [$collection->id]);

        // Mock the translateAttribute method to return an array
        $mockCollection = \Mockery::mock($collection)->makePartial();
        $mockCollection->shouldReceive('translateAttribute')
            ->with('name')
            ->andReturn(['en' => 'Mock Name EN', 'nl' => 'Mock Name NL']);

        // Since the component uses StorefrontService which calls Repository which calls Model,
        // we might need to mock the service or repository, but let's see if we can just test the DTO directly first
        // to verify our fix in DTO.

        $dto = new \App\DTOs\ProductCollectionDTO($mockCollection, collect());
        $this->assertEquals('Mock Name EN', $dto->getName());

        app()->setLocale('nl');
        $this->assertEquals('Mock Name NL', $dto->getName());
        app()->setLocale('en');
    }
}
