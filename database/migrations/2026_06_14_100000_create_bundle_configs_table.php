<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundle_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id')->unique();
            $table->foreign('collection_id')->references('id')->on('lunar_collections')->onDelete('cascade');
            $table->boolean('enabled')->default(false);
            $table->json('discount_tiers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_configs');
    }
};
