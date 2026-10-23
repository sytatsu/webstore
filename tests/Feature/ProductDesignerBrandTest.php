<?php

namespace Tests\Feature;

use Lunar\Models\Brand;
use Lunar\FieldTypes\Toggle;
use Lunar\Models\TaxClass;
use Lunar\Models\ProductVariant;
use Lunar\FieldTypes\Text;
use Lunar\Models\Price;
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

        $this->useEnglishAsDefaultLanguage();

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
        $brand = Brand::factory()->create([
            'name' => 'Gucci',
            'attribute_data' => collect([
                'brand_is_designer' => new Toggle(true),
            ]),
        ]);

        $taxClass = TaxClass::factory()->create();

        $variant = ProductVariant::factory()->create([
            'sku' => 'TEST-SKU-1',
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $product = $variant->product;
        $product->update([
            'brand_id' => $brand->id,
            'attribute_data' => collect([
                'name' => new Text('Test Product'),
                'description' => new Text('Test Description'),
            ]),
        ]);

        Price::factory()->create([
            'priceable_type' => ProductVariant::class,
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
        $brand = Brand::factory()->create([
            'name' => 'Nike',
            'attribute_data' => collect([
                'brand_is_designer' => new Toggle(false),
            ]),
        ]);

        $taxClass = TaxClass::factory()->create();

        $variant = ProductVariant::factory()->create([
            'sku' => 'TEST-SKU-2',
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $product = $variant->product;
        $product->update([
            'brand_id' => $brand->id,
            'attribute_data' => collect([
                'name' => new Text('Test Product'),
                'description' => new Text('Test Description'),
            ]),
        ]);

        Price::factory()->create([
            'priceable_type' => ProductVariant::class,
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
        $taxClass = TaxClass::factory()->create();

        $variant = ProductVariant::factory()->create([
            'sku' => 'TEST-SKU-3',
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $product = $variant->product;
        $product->update([
            'attribute_data' => collect([
                'name' => new Text('Test Product'),
                'description' => new Text('Test Description'),
                'brand' => new Text('Adidas'),
            ]),
        ]);

        Price::factory()->create([
            'priceable_type' => ProductVariant::class,
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
