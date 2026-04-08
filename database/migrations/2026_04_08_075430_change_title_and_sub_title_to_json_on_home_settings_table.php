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
        Schema::table('home_settings', function (Blueprint $table) {
            $table->json('title')->nullable()->change();
            $table->json('sub_title')->nullable()->change();
        });

        // Migrate existing data to JSON format
        $settings = DB::table('home_settings')->get();
        $defaultLocale = config('app.fallback_locale') ?: 'en';

        foreach ($settings as $setting) {
            $updatedTitle = $setting->title ? json_encode([$defaultLocale => $setting->title]) : null;
            $updatedSubTitle = $setting->sub_title ? json_encode([$defaultLocale => $setting->sub_title]) : null;

            DB::table('home_settings')
                ->where('id', $setting->id)
                ->update([
                    'title' => $updatedTitle,
                    'sub_title' => $updatedSubTitle,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->text('sub_title')->nullable()->change();
        });
    }
};
