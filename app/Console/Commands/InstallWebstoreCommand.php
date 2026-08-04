<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lunar\Admin\Models\Staff;
use Lunar\Facades\DB;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxZone;

class InstallWebstoreCommand extends Command
{
    /**
     * Sytatsu's take on `lunar:install`: same baseline setup, but with
     * Dutch/English languages and Euro as the default currency instead of
     * Lunar's English/USD defaults, and it seeds the webstore afterwards.
     */
    protected $signature = 'webstore:install {--fresh : Drop all tables and re-run every migration from scratch}';

    protected $description = 'Install Lunar with Sytatsu defaults (Dutch primary / English secondary, EUR currency) and seed the webstore';

    public function handle(): void
    {
        $this->components->info('Installing the webstore...');

        if (! $this->configExists('lunar')) {
            $this->components->info('Publishing Lunar configuration...');
            $this->call('vendor:publish', ['--tag' => 'lunar']);
        }

        $this->components->info('Running migrations...');
        $this->call($this->option('fresh') ? 'migrate:fresh' : 'migrate');

        DB::transaction(function () {
            if (class_exists(Staff::class) && ! Staff::whereAdmin(true)->exists()) {
                $this->components->info('Create a Lunar admin user');
                $this->call('lunar:create-admin');
            }

            if (! Country::count()) {
                $this->components->info('Importing countries');
                $this->call('lunar:import:address-data');
            }

            if (! Channel::whereDefault(true)->exists()) {
                $this->components->info('Setting up default channel');

                Channel::create([
                    'name' => 'Webstore',
                    'handle' => 'webstore',
                    'default' => true,
                    'url' => config('app.url'),
                ]);
            }

            if (! Language::count()) {
                $this->components->info('Adding languages (Dutch default, English secondary)');

                Language::create([
                    'code' => 'nl',
                    'name' => 'Dutch',
                    'default' => true,
                ]);

                Language::create([
                    'code' => 'en',
                    'name' => 'English',
                    'default' => false,
                ]);
            }

            if (! Currency::whereDefault(true)->exists()) {
                $this->components->info('Adding a default currency (EUR)');

                Currency::create([
                    'code' => 'EUR',
                    'name' => 'Euro',
                    'exchange_rate' => 1,
                    'decimal_places' => 2,
                    'default' => true,
                    'enabled' => true,
                ]);
            }

            if (! CustomerGroup::whereDefault(true)->exists()) {
                $this->components->info('Adding a default customer group');

                CustomerGroup::create([
                    'name' => 'Retail',
                    'handle' => 'retail',
                    'default' => true,
                ]);
            }

            if (! CollectionGroup::count()) {
                $this->components->info('Adding an initial collection group');

                // Handle must be "printed": navigation_collection_groups and
                // the collection seeders (Pokeballs, Mini-friends) all look
                // for this handle specifically.
                CollectionGroup::create([
                    'name' => 'Printed',
                    'handle' => 'printed',
                ]);
            }

            if (! TaxClass::count()) {
                $this->components->info('Adding a default tax class');

                TaxClass::create([
                    'name' => 'Default Tax Class',
                    'default' => true,
                ]);
            }

            if (! TaxZone::count()) {
                $this->components->info('Adding a default tax zone');

                $taxZone = TaxZone::create([
                    'name' => 'Default Tax Zone',
                    'zone_type' => 'country',
                    'price_display' => 'tax_exclusive',
                    'default' => true,
                    'active' => true,
                ]);

                $taxZone->countries()->createMany(
                    Country::get()->map(fn ($country) => [
                        'country_id' => $country->id,
                    ])
                );
            }

            $hasCoreProductAttributes = Attribute::whereAttributeType(Product::morphName())
                ->whereHandle('name')
                ->exists();

            if (! $hasCoreProductAttributes) {
                $this->components->info('Setting up initial product attributes');

                $group = AttributeGroup::whereHandle('details')->first() ?? AttributeGroup::create([
                    'attributable_type' => Product::morphName(),
                    'name' => collect(['en' => 'Details']),
                    'handle' => 'details',
                    'position' => 1,
                ]);

                Attribute::create([
                    'attribute_type' => 'product',
                    'attribute_group_id' => $group->id,
                    'position' => 1,
                    'name' => ['en' => 'Name'],
                    'handle' => 'name',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => true,
                    'default_value' => null,
                    'configuration' => ['richtext' => false],
                    'system' => true,
                    'description' => ['en' => ''],
                ]);

                Attribute::create([
                    'attribute_type' => 'product',
                    'attribute_group_id' => $group->id,
                    'position' => 2,
                    'name' => ['en' => 'Description'],
                    'handle' => 'description',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => false,
                    'default_value' => null,
                    'configuration' => ['richtext' => true],
                    'system' => false,
                    'description' => ['en' => ''],
                ]);
            }

            $hasCoreCollectionAttributes = Attribute::whereAttributeType(Collection::morphName())
                ->whereHandle('name')
                ->exists();

            if (! $hasCoreCollectionAttributes) {
                $this->components->info('Setting up initial collection attributes');

                $collectionGroup = AttributeGroup::whereHandle('collection_details')->first() ?? AttributeGroup::create([
                    'attributable_type' => Collection::morphName(),
                    'name' => collect(['en' => 'Details']),
                    'handle' => 'collection_details',
                    'position' => 1,
                ]);

                Attribute::create([
                    'attribute_type' => 'collection',
                    'attribute_group_id' => $collectionGroup->id,
                    'position' => 1,
                    'name' => ['en' => 'Name'],
                    'handle' => 'name',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => true,
                    'default_value' => null,
                    'configuration' => ['richtext' => false],
                    'system' => true,
                    'description' => ['en' => ''],
                ]);

                Attribute::create([
                    'attribute_type' => 'collection',
                    'attribute_group_id' => $collectionGroup->id,
                    'position' => 2,
                    'name' => ['en' => 'Description'],
                    'handle' => 'description',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => false,
                    'default_value' => null,
                    'configuration' => ['richtext' => true],
                    'system' => false,
                    'description' => ['en' => ''],
                ]);
            }

            if (! ProductType::count()) {
                $this->components->info('Adding a product type');

                $type = ProductType::create([
                    'name' => 'Stock',
                ]);

                $type->mappedAttributes()->attach(
                    Attribute::whereAttributeType(Product::morphName())->get()->pluck('id')
                );
            }
        });

        $this->components->info('Publishing Filament assets');
        $this->call('filament:assets');

        $this->components->info('Seeding the webstore...');
        $this->call('db:seed');

        $this->components->info('Webstore installed 🚀');
    }

    private function configExists(string $fileName): bool
    {
        if (! File::isDirectory(config_path($fileName))) {
            return false;
        }

        return ! empty(File::allFiles(config_path($fileName)));
    }
}
