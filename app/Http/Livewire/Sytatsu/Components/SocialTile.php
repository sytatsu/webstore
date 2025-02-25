<?php

namespace App\Http\Livewire\Sytatsu\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use function asset;
use function config;
use function view;

class SocialTile extends Component
{
    public string $href;
    public string $src;
    public string $alt;
    public int $dimensions;

    public function mount(
        string $config,
        string $src,
        string $alt,
        int $dimensions = 80
    ): void {
        $this->href = config("{$config}.href");
        $this->src = asset($src);
        $this->alt = $alt;
        $this->dimensions = $dimensions;
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.social', [
            'href' => $this->href,
            'src' => $this->src,
            'alt' => $this->alt,
            'dimensions' => $this->dimensions,
        ]);
    }
}
