<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\Currency;
use Lunar\Models\Channel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductDesignerBrandTest extends TestCase
{
    use RefreshDatabase;

    protected $productType;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create([
            'code' => 'en',
            'default' => true,
        ]);

        Currency::factory()->create([
            'code' => 'EUR',
            'default' => true,
            'decimal_places' => 2,
        ]);

        Channel::factory()->create([
            'handle' => 'default',
            'default' => true,
            'name' => 'Default Channel',
        ]);

        $this->productType = ProductType::factory()->create();
    }

    #[Test]
    public function brand_label_changes_to_designer_when_brand_is_designer_is_true()
    {
        $brand = \Lunar\Models\Brand::factory()->create([
            'name' => 'Gucci',
            'attribute_data' => collect([
                'brand_is_designer' => new \Lunar\FieldTypes\Toggle(true),
            ]),
        ]);

        $taxClass = \Lunar\Models\TaxClass::factory()->create();

        $variant = \Lunar\Models\ProductVariant::factory()->create([
            'sku' => 'TEST-SKU-1',
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $product = $variant->product;
        $product->update([
            'brand_id' => $brand->id,
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Test Product'),
                'description' => new \Lunar\FieldTypes\Text('Test Description'),
            ]),
        ]);

        \Lunar\Models\Price::factory()->create([
            'priceable_type' => \Lunar\Models\ProductVariant::class,
            'priceable_id' => $variant->id,
            'currency_id' => Currency::first()->id,
            'min_quantity' => 1,
            'price' => 1000,
            'customer_group_id' => null,
        ]);

        $this->get(route('sytatsu.webstore.product', $product->id))
            ->assertStatus(200)
            ->assertSee('Designer:')
            ->assertSee('Gucci')
            ->assertDontSee('Brand:')
            ->assertDontSee('Brand Is Designer:');
    }

    #[Test]
    public function brand_label_remains_brand_when_brand_is_designer_is_false()
    {
        $brand = \Lunar\Models\Brand::factory()->create([
            'name' => 'Nike',
            'attribute_data' => collect([
                'brand_is_designer' => new \Lunar\FieldTypes\Toggle(false),
            ]),
        ]);

        $taxClass = \Lunar\Models\TaxClass::factory()->create();

        $variant = \Lunar\Models\ProductVariant::factory()->create([
            'sku' => 'TEST-SKU-2',
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $product = $variant->product;
        $product->update([
            'brand_id' => $brand->id,
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Test Product'),
                'description' => new \Lunar\FieldTypes\Text('Test Description'),
            ]),
        ]);

        \Lunar\Models\Price::factory()->create([
            'priceable_type' => \Lunar\Models\ProductVariant::class,
            'priceable_id' => $variant->id,
            'currency_id' => Currency::first()->id,
            'min_quantity' => 1,
            'price' => 1000,
            'customer_group_id' => null,
        ]);

        $this->get(route('sytatsu.webstore.product', $product->id))
            ->assertStatus(200)
            ->assertSee('Brand:')
            ->assertSee('Nike')
            ->assertDontSee('Designer:')
            ->assertDontSee('Brand Is Designer:');
    }

    #[Test]
    public function brand_label_is_brand_when_brand_is_designer_is_missing()
    {
        $taxClass = \Lunar\Models\TaxClass::factory()->create();

        $variant = \Lunar\Models\ProductVariant::factory()->create([
            'sku' => 'TEST-SKU-3',
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $product = $variant->product;
        $product->update([
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Test Product'),
                'description' => new \Lunar\FieldTypes\Text('Test Description'),
                'brand' => new \Lunar\FieldTypes\Text('Adidas'),
            ]),
        ]);

        \Lunar\Models\Price::factory()->create([
            'priceable_type' => \Lunar\Models\ProductVariant::class,
            'priceable_id' => $variant->id,
            'currency_id' => Currency::first()->id,
            'min_quantity' => 1,
            'price' => 1000,
            'customer_group_id' => null,
        ]);

        $this->get(route('sytatsu.webstore.product', $product->id))
            ->assertStatus(200)
            ->assertSee('Brand:')
            ->assertSee('Adidas')
            ->assertDontSee('Designer:');
    }
}
