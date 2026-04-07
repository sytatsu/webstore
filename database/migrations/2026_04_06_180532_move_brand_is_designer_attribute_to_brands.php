<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Brand;
use Lunar\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure we have an Attribute Group for Brands
        $brandGroup = AttributeGroup::whereHandle('brand_details')
            ->whereAttributableType(Brand::class)
            ->first();

        if (!$brandGroup) {
            $brandGroup = AttributeGroup::create([
                'attributable_type' => 'brand',
                'name' => ['en' => 'Brand Details'],
                'handle' => 'brand_details',
                'position' => 1,
            ]);
        }

        // 2. Find the attribute and move it
        $attribute = Attribute::whereHandle('brand_is_designer')->first();

        if ($attribute) {
            $attribute->update([
                'attribute_type' => 'brand',
                'attribute_group_id' => $brandGroup->id,
            ]);
        } else {
            // Create the attribute if it doesn't exist at all
            Attribute::create([
                'attribute_type' => 'brand',
                'attribute_group_id' => $brandGroup->id,
                'position' => 10,
                'name' => ['en' => 'Brand is Designer'],
                'handle' => 'brand_is_designer',
                'section' => 'main',
                'type' => \Lunar\FieldTypes\Toggle::class,
                'required' => false,
                'default_value' => null,
                'configuration' => [],
                'system' => false,
            ]);
        }

        // 3. Migrate data from Products to Brands
        // We set brand_is_designer to true for a brand if ANY of its products have it as true
        try {
            $productsWithDesignerBrand = Product::where('attribute_data->brand_is_designer->value', true)
                ->whereNotNull('brand_id')
                ->get();
        } catch (\Exception $e) {
            // If the column doesn't exist or doesn't support JSON query, handle it gracefully
            $productsWithDesignerBrand = collect();
        }

        $brandIds = $productsWithDesignerBrand->pluck('brand_id')->unique();

        foreach ($brandIds as $brandId) {
            $brand = Brand::find($brandId);
            if ($brand) {
                $attributeData = $brand->attribute_data ?? collect();

                // Lunar uses FieldTypes, but in database it's often stored as a simple value object for Toggles
                // Based on how Lunar works, we might need to set it properly.
                // For a Toggle, it usually looks like {"value": true} in the JSON

                $attributeData['brand_is_designer'] = new \Lunar\FieldTypes\Toggle(true);

                $brand->update([
                    'attribute_data' => $attributeData,
                ]);
            }
        }

        // 4. Clean up products (optional but recommended to avoid stale data)
        foreach ($productsWithDesignerBrand as $product) {
            $attributeData = $product->attribute_data;
            if (isset($attributeData['brand_is_designer'])) {
                unset($attributeData['brand_is_designer']);
                $product->update(['attribute_data' => $attributeData]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $brandGroup = AttributeGroup::whereHandle('brand_details')
            ->whereAttributableType(Brand::class)
            ->first();

        $productGroup = AttributeGroup::whereHandle('details')
            ->whereAttributableType(Product::class)
            ->first();

        $attribute = Attribute::whereHandle('brand_is_designer')
            ->whereAttributeType('brand')
            ->first();

        if ($attribute && $productGroup) {
            $attribute->update([
                'attribute_type' => 'product',
                'attribute_group_id' => $productGroup->id,
            ]);
        }

        // Note: Reverting data migration is complex because we don't know which products specifically had it.
        // We could set it to true for ALL products of that brand, but that might be incorrect if only some had it.
        // Given this is a structural change, usually the down migration focuses on the schema/attribute definition.
    }
};
