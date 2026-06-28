<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bundle_configs', function (Blueprint $table) {
            $table->json('bundle_name')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('bundle_configs', function (Blueprint $table) {
            $table->dropColumn('bundle_name');
        });
    }
};
