<?php

namespace App\Console\Commands\Seeders;

use App\Enums\ShippingCarrierEnum;
use App\Models\DeliveryOption;
use Illuminate\Console\Command;

class SeedDeliveryOptionsCommand extends Command
{
    protected $signature = 'webstore:seed:delivery-options';

    protected $description = 'Seed the default delivery options';

    public function handle(): void
    {
        $options = [
            [
                'carrier' => ShippingCarrierEnum::POSTNL->value,
                'name' => 'Basic Delivery - PostNL',
                'description' => 'Sending items in 1-2 business days with Track & Trace',
                'identifier' => 'NLD_BASDEL',
                'price' => 600,
                'free_shipping' => false,
                'enabled' => true,
                'sort_order' => 0,
            ],
            [
                'carrier' => ShippingCarrierEnum::POSTNL->value,
                'name' => 'Free Tracked Delivery - PostNL',
                'description' => 'Sending items within 1-2 business days with Track & Trace',
                'identifier' => 'NLD_FREETARDEL',
                'price' => 0,
                'free_shipping' => true,
                'enabled' => true,
                'sort_order' => 1,
            ],
            [
                'carrier' => ShippingCarrierEnum::DHL->value,
                'name' => 'Basic Delivery - DHL',
                'description' => 'Sending items in 1-2 business days with Track & Trace',
                'identifier' => 'DHL_BASDEL',
                'price' => 600,
                'free_shipping' => false,
                'enabled' => true,
                'sort_order' => 0,
            ],
            [
                'carrier' => ShippingCarrierEnum::DHL->value,
                'name' => 'Free Tracked Delivery - DHL',
                'description' => 'Sending items within 1-2 business days with Track & Trace',
                'identifier' => 'DHL_FREETARDEL',
                'price' => 0,
                'free_shipping' => true,
                'enabled' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($options as $option) {
            DeliveryOption::updateOrCreate(
                ['identifier' => $option['identifier']],
                $option,
            );
        }

        $this->components->info('Delivery options seeded');
    }
}
