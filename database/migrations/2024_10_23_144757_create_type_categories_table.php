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
        Schema::create('type_categories', function (Blueprint $table) {
            $table->uuid()->primary();

            $table->string('name', 100);
            $table->text('description')->nullable();

            $table->foreignUuid('parent_type_category_uuid')->nullable()
                ->constrained('type_categories', 'uuid')
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
        Schema::dropIfExists('type_categories');
    }
};
