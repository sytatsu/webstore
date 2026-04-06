<?php

use Illuminate\Database\Migrations\Migration;
use Lunar\FieldTypes\Toggle;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Collection;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $collectionGroup = AttributeGroup::whereHandle('collection_details')->first();

        $attributes = [
            [
                'name' => 'Filter Categories',
                'handle' => 'filter_categories',
            ],
            [
                'name' => 'Filter Price',
                'handle' => 'filter_price',
            ],
            [
                'name' => 'Filter Availability',
                'handle' => 'filter_availability',
            ],
        ];

        foreach ($attributes as $index => $attr) {
            Attribute::create([
                'attribute_type' => 'collection',
                'attribute_group_id' => $collectionGroup->id,
                'position' => 20 + $index,
                'name' => ['en' => $attr['name']],
                'handle' => $attr['handle'],
                'section' => 'main',
                'type' => Toggle::class,
                'required' => false,
                'default_value' => null,
                'configuration' => [],
                'system' => false,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Attribute::whereIn('handle', ['filter_categories', 'filter_price', 'filter_availability'])
            ->whereAttributeType('collection')
            ->delete();
    }
};
