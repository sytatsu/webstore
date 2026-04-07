<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renaming data is usually better in seeders, but since user asked for "migseeder ration"
        // and specifically "collection printed should be renamed from main", I will put the rename logic here too.

        $mainCollection = \Lunar\Models\Collection::all()->filter(function($c) {
            return strtolower((string)$c->translate('name')) === 'main';
        })->first();

        if ($mainCollection) {
            $mainCollection->attribute_data['name'] = new \Lunar\FieldTypes\TranslatedText([
                'en' => 'Printed',
                'nl' => 'Geprint',
            ]);
            $mainCollection->save();

            if ($mainCollection->defaultUrl && $mainCollection->defaultUrl->slug === 'main') {
                $mainCollection->defaultUrl->update(['slug' => 'printed']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $printedCollection = \Lunar\Models\Collection::all()->filter(function($c) {
            return strtolower((string)$c->translate('name')) === 'printed';
        })->first();

        if ($printedCollection) {
            $printedCollection->attribute_data['name'] = new \Lunar\FieldTypes\TranslatedText([
                'en' => 'Main',
            ]);
            $printedCollection->save();

            if ($printedCollection->defaultUrl && $printedCollection->defaultUrl->slug === 'printed') {
                $printedCollection->defaultUrl->update(['slug' => 'main']);
            }
        }
    }
};
