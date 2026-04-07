<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if a Default Tax Class already exists
        $taxClass = DB::table('lunar_tax_classes')->where('default', 1)->first();
        if ($taxClass) {
            $taxClassId = $taxClass->id;
        } else {
            $taxClassId = DB::table('lunar_tax_classes')->insertGetId([
                'name' => 'Default Tax Class',
                'default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ensure a Default Tax Zone exists
        $taxZone = DB::table('lunar_tax_zones')->where('default', 1)->first();
        if ($taxZone) {
            $taxZoneId = $taxZone->id;
        } else {
            $taxZoneId = DB::table('lunar_tax_zones')->insertGetId([
                'name' => 'Default Tax Zone',
                'zone_type' => 'all', // Standard for default
                'price_display' => 'incl',
                'active' => 1,
                'default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create or Update a Tax Rate
        $taxRate = DB::table('lunar_tax_rates')
            ->where('tax_zone_id', $taxZoneId)
            ->where('name', 'Standard Tax')
            ->first();

        if ($taxRate) {
            $taxRateId = $taxRate->id;
        } else {
            $taxRateId = DB::table('lunar_tax_rates')->insertGetId([
                'tax_zone_id' => $taxZoneId,
                'priority' => 1,
                'name' => 'Standard Tax',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create or Update Tax Rate Amount (21%)
        DB::table('lunar_tax_rate_amounts')->updateOrInsert(
            [
                'tax_class_id' => $taxClassId,
                'tax_rate_id' => $taxRateId,
            ],
            [
                'percentage' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find the standard tax rate
        $taxRate = DB::table('lunar_tax_rates')->where('name', 'Standard Tax')->first();

        if ($taxRate) {
            DB::table('lunar_tax_rate_amounts')->where('tax_rate_id', $taxRate->id)->where('percentage', 21)->delete();
            DB::table('lunar_tax_rates')->where('id', $taxRate->id)->delete();
        }

        // Only delete tax zone/class if they are the ones we created (identified by name and being default)
        DB::table('lunar_tax_zones')->where('name', 'Default Tax Zone')->where('default', 1)->delete();
        DB::table('lunar_tax_classes')->where('name', 'Default Tax Class')->where('default', 1)->delete();
    }
};
