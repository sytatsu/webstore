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
    public string $srcLight;
    public string $srcDark;
    public string $alt;
    public int $dimensions;

    public function mount(
        string $config,
        string $srcLight,
        string $srcDark,
        string $alt,
        int $dimensions = 80
    ): void {
        $this->href = config("{$config}.href");
        $this->srcLight = asset($srcLight);
        $this->srcDark = asset($srcDark);
        $this->alt = $alt;
        $this->dimensions = $dimensions;
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.social', [
            'href' => $this->href,
            'srcLight' => $this->srcLight,
            'srcDark' => $this->srcDark,
            'alt' => $this->alt,
            'dimensions' => $this->dimensions,
        ]);
    }
}
