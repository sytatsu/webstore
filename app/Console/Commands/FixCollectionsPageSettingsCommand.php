<?php

namespace App\Console\Commands;

use App\Filament\Pages\CollectionsPageSettingsPage;
use App\Models\WebstoreSetting;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Collection;

class FixCollectionsPageSettingsCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The old WebstoreSettingResource Repeater dehydrated
     * collections_page_collections to collection ids (and sometimes
     * `{"collection_id": ...}` rows), while CollectionsPage and
     * CollectionsPageSettingsPage both expect a flat list of slugs.
     * Locally that was fixed by hand; this command does the same on any
     * environment: it rewrites the three collections_page_* settings into
     * the shape the code expects, either from what's stored or from the
     * values passed in.
     */
    protected $signature = 'webstore:fix-collections-page-settings
        {--collections=* : Collection slugs or ids to show, in order (replaces the stored list)}
        {--title-en= : English title (replaces the stored value)}
        {--title-nl= : Dutch title (replaces the stored value)}
        {--description-en= : English description (replaces the stored value)}
        {--description-nl= : Dutch description (replaces the stored value)}
        {--dry-run : Show what would be written without saving}
        {--force : Skip the confirmation prompt in production}';

    protected $description = 'Overwrite the Collections page settings with valid, normalized values';

    public function handle(): int
    {
        $keys = [
            CollectionsPageSettingsPage::TITLE_KEY,
            CollectionsPageSettingsPage::DESCRIPTION_KEY,
            CollectionsPageSettingsPage::COLLECTIONS_KEY,
        ];

        $current = collect($keys)->mapWithKeys(fn (string $key) => [$key => WebstoreSetting::getByKey($key)]);

        $new = [
            CollectionsPageSettingsPage::TITLE_KEY => $this->translations(
                $current[CollectionsPageSettingsPage::TITLE_KEY],
                $this->option('title-en'),
                $this->option('title-nl'),
            ),
            CollectionsPageSettingsPage::DESCRIPTION_KEY => $this->translations(
                $current[CollectionsPageSettingsPage::DESCRIPTION_KEY],
                $this->option('description-en'),
                $this->option('description-nl'),
            ),
            CollectionsPageSettingsPage::COLLECTIONS_KEY => $this->collectionSlugs(
                $this->option('collections') ?: $current[CollectionsPageSettingsPage::COLLECTIONS_KEY],
            ),
        ];

        $this->table(
            ['Key', 'Current', 'New'],
            collect($keys)->map(fn (string $key) => [
                $key,
                json_encode($current[$key], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($new[$key], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ])->all(),
        );

        if ($this->option('dry-run')) {
            $this->components->info('Dry run, nothing was saved.');

            return self::SUCCESS;
        }

        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        DB::transaction(function () use ($new) {
            foreach ($new as $key => $value) {
                WebstoreSetting::setByKey($key, $value);
            }
        });

        $this->components->info('Collections page settings saved.');

        return self::SUCCESS;
    }

    /**
     * Same per-locale shape CollectionsPageSettingsPage saves: a bare
     * string is treated as the English value, empty locales are dropped.
     */
    protected function translations($stored, ?string $en, ?string $nl): array
    {
        $value = match (true) {
            is_string($stored) => ['en' => $stored],
            is_array($stored) => $stored,
            default => [],
        };

        $value = collect(['en' => $value['en'] ?? null, 'nl' => $value['nl'] ?? null])
            ->map(fn ($text) => is_string($text) ? $text : null);

        if ($en !== null) {
            $value['en'] = $en;
        }

        if ($nl !== null) {
            $value['nl'] = $nl;
        }

        return $value->filter(fn ($text) => filled($text))->all();
    }

    /**
     * Accepts slugs, numeric ids and `{"collection_id": ...}` / `{"slug": ...}`
     * rows, and resolves each to the collection's default URL slug, which
     * is what CollectionsPage keys its results by. Unknown entries and
     * duplicates are dropped (with a warning) instead of being saved.
     */
    protected function collectionSlugs($entries): array
    {
        $slugs = [];

        foreach ((array) $entries as $entry) {
            if (is_array($entry)) {
                $entry = $entry['slug'] ?? $entry['collection_id'] ?? null;
            }

            if (! is_scalar($entry) || blank($entry)) {
                $this->components->warn('Skipping unreadable entry: '.json_encode($entry));

                continue;
            }

            $collection = is_numeric($entry)
                ? Collection::with('defaultUrl')->find((int) $entry)
                : Collection::with('defaultUrl')->whereHas('urls', fn ($query) => $query->where('slug', $entry))->first();

            $slug = $collection?->defaultUrl?->slug;

            if ($slug === null) {
                $this->components->warn("Skipping \"{$entry}\": no collection with a default URL found.");

                continue;
            }

            if (! in_array($slug, $slugs, true)) {
                $slugs[] = $slug;
            }
        }

        return $slugs;
    }
}
