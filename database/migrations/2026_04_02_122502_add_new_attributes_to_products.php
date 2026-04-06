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
        $productGroup = AttributeGroup::whereHandle('details')->first();

        Attribute::create([
            'attribute_type' => 'product',
            'attribute_group_id' => $productGroup->id,
            'position' => 10,
            'name' => ['en' => 'Brand is Designer'],
            'handle' => 'brand_is_designer',
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
        Attribute::whereHandle('brand_is_designer')->whereAttributeType('product')->delete();
    }
};
