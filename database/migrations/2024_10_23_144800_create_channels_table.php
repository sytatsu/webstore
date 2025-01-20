<?php

use App\Enums\ChannelEnum;
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
        Schema::create('channels', function (Blueprint $table) {
            $table->uuid()->primary();

            $table->enum('channel_type', [
                ChannelEnum::STOCK->name,
                ChannelEnum::DOWNLOAD->name,
                ChannelEnum::ON_REQUEST->name,
            ]);

            $table->integer('channel_quantity')->default(0);

            $table->foreignUuid('channel_location_uuid')->nullable()
                ->constrained('channel_locations', 'uuid')
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
        Schema::dropIfExists('channels');
    }
};
