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

            $table->foreignUuid('variant_uuid')->nullable()
                ->constrained('variants', 'uuid')
                ->nullOnDelete();

            /**--- Availability ---*/
            $table->enum('availability_type', [
                AvailabilityEnum::STOCK->name,
                AvailabilityEnum::DOWNLOAD->name,
                AvailabilityEnum::ON_REQUEST->name,
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
        Schema::dropIfExists('product_variants');
    }
};
