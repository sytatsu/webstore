<?php

use App\Enums\ShippingCarrierEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        DB::table('delivery_options')->insert([
            [
                'carrier' => ShippingCarrierEnum::POSTNL->value,
                'name' => 'Basic Delivery - PostNL',
                'description' => 'Sending items in 1-2 business days with Track & Trace',
                'identifier' => 'NLD_BASDEL',
                'price' => 600,
                'free_shipping' => false,
                'enabled' => true,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
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
                'created_at' => $now,
                'updated_at' => $now,
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
                'created_at' => $now,
                'updated_at' => $now,
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
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('delivery_options')->whereIn('identifier', [
            'NLD_BASDEL', 'NLD_FREETARDEL', 'DHL_BASDEL', 'DHL_FREETARDEL',
        ])->delete();
    }
};
