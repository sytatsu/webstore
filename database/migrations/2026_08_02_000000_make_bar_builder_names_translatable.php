<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * English name => Dutch translation, for the values seeded before this
     * migration existed. Anything not listed here falls back to the
     * English value until an admin fills in the Dutch name.
     */
    private array $translations = [
        // base colours
        'Jet' => 'Gitzwart',
        'Graphite' => 'Grafiet',
        'Chalk' => 'Krijt',
        'Snow' => 'Sneeuw',
        'Signal Red' => 'Signaalrood',
        'Ultramarine' => 'Ultramarijn',
        'Pine' => 'Dennengroen',
        'Sun' => 'Zon',
        'Muted Red' => 'Gedempt rood',
        'Muted Blue' => 'Gedempt blauw',
        'Muted Green' => 'Gedempt groen',
        'Muted Purple' => 'Gedempt paars',
        // cap colour combinations
        'Ember' => 'Gloed',
        'Coral Pop' => 'Koraal Pop',
        'Tangerine' => 'Mandarijn',
        'Sunburst' => 'Zonnestraal',
        'Citrus' => 'Citrus',
        'Forest' => 'Bos',
        'Lagoon' => 'Lagune',
        'Marine' => 'Marine',
        'Cobalt' => 'Kobalt',
        'Grape' => 'Druif',
        'Fuchsia' => 'Fuchsia',
        'Candy' => 'Snoep',
        'Slate' => 'Leisteen',
        'Sandstone' => 'Zandsteen',
        // icons
        'Dog paw' => 'Hondenpoot',
        'Heart' => 'Hart',
        'Music note' => 'Muzieknoot',
        'Headset' => 'Headset',
        'Kitten paw' => 'Kattenpoot',
        'twitter' => 'Twitter',
        'Instagram' => 'Instagram',
    ];

    private array $tables = [
        'bar_builder_base_colors',
        'bar_builder_cap_combos',
        'bar_builder_icons',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            DB::statement("ALTER TABLE {$table} CHANGE name name_legacy VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE {$table} ADD COLUMN name JSON NULL AFTER name_legacy");

            foreach (DB::table($table)->get(['id', 'name_legacy']) as $row) {
                DB::table($table)->where('id', $row->id)->update([
                    'name' => json_encode([
                        'en' => $row->name_legacy,
                        'nl' => $this->translations[$row->name_legacy] ?? $row->name_legacy,
                    ], JSON_UNESCAPED_UNICODE),
                ]);
            }

            DB::statement("ALTER TABLE {$table} MODIFY name JSON NOT NULL");
            DB::statement("ALTER TABLE {$table} DROP COLUMN name_legacy");
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            DB::statement("ALTER TABLE {$table} CHANGE name name_json JSON NOT NULL");
            DB::statement("ALTER TABLE {$table} ADD COLUMN name VARCHAR(255) NULL AFTER name_json");

            foreach (DB::table($table)->get(['id', 'name_json']) as $row) {
                $decoded = json_decode($row->name_json, true) ?: [];

                DB::table($table)->where('id', $row->id)->update([
                    'name' => $decoded['en'] ?? (reset($decoded) ?: ''),
                ]);
            }

            DB::statement("ALTER TABLE {$table} MODIFY name VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE {$table} DROP COLUMN name_json");
        }
    }
};
