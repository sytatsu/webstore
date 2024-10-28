<?php

use App\Enums\AvailabilityEnum;
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
        Schema::create('warehouse_product_variants', function (Blueprint $table) {
            $table->uuid()->primary();

            $table->foreignUuid('warehouse_product_uuid')
                ->constrained('warehouse_products', 'uuid')
                ->cascadeOnDelete();

            $table->foreignUuid('type_variant_uuid')
                ->constrained('type_variants', 'uuid')
                ->cascadeOnDelete();

            /**--- Product information ---*/
            $table->integer('price');

            /**--- Availability ---*/
            $table->enum('availability_type', [
                AvailabilityEnum::STOCK,
                AvailabilityEnum::DOWNLOAD,
                AvailabilityEnum::ON_REQUEST,
            ]);

            $table->integer('availability_quantity')->default(0);

            /**--- Timestamps ---*/
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_product_variants');
    }
};
