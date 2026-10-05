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
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id')->unique();
            $table->foreign('collection_id')->references('id')->on('lunar_collections')->onDelete('cascade');
            // The hidden Lunar product variant that carries the tiered
            // pricing (one Price row per tier, keyed by min_quantity). See
            // App\Services\BundleService::syncPurchasable().
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->foreign('product_variant_id')->references('id')->on('lunar_product_variants')->onDelete('set null');
            $table->json('name')->nullable();
            $table->boolean('enabled')->default(false);
            $table->unsignedInteger('max_items')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};
