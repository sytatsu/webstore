<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The default collection group was created with handle "main" (mirroring
     * Lunar's own installer default), but navigation_collection_groups and
     * the collection seeders all expect a "printed" handle (see
     * 2026_04_12_183402_ensure_webstore_settings_exist and
     * 2026_04_06_171507_rename_main_collection_to_printed, which only
     * renamed the root Collection, not its CollectionGroup). Since nothing
     * had handle "printed", the nav bar silently showed no collections.
     * Renaming in place keeps existing collections' group_id intact.
     */
    public function up(): void
    {
        $mainGroupId = DB::table('lunar_collection_groups')->where('handle', 'main')->value('id');
        $printedExists = DB::table('lunar_collection_groups')->where('handle', 'printed')->exists();

        if ($mainGroupId && ! $printedExists) {
            DB::table('lunar_collection_groups')->where('id', $mainGroupId)->update([
                'handle' => 'printed',
                'name' => 'Printed',
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('lunar_collection_groups')->where('handle', 'printed')->update([
            'handle' => 'main',
            'name' => 'Main',
            'updated_at' => now(),
        ]);
    }
};
