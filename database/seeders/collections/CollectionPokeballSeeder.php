<?php

namespace Database\Seeders\collections;

use Illuminate\Database\Seeder;
use App\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Illuminate\Support\Str;

class CollectionPokeballSeeder extends Seeder
{
    public function run(): void
    {
        $collectionGroup = CollectionGroup::where('handle', 'printed')->first()
            ?? CollectionGroup::first();

        $pokeballs = Collection::where('attribute_data->name->en', 'Pokeballs')->first();

        if (!$pokeballs) {
            $pokeballs = Collection::create(
                [
                    'collection_group_id' => $collectionGroup->id,
                    'attribute_data' => [
                        'name' => new TranslatedText([
                            'en' => 'Pokeballs',
                            'nl' => 'Pokéballs',
                        ]),
                    ],
                ]
            );
        } else if ($pokeballs->translate('name', 'nl') !== 'Pokéballs') {
            $pokeballs->attribute_data['name'] = new TranslatedText([
                'en' => 'Pokeballs',
                'nl' => 'Pokéballs',
            ]);
            $pokeballs->save();
        }

        if (!$pokeballs->defaultUrl) {
            $pokeballs->urls()->create([
                'slug' => 'pokeballs',
                'default' => true,
                'language_id' => 1,
            ]);
        } else if ($pokeballs->defaultUrl->slug !== 'pokeballs') {
            $pokeballs->defaultUrl->update(['slug' => 'pokeballs']);
        }

        for ($i = 1; $i <= 9; $i++) {
            $genName = "Generation $i";
            $genNameNl = "Generatie $i";
            $genSlug = Str::slug($genName);

            $genCollection = Collection::where('parent_id', $pokeballs->id)
                ->get()
                ->filter(function($c) use ($genName) {
                    return (string)$c->translate('name') === $genName;
                })->first();

            if (!$genCollection) {
                $genCollection = Collection::create([
                    'collection_group_id' => $collectionGroup->id,
                    'attribute_data' => [
                        'name' => new TranslatedText([
                            'en' => $genName,
                            'nl' => $genNameNl,
                        ]),
                    ],
                ]);

                $genCollection->parent_id = $pokeballs->id;
                $genCollection->save();

                $genCollection->urls()->create([
                    'slug' => $genSlug,
                    'default' => true,
                    'language_id' => 1,
                ]);
            } else if ($genCollection->translate('name', 'nl') !== $genNameNl) {
                $genCollection->attribute_data['name'] = new TranslatedText([
                    'en' => $genName,
                    'nl' => $genNameNl,
                ]);
                $genCollection->save();
            }
        }
    }
}
