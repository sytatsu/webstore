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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid()->primary();

            /**--- Product information ---*/
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('sku');
            $table->integer('price');

            $table->foreignUuid('product_uuid')
                ->constrained('products', 'uuid')
                ->cascadeOnDelete();

            /**--- Timestamps ---*/
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variants_many_variants', function (Blueprint $table) {
            $table->foreignUuid('product_variant_uuid')
                ->constrained('product_variants', 'uuid')
                ->cascadeOnDelete();

            $table->foreignUuid('variant_uuid')
                ->constrained('variants', 'uuid')
                ->cascadeOnDelete();
        });

        Schema::create('product_variants_many_availabilities', function (Blueprint $table) {
            $table->foreignUuid('product_variant_uuid')
                ->constrained('product_variants', 'uuid', 'fk_availabilities_product_variant_uuid')
                ->cascadeOnDelete();

            $table->foreignUuid('availability_uuid')
                ->constrained('availabilities', 'uuid', 'fk_availabilities_availability_uuid')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants_many_availabilities');
        Schema::dropIfExists('product_variants_many_variants');
        Schema::dropIfExists('product_variants');
    }
};
