<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('pickup_locations')->insert([
            'name' => 'JouwPlekje',
            'identifier' => 'PICKUP_JOUWPLEKJE',
            'address_line_1' => 'Dorpsstraat 75',
            'address_line_2' => null,
            'postcode' => '2712 AD',
            'city' => 'Zoetermeer',
            'country' => 'Netherlands',
            'availability_note' => json_encode([
                'en' => "Available for pick-up on Fridays or Saturdays. We'll email you to confirm the exact day.",
                'nl' => 'Afhalen mogelijk op vrijdag of zaterdag. We laten je per e-mail weten welke dag precies.',
            ], JSON_UNESCAPED_UNICODE),
            'price' => 0,
            'enabled' => true,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('pickup_locations')->where('identifier', 'PICKUP_JOUWPLEKJE')->delete();
    }
};
