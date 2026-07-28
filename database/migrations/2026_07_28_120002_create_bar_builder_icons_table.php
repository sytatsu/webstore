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
        Schema::create('bar_builder_icons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // One or more SVG path "d" strings (a glyph can be made of several
            // sub-paths, e.g. the toes + pad of a paw print) authored on a 0-100 canvas.
            $table->json('svg_paths');
            $table->decimal('cx', 6, 2)->default(50);
            $table->decimal('cy', 6, 2)->default(50);
            $table->decimal('scale', 4, 2)->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bar_builder_icons');
    }
};
