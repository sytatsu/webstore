<?php

use App\Enums\ProductTypeEnum;
use App\Enums\ProductVariantType;
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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid()->primary();

            /**--- Product base information ---*/
            $table->string('name', 100);
            $table->text('description')->nullable();

            $table->enum('product_type', [
                ProductTypeEnum::DIGITAL->name,
                ProductTypeEnum::PRINTED->name,
                ProductTypeEnum::THIRD_PARTY->name,
                ProductTypeEnum::CUSTOM_REQUEST->name,
            ]);

            $table->foreignUuid('brand_uuid')->nullable()
                ->constrained('brands', 'uuid')
                ->nullOnDelete();

            $table->foreignUuid('category_uuid')->nullable()
                ->constrained('categories', 'uuid')
                ->nullOnDelete();

            /**--- Variant information ---*/
            $table->boolean('has_multiple_variants')->default(false);
            $table->enum('product_variant_type', [
                ProductVariantType::UNIQUE->name,
                ProductVariantType::GENERIC->name,
            ]);

            /**--- Timestamps ---*/
            $table->timestamp('discontinued_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
