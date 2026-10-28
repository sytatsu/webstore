<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The Fleet game package (sytatsu/filament-fleet) was removed in favour of
 * Flagship RPG. This drops all of its tables and forgets its migrations, so
 * nothing of Fleet stays in the database. It can't be undone: restore a
 * backup if you need the data back.
 */
return new class extends Migration
{
    private const TABLES = [
        'fleet_achievements',
        'fleet_battles',
        'fleet_captains',
        'fleet_commanders',
        'fleet_fit_templates',
        'fleet_fits',
        'fleet_missions',
        'fleet_modules',
        'fleet_queue_items',
        'fleet_research',
        'fleet_ships',
        'fleet_squadrons',
        'fleet_upgrades',
    ];

    private const MIGRATIONS = [
        '2026_10_19_000001_create_fleet_commanders_table',
        '2026_10_19_000002_create_fleet_missions_table',
        '2026_10_19_000003_create_fleet_ships_table',
        '2026_10_19_000004_create_fleet_upgrades_table',
        '2026_10_20_000005_create_fleet_battles_table',
        '2026_10_20_000006_create_fleet_achievements_table',
        '2026_10_23_000007_rename_carrier_to_command_ship',
        '2026_10_23_000008_remove_legacy_light_craft',
        '2026_10_23_000009_one_row_per_ship',
        '2026_10_23_000010_create_fleet_queue_items_table',
        '2026_10_23_000011_create_fleet_research_table',
        '2026_10_23_000012_refund_upgrades_replaced_by_research',
        '2026_10_23_000013_create_fleet_squadrons_table',
        '2026_10_23_000014_create_starter_squadrons',
        '2026_10_23_000015_create_fleet_modules_and_fits_tables',
        '2026_10_23_000016_add_flight_legs_and_mining_missions',
        '2026_10_23_000017_create_fleet_captains_table',
        '2026_10_24_000018_add_carrier_ship_to_fleet_queue_items',
    ];

    public function up(): void
    {
        // The Fleet tables reference each other, so drop them without
        // foreign key checks instead of working out the right order.
        Schema::withoutForeignKeyConstraints(function () {
            foreach (self::TABLES as $table) {
                Schema::dropIfExists($table);
            }
        });

        DB::table(config('database.migrations.table', 'migrations'))
            ->whereIn('migration', self::MIGRATIONS)
            ->delete();
    }

    public function down(): void
    {
        // Nothing to restore: Fleet is gone.
    }
};
