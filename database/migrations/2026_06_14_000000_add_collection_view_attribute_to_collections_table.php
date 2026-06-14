<?php

use Illuminate\Database\Migrations\Migration;
use Lunar\FieldTypes\Dropdown;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Collection;

return new class extends Migration
{
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
            'position' => 25,
            'name' => ['en' => 'Collection View'],
            'handle' => 'collection_view',
            'section' => 'main',
            'type' => Dropdown::class,
            'required' => false,
            'default_value' => 'default',
            'configuration' => [
                'lookups' => [
                    ['label' => 'Default', 'value' => 'default'],
                    ['label' => 'Bundle', 'value' => ' bundle'],
                ],
            ],
            'system' => false,
        ]);
    }

    public function down(): void
    {
        Attribute::where('handle', 'collection_view')
            ->where('attribute_type', 'collection')
            ->delete();
    }
};
