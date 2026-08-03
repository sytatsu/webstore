<?php

namespace App\Console\Commands\Seeders;

use App\Models\Collection;
use App\Models\WebstoreSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\CollectionGroup;

class SeedPokeballCollectionCommand extends Command
{
    protected $signature = 'webstore:seed:pokeballs';

    protected $description = 'Seed the Pokeballs collection and its generation sub-collections';

    public function handle(): void
    {
        $collectionGroup = CollectionGroup::where('handle', 'printed')->first()
            ?? CollectionGroup::first();

        $pokeballs = Collection::where('attribute_data->name->value->en', 'Pokeballs')->first();

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
        } else if ($pokeballs->translateAttribute('name', 'nl') !== 'Pokéballs') {
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
                ->filter(function ($c) use ($genName) {
                    return (string) $c->translateAttribute('name') === $genName;
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
            } else if ($genCollection->translateAttribute('name', 'nl') !== $genNameNl) {
                $genCollection->attribute_data['name'] = new TranslatedText([
                    'en' => $genName,
                    'nl' => $genNameNl,
                ]);
                $genCollection->save();
            }
        }

        $this->featureOnHomepage($pokeballs->id);

        $this->components->info('Pokeballs collection seeded');
    }

    private function featureOnHomepage(int $collectionId): void
    {
        $featured = WebstoreSetting::getByKey('home_featured_collections', []);

        if (!is_array($featured)) {
            $featured = [];
        }

        if (!in_array($collectionId, $featured)) {
            $featured[] = $collectionId;
            WebstoreSetting::setByKey('home_featured_collections', $featured);
        }
    }
}
