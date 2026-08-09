<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Services\StorefrontService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Livewire\Component;

class SearchBox extends Component
{
    public string $query = '';

    private const MIN_QUERY_LENGTH = 2;

    public function getResultsProperty(): Collection
    {
        $term = trim($this->query);

        if (mb_strlen($term) < self::MIN_QUERY_LENGTH) {
            return collect();
        }

        return app(StorefrontService::class)->searchProducts($term, 6);
    }

    public function getPagesProperty(): Collection
    {
        $term = trim($this->query);

        if (mb_strlen($term) < self::MIN_QUERY_LENGTH) {
            return collect();
        }

        return app(StorefrontService::class)->searchPages($term);
    }

    public function search(): ?Redirector
    {
        $term = trim($this->query);

        if ($term === '') {
            return null;
        }

        return redirect()->route('sytatsu.webstore.search', ['q' => $term]);
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.livewire.search-box', [
            'results' => $this->getResultsProperty(),
            'pages' => $this->getPagesProperty(),
        ]);
    }
}
