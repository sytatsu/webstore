<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Lunar\FieldTypes\Toggle;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Collection;
use Lunar\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $productGroup = AttributeGroup::whereHandle('product_details')->first();

        if (! $productGroup) {
            $productGroup = AttributeGroup::create([
                'attributable_type' => Product::class,
                'name' => ['en' => 'Product Details'],
                'handle' => 'product_details',
                'position' => 1,
            ]);
        }

        Attribute::create([
            'attribute_type' => Product::class,
            'attribute_group_id' => $productGroup->id,
            'position' => 10,
            'name' => ['en' => 'Is Designer Brand'],
            'handle' => 'brand-is-designer',
            'section' => 'main',
            'type' => Toggle::class,
            'required' => false,
            'default_value' => null,
            'configuration' => [],
            'system' => false,
        ]);

        $collectionGroup = AttributeGroup::whereHandle('collection_details')->first();

        if (! $collectionGroup) {
            $collectionGroup = AttributeGroup::create([
                'attributable_type' => Collection::class,
                'name' => ['en' => 'Collection Details'],
                'handle' => 'collection_details',
                'position' => 1,
            ]);
        }

        Attribute::create([
            'attribute_type' => Collection::class,
            'attribute_group_id' => $collectionGroup->id,
            'position' => 10,
            'name' => ['en' => 'Show Filters'],
            'handle' => 'filters',
            'section' => 'main',
            'type' => Toggle::class,
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
        Attribute::whereHandle('brand-is-designer')->whereAttributeType(Product::class)->delete();
        Attribute::whereHandle('filters')->whereAttributeType(Collection::class)->delete();
    }
};
