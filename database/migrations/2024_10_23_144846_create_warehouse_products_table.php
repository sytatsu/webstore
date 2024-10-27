<?php

use App\Enums\ProductTypeEnum;
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

            $table->foreignUuid('type_brand_uuid')
                ->constrained('type_brands', 'uuid')
                ->cascadeOnDelete();

            $table->foreignUuid('type_category_uuid')
                ->constrained('type_categories', 'uuid')
                ->cascadeOnDelete();

            /**--- Product information ---*/
            $table->string('name', 100);
            $table->text('description');

            $table->enum('type', [
                ProductTypeEnum::DIGITAL->name,
                ProductTypeEnum::PRINTED->name,
                ProductTypeEnum::THIRD_PARTY->name,
                ProductTypeEnum::CUSTOM_REQUEST->name,
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
        Schema::dropIfExists('warehouse_products');
    }
};
