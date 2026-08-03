<?php

namespace App\Console\Commands\Seeders;

use App\Models\Collection;
use App\Models\WebstoreSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\CollectionGroup;

class SeedMiniFriendsCollectionCommand extends Command
{
    protected $signature = 'webstore:seed:mini-friends';

    protected $description = 'Seed the Mini-friends collection and its category sub-collections';

    public function handle(): void
    {
        $collectionGroup = CollectionGroup::where('handle', 'printed')->first()
            ?? CollectionGroup::first();

        // Find "Printed" collection to be the parent
        $printed = Collection::all()->filter(function ($c) {
            return strtolower((string) $c->translateAttribute('name')) === 'printed';
        })->first();

        $miniFriends = Collection::where('attribute_data->name->value->en', 'Mini-friends')->first();

        if (!$miniFriends) {
            $miniFriends = Collection::create(
                [
                    'collection_group_id' => $collectionGroup->id,
                    'attribute_data' => [
                        'name' => new TranslatedText([
                            'en' => 'Mini-friends',
                            'nl' => 'Mini-friends',
                        ]),
                    ],
                ]
            );

            if ($printed) {
                $miniFriends->parent_id = $printed->id;
                $miniFriends->save();
            }
        } else if ($miniFriends->translateAttribute('name', 'nl') !== 'Mini-friends') {
            $miniFriends->attribute_data['name'] = new TranslatedText([
                'en' => 'Mini-friends',
                'nl' => 'Mini-friends',
            ]);
            $miniFriends->save();
        }

        if (!$miniFriends->defaultUrl) {
            $miniFriends->urls()->create([
                'slug' => 'mini-friends',
                'default' => true,
                'language_id' => 1,
            ]);
        } else if ($miniFriends->defaultUrl->slug !== 'mini-friends') {
            $miniFriends->defaultUrl->update(['slug' => 'mini-friends']);
        }

        $categories = [
            'Fantasy' => 'Fantasie',
            'Safari' => 'Safari',
            'Pets' => 'Huisdieren',
            'Ocean' => 'Oceaan',
            'Birds' => 'Vogels',
            'Dinosaurs' => 'Dinosaurs',
        ];

        foreach ($categories as $categoryName => $categoryNameNl) {
            $categorySlug = Str::slug($categoryName);

            $categoryCollection = Collection::where('parent_id', $miniFriends->id)
                ->get()
                ->filter(function ($c) use ($categoryName) {
                    return (string) $c->translateAttribute('name') === $categoryName;
                })->first();

            if (!$categoryCollection) {
                $categoryCollection = Collection::create([
                    'collection_group_id' => $collectionGroup->id,
                    'attribute_data' => [
                        'name' => new TranslatedText([
                            'en' => $categoryName,
                            'nl' => $categoryNameNl,
                        ]),
                    ],
                ]);

                $categoryCollection->parent_id = $miniFriends->id;
                $categoryCollection->save();

                $categoryCollection->urls()->create([
                    'slug' => $categorySlug,
                    'default' => true,
                    'language_id' => 1,
                ]);
            } else if ($categoryCollection->translateAttribute('name', 'nl') !== $categoryNameNl) {
                $categoryCollection->attribute_data['name'] = new TranslatedText([
                    'en' => $categoryName,
                    'nl' => $categoryNameNl,
                ]);
                $categoryCollection->save();
            }
        }

        $this->featureOnHomepage($miniFriends->id);

        $this->components->info('Mini-friends collection seeded');
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
