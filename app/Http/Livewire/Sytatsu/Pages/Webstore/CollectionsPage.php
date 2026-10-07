<?php

namespace App\Http\Livewire\Sytatsu\Pages\Webstore;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;
use App\Models\WebstoreSetting;
use Lunar\Models\Collection;

class CollectionsPage extends SytatsuBasePage
{
    protected string $view = 'sytatsu.webstore.collections';

    public function mount(): void
    {
        $title = WebstoreSetting::getByKey('collections_page_title');
        $this->setTitle($this->translateValue($title) ?? __('Collections'));
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
    {
        $handles = WebstoreSetting::getByKey('collections_page_collections', []);
        $description = WebstoreSetting::getByKey('collections_page_description');
        $translatedDescription = $this->translateValue($description);

        $this->setDescription($translatedDescription ? \Illuminate\Support\Str::limit(strip_tags($translatedDescription), 160) : null);

        // whereIn('slug', $handles) only filters, it doesn't honor the
        // array's order — re-sort by $handles ourselves so the order
        // chosen on CollectionsPageSettingsPage is actually reflected
        // here instead of whatever order the query happened to return.
        $collectionsBySlug = Collection::query()
            ->whereHas('urls', function ($query) use ($handles) {
                $query->whereIn('slug', $handles);
            })
            ->with(['defaultUrl', 'thumbnail'])
            ->get()
            ->keyBy(fn ($collection) => $collection->defaultUrl?->slug);

        $collections = \Illuminate\Support\Collection::make($handles)
            ->map(fn ($slug) => $collectionsBySlug->get($slug))
            ->filter()
            ->values();

        $this->setViewAttributes([
            'collections' => $collections,
            'title' => $this->translateValue(WebstoreSetting::getByKey('collections_page_title')) ?? __('Collections'),
            'description' => $this->translateValue($description),
            'maxWidth' => 'max-w-[85rem]',
        ]);

        return parent::render();
    }

    protected function translateValue($value)
    {
        if (is_array($value)) {
            return $value[app()->getLocale()] ?? $value[config('app.fallback_locale', 'en')] ?? collect($value)->first();
        }

        return $value;
    }
}
