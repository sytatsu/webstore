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
        DB::table('webstore_settings')->insertOrIgnore([
            [
                'key' => 'collections_page_collections',
                'value' => json_encode(['pokeballs', 'mini-friends']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('webstore_settings')
            ->whereIn('key', [
                'collections_page_title',
                'collections_page_description',
                'collections_page_collections'
            ])
            ->delete();
    }
};
