<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Admin\Models\Staff;
use Tests\TestCase;

/**
 * Confirms the Filament auto-discovery wiring (app/Filament/Clusters/Bundles.php
 * + app/Filament/Resources/BundleResource.php) actually resolves to a real,
 * reachable admin page — the Livewire-level tests in BundleAdminTest exercise
 * the resource's form/action logic directly, but never hit real HTTP routing.
 */
class BundleAdminHttpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_bundles_admin_page_is_reachable()
    {
        $staff = Staff::factory()->create(['admin' => true]);

        $response = $this->actingAs($staff, 'staff')->get('/lunar/bundles/bundles');

        $response->assertSuccessful();
        $response->assertSeeText('Bundles');
    }
}
