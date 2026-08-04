<?php

namespace App\Console\Commands\Seeders;

use App\Models\BarBuilderBaseColor;
use App\Models\BarBuilderCapCombo;
use App\Models\BarBuilderIcon;
use Illuminate\Console\Command;
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

class SeedClickerzBarProductCommand extends Command
{
    protected $signature = 'webstore:seed:clickerz-bar';

    protected $description = 'Seed the Clickerz Bar product, its Bar Builder catalog (base colors, cap combos, icons), and its variants';

    /**
     * The number of caps the customer can choose from, and their all-in price.
     */
    private const BASE_PRICE = 0.95;

    private const CAP_PRICE = 1.00;

    private const MIN_CAPS = 2;

    private const MAX_CAPS = 10;

    public function handle(): void
    {
        $this->seedBaseColors();
        $this->seedCapCombos();
        $this->seedIcons();
        $this->seedProduct();

        $this->components->info('Clickerz Bar product seeded');
    }

    private function seedBaseColors(): void
    {
        $colors = [
            ['name' => 'Lava Red', 'name_nl' => 'Lava Rood', 'hex' => '#ED2F2E'],
            ['name' => 'Army Red', 'name_nl' => 'Leger Rood', 'hex' => '#BF312E'],
            ['name' => 'Muted Red', 'name_nl' => 'Gedempt Rood', 'hex' => '#A9564B'],
            ['name' => 'Pastel Peach', 'name_nl' => 'Pastel Perzik', 'hex' => '#F6BF8B'],
            ['name' => 'Sunrise Orange', 'name_nl' => 'Zonsopgang Oranje', 'hex' => '#F88B17'],
            ['name' => 'Pastel Banana Yellow', 'name_nl' => 'Pastel Banaan Geel', 'hex' => '#F7D475'],
            ['name' => 'Savannah Yellow', 'name_nl' => 'Savannah Geel', 'hex' => '#F3C432'],
            ['name' => 'Army Dark Green', 'name_nl' => 'Leger Donkergroen', 'hex' => '#5F6244'],
            ['name' => 'Pastel Mint', 'name_nl' => 'Pastel Mint', 'hex' => '#D2DEBB'],
            ['name' => 'Muted Green', 'name_nl' => 'Gedempt Groen', 'hex' => '#578052'],
            ['name' => 'Forest Green', 'name_nl' => 'Bosgroen', 'hex' => '#60AD70'],
            ['name' => 'Pastel Ice', 'name_nl' => 'Pastel Ijs', 'hex' => '#A4D0DF'],
            ['name' => 'Sapphire Blue', 'name_nl' => 'Sapphire Blauw', 'hex' => '#0163A6'],
            ['name' => 'Muted Blue', 'name_nl' => 'Gedempt Blauw', 'hex' => '#4E6F8A'],
            ['name' => 'Army Dark Blue', 'name_nl' => 'Leger Donkerblauw', 'hex' => '#2E4462'],
            ['name' => 'Electric Indigo', 'name_nl' => 'Elektrisch Indigo', 'hex' => '#6858A9'],
            ['name' => 'Lavender Purple', 'name_nl' => 'Lavendel Paars', 'hex' => '#9572BF'],
            ['name' => 'Muted Purple', 'name_nl' => 'Gedempt Paars', 'hex' => '#786490'],
            ['name' => 'Lotus Pink', 'name_nl' => 'Lotus Roze', 'hex' => '#DD76C0'],
            ['name' => 'Sakura Pink', 'name_nl' => 'Sakura Roze', 'hex' => '#EAADBD'],

            ['name' => 'Beige', 'name_nl' => 'Beige', 'hex' => '#DBBAA5'],
            ['name' => 'Pastel Peanut', 'name_nl' => 'Pastel Pinda', 'hex' => '#BF9573'],

            ['name' => 'Charcoal Black', 'name_nl' => 'Houtscool Zwart', 'hex' => '#2F2E30'],
            ['name' => 'Ash Grey', 'name_nl' => 'As Grijs', 'hex' => '#485155'],
            ['name' => 'Fossil Grey', 'name_nl' => 'Fossiel Grijs', 'hex' => '#8A8C94'],
            ['name' => 'Muted White', 'name_nl' => 'Gedempt Wit', 'hex' => '#BBADA4'],
            ['name' => 'Cotton White', 'name_nl' => 'Katoen Wit', 'hex' => '#F4EFEB'],
        ];

        foreach ($colors as $index => $color) {
            BarBuilderBaseColor::firstOrCreate(
                ['name->en' => $color['name']],
                [
                    'name' => ['en' => $color['name'], 'nl' => $color['name_nl']],
                    'hex' => $color['hex'],
                    'enabled' => $color['enabled'] ?? true,
                    'sort_order' => $index,
                ]
            );
        }
    }

