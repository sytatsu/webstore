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
        // For development/local environment, if we have data that isn't JSON, we might need to clear it or convert it.
        // Since we are likely using "nl" as default, we can wrap existing text in JSON if it's not JSON.
        $banners = DB::table('notification_banners')->get();
        foreach ($banners as $banner) {
            $text = $banner->banner_text;
            if ($text && !str_starts_with($text, '{') && !str_starts_with($text, '[')) {
                $json = json_encode(['nl' => $text]);
                DB::table('notification_banners')->where('id', $banner->id)->update(['banner_text' => $json]);
            }
        }

        Schema::table('notification_banners', function (Blueprint $table) {
            $table->json('banner_text')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_banners', function (Blueprint $table) {
            $table->string('banner_text')->change();
        });
    }
};
