<?php

namespace App\Http\Livewire\Sytatsu\Components\Partials;

use App\Services\LayoutService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use function view;

class Head extends Component
{
    protected string $layout;
    protected ?string $appName;
    protected ?string $title;
    protected LayoutService $layoutService;

    public function mount(
        ?string $title = null,
        ?string $appName = null,
        LayoutService $layoutService
    ): void {
        $this->layout = 'minimal';
        $this->appName = $appName;
        $this->title = $title;
        $this->layoutService = $layoutService;
    }

    public function render(): Factory|Application|View
    {
        return view('sytatsu.components.partials.head', $this->layoutService->getAttributes($this->layout, $this->title, $this->appName));
    }
}
