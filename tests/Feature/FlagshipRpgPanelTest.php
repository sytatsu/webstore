<?php

namespace Tests\Feature;

use Filament\Auth\Pages\Login as FilamentLogin;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Lunar\Admin\Models\Staff;
use Sytatsu\FlagshipRpg\Filament\Pages\Auth\Login as GameLogin;
use Sytatsu\FlagshipRpg\Models\RpgAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Flagship RPG panel next to the real webshop (Lunar) panel: separate guards,
 * staff can play with their webshop login, game accounts never reach the webshop.
 */
class FlagshipRpgPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_game_account_cannot_log_in_to_the_webshop_panel(): void
    {
        $account = RpgAccount::query()->forceCreate([
            'name' => 'Nova', 'email' => 'nova@example.com', 'password' => 'secret-pass',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('lunar'));

        Livewire::test(FilamentLogin::class)
            ->fillForm(['email' => $account->email, 'password' => 'secret-pass'])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest('staff');
    }

    public function test_staff_log_in_to_the_game_with_their_webshop_account(): void
    {
        $staff = Staff::factory()->create(['email' => 'sam@example.com', 'password' => 'staff-pass']);

        Filament::setCurrentPanel(Filament::getPanel('flagship-rpg'));

        Livewire::test(GameLogin::class)
            ->fillForm(['email' => 'sam@example.com', 'password' => 'staff-pass'])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $account = RpgAccount::query()->where('staff_id', $staff->id)->firstOrFail();
        $this->assertAuthenticatedAs($account, 'rpg');
        $this->assertGuest('staff');

        $this->get('/flagship-rpg')->assertOk();
        $this->get('/lunar')->assertRedirect('/lunar/login');
    }

    public function test_a_webshop_login_does_not_open_the_game(): void
    {
        $this->actingAs(Staff::factory()->create(['admin' => true]), 'staff');

        $this->get('/flagship-rpg')->assertRedirect('/flagship-rpg/login');
    }
}
