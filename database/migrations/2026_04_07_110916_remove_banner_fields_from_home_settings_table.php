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
            $table->dropColumn([
                'banner_active',
                'banner_text',
                'banner_start_at',
                'banner_end_at',
                'banner_url',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->boolean('banner_active')->default(false);
            $table->string('banner_text')->nullable();
            $table->string('banner_url')->nullable();
            $table->timestamp('banner_start_at')->nullable();
            $table->timestamp('banner_end_at')->nullable();
        });
    }
};
