<?php

use Illuminate\Database\Migrations\Migration;
use Lunar\FieldTypes\File;
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
            'position' => 10,
            'name' => ['en' => 'Collection Image'],
            'handle' => 'collection_image',
            'section' => 'main',
            'type' => File::class,
            'required' => false,
            'default_value' => null,
            'configuration' => [],
            'system' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Attribute::where('handle', 'collection_image')
            ->whereAttributeType('collection')
            ->delete();
    }
};
