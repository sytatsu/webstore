<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'navigation_collection_groups',
                'value' => json_encode(['printed']),
            ],
            [
                'key' => 'home_title',
                'value' => json_encode([
                    'en' => 'Welcome to our webstore',
                    'nl' => 'Welkom in onze webshop',
                ]),
            ],
            [
                'key' => 'home_sub_title',
                'value' => json_encode([
                    'en' => 'Discover our collection of unique products.',
                    'nl' => 'Ontdek onze collectie unieke producten.',
                ]),
            ],
            [
                'key' => 'home_featured_collections',
                'value' => json_encode([]),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('webstore_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to delete these settings on rollback as they are essential for the webstore.
        // However, if we must:
        // DB::table('webstore_settings')->whereIn('key', [
        //     'navigation_collection_groups',
        //     'home_title',
        //     'home_sub_title',
        //     'home_featured_collections',
        // ])->delete();
    }
};