    private function seedCapCombos(): void
    {
        // Mirrors the base colour palette (in the same rainbow order), so
        // caps and the base share one consistent set of colours. White
        // caps aren't offered here (beige, peanut, black through white are
        // base-only), and muted colours are pulled to the end with the
        // pairing flipped: a white cap with the muted colour as the letters.
        $combos = [
            ['name' => 'Lava Red | Cotton White', 'name_nl' => 'Lava Rood | Katoen Wit', 'cap_hex' => '#ED2F2E', 'text_hex' => '#FFFFFF'],
            ['name' => 'Army Red | Cotton White', 'name_nl' => 'Leger Rood | Katoen Wit', 'cap_hex' => '#BF312E', 'text_hex' => '#FFFFFF'],
            ['name' => 'Pastel Peach | Cotton White', 'name_nl' => 'Pastel Perzik | Katoen Wit', 'cap_hex' => '#F6BF8B', 'text_hex' => '#FFFFFF'],
            ['name' => 'Sunrise Orange | Cotton White', 'name_nl' => 'Zonsopgang Oranje | Katoen Wit', 'cap_hex' => '#F88B17', 'text_hex' => '#FFFFFF'],
            ['name' => 'Pastel Banana Yellow | Cotton White', 'name_nl' => 'Pastel Banaan Geel | Katoen Wit', 'cap_hex' => '#F7D475', 'text_hex' => '#FFFFFF'],
            ['name' => 'Savannah Yellow | Cotton White', 'name_nl' => 'Savannah Geel | Katoen Wit', 'cap_hex' => '#F3C432', 'text_hex' => '#FFFFFF'],
            ['name' => 'Army Dark Green | Cotton White', 'name_nl' => 'Leger Donkergroen | Katoen Wit', 'cap_hex' => '#5F6244', 'text_hex' => '#FFFFFF'],
            ['name' => 'Pastel Mint | Cotton White', 'name_nl' => 'Pastel Mint | Katoen Wit', 'cap_hex' => '#D2DEBB', 'text_hex' => '#FFFFFF'],
            ['name' => 'Forest Green | Cotton White', 'name_nl' => 'Bosgroen | Katoen Wit', 'cap_hex' => '#60AD70', 'text_hex' => '#FFFFFF'],
            ['name' => 'Pastel Ice | Cotton White', 'name_nl' => 'Pastel Ijs | Katoen Wit', 'cap_hex' => '#A4D0DF', 'text_hex' => '#FFFFFF'],
            ['name' => 'Sapphire Blue | Cotton White', 'name_nl' => 'Sapphire Blauw | Katoen Wit', 'cap_hex' => '#0163A6', 'text_hex' => '#FFFFFF'],
            ['name' => 'Army Dark Blue | Cotton White', 'name_nl' => 'Leger Donkerblauw | Katoen Wit', 'cap_hex' => '#2E4462', 'text_hex' => '#FFFFFF'],
            ['name' => 'Electric Indigo | Cotton White', 'name_nl' => 'Elektrisch Indigo | Katoen Wit', 'cap_hex' => '#6858A9', 'text_hex' => '#FFFFFF'],
            ['name' => 'Lavender Purple | Cotton White', 'name_nl' => 'Lavendel Paars | Katoen Wit', 'cap_hex' => '#9572BF', 'text_hex' => '#FFFFFF'],
            ['name' => 'Lotus Pink | Cotton White', 'name_nl' => 'Lotus Roze | Katoen Wit', 'cap_hex' => '#DD76C0', 'text_hex' => '#FFFFFF'],
            ['name' => 'Sakura Pink | Cotton White', 'name_nl' => 'Sakura Roze | Katoen Wit', 'cap_hex' => '#EAADBD', 'text_hex' => '#FFFFFF'],

            ['name' => 'Muted White | Muted Red', 'name_nl' => 'Gedempt Wit | Gedempt Rood', 'cap_hex' => '#FFFFFF', 'text_hex' => '#A9564B'],
            ['name' => 'Muted White | Muted Green', 'name_nl' => 'Gedempt Wit | Gedempt Groen', 'cap_hex' => '#FFFFFF', 'text_hex' => '#578052'],
            ['name' => 'Muted White | Muted Blue', 'name_nl' => 'Gedempt Wit | Gedempt Blauw', 'cap_hex' => '#FFFFFF', 'text_hex' => '#4E6F8A'],
            ['name' => 'Muted White | Muted Purple', 'name_nl' => 'Gedempt Wit | Gedempt Paars', 'cap_hex' => '#FFFFFF', 'text_hex' => '#786490'],
        ];

        foreach ($combos as $index => $combo) {
            BarBuilderCapCombo::firstOrCreate(
                ['name->en' => $combo['name'], 'cap_hex' => $combo['cap_hex'], 'text_hex' => $combo['text_hex']],
                [
                    'name' => ['en' => $combo['name'], 'nl' => $combo['name_nl']],
                    'enabled' => $combo['enabled'] ?? true,
                    'sort_order' => $index,
                ]
            );
        }
    }

    private function seedIcons(): void
    {
        BarBuilderIcon::firstOrCreate(
            ['name->en' => 'Dog paw'],
            [
                'name' => ['en' => 'Dog paw', 'nl' => 'Hondenpoot'],
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
            ['name->en' => 'Heart'],
            [
                'name' => ['en' => 'Heart', 'nl' => 'Hart'],
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
            ['name->en' => 'Music note'],
            [
                'name' => ['en' => 'Music note', 'nl' => 'Muzieknoot'],
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
            ['name->en' => 'Headset'],
            [
                'name' => ['en' => 'Headset', 'nl' => 'Headset'],
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

        if (!$product->thumbnail) {
            $product->addMedia(resource_path('images/seeders/clickerz-bar-thumbnail.png'))
                ->preservingOriginal()
                ->usingFileName('clickerz-bar.png')
                ->withCustomProperties(['primary' => true])
                ->toMediaCollection(config('lunar.media.collection'));
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
