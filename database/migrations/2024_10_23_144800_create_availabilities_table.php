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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->uuid()->primary();

            $table->enum('availability_type', [
                AvailabilityEnum::STOCK->name,
                AvailabilityEnum::DOWNLOAD->name,
                AvailabilityEnum::ON_REQUEST->name,
            ]);

            $table->integer('availability_quantity')->default(0);

            $table->foreignUuid('availability_location_uuid')->nullable()
                ->constrained('availability_locations', 'uuid')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
