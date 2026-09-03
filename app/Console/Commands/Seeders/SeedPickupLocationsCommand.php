<?php

namespace App\Console\Commands\Seeders;

use App\Models\PickupLocation;
use Illuminate\Console\Command;

class SeedPickupLocationsCommand extends Command
{
    protected $signature = 'webstore:seed:pickup-locations';

    protected $description = 'Seed the default pickup locations';

    public function handle(): void
    {
        PickupLocation::updateOrCreate(
            ['identifier' => 'PICKUP_JOUWPLEKJE'],
            [
                'name' => 'JouwPlekje',
                'address_line_1' => 'Dorpsstraat 75',
                'address_line_2' => null,
                'postcode' => '2712 AD',
                'city' => 'Zoetermeer',
                'country' => 'Netherlands',
                'availability_note' => [
                    'en' => "Available for pick-up on Fridays or Saturdays. We'll email you to confirm the exact day.",
                    'nl' => 'Afhalen mogelijk op vrijdag of zaterdag. We laten je per e-mail weten welke dag precies.',
                ],
                'price' => 0,
                'enabled' => true,
                'sort_order' => 0,
            ],
        );

        $this->components->info('Pickup locations seeded');
    }
}
