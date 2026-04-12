<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('webstore_settings')->whereIn('key', [
            'home_title',
            'home_sub_title',
        ])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-adding with default values if rolled back.
        DB::table('webstore_settings')->insert([
            [
                'key' => 'home_title',
                'value' => json_encode([
                    'en' => 'Welcome to our webstore',
                    'nl' => 'Welkom in onze webshop',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'home_sub_title',
                'value' => json_encode([
                    'en' => 'Discover our collection of unique products.',
                    'nl' => 'Ontdek onze collectie unieke producten.',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
