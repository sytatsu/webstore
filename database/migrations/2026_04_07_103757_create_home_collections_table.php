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
        Schema::create('home_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_setting_id')->constrained('home_settings')->cascadeOnDelete();
            $table->foreignId('collection_id')->constrained('lunar_collections')->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_collections');
    }
};
