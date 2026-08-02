<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutSuccessPageTest extends TestCase
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
    public function direct_navigation_without_the_checkout_success_flag_redirects_home()
    {
        $this->get(route('sytatsu.webstore.checkout.success'))
            ->assertRedirect(route('sytatsu.webstore.welcome'));
    }

    #[Test]
    public function the_success_page_renders_on_the_legitimate_first_visit_despite_the_cart_being_empty()
    {
        // Lunar spins up a fresh, empty cart for the session as soon as an order completes,
        // so this reproduces the exact post-payment state the success page must still handle.
        $this->withSession(['checkout_success' => true])
            ->get(route('sytatsu.webstore.checkout.success'))
            ->assertOk();
    }

}
