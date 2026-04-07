<?php

use Illuminate\Database\Migrations\Migration;
use Lunar\FieldTypes\Toggle;
use Lunar\FieldTypes\Dropdown;
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

        if (!$collectionGroup) {
            $collectionGroup = AttributeGroup::create([
                'attributable_type' => Collection::class,
                'name' => ['en' => 'Collection Details'],
                'handle' => 'collection_details',
                'position' => 1,
            ]);
        }

        Attribute::create([
            'attribute_type' => 'collection',
            'attribute_group_id' => $collectionGroup->id,
            'position' => 23,
            'name' => ['en' => 'Show Sorting'],
            'handle' => 'show_sorting',
            'section' => 'main',
            'type' => Toggle::class,
            'required' => false,
            'default_value' => null,
            'configuration' => [],
            'system' => false,
        ]);

        Attribute::create([
            'attribute_type' => 'collection',
            'attribute_group_id' => $collectionGroup->id,
            'position' => 24,
            'name' => ['en' => 'Default Sort'],
            'handle' => 'default_sort',
            'section' => 'main',
            'type' => Dropdown::class,
            'required' => false,
            'default_value' => 'newest',
            'configuration' => [
                'lookups' => [
                    ['label' => 'Newest to Oldest', 'value' => 'newest'],
                    ['label' => 'Oldest to Newest', 'value' => 'oldest'],
                    ['label' => 'Alphabetically', 'value' => 'alphabetical'],
                ],
            ],
            'system' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Attribute::whereIn('handle', ['show_sorting', 'default_sort'])
            ->whereAttributeType('collection')
            ->delete();
    }
};
