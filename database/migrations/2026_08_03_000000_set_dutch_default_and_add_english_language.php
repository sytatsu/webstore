<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 2026_04_06_175603_add_dutch_language.php inserted Dutch without ever
     * marking it default, so Language::getDefault() returns null and
     * Lunar's UrlGenerator crashes the first time a product/collection is
     * created. This corrects that and adds English as the secondary
     * language, matching the intended Dutch-primary/English-secondary setup.
     */
    public function up(): void
    {
        DB::table('lunar_languages')->updateOrInsert(
            ['code' => 'nl'],
            [
                'name' => 'Dutch',
                'default' => 1,
                'updated_at' => now(),
            ]
        );

        DB::table('lunar_languages')->updateOrInsert(
            ['code' => 'en'],
            [
                'name' => 'English',
                'default' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('lunar_languages')->where('code', 'nl')->update(['default' => 0]);
        DB::table('lunar_languages')->where('code', 'en')->delete();
    }
};
