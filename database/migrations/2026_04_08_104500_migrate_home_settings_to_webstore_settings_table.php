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
        $activeSettings = DB::table('home_settings')->where('is_active', true)->first();

        if ($activeSettings) {
            // Migrate Title
            DB::table('webstore_settings')->updateOrInsert(
                ['key' => 'home_title'],
                [
                    'value' => $activeSettings->title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Migrate Subtitle
            DB::table('webstore_settings')->updateOrInsert(
                ['key' => 'home_sub_title'],
                [
                    'value' => $activeSettings->sub_title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Migrate Featured Collections
            $collections = DB::table('home_collections')
                ->where('home_setting_id', $activeSettings->id)
                ->orderBy('position')
                ->pluck('collection_id')
                ->toArray();

            DB::table('webstore_settings')->updateOrInsert(
                ['key' => 'home_featured_collections'],
                [
                    'value' => json_encode($collections),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Schema::dropIfExists('home_collections');
        Schema::dropIfExists('home_settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreating dropped tables would be complex and incomplete as we lose multiple records from home_settings.
        // For now, only dropping the new settings if we roll back.
        DB::table('webstore_settings')->whereIn('key', [
            'home_title',
            'home_sub_title',
            'home_featured_collections',
        ])->delete();
    }
};
