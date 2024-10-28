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
        Schema::create('warehouse_products', function (Blueprint $table) {
            $table->uuid()->primary();

            /**--- Product base information ---*/
            $table->string('name', 100);
            $table->text('description');

            $table->enum('product_type', [
                ProductTypeEnum::DIGITAL,
                ProductTypeEnum::PRINTED,
                ProductTypeEnum::THIRD_PARTY,
                ProductTypeEnum::CUSTOM_REQUEST,
            ]);

            $table->enum('product_variant_type', [
                ProductVariantType::UNIQUE,
                ProductVariantType::GENERIC,
            ]);

            $table->foreignUuid('type_brand_uuid')
                ->constrained('type_brands', 'uuid')
                ->cascadeOnDelete();

            $table->foreignUuid('type_category_uuid')
                ->constrained('type_categories', 'uuid')
                ->cascadeOnDelete();

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
        Schema::dropIfExists('warehouse_products');
    }
};
