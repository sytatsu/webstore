<?php

namespace Database\Seeders\products;

use App\Models\BarBuilderBaseColor;
use App\Models\BarBuilderCapCombo;
use App\Models\BarBuilderIcon;
use Illuminate\Database\Seeder;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;

class ClickerzBarProductSeeder extends Seeder
{
    /**
     * The number of caps the customer can choose from, and their all-in price.
     */
    private const BASE_PRICE = 0.95;

    private const CAP_PRICE = 1.00;

    private const MIN_CAPS = 2;

    private const MAX_CAPS = 10;

    public function run(): void
    {
        $this->seedBaseColors();
        $this->seedCapCombos();
        $this->seedIcons();
        $this->seedProduct();
    }

    private function seedBaseColors(): void
    {
        $colors = [
            ['name' => 'Jet', 'hex' => '#101216'],
            ['name' => 'Graphite', 'hex' => '#3A3E45'],
            ['name' => 'Chalk', 'hex' => '#E4E1D9'],
            ['name' => 'Snow', 'hex' => '#FFFFFF'],
            ['name' => 'Signal Red', 'hex' => '#E23127'],
            ['name' => 'Ultramarine', 'hex' => '#2B44FF'],
            ['name' => 'Pine', 'hex' => '#128A5B'],
            ['name' => 'Sun', 'hex' => '#FFC61E', 'enabled' => false],
            ['name' => 'Muted Red', 'hex' => '#A9564B'],
            ['name' => 'Muted Blue', 'hex' => '#4E6F8A'],
            ['name' => 'Muted Green', 'hex' => '#578052'],
            ['name' => 'Muted Purple', 'hex' => '#786490'],
        ];

        foreach ($colors as $index => $color) {
            BarBuilderBaseColor::firstOrCreate(
                ['name' => $color['name']],
                [
                    'hex' => $color['hex'],
                    'enabled' => $color['enabled'] ?? true,
                    'sort_order' => $index,
                ]
            );
        }
    }

    private function seedCapCombos(): void
    {
        $combos = [
            ['name' => 'Ember', 'cap_hex' => '#E23127', 'text_hex' => '#FFFFFF'],
            ['name' => 'Coral Pop', 'cap_hex' => '#FF6B57', 'text_hex' => '#FFFFFF'],
            ['name' => 'Tangerine', 'cap_hex' => '#FF8A1F', 'text_hex' => '#FFFFFF'],
            ['name' => 'Sunburst', 'cap_hex' => '#FFC61E', 'text_hex' => '#FFFFFF'],
            ['name' => 'Citrus', 'cap_hex' => '#9CD323', 'text_hex' => '#FFFFFF'],
            ['name' => 'Forest', 'cap_hex' => '#128A5B', 'text_hex' => '#FFFFFF'],
            ['name' => 'Lagoon', 'cap_hex' => '#00A6A6', 'text_hex' => '#FFFFFF'],
            ['name' => 'Marine', 'cap_hex' => '#2FA8E0', 'text_hex' => '#FFFFFF'],
            ['name' => 'Cobalt', 'cap_hex' => '#2B44FF', 'text_hex' => '#FFFFFF'],
            ['name' => 'Grape', 'cap_hex' => '#7A3CF0', 'text_hex' => '#FFFFFF'],
            ['name' => 'Fuchsia', 'cap_hex' => '#E0308F', 'text_hex' => '#FFFFFF'],
            ['name' => 'Candy', 'cap_hex' => '#FF9FC4', 'text_hex' => '#E0308F', 'enabled' => false],
            ['name' => 'Slate', 'cap_hex' => '#3A3E45', 'text_hex' => '#FFC61E'],
            ['name' => 'Sandstone', 'cap_hex' => '#E4E1D9', 'text_hex' => '#E23127'],
            ['name' => 'Muted Red', 'cap_hex' => '#E4E1D9', 'text_hex' => '#A9564B'],
            ['name' => 'Muted Blue', 'cap_hex' => '#E4E1D9', 'text_hex' => '#4E6F8A'],
            ['name' => 'Muted Green', 'cap_hex' => '#E4E1D9', 'text_hex' => '#578052'],
            ['name' => 'Muted Purple', 'cap_hex' => '#E4E1D9', 'text_hex' => '#786490'],
        ];

        foreach ($combos as $index => $combo) {
            BarBuilderCapCombo::firstOrCreate(
                ['name' => $combo['name'], 'cap_hex' => $combo['cap_hex'], 'text_hex' => $combo['text_hex']],
                [
                    'enabled' => $combo['enabled'] ?? true,
                    'sort_order' => $index,
                ]
            );
        }
    }

