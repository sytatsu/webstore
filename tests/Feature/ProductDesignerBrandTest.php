<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\Currency;
use Tests\TestCase;

class ProductDesignerBrandTest extends TestCase
{
    use RefreshDatabase;

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

        ProductType::factory()->create();
    }

    /** @test */
    public function brand_label_changes_to_designer_when_brand_is_designer_is_true()
    {
        $product = Product::factory()->create([
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Test Product'),
                'description' => new \Lunar\FieldTypes\Text('Test Description'),
                'brand' => new \Lunar\FieldTypes\Text('Gucci'),
                'brand_is_designer' => new \Lunar\FieldTypes\Toggle(true),
            ]),
        ]);

        $variant = \Lunar\Models\ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->get(route('sytatsu.webstore.product', $product->id))
            ->assertStatus(200)
            ->assertSee('Designer:')
            ->assertSee('Gucci')
            ->assertDontSee('Brand:')
            ->assertDontSee('Brand Is Designer:');
    }

    /** @test */
    public function brand_label_remains_brand_when_brand_is_designer_is_false()
    {
        $product = Product::factory()->create([
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Test Product'),
                'description' => new \Lunar\FieldTypes\Text('Test Description'),
                'brand' => new \Lunar\FieldTypes\Text('Nike'),
                'brand_is_designer' => new \Lunar\FieldTypes\Toggle(false),
            ]),
        ]);

        $variant = \Lunar\Models\ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->get(route('sytatsu.webstore.product', $product->id))
            ->assertStatus(200)
            ->assertSee('Brand:')
            ->assertSee('Nike')
            ->assertDontSee('Designer:')
            ->assertDontSee('Brand Is Designer:');
    }

    /** @test */
    public function brand_label_is_brand_when_brand_is_designer_is_missing()
    {
        $product = Product::factory()->create([
            'attribute_data' => collect([
                'name' => new \Lunar\FieldTypes\Text('Test Product'),
                'description' => new \Lunar\FieldTypes\Text('Test Description'),
                'brand' => new \Lunar\FieldTypes\Text('Adidas'),
            ]),
        ]);

        $variant = \Lunar\Models\ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->get(route('sytatsu.webstore.product', $product->id))
            ->assertStatus(200)
            ->assertSee('Brand:')
            ->assertSee('Adidas')
            ->assertDontSee('Designer:');
    }
}
