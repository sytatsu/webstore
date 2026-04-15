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

        $collections = Collection::query()
            ->whereHas('urls', function ($query) use ($handles) {
                $query->whereIn('slug', $handles);
            })
            ->with(['defaultUrl', 'thumbnail'])
            ->get();

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