    private function seedIcons(): void
    {
        BarBuilderIcon::firstOrCreate(
            ['name' => 'Dog paw'],
            [
                'svg_paths' => [
                    'M15 41 a9 12 0 1 0 18 0 a9 12 0 1 0 -18 0 Z',
                    'M32.5 26 a9.5 13 0 1 0 19 0 a9.5 13 0 1 0 -19 0 Z',
                    'M51.5 26 a9.5 13 0 1 0 19 0 a9.5 13 0 1 0 -19 0 Z',
                    'M70 41 a9 12 0 1 0 18 0 a9 12 0 1 0 -18 0 Z',
                    'M26 74 a25.5 19 0 1 0 51 0 a25.5 19 0 1 0 -51 0 Z',
                ],
                'cx' => 51.5,
                'cy' => 53,
                'scale' => 0.60,
                'sort_order' => 0,
            ]
        );

        BarBuilderIcon::firstOrCreate(
            ['name' => 'Heart'],
            [
                'svg_paths' => [
                    'M 50 82 L 20 52 A 18 18 0 1 1 50 34 A 18 18 0 1 1 80 52 Z',
                ],
                'cx' => 50,
                'cy' => 58,
                'scale' => 0.62,
                'sort_order' => 1,
            ]
        );

        BarBuilderIcon::firstOrCreate(
            ['name' => 'Music note'],
            [
                'svg_paths' => [
                    'M22 78 a13 10 0 1 0 26 0 a13 10 0 1 0 -26 0 Z',
                    'M46 20 h6 v58 h-6 Z',
                    'M52 20 C 75 25, 78 45, 60 55 C 68 45, 68 30, 52 30 Z',
                ],
                'cx' => 50,
                'cy' => 50,
                'scale' => 0.55,
                'sort_order' => 2,
            ]
        );

        BarBuilderIcon::firstOrCreate(
            ['name' => 'Headset'],
            [
                'svg_paths' => [
                    'M15 52 A35 35 0 0 1 85 52 L85 60 A35 35 0 0 0 15 60 Z',
                    'M8 56 a9 16 0 1 0 18 0 a9 16 0 1 0 -18 0 Z',
                    'M74 56 a9 16 0 1 0 18 0 a9 16 0 1 0 -18 0 Z',
                ],
                'cx' => 50,
                'cy' => 62,
                'scale' => 0.62,
                'sort_order' => 3,
            ]
        );
    }

    private function seedProduct(): void
    {
        $product = Product::query()
            ->whereJsonContains('attribute_data->name->value->en', 'Clickerz Bar')
            ->first();

        if (!$product) {
            $productType = ProductType::first();

            $product = Product::create([
                'product_type_id' => $productType->id,
                'status' => 'published',
                'attribute_data' => collect([
                    'name' => new TranslatedText([
                        'en' => 'Clickerz Bar',
                        'nl' => 'Clickerz Bar',
                    ]),
                    'description' => new TranslatedText([
                        'en' => 'Build your own clicker bar. Choose your word, pick a colour for every cap and finish it off with a base colour.',
                        'nl' => 'Stel je eigen clicker bar samen. Kies je woord, geef elke cap een kleur en kies een basiskleur.',
                    ]),
                ]),
            ]);
        }

        $defaultChannel = Channel::whereDefault(true)->first() ?? Channel::first();
        $defaultCustomerGroup = CustomerGroup::whereDefault(true)->first() ?? CustomerGroup::first();
        $defaultCurrency = Currency::whereDefault(true)->first() ?? Currency::first();
        $defaultTaxClass = TaxClass::whereDefault(true)->first() ?? TaxClass::first();
        $language = Language::whereDefault(true)->first() ?? Language::first();

        if (!$product->channels()->where('channel_id', $defaultChannel->id)->exists()) {
            $product->channels()->attach($defaultChannel->id, ['enabled' => true]);
        }

        if (!$product->customerGroups()->where('lunar_customer_groups.id', $defaultCustomerGroup->id)->exists()) {
            $product->customerGroups()->attach($defaultCustomerGroup->id, [
                'enabled' => true,
                'visible' => true,
                'purchasable' => true,
            ]);
        }

        if (!$product->defaultUrl) {
            $product->urls()->create([
                'slug' => 'clickerz-bar',
                'default' => true,
                'language_id' => $language->id,
            ]);
        }

        $capsOption = ProductOption::firstOrCreate(
            ['handle' => 'caps'],
            [
                'name' => ['en' => 'Caps', 'nl' => 'Caps'],
                'label' => ['en' => 'Caps', 'nl' => 'Caps'],
                'shared' => false,
            ]
        );

        if (!$product->productOptions()->where('lunar_product_options.id', $capsOption->id)->exists()) {
            $product->productOptions()->attach($capsOption->id, ['position' => 0]);
        }

        for ($caps = self::MIN_CAPS; $caps <= self::MAX_CAPS; $caps++) {
            $value = ProductOptionValue::firstOrCreate(
                [
                    'product_option_id' => $capsOption->id,
                    'meta->caps' => $caps,
                ],
                [
                    'name' => ['en' => "{$caps} caps", 'nl' => "{$caps} caps"],
                    'position' => $caps - self::MIN_CAPS,
                    'meta' => ['caps' => $caps],
                ]
            );

            $variant = $product->variants()->firstOrCreate(
                ['sku' => "CLKR-BAR-{$caps}"],
                [
                    'tax_class_id' => $defaultTaxClass->id,
                    'shippable' => true,
                    'purchasable' => 'always',
                    'unit_quantity' => 1,
                ]
            );

            if (!$variant->values()->where('value_id', $value->id)->exists()) {
                $variant->values()->attach($value->id);
            }

            $priceInMinorUnits = (int) round(
                (self::BASE_PRICE + $caps * self::CAP_PRICE) * (10 ** $defaultCurrency->decimal_places)
            );

            $variant->prices()->firstOrCreate(
                [
                    'currency_id' => $defaultCurrency->id,
                    'customer_group_id' => null,
                    'min_quantity' => 1,
                ],
                [
                    'price' => $priceInMinorUnits,
                ]
            );
        }
    }
}
